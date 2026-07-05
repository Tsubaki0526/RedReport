<?php
require_once __DIR__ . '/../../autoload.inc.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailQueue {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public static function getInstance(PDO $pdo): self {
        return new self($pdo);
    }

    public function add($para, $asunto, $cuerpo, $adjuntos = [], $id_cliente = null, $id_factura = null): int {
        $stmt = $this->pdo->prepare("INSERT INTO tb_email_queue (para, asunto, cuerpo, adjuntos, id_cliente, id_factura, estado, created_at) VALUES (?, ?, ?, ?, ?, ?, 'pendiente', NOW())");
        $stmt->execute([
            $para,
            $asunto,
            $cuerpo,
            !empty($adjuntos) ? json_encode($adjuntos) : null,
            $id_cliente,
            $id_factura
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function processQueue($limit = 10): array {
        $sent = 0;
        $failed = 0;
        $errors = [];

        $stmt = $this->pdo->prepare("SELECT * FROM tb_email_queue WHERE estado = 'pendiente' ORDER BY created_at ASC LIMIT ?");
        $stmt->execute([(int) $limit]);
        $items = $stmt->fetchAll();

        if (empty($items)) {
            return ['sent' => 0, 'failed' => 0, 'errors' => []];
        }

        foreach ($items as $item) {
            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = SMTP_HOST;
                $mail->SMTPAuth   = true;
                $mail->Username   = SMTP_USER;
                $mail->Password   = SMTP_PASS;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = SMTP_PORT;
                $mail->CharSet    = 'UTF-8';

                $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
                $mail->addAddress($item['para']);
                $mail->Subject = $item['asunto'];
                $mail->Body    = $item['cuerpo'];
                $mail->isHTML(true);

                if (!empty($item['adjuntos'])) {
                    $adjuntos = json_decode($item['adjuntos'], true);
                    if (is_array($adjuntos)) {
                        foreach ($adjuntos as $adj) {
                            $path = $adj;
                            if (!empty($adj['path'])) $path = $adj['path'];
                            if (!empty($adj['name'])) $path = $adj['name'];
                            if (file_exists($path)) {
                                $mail->addAttachment($path);
                            }
                        }
                    }
                }

                $mail->send();

                $upd = $this->pdo->prepare("UPDATE tb_email_queue SET estado = 'enviado', sent_at = NOW(), intentos = intentos + 1 WHERE id_cola = ?");
                $upd->execute([$item['id_cola']]);
                $sent++;
            } catch (Exception $e) {
                $msg = $mail->ErrorInfo ?? $e->getMessage();
                $intentos = $item['intentos'] + 1;
                $nuevoEstado = $intentos >= 3 ? 'error' : 'pendiente';
                $upd = $this->pdo->prepare("UPDATE tb_email_queue SET estado = ?, intentos = ?, error_msg = ? WHERE id_cola = ?");
                $upd->execute([$nuevoEstado, $intentos, $msg, $item['id_cola']]);
                $failed++;
                $errors[] = ['id' => $item['id_cola'], 'error' => $msg];
            }
        }

        return ['sent' => $sent, 'failed' => $failed, 'errors' => $errors];
    }

    public function getStatus(): array {
        $total = $this->pdo->query("SELECT COUNT(*) FROM tb_email_queue")->fetchColumn();
        $pendientes = $this->pdo->query("SELECT COUNT(*) FROM tb_email_queue WHERE estado = 'pendiente'")->fetchColumn();
        $enviados = $this->pdo->query("SELECT COUNT(*) FROM tb_email_queue WHERE estado = 'enviado'")->fetchColumn();
        $errores = $this->pdo->query("SELECT COUNT(*) FROM tb_email_queue WHERE estado = 'error'")->fetchColumn();
        return [
            'total'      => (int) $total,
            'pendientes' => (int) $pendientes,
            'enviados'   => (int) $enviados,
            'errores'    => (int) $errores
        ];
    }

    public static function addStatic(PDO $pdo, $para, $asunto, $cuerpo, $adjuntos = [], $id_cliente = null, $id_factura = null): int {
        $eq = new self($pdo);
        return $eq->add($para, $asunto, $cuerpo, $adjuntos, $id_cliente, $id_factura);
    }

    public static function processStatic(PDO $pdo, $limit = 10): array {
        $eq = new self($pdo);
        return $eq->processQueue($limit);
    }

    public static function statusStatic(PDO $pdo): array {
        $eq = new self($pdo);
        return $eq->getStatus();
    }

    public function retryErrors(): int {
        $stmt = $this->pdo->prepare("UPDATE tb_email_queue SET estado = 'pendiente', intentos = 0, error_msg = NULL WHERE estado = 'error'");
        $stmt->execute();
        return $stmt->rowCount();
    }

    public static function retryErrorsStatic(PDO $pdo): int {
        $eq = new self($pdo);
        return $eq->retryErrors();
    }
}
