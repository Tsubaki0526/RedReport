<?php
if (defined('TOTP_CARGADO')) return;
define('TOTP_CARGADO', true);

class TOTP
{
    public function generateSecret()
    {
        return $this->base32Encode(random_bytes(16));
    }

    public function getQRCodeUrl($secret, $userEmail)
    {
        $appName = defined('APP_NAME') ? APP_NAME : 'RedReport';
        return 'otpauth://totp/' . rawurlencode($appName) . ':' . rawurlencode($userEmail)
            . '?secret=' . rawurlencode($secret) . '&issuer=' . rawurlencode($appName);
    }

    public function verify($secret, $code)
    {
        if (!preg_match('/^\d{6}$/', $code)) {
            return false;
        }
        $key = $this->base32Decode($secret);
        if ($key === false) {
            return false;
        }
        $counter = floor(time() / 30);
        for ($i = -1; $i <= 1; $i++) {
            $expected = $this->generateTOTP($key, $counter + $i);
            if (hash_equals($expected, $code)) {
                return true;
            }
        }
        return false;
    }

    public function getGoogleQRCodeUrl($secret, $userEmail)
    {
        $otpauth = $this->getQRCodeUrl($secret, $userEmail);
        return 'https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=' . rawurlencode($otpauth);
    }

    private function generateTOTP($key, $counter)
    {
        $counterBin = pack('N*', 0) . pack('N*', $counter & 0xFFFFFFFF);
        $hash = hash_hmac('sha1', $counterBin, $key, true);
        $offset = ord($hash[19]) & 0x0f;
        $code = (ord($hash[$offset]) & 0x7f) << 24
              | (ord($hash[$offset + 1]) & 0xff) << 16
              | (ord($hash[$offset + 2]) & 0xff) << 8
              | (ord($hash[$offset + 3]) & 0xff);
        $code %= 1000000;
        return str_pad($code, 6, '0', STR_PAD_LEFT);
    }

    private function base32Encode($data)
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $binary = '';
        foreach (str_split($data) as $char) {
            $binary .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }
        $output = '';
        $len = strlen($binary);
        for ($i = 0; $i < $len; $i += 5) {
            $chunk = substr($binary, $i, 5);
            $output .= $alphabet[bindec(str_pad($chunk, 5, '0', STR_PAD_RIGHT))];
        }
        return $output;
    }

    private function base32Decode($data)
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $data = strtoupper(str_replace('=', '', $data));
        $binary = '';
        $len = strlen($data);
        for ($i = 0; $i < $len; $i++) {
            $pos = strpos($alphabet, $data[$i]);
            if ($pos === false) return false;
            $binary .= str_pad(decbin($pos), 5, '0', STR_PAD_LEFT);
        }
        $output = '';
        $blen = strlen($binary);
        for ($i = 0; $i + 7 < $blen; $i += 8) {
            $chunk = substr($binary, $i, 8);
            $output .= chr(bindec($chunk));
        }
        return $output;
    }
}
