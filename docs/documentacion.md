# RedReport — Documentación Técnica para Compradores

**Versión del sistema:** 2.0.0  
**Última actualización:** Julio 2026  
**Plataforma:** CodeCanyon  

---

## Índice

1. [Introducción](#1-introducción)
2. [Requisitos del Sistema](#2-requisitos-del-sistema)
3. [Instalación Paso a Paso](#3-instalación-paso-a-paso)
4. [Configuración Post-Instalación](#4-configuración-post-instalación)
5. [Guía de Módulos](#5-guía-de-módulos)
6. [Roles y Permisos](#6-roles-y-permisos)
7. [App Móvil PWA](#7-app-móvil-pwa)
8. [Seguridad](#8-seguridad)
9. [API REST](#9-api-rest)
10. [Base de Datos](#10-base-de-datos)
11. [Personalización](#11-personalización)
12. [Solución de Problemas Comunes](#12-solución-de-problemas-comunes)
13. [Soporte](#13-soporte)

---

## 1. Introducción

### 1.1 ¿Qué es RedReport?

RedReport es un sistema de gestión empresarial integral diseñado específicamente para **Proveedores de Servicios de Internet (ISP)**. Proporciona una plataforma unificada que cubre todas las operaciones críticas de un ISP moderno: administración de clientes, facturación recurrente, inventario de equipos, instalaciones con geolocalización, órdenes de servicio, tickets de soporte, monitoreo de red, ventas y más.

El sistema está construido con **PHP 8.x nativo** (compatible con 7.4+) y **MySQL/MariaDB**, utilizando únicamente librerías modernas del ecosistema PHP (PHPMailer, Dompdf) y componentes frontend de primer nivel (Bootstrap 5, DataTables, Chart.js, Leaflet, SweetAlert2). No depende de frameworks pesados como Laravel o Symfony, lo que lo hace **ligero, rápido y fácil de desplegar** en cualquier hosting compartido o VPS.

### 1.2 ¿Para quién es este producto?

- **ISP pequeños y medianos** que necesitan un sistema todo-en-uno para gestionar su operación diaria.
- **Empresas WISP** que requieren geolocalización de clientes, mapas de cobertura y monitoreo de red.
- **Integradores y consultores** que implementan soluciones de gestión para múltiples ISP.
- **Desarrolladores** que buscan una base de código limpia, bien estructurada y fácil de extender.

### 1.3 Propuesta de Valor

- **Código 100% abierto** (sin ionCube ni cifrados): puedes auditar, modificar y extender cada línea.
- **App móvil PWA incluida** — sin necesidad de publicar en Google Play o App Store.
- **Interfaz profesional** con Bootstrap 5, diseño responsivo, modo oscuro y sidebar dinámica.
- **Arquitectura modular** — cada módulo es independiente y fácil de mantener.
- **Facturación recurrente** automatizada para contratos activos.
- **Reportes exportables** a PDF, Excel, CSV y copia impresa con DataTables Buttons.
- **API REST completa** para integraciones externas (pagos, apps, etc.).
- **Seguridad empresarial**: CSRF, 2FA Google Authenticator, bloqueo por intentos, bitácora de auditoría.

---

## 2. Requisitos del Sistema

### 2.1 Requisitos Mínimos

| Componente | Versión Mínima | Recomendada |
|------------|---------------|-------------|
| PHP | 7.4 | **8.1+** |
| MySQL | 5.7 | **8.0** |
| MariaDB | 10.3 | **10.11** |
| Servidor Web | Apache 2.4 / nginx | Apache 2.4 |
| Composer | 2.x | 2.x |
| Memoria PHP | 128 MB | **256 MB** |
| Espacio en disco | 50 MB | 200 MB |

### 2.2 Extensiones PHP Requeridas

Las siguientes extensiones deben estar instaladas y habilitadas en el servidor:

| Extensión | Propósito |
|-----------|-----------|
| `pdo_mysql` | Conexión a base de datos MySQL/MariaDB |
| `mbstring` | Manejo de caracteres UTF-8 |
| `gd` | Procesamiento de imágenes (fotos de instalaciones) |
| `dom` | Generación de PDFs con Dompdf |
| `json` | API REST y comunicaciones AJAX |
| `curl` | Integraciones externas y monitoreo SNMP |
| `openssl` | PHPMailer SMTP seguro, 2FA |
| `fileinfo` | Validación de archivos subidos |
| `zip` | Exportación de reportes Excel/CSV comprimidos |
| `bcmath` | Cálculos financieros precisos |
| `xml` | Procesamiento de plantillas de email |

### 2.3 Servidor Web

- **Apache**: El archivo `.htaccess` incluido activa `DirectoryIndex index.php` y `Options -Indexes`. Asegúrate de que `mod_rewrite` esté habilitado si usas rutas amigables.
- **nginx**: No requiere configuración especial. Apunta el `root` del servidor al directorio del proyecto y asegura que `index.php` sea el archivo de índice.
- **Permisos**: El servidor web debe tener permisos de escritura en:
  - `logs/` — archivos de log de la aplicación
  - `.env` — configuración de base de datos y SMTP
  - `public/uploads/` — fotos de instalaciones y comprobantes de pago

### 2.4 Stack de Tecnologías

| Componente | Tecnología |
|------------|-----------|
| Backend | PHP 7.4+ nativo (POO básico, PDO, procedimental organizado) |
| Base de Datos | MySQL 5.7+ / MariaDB 10.3+ (utf8mb4) |
| Frontend | Bootstrap 5.3.3, Font Awesome 6.5.2 |
| Tablas | DataTables 1.13.11 con Buttons, Responsive, ColVis |
| PDF | Dompdf 2.0+ |
| Correo | PHPMailer 6.9+ |
| Gráficos | Chart.js |
| Mapas | Leaflet + OSM (OpenStreetMap) + Leaflet.heat |
| Alertas | SweetAlert2 |
| Firma digital | SignaturePad |
| Dependencias PHP | Composer (dompdf, phpmailer) |

---

## 3. Instalación Paso a Paso

### 3.1 Subir Archivos al Servidor

1. Descarga el archivo ZIP de CodeCanyon y extráelo en tu computadora.
2. Sube **todo el contenido** (excluyendo la carpeta `screenshots/` y archivos `license.txt`, `changelog.txt` si prefieres) a la raíz de tu dominio o subdominio mediante FTP, cPanel File Manager, SCP o similar.

   **Ejemplos:**
   - Dominio principal: `/public_html/` → `https://midominio.com/`
   - Subdirectorio: `/public_html/redreport/` → `https://midominio.com/redreport/`
   - Subdominio: `/public_html/admin.midominio.com/` → `https://admin.midominio.com/`

3. Establece los permisos correctos:

   ```bash
   chmod 755 -R /ruta/al/proyecto/
   chmod 777 -R /ruta/al/proyecto/logs/
   chmod 666 /ruta/al/proyecto/.env  # (se crea automáticamente)
   chmod 777 /ruta/al/proyecto/public/uploads/
   ```

   > **⚠️ Importante:** En entornos de producción, ajusta los permisos a `750`/`640` después de la instalación.

### 3.2 Instalar Dependencias con Composer

Ejecuta el siguiente comando desde la raíz del proyecto:

```bash
cd /ruta/al/proyecto/
composer install --no-dev --optimize-autoloader
```

Esto descargará e instalará:
- **Dompdf** (`dompdf/dompdf`) — generación de PDFs (facturas, comprobantes, reportes).
- **PHPMailer** (`phpmailer/phpmailer`) — envío de correos SMTP con adjuntos y HTML.

> **Nota para hosting compartido:** Si no tienes acceso SSH, puedes ejecutar Composer localmente y luego subir la carpeta `vendor/` generada junto con el resto de archivos.

### 3.3 Instalación Guiada (Recomendada)

Abre tu navegador y navega al directorio donde subiste los archivos:

```
https://midominio.com/install/
```

El asistente de instalación te guiará a través de **4 pasos**:

#### Paso 1: Verificación de Requisitos

El wizard verifica automáticamente:
- Versión de PHP (7.4+ requerido)
- Extensiones PHP habilitadas (`pdo_mysql`, `mbstring`, `gd`, `dom`, `json`, `curl`, `openssl`, `fileinfo`, `zip`)
- Permisos de escritura en `logs/` y `.env`
- Disponibilidad de Composer y `vendor/autoload.php`

Los requisitos cumplidos se muestran en verde (✓) y los faltantes en rojo (✗). No podrás continuar hasta cumplir todos los requisitos obligatorios.

#### Paso 2: Configuración de Base de Datos

Ingresa los siguientes datos de tu servidor MySQL/MariaDB:

| Campo | Descripción |
|-------|-------------|
| Host | Normalmente `localhost` o `127.0.0.1` |
| Puerto | Generalmente `3306` |
| Base de Datos | Nombre de la base de datos (se crea automáticamente si no existe) |
| Usuario | Usuario con permisos de creación y modificación de tablas |
| Contraseña | Contraseña del usuario MySQL |

El sistema probará la conexión en segundo plano. Si la base de datos no existe, intentará crearla automáticamente con charset `utf8mb4`.

#### Paso 3: Configuración del Administrador

Define las credenciales del usuario administrador inicial:

| Campo | Descripción |
|-------|-------------|
| Usuario | Nombre de usuario para el admin (por defecto: `administrador`) |
| Email | Correo electrónico del administrador |
| Contraseña | Mínimo 4 caracteres. Se almacena usando `password_hash()` con bcrypt. |
| URL del Sistema | Se detecta automáticamente. Verifica que sea correcta. |

#### Paso 4: Instalación

Al confirmar, el instalador:
1. Importa el archivo `redreport.sql` (24 tablas + datos iniciales).
2. Actualiza la contraseña del administrador con bcrypt.
3. Crea el archivo `.env` con la configuración ingresada.
4. Redirige a la pantalla de login del sistema.

**¡La instalación ha finalizado!** Accede con las credenciales de administrador que configuraste.

### 3.4 Instalación Manual (Sin Wizard)

Si el asistente web no funciona (por ejemplo, en entornos muy restrictivos), sigue estos pasos:

#### 1. Crear la Base de Datos

Accede a phpMyAdmin, MySQL Workbench o la línea de comandos:

```sql
CREATE DATABASE IF NOT EXISTS redreport
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
```

#### 2. Importar el Esquema SQL

Importa el archivo `redreport.sql` incluido en el paquete:

```bash
mysql -u usuario -p redreport < redreport.sql
```

O a través de phpMyAdmin: selecciona la base de datos → Importar → selecciona `redreport.sql`.

#### 3. Configurar el Archivo `.env`

Copia el archivo de ejemplo y edítalo con tus datos:

```bash
cp .env.example .env
```

Edita el archivo `.env` con los valores correctos:

```env
DB_HOST=localhost
DB_NAME=redreport
DB_USER=tu_usuario
DB_PASS=tu_contraseña
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=correo@gmail.com
SMTP_PASS=contraseña_app
SMTP_FROM=no-reply@tudominio.com
SMTP_FROM_NAME=RedReport
APP_URL=https://midominio.com/
APP_NAME=RedReport
APP_ENV=production
APP_DEBUG=false
API_KEY=TuClaveAPISegura
```

#### 4. Actualizar Contraseña del Administrador

Ejecuta esta consulta SQL para establecer la contraseña del admin (reemplaza `admin123` por tu contraseña):

```sql
UPDATE tb_usuarios
SET password = '$2y$10$' -- genera un hash bcrypt válido
WHERE id_rol = 1
LIMIT 1;
```

> Puedes generar un hash bcrypt en https://bcrypt-generator.com/ o con PHP CLI: `php -r "echo password_hash('tu_password', PASSWORD_BCRYPT);"`

#### 5. Instalar Dependencias

```bash
composer install --no-dev --optimize-autoloader
```

#### 6. Verificar

Abre `https://midominio.com/index.php` en tu navegador. Deberías ver la pantalla de inicio de sesión.

---

## 4. Configuración Post-Instalación

### 4.1 Configuración SMTP para Correos Electrónicos

El sistema necesita un servidor SMTP para enviar:
- Notificaciones automáticas a clientes y empleados
- Facturas por correo electrónico
- Recuperación de contraseña
- Alertas del sistema

Puedes configurarlo desde **Configuración → Sistema** en la interfaz web, o editando directamente el archivo `.env`:

```env
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=tu_correo@gmail.com
SMTP_PASS=tu_contraseña_de_aplicacion
SMTP_FROM=no-reply@tudominio.com
SMTP_FROM_NAME=RedReport
```

> **Importante para Gmail:** Debes usar una **contraseña de aplicación** (no tu contraseña normal). Actívala en https://myaccount.google.com/apppasswords

> **Recomendación:** Usa servicios transaccionales como SendGrid, Mailgun, o el SMTP de tu hosting para mejor deliverability.

Después de configurar, usa el botón **"Probar Envío de Email"** en la misma página de configuración para verificar que todo funcione correctamente.

### 4.2 Configuración de la Empresa

Ve a **Configuración → Empresa** y completa los datos de tu ISP:

- **Nombre de la empresa** (se muestra en facturas, PDFs, etc.)
- **NIT/Documento legal**
- **Dirección física**
- **Teléfono y correo de contacto**
- **Logo** (aparecerá en facturas PDF y el sistema)
- **Ubicación en el mapa** (selector interactivo con Leaflet)

Los datos de la empresa se almacenan en la tabla `tb_empresa` y se utilizan en toda la facturación y documentación del sistema.

### 4.3 Creación de Usuarios

El usuario administrador se crea durante la instalación. Para agregar más usuarios:

1. Ve a **Usuarios y Seguridad → Usuarios** (solo visible para rol Administrador).
2. Haz clic en **"Nuevo Usuario"**.
3. Completa los campos:
   - **Nombre**: Nombre completo del empleado.
   - **Documento**: Número de identificación.
   - **Teléfono**: Número de contacto.
   - **Email**: Correo electrónico (se usa para notificaciones y recuperación de contraseña).
   - **Rol**: Selecciona entre Administrador (1), Gestión (2), Instalador (3) o Ventas (4).
   - **Contraseña**: Se almacena cifrada con bcrypt.

> **Usuarios demo preinstalados:**
>
> | Usuario | Contraseña | Rol |
> |---------|-----------|-----|
> | administrador | admin | Administrador |
> | gestor | 123456 | Gestión |
> | instalador1 | 123456 | Instalador |
> | vendedor1 | 123456 | Ventas |

**⚠️ Importante:** Cambia las contraseñas de los usuarios demo inmediatamente después de la instalación en producción.

### 4.4 Configuración de API Key

Para consumir la API REST, necesitas configurar una clave de API:

1. Ve a **Configuración → Sistema**.
2. En el campo **"API Key"**, ingresa una clave segura (mínimo 32 caracteres alfanuméricos).
3. Haz clic en **"Guardar"**.

Alternativamente, edita el archivo `.env`:

```env
API_KEY=TuClaveSuperSeguraDe32CaracteresOMas
```

La API Key se envía en cada petición mediante el header `X-API-Key` o como parámetro `api_key` en la URL.

### 4.5 Verificación de Funcionamiento

Completa esta lista de verificación después de la instalación:

- [ ] Inicio de sesión con credenciales de administrador
- [ ] Dashboard carga con KPIs correctos (clientes, facturas, etc.)
- [ ] Módulo de clientes: crear, editar y listar clientes
- [ ] Facturación: crear una factura de prueba y verificar PDF
- [ ] Configuración SMTP: enviar un email de prueba
- [ ] Mapa: verificar que Leaflet carga correctamente
- [ ] App móvil: acceder desde el teléfono a `/movil/`
- [ ] Modo oscuro: activar/desactivar desde el navbar
- [ ] Logs: verificar que `logs/app.log` se crea sin errores

---

## 5. Guía de Módulos

### 5.1 Dashboard

**Ruta:** `/index.php`  
**Acceso:** Todos los roles

El dashboard principal muestra una vista general del estado del ISP con los siguientes indicadores:

**KPIs principales (tarjetas superiores):**
- Total de clientes registrados
- Clientes activos, suspendidos y cortados
- Contratos activos
- Instalaciones pendientes vs. completadas
- Equipos disponibles en inventario
- Ingresos del mes actual
- Facturas pendientes y total adeudado
- Ventas del mes (cantidad y monto)

**Secciones adicionales:**
- **Gráfico de ventas anual** (Chart.js de barras) — muestra la tendencia mes a mes.
- **Top 5 morosos** — clientes con mayor deuda acumulada, incluyendo días de mora.
- **Instalaciones recientes** — últimas 5 instalaciones completadas con nombre del instalador.
- **Órdenes recientes** — últimas órdenes de servicio creadas (visible si hay datos).
- **Tickets recientes** — últimos tickets de soporte abiertos (visible si hay datos).

Las tarjetas de KPIs usan un esquema de 8 colores (azul, verde, naranja, teal, púrpura, rojo, rosa, índigo) con efecto degradado para mejor legibilidad.

### 5.2 Clientes

**Ruta:** `/clientes/`  
**Acceso:** Administrador (1), Gestión (2)

El módulo de clientes es el núcleo del sistema. Alberga toda la información del cliente final.

**Funcionalidades:**

- **Listado con DataTable**: búsqueda en vivo, ordenamiento por cualquier columna, paginación, exportación a Excel/CSV/PDF/Copy/Print.
- **CRUD completo**: crear, editar, eliminar y visualizar clientes.
- **Campos del cliente**:
  - Nombre, documento, teléfono, dirección, email
  - Coordenadas geográficas (lat/lng) con selector de mapa Leaflet
  - Estado del servicio: `Activo`, `Suspendido`, `Cortado`
  - Fecha de instalación e instalador asignado
  - Contraseña para acceso al portal del cliente
  - IP asignada y datos de red
- **Ficha del cliente** (`/clientes/vistas/ficha.php`): página de detalle con:
  - Información general del cliente
  - Timeline de actividad (facturas, pagos, tickets, órdenes)
  - Estado de cuenta y resumen financiero
  - Contratos y plan contratado
- **Importación CSV**: sube un archivo CSV con preview visual, mapeo de columnas y carga masiva validada.
- **Búsqueda global**: presiona `Ctrl+K` o haz clic en el icono de búsqueda en el navbar para buscar clientes, facturas, tickets y órdenes en tiempo real.

### 5.3 Facturación

**Ruta:** `/facturacion/`  
**Acceso:** Administrador (1), Gestión (2)

El módulo de facturación maneja todo el ciclo de ingresos del ISP.

**Facturas:**
- Creación con número automático (`FAC-00001`, `FAC-00002`, ...).
- **Items dinámicos**: agrega/elimina líneas de producto/servicio con precio y cantidad.
- Cálculo automático de subtotal, IVA y total.
- Estados: `pendiente`, `pagada`, `vencida`, `anulada`.
- Fecha de emisión y fecha de vencimiento.
- Generación de PDF profesional con Dompdf (logo de empresa, datos, items, totales).
- Envío por email con PDF adjunto.
- Filtros por estado, fecha, cliente.

**Pagos:**
- Registro de pagos parciales o totales.
- Métodos de pago: Efectivo, Transferencia, Tarjeta, Cheque, Otro.
- Referencia de pago y comprobante (PDF subido).
- Historial completo de pagos por factura.
- Generación de recibo PDF (`facturacion/pdf_comprobante.php`).

**Cartera:**
- Vista de todas las facturas pendientes y vencidas.
- Totales por cliente.
- Antigüedad de la deuda.

**Facturación Recurrente:**
- Genera automáticamente facturas para contratos activos que no tengan factura en el mes actual.
- Se ejecuta bajo demanda desde el panel de facturación.

**Dashboard de facturación:**
- Ingresos del mes vs. mes anterior.
- Facturas emitidas, pagadas, pendientes y vencidas.
- Gráfico de ingresos mensuales.

### 5.4 Ventas

**Ruta:** `/ventas/`  
**Acceso:** Administrador (1), Gestión (2), Ventas (4)

Módulo comercial para gestionar la cartera de productos y el equipo de ventas.

**Planes (CRUD):**
- Nombre, velocidad de descarga/subida, precio, descripción.
- Estado activo/inactivo.
- Precio de instalación (opcional).

**Contratos:**
- Vinculación de cliente + plan.
- Fecha de inicio, período (meses), fecha de renovación.
- Estados: `activo`, `cancelado`, `expirado`.
- Monto mensual y método de pago.

**Registro de Ventas:**
- Tipo de venta: `nuevo`, `renovación`, `upgrade`.
- Monto, comisión, vendedor asignado.
- Fecha y notas.
- Dashboard de ventas con métricas mensuales y comisiones.

### 5.5 Inventario

**Ruta:** `/inventario/`  
**Acceso:** Administrador (1), Gestión (2)

Gestión del stock de equipos y dispositivos del ISP.

**Tipos de Equipo:**
- Configurables desde la interfaz (`tb_tipos_equipo`).
- Ejemplos: Router, CPE, Fuente, Cable, Antena, Switch, etc.

**Equipos (CRUD):**
- Código único (número de serie), tipo, marca, modelo.
- Estado: `Disponible`, `Asignado`, `En reparación`, `Dado de baja`.
- Precio de compra, precio de venta, proveedor.
- Fecha de ingreso, ubicación física (bodega).

**Alertas de Stock Mínimo:**
- Define un límite mínimo por tipo de equipo.
- El sistema muestra una alerta visual cuando el stock disponible está por debajo del mínimo.
- Las notificaciones automáticas también pueden alertar sobre stock bajo.

**Devolución a Almacén:**
- Proceso de devolución de equipos desde instalaciones o cambios.
- Actualiza el estado del equipo a "Disponible" automáticamente.

**Filtros:**
- Por estado, tipo, marca, bodega, fecha de ingreso.
- DataTable con exportación.

### 5.6 Instalaciones

**Ruta:** `/instalaciones/`  
**Acceso:** Administrador (1), Instalador (3)

Módulo para gestionar el proceso de instalación de nuevos clientes.

**Asignación:**
- Lista de clientes pendientes de instalación (sin fecha de instalación asignada).
- Asignación a un instalador (técnico) específico.
- Programación de fecha y hora.

**Geolocalización:**
- Mapa Leaflet interactivo con marcador de ubicación del cliente.
- El instalador puede actualizar la ubicación desde el campo o desde la app móvil.

**Registro de Instalación:**
- Fecha de instalación efectiva.
- Equipos instalados (selección desde inventario).
- Fotos del sitio (subida con drag-and-drop y previsualización).
- Firma digital del cliente (SignaturePad).
- Notas adicionales.

**Galería de Fotos:**
- Vista de todas las fotos asociadas a una instalación.
- Subida AJAX sin recarga de página.
- Posibilidad de eliminar fotos.

### 5.7 Órdenes de Servicio

**Ruta:** `/ordenes/`  
**Acceso:** Administrador (1), Gestión (2), Instalador (3)

Gestión de solicitudes de soporte técnico y servicio post-venta.

**Campos de la orden:**
- **Tipo**: avería, instalación, cambio de equipo, visita técnica, otro.
- **Prioridad**: baja, media, alta, crítica.
- **Estado**: pendiente, asignada, en_proceso, resuelta, cerrada.
- **Cliente** asociado, descripción del problema.
- **Técnico asignado** (usuario con rol Instalador).
- **Solución** registrada (visible una vez resuelta).
- Fecha de creación, asignación y resolución.

**Proceso:**
1. Se crea la orden con estado `pendiente`.
2. El administrador o gestor la asigna a un técnico (estado → `asignada`).
3. El técnico la marca como `en_proceso` cuando comienza a trabajar.
4. El técnico registra la solución y cambia a `resuelta`.
5. El administrador la cierra definitivamente.

**Filtros:** por estado, prioridad, técnico, fechas.

### 5.8 Tickets de Soporte

**Ruta:** `/tickets/`  
**Acceso:** Administrador (1), Gestión (2)

Sistema de tickets para soporte al cliente unificado (similar a un helpdesk).

**Categorías:**
- Facturación, Soporte Técnico, Ventas, Cancelación, Otro.
- Configurables desde la interfaz administrativa.

**Funcionalidades:**
- Creación de tickets (desde el sistema o desde el portal del cliente).
- Asignación a agentes de soporte.
- Estados: abierto, en_curso, resuelto, cerrado.
- Prioridad: baja, media, alta, urgente.
- Historial completo de cambios y comentarios.
- Envío de notificaciones al cliente cuando el ticket cambia de estado.

**Integración con Portal Cliente:**
- Los clientes pueden ver sus tickets desde el portal del cliente.
- Pueden crear nuevos tickets y dar seguimiento a los existentes.

### 5.9 Mapa de Cobertura

**Ruta:** `/mapa/`  
**Acceso:** Todos los roles

Visualización geográfica de la red y clientes usando Leaflet.js con OpenStreetMap.

**Capas disponibles:**
- **Clientes**: marca todos los clientes con colores según estado de servicio (verde=Activo, amarillo=Suspendido, rojo=Cortado).
- **Zonas de cobertura**: polígonos en el mapa que definen áreas de cobertura del ISP. CRUD completo de zonas con nombre, color y coordenadas.
- **Mapa de Calor**: visualización de densidad de clientes usando Leaflet.heat. Los pesos se asignan según estado del servicio.
- **Ubicación de la empresa**: marcador especial con la dirección de la empresa.

**Administración de Zonas:**
- Crear/editar/eliminar zonas poligonales.
- Asignar nombre y color a cada zona.
- Las zonas se renderizan en el mapa con el color asignado.

### 5.10 Monitoreo SNMP

**Ruta:** `/monitoreo/`  
**Acceso:** Administrador (1), Gestión (2)

Monitoreo básico de dispositivos de red mediante SNMP y ping.

**Funcionalidades:**
- Escaneo de dispositivos en la red.
- Prueba de ping con respuesta en tiempo real.
- Consulta SNMP (snmpget) para obtener datos de dispositivos compatibles.
- Estado en tiempo real (online/offline).
- Tabla de dispositivos con dirección IP, tipo, estado.

> **Nota:** Este módulo requiere que las funciones de ejecución de comandos (`exec()`, `shell_exec()`) estén habilitadas en el servidor, así como las herramientas de sistema `ping` y `snmpget` (net-snmp).

### 5.11 Informes

**Ruta:** `/informes/`  
**Acceso:** Administrador (1), Gestión (2)

Generación de reportes exportables para análisis gerencial.

**Tipos de informes:**
- **Clientes**: listado completo, por estado de servicio, por zona.
- **Facturación**: ingresos por período, facturas emitidas vs. cobradas, cartera.
- **Ventas**: comisiones, ventas por vendedor, contratos activos.
- **Inventario**: equipos disponibles, asignados, en reparación.
- **Instalaciones**: completadas vs. pendientes, por instalador.

**Exportación:**
- PDF (formato profesional con membrete)
- Excel (.xlsx)
- CSV
- Copia impresa
- Copia al portapapeles

Todos los informes usan DataTables Buttons para exportación con un solo clic.

### 5.12 API REST

**Ruta:** `/api/`  
**Documentación interactiva:** `/api/documentacion.php`  
**Acceso:** Via API Key

Interfaz de programación para integración con sistemas externos.

**Endpoints disponibles:**
- `GET/POST/PUT/DELETE /api/?endpoint=clientes` — CRUD de clientes.
- `GET /api/?endpoint=facturas` — listado de facturas.
- `GET /api/?endpoint=contratos` — listado de contratos.
- `GET /api/?endpoint=planes` — planes activos.
- `GET /api/?endpoint=dashboard` — KPIs resumidos (total clientes, activos, contratos, deuda).

La documentación interactiva incluye ejemplos de código, estructura JSON de respuesta y códigos de error. Ver sección 9 para referencia detallada.

### 5.13 Configuración

**Ruta:** `/configuracion/`  
**Acceso:** Administrador (1)

Panel central de administración del sistema con múltiples sub-módulos:

**Sistema:**
- Edición del archivo `.env` desde la interfaz web.
- Configuración de DB, SMTP, APP_URL, APP_ENV, APP_DEBUG.
- API Key management.
- Botón de prueba de email SMTP.
- Estado de variables del sistema.

**Empresa:**
- Datos de la empresa (nombre, NIT, dirección, teléfono, email, logo).
- Ubicación en mapa con selector geográfico.

**Roles y Permisos:**
- Gestión de módulos del sistema (`tb_modulos`).
- Asignación de permisos por rol (`tb_permisos`).
- Interfaz visual con tabla de permisos (rol × módulo = checkboxes).
- Permite granularidad fina (qué rol puede acceder a qué módulo).

**Plantillas de Email:**
- CRUD completo de plantillas (`tb_plantillas_email`).
- Variables dinámicas: `{cliente_nombre}`, `{factura_numero}`, `{factura_total}`, `{empresa_nombre}`, etc.
- Vista previa de la plantilla renderizada.
- Asociación a eventos automáticos.

**Health Dashboard:**
- Estado de PHP (versión, extensiones, configuración).
- Estado de MySQL (versión, uptime, conexiones, tamaño de BD).
- Estado del disco (uso, disponible, total).
- Estado de backups (fecha del último backup, tamaño).
- Todo mostrado en tarjetas con indicadores visuales.

**Mantenimiento de BD:**
- Optimización de tablas.
- Reparación de tablas.
- Análisis de tablas.
- Progreso en tiempo real vía AJAX.
- Útil para mantener el rendimiento de la base de datos.

**Visor de Logs:**
- Visualización del archivo `logs/app.log`.
- DataTable con filtros por fecha y tipo de error.
- Descarga del archivo de log.
- Limpieza del archivo de log.

**Cola de Correos:**
- Visualización de `tb_email_queue`.
- Reprocesamiento de correos fallidos.
- Estados: pendiente, enviado, fallido.

---

## 6. Roles y Permisos

### 6.1 Roles Predefinidos

| ID | Nombre | Descripción |
|----|--------|-------------|
| 1 | **Administrador** | Acceso total al sistema. Puede gestionar usuarios, roles, configuración, backups y auditoría. |
| 2 | **Gestión** | Acceso a operaciones del día a día: clientes, facturación, inventario, informes, monitoreo, tickets. |
| 3 | **Instalador** | Acceso a instalaciones, mapa y órdenes de servicio. Orientado a técnicos de campo. |
| 4 | **Ventas** | Acceso al módulo de ventas, planes, contratos y dashboard comercial. |

### 6.2 Matriz de Acceso por Módulo

| Módulo | Admin (1) | Gestión (2) | Instalador (3) | Ventas (4) |
|--------|:---------:|:-----------:|:--------------:|:----------:|
| Dashboard | ✓ | ✓ | ✓ | ✓ |
| Clientes | ✓ | ✓ | — | — |
| Facturación | ✓ | ✓ | — | — |
| Ventas | ✓ | ✓ | — | ✓ |
| Inventario | ✓ | ✓ | — | — |
| Instalaciones | ✓ | — | ✓ | — |
| Órdenes | ✓ | ✓ | ✓ | — |
| Tickets | ✓ | ✓ | — | — |
| Mapa | ✓ | ✓ | ✓ | ✓ |
| Monitoreo | ✓ | ✓ | — | — |
| Informes | ✓ | ✓ | — | — |
| API | ✓ | — | — | — |
| Usuarios | ✓ | — | — | — |
| Auditoría | ✓ | — | — | — |
| Configuración | ✓ | — | — | — |
| Backup | ✓ | — | — | — |
| Plantillas Email | ✓ | — | — | — |
| App Móvil | ✓ | ✓ | ✓ | ✓ |

### 6.3 Permisos Personalizados

El sistema incluye un subsistema de permisos granular administrado desde **Configuración → Roles y Permisos**:

- **`tb_modulos`**: define cada módulo del sistema con nombre único y ruta asociada.
- **`tb_permisos`**: tabla pivote `(id_rol, id_modulo)` que define los accesos.

Puedes agregar nuevos módulos o modificar permisos existentes sin tocar código. La interfaz muestra una matriz de checkboxes (filas = módulos, columnas = roles) que permite activar o desactivar accesos al instante.

### 6.4 Verificación de Acceso en Código

Cada archivo de módulo utiliza la función `verificar_acceso([roles])` definida en `app/config/seguridad.php`:

```php
// Ejemplo: solo Administrador (1) y Gestión (2)
verificar_acceso([1, 2]);

// Ejemplo: solo Administrador (1)
verificar_acceso([1]);
```

Si el usuario no tiene el rol requerido, es redirigido automáticamente al dashboard con un mensaje de acceso denegado.

---

## 7. App Móvil PWA

### 7.1 Descripción

RedReport incluye una **aplicación móvil progresiva (PWA)** completa en la carpeta `/movil/`. No requiere publicación en Google Play ni App Store — los usuarios simplemente acceden desde el navegador de su teléfono y pueden instalarla como una app nativa.

### 7.2 Acceso

```
https://midominio.com/movil/
```

**Características PWA:**
- `manifest.json` con nombre, iconos (192px y 512px), tema y orientación.
- `sw.js` (Service Worker) con precarga de recursos críticos y estrategia cache-first.
- Instalación en la pantalla de inicio del dispositivo (iOS y Android).
- Funcionamiento offline parcial (recursos cacheados).

### 7.3 Login Unificado

La app móvil tiene un sistema de autenticación que soporta **dos tipos de usuarios**:

- **Empleado**: usuarios del sistema con roles Instalador, Gestión, Ventas o Administrador.
- **Cliente**: clientes registrados en `tb_clientes` con contraseña habilitada para portal.

El selector de tipo de usuario aparece en la pantalla de login como tabs (Empleado / Cliente).

### 7.4 Módulo Instalador

Diseñado para técnicos de campo. Funcionalidades:

- **Dashboard del instalador**: resumen de órdenes asignadas, instalaciones pendientes.
- **Instalaciones**: registro de instalaciones con:
  - Captura de **ubicación GPS** del dispositivo (geolocalización HTML5).
  - **Fotos** desde la cámara del teléfono (subida con previsualización).
  - **Firma digital** del cliente mediante SignaturePad.
  - Selección de equipos instalados desde el inventario disponible.
  - Notas y comentarios.
- **Órdenes de Servicio**:
  - Ver órdenes asignadas al técnico.
  - Cambiar estado (en_proceso, resuelta).
  - Registrar solución.

### 7.5 Módulo Cliente

Diseñado para que los clientes del ISP gestionen sus servicios:

- **Dashboard**: resumen de estado de cuenta, facturas pendientes, tickets activos.
- **Facturas**: listado de facturas con estado, monto y fecha. Posibilidad de ver PDF.
- **Tickets de Soporte**:
  - Crear nuevos tickets.
  - Ver historial de tickets.
  - Dar seguimiento a tickets existentes.

### 7.6 Dark Mode Automático

La app móvil respeta la preferencia del sistema operativo mediante `@media(prefers-color-scheme: dark)`. Cuando el dispositivo está en modo oscuro, la app cambia automáticamente a un tema oscuro con fondos navy y texto claro.

### 7.7 Navegación Inferior (Bottom Nav)

La app utiliza un menú de navegación inferior fijo estilo app nativa con iconos, que incluye:
- Inicio (Dashboard)
- Módulo principal según el rol
- Perfil / Configuración

---

## 8. Seguridad

### 8.1 CSRF (Cross-Site Request Forgery)

Toda operación POST en el sistema requiere un token CSRF válido.

**Implementación:**
- **Generación**: `csrf_token()` genera un token de 32 bytes hexadecimales almacenado en sesión.
- **Campo HTML**: `csrf_field()` devuelve un `<input type="hidden">` con el token.
- **Verificación**: `csrf_verify($token)` compara el token recibido con el de sesión usando `hash_equals()`.
- **Manejo de error**: `csrf_die()` muestra una alerta SweetAlert2 y redirige a recargar la página.

**Uso en formularios:**
```php
<form method="POST">
  <?= csrf_field() ?>
  ...
</form>
```

**Uso en controladores:**
```php
if (!csrf_verify($_POST['_csrf_token'] ?? '')) {
    csrf_die();
}
```

### 8.2 Autenticación en Dos Factores (2FA)

El sistema soporta 2FA mediante **Google Authenticator** (TOTP - Time-based One-Time Password).

**Configuración:**
1. El usuario administrador accede a su **Perfil**.
2. Escanea el código QR con la app Google Authenticator.
3. Ingresa un código de verificación para confirmar.
4. A partir de ese momento, cada inicio de sesión requerirá el código 2FA.

**Implementación técnica:**
- Clase TOTP nativa (sin librerías externas).
- Secret almacenado en `tb_usuarios.google2fa_secret` cifrado.
- Tolerancia de ±1 paso de reloj (30 segundos de desfase permitido).
- Flujo: login → verificar contraseña → verificar 2FA (si está habilitado) → dashboard.
- La pantalla de verificación 2FA está en `/login/verificar_2fa.php`.

### 8.3 Timeout de Sesión

- **Duración**: 30 minutos de inactividad (valor configurable en `SESSION_TIMEOUT = 1800` segundos).
- **Mecanismo**: `verificar_sesion()` en `seguridad.php` comprueba `$_SESSION['_ultimo_acceso']` en cada carga de página.
- **Comportamiento**: si han pasado más de 30 minutos, la sesión se destruye y el usuario es redirigido al login.
- **Aplicación**: tanto en el sistema web principal (`sesion.php`) como en la app móvil (`movil/header.php`).

### 8.4 Bloqueo por Intentos Fallidos

- **Límite**: 5 intentos fallidos de inicio de sesión.
- **Duración del bloqueo**: 15 minutos.
- **Campos en base de datos**: `tb_usuarios.intentos_fallidos` y `tb_usuarios.bloqueado_hasta`.
- **Flujo**:
  1. El usuario intenta iniciar sesión con una contraseña incorrecta.
  2. `registrar_intento($pdo, $id_usuario, false)` incrementa el contador.
  3. Al alcanzar 5 intentos, se establece `bloqueado_hasta = NOW() + 15 minutos`.
  4. `verificar_bloqueo($pdo, $id_usuario)` verifica si la cuenta está bloqueada antes de procesar el login.
  5. Pasados los 15 minutos, el bloqueo se levanta automáticamente.

### 8.5 Headers de Seguridad

El archivo `sesion.php` establece los siguientes headers HTTP en cada petición autenticada:

```
Cache-Control: no-store, no-cache, must-revalidate, max-age=0
Pragma: no-cache
X-Frame-Options: DENY
X-Content-Type-Options: nosniff
X-XSS-Protection: 1; mode=block
Referrer-Policy: strict-origin-when-cross-origin
```

### 8.6 Protección XSS

- Toda salida de datos ingresados por el usuario se escapa con `htmlspecialchars()`.
- La función helper `hescape($str)` está disponible globalmente para simplificar: `<?= hescape($variable) ?>`.
- Las contraseñas se almacenan con `password_hash()` usando bcrypt (PASSWORD_BCRYPT).
- Los tokens CSRF se comparan con `hash_equals()` para prevenir timing attacks.

### 8.7 Bitácora de Auditoría

Todas las acciones importantes quedan registradas en la tabla `tb_bitacora` mediante la función `bitacora()`.

**Campos registrados:**
- `id_usuario`: quién realizó la acción.
- `accion`: CREATE, UPDATE, DELETE, LOGIN, LOGOUT, etc.
- `tabla_afectada`: qué tabla fue modificada.
- `id_registro_afectado`: registro específico.
- `detalle`: descripción textual de la acción.
- `direccion_ip`: IP del usuario que realizó la acción.

**Ejemplos de uso en el código:**
```php
bitacora($pdo, $_SESSION['id_usuario'], 'CREATE', 'tb_clientes', $id_cliente, "Cliente creado: $nombre");
bitacora($pdo, $_SESSION['id_usuario'], 'UPDATE', 'tb_facturas', $id_factura, "Estado cambiado a: pagada");
```

**Módulo de Auditoría** (`/auditoria/`):
- Visor de la bitácora con DataTable.
- Filtros por usuario, acción, tabla y rango de fechas.
- Exportación de resultados.

### 8.8 HTTPS

Se recomienda firmemente ejecutar RedReport bajo HTTPS. El sistema detecta automáticamente el protocolo:

```php
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $protocol = 'https';
}
```

Si usas un proxy inverso (nginx, Cloudflare, ngrok), el sistema respeta el header `X-Forwarded-Proto` para construir correctamente las URLs.

---

## 9. API REST

### 9.1 Base URL

```
https://midominio.com/api/index.php
```

### 9.2 Autenticación

Todas las solicitudes requieren una API Key. Se envía de dos formas:

**Header HTTP:**
```
X-API-Key: TU_API_KEY
```

**Query string:**
```
https://midominio.com/api/index.php?endpoint=clientes&api_key=TU_API_KEY
```

### 9.3 Endpoints

#### Clientes

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `?endpoint=clientes` | Listar todos los clientes |
| `GET` | `?endpoint=clientes&id=1` | Obtener cliente por ID |
| `POST` | `?endpoint=clientes` | Crear nuevo cliente |
| `PUT` | `?endpoint=clientes&id=1` | Actualizar cliente existente |
| `DELETE` | `?endpoint=clientes&id=1` | Eliminar cliente |

**Ejemplo de respuesta GET:**
```json
{
  "data": [
    {
      "id_cliente": 1,
      "nombre": "Juan Perez",
      "documento": "123456789",
      "telefono": "3001234567",
      "direccion": "Calle 123",
      "email": "juan@example.com",
      "estado_servicio": "Activo",
      "lat": "4.7110",
      "lng": "-74.0721"
    }
  ]
}
```

#### Facturas

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `?endpoint=facturas` | Listar facturas |
| `GET` | `?endpoint=facturas&id=1` | Obtener factura por ID |
| `POST` | `?endpoint=facturas` | Crear nueva factura |

#### Contratos

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `?endpoint=contratos` | Listar contratos |
| `GET` | `?endpoint=contratos&id=1` | Obtener contrato por ID |
| `POST` | `?endpoint=contratos` | Crear nuevo contrato |

#### Planes

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `?endpoint=planes` | Listar planes activos |

#### Dashboard

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `?endpoint=dashboard` | KPIs resumidos |

**Respuesta:**
```json
{
  "data": {
    "total": 150,
    "activos": 120,
    "contratos": 110,
    "deuda": 4500000
  }
}
```

### 9.4 Códigos de Respuesta

| Código | Significado |
|--------|-------------|
| `200` | OK — solicitud exitosa |
| `201` | Creado — recurso creado correctamente |
| `400` | Bad Request — datos inválidos |
| `401` | Unauthorized — API Key inválida o faltante |
| `404` | Not Found — endpoint no encontrado |
| `500` | Internal Server Error — error del servidor |

### 9.5 Métodos Soportados

- `GET` — obtener recursos.
- `POST` — crear recursos.
- `PUT` — actualizar recursos.
- `DELETE` — eliminar recursos.
- `OPTIONS` — preflight CORS (respuesta 200 automática).

CORS está habilitado para todos los orígenes (`Access-Control-Allow-Origin: *`).

---

## 10. Base de Datos

### 10.1 Esquema General

RedReport utiliza una base de datos MySQL/MariaDB con charset `utf8mb4` y collation `utf8mb4_general_ci`. El esquema completo está en el archivo `redreport.sql` incluido en el paquete.

### 10.2 Tablas Principales

| Tabla | Propósito |
|-------|-----------|
| `tb_rol` | Roles del sistema (Administrador, Gestión, Instalador, Ventas) |
| `tb_usuarios` | Usuarios del sistema con credenciales, 2FA y control de bloqueo |
| `tb_clientes` | Clientes del ISP con datos personales, ubicación y estado de servicio |
| `tb_ips` | Direcciones IP asignadas a clientes |
| `tb_red` | Datos de red del cliente |
| `tb_facturas` | Facturas con numeración, montos, estados y fechas |
| `tb_factura_items` | Items individuales de cada factura |
| `tb_pagos` | Historial de pagos con método, referencia y comprobante |
| `tb_planes` | Planes de Internet (velocidad, precio, descripción) |
| `tb_contratos` | Contratos activos/cancelados vinculando clientes con planes |
| `tb_ventas` | Registro de ventas con comisiones y vendedor |
| `tb_equipos` | Inventario de equipos y dispositivos |
| `tb_tipos_equipo` | Tipologías de equipos (Router, CPE, Antena, etc.) |
| `tb_cobertura_zonas` | Zonas de cobertura geográfica (polígonos) |
| `tb_instalacion_fotos` | Fotos de instalaciones |
| `tb_ordenes` | Órdenes de servicio técnico |
| `tb_tickets` | Tickets de soporte al cliente |
| `tb_notificaciones` | Alertas automáticas del sistema |
| `tb_bitacora` | Auditoría de acciones de usuarios |
| `tb_modulos` | Módulos del sistema para permisos |
| `tb_permisos` | Permisos por rol y módulo |
| `tb_empresa` | Datos de la empresa |
| `tb_plantillas_email` | Plantillas de correo electrónico |
| `tb_email_queue` | Cola de correos para envío asíncrono |

### 10.3 Conexión a la Base de Datos

La conexión se realiza mediante PDO singleton en `app/config/conexion.php`:

```php
$servidor = "mysql:dbname=" . DB_NAME . ";host=" . DB_HOST;
$pdo = new PDO($servidor, DB_USER, DB_PASS, [
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
]);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
```

**Regla importante:** Siempre usar `$pdo` (ya inicializado) mediante `require_once '../app/config/conexion.php'`. Nunca crear una nueva instancia de `PDO` directamente.

### 10.4 Migraciones y Actualizaciones

Este sistema no utiliza un framework de migraciones. Los cambios de esquema se manejan de dos formas:

1. **Versión completa**: se reemplaza el archivo `redreport.sql` y se ejecuta una instalación limpia.
2. **Parches SQL incrementales**: se proporcionan archivos SQL individuales para cada versión (en futuras actualizaciones).

Para actualizar entre versiones:
1. Realiza un backup completo de la base de datos.
2. Ejecuta los parches SQL en orden secuencial.
3. Reemplaza los archivos del sistema (excluyendo `.env` y `uploads/`).

---

## 11. Personalización

### 11.1 Estilos CSS

Todo el CSS del sistema está centralizado en un único archivo:

```
public/css/redreport.css
```

**Variables CSS para modo oscuro:**
```css
body.dark-mode {
  --body-bg: #0f172a;
  --card-bg: #1e293b;
  --border-color: #334155;
  --text-primary: #e2e8f0;
  --text-secondary: #94a3b8;
  --navbar-bg: #020617;
  --input-bg: #334155;
  --table-stripe: #1e293b;
  --table-hover: #334155;
}
```

### 11.2 Sidebar

La sidebar usa un esquema de color navy (`#0f172a`) con brand azul (`#2563eb`). Los colores se pueden modificar en las variables del sidebar dentro de `redreport.css`.

### 11.3 Añadir Nuevos Módulos

Para agregar un nuevo módulo al sistema:

1. Crea el directorio del módulo en la raíz (ej: `/nuevo-modulo/`).
2. Crea los archivos PHP con la funcionalidad deseada.
3. Registra el módulo en `tb_modulos` desde la interfaz de permisos.
4. Asigna permisos a los roles correspondientes desde **Configuración → Roles y Permisos**.
5. Agrega el enlace en `parte1.php` (sidebar).

### 11.4 Traducción

El sistema está desarrollado en español (Latinoamérica). Para cambiar el idioma, modifica las cadenas de texto en los archivos de vista dentro de cada módulo. No se utiliza un sistema de internacionalización (i18n) con archivos de idioma separados.

### 11.5 Logotipo y Marca

- **Favicon**: `public/img/favicon.png`
- **Iconos PWA**: `public/img/icon-192.png`, `public/img/icon-512.png`
- **Logo de empresa**: se sube desde la interfaz de Configuración → Empresa
- **Nombre del sistema**: variable `APP_NAME` en `.env`

---

## 12. Solución de Problemas Comunes

### 12.1 Página en Blanco (White Screen)

**Causa probable:** Error de PHP con `display_errors` deshabilitado.

**Soluciones:**
1. Temporalmente, cambia `APP_ENV=development` y `APP_DEBUG=true` en el `.env` para ver los errores.
2. Revisa el archivo `logs/app.log` en la raíz del proyecto.
3. Verifica los logs del servidor web (Apache error log / nginx error log).
4. Asegúrate de que las extensiones PHP requeridas estén instaladas.
5. Verifica que `vendor/autoload.php` exista (ejecuta `composer install`).

### 12.2 Error de Conexión a Base de Datos

**Mensaje típico:** "Error de conexión a la base de datos. Verifica las credenciales en el archivo .env"

**Soluciones:**
1. Verifica que el archivo `.env` exista y tenga los valores correctos.
2. Confirma que el servicio MySQL/MariaDB esté corriendo.
3. Verifica credenciales: usuario, contraseña, host y puerto.
4. Asegúrate de que la base de datos exista y tenga permisos.
5. Si usas `localhost` y no funciona, prueba con `127.0.0.1`.
6. Algunos hosting usan un host diferente (ej: `mysql.tudominio.com`). Consulta con tu proveedor.
7. Verifica que el usuario MySQL tenga permisos desde el host donde está el servidor web.

### 12.3 Error 404 en Módulos (Apache)

**Causa:** El servidor no encuentra el archivo PHP solicitado.

**Soluciones:**
1. Verifica que hayas subido TODOS los archivos del proyecto.
2. Asegúrate de que `AllowOverride All` esté configurado en Apache para que el `.htaccess` funcione.
3. Si usas nginx, no hay problema — el sistema no usa rewrite rules complejas.
4. Verifica que `DirectoryIndex index.php` esté funcionando.

### 12.4 El Correo Electrónico No se Envía

**Soluciones:**
1. Usa el botón **"Probar Email"** en Configuración → Sistema para diagnosticar.
2. Verifica las credenciales SMTP en el archivo `.env`.
3. Para Gmail: asegúrate de usar una **contraseña de aplicación** (no la contraseña normal).
4. Para hosting: usa el SMTP de tu proveedor (generalmente `mail.tudominio.com`).
5. Verifica que el puerto SMTP (587 para TLS, 465 para SSL) no esté bloqueado por el firewall.
6. Revisa los logs de PHPMailer en `logs/app.log`.
7. Algunos hosting bloquean conexiones SMTP salientes. En ese caso, usa un servicio de email transaccional como SendGrid o Mailgun.

### 12.5 DataTable No Carga o Muestra Error "TN18"

**Causas probables:**
- Conflicto con versiones de jQuery o DataTables.
- `targets: -1` usado con `responsive: true` (incompatible).

**Soluciones:**
1. El sistema ya incluye las correcciones necesarias para evitar este error.
2. Si agregas nuevas tablas, copia el patrón de inicialización de los módulos existentes.
3. Asegúrate de que el `id` único de la tabla esté bien definido en el HTML.

### 12.6 Errores de Permisos

**Síntoma:** No se puede escribir en `logs/` o subir fotos.

**Solución:**
```bash
chmod 777 -R logs/
chmod 777 -R public/uploads/
```

En producción, ajusta a permisos más restrictivos:
```bash
chmod 750 logs/
chmod 640 logs/*.log
chmod 750 public/uploads/
```

### 12.7 Instalador Web No Carga

**Causas:**
- Permisos incorrectos en la raíz del proyecto.
- El archivo `.env` ya existe (el instalador detecta instalación previa).
- Falta `vendor/autoload.php`.

**Soluciones:**
1. Si el `.env` existe y quieres reinstalar, elimínalo primero.
2. Ejecuta `composer install` si la carpeta `vendor/` no existe.
3. Sigue el procedimiento de instalación manual (sección 3.4).

### 12.8 El Mapa Leaflet No se Muestra

**Soluciones:**
1. Verifica que las URLs de los tiles de OpenStreetMap no estén bloqueadas.
2. Asegúrate de tener conexión a Internet (los tiles se cargan desde OSM).
3. Si usas HTTPS, verifica que los tiles también se carguen por HTTPS.
4. Revisa la consola del navegador (F12) para errores de JavaScript.

### 12.9 APP_URL Incorrecta

**Síntoma:** Enlaces rotos, imágenes que no cargan, redirecciones a rutas incorrectas.

**Solución:**
1. Edita `APP_URL` en el archivo `.env` con la URL completa, incluyendo el protocolo y barra final:
   ```
   APP_URL=https://midominio.com/
   APP_URL=https://midominio.com/redreport/
   ```
2. El sistema también detecta automáticamente la URL desde `__DIR__` y `HTTP_HOST`, pero es recomendable definirla explícitamente.

### 12.10 Modo Oscuro No Persiste

El modo oscuro se guarda en `localStorage` del navegador. Soluciones:
1. Verifica que `localStorage` esté habilitado en el navegador.
2. Limpia el `localStorage` del sitio y vuelve a activar el modo oscuro desde el botón del navbar.
3. El toggle está en el navbar (icono de luna/sol).

---

## 13. Soporte

### 13.1 Documentación Adicional

- **Documentación de API interactiva**: `/api/documentacion.php` (dentro del sistema, requiere autenticación).
- **README.md**: instrucciones básicas en la raíz del proyecto.
- **CHANGELOG**: historial de cambios en `changelog.txt`.
- **Código comentado**: los archivos PHP incluyen comentarios descriptivos.

### 13.2 Soporte Técnico

Si encuentras algún problema o tienes preguntas sobre la instalación, configuración o personalización:

1. **Revisa esta documentación** — la mayoría de problemas comunes están cubiertos en la sección 12.
2. **Revisa los logs del sistema** en `logs/app.log` y los logs del servidor web.
3. **Crea un ticket en CodeCanyon** a través de la página del producto, incluyendo:
   - Versión del sistema (revisa `VERSION` en la raíz).
   - Versión de PHP, MySQL y servidor web.
   - Pasos para reproducir el problema.
   - Mensajes de error completos (de `logs/app.log` y consola del navegador).

### 13.3 Actualizaciones

Las actualizaciones se distribuyen a través de CodeCanyon. Para actualizar:

1. Descarga la nueva versión desde CodeCanyon.
2. Realiza un **backup completo** (archivos + base de datos).
3. Revisa el `changelog.txt` para cambios importantes.
4. Reemplaza los archivos del sistema (excepto `.env`, `public/uploads/` y la carpeta `vendor/`).
5. Si hay nuevos requisitos, ejecuta `composer install`.
6. Ejecuta los parches SQL si se incluyen en la actualización.

---

*RedReport v2.0.0 — Documentación para compradores*  
*© 2026 — Todos los derechos reservados*
