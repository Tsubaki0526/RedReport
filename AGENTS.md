# RedReport — Summary

## Stack
- PHP 7.4+, MySQL, Bootstrap 5, Font Awesome 6, DataTables BS5 + Buttons, SweetAlert2, Chart.js, Leaflet/OSM, Dompdf, PHPMailer, SignaturePad
- jQuery (DataTables dependency), Popper.js (BS5)

## Architecture
- `app/config/config.php` — core config with `.env` loading via `getenv()` fallback
- `app/config/conexion.php` — PDO singleton via `$pdo`, utf8mb4, ERRMODE_EXCEPTION
- `app/config/seguridad.php` — `csrf_field()`, `csrf_verify()`, `verificar_sesion()`, `verificar_acceso()`, `verificar_bloqueo()`, `bitacora()`, `hescape()`
- `sesion.php` — session start + security headers + timeout check
- `parte1.php` / `parte2.php` — layout templates (dark sidebar, BS5 CDN, DataTables Buttons)
- `.env` — DB_HOST, DB_NAME, DB_USER, DB_PASS, SMTP_*, APP_URL, APP_NAME, APP_ENV, APP_DEBUG, API_KEY

## Roles
| ID | Nombre         | Description                     |
|----|----------------|---------------------------------|
| 1  | Administrador  | Full access, admin settings     |
| 2  | Gestion        | Reports, clients, inventory     |
| 3  | Instalador     | Installations, map, orders      |
| 4  | Ventas         | Sales, contracts, plans         |

## Módulos
| Módulo          | Descripción                              | Acceso              |
|-----------------|------------------------------------------|---------------------|
| Clientes        | CRUD, IPs, red, ficha, estado servicio   | Admin, Gestión      |
| Facturación     | Facturas, PDF, pagos, cartera, recurrente| Admin, Gestión      |
| Ventas          | Dashboard, planes, contratos, ventas     | Admin, Gestión, Ventas |
| Inventario      | Equipos, stock mínimo, devolución        | Admin, Gestión      |
| Instalaciones   | Asignación, geolocalización, fotos       | Admin, Instalador   |
| Ordenes servicio| CRUD, asignación técnico, estados        | Admin, Gestión, Instalador |
| Tickets soporte | Soporte unificado (reportes + tickets)   | Admin, Gestión      |
| Mapa            | Cobertura Leaflet/OSM, zonas, empresa    | Todos               |
| Usuarios        | CRUD, roles, perfil                      | Admin               |
| Configuración   | Sistema (.env), empresa, permisos        | Admin               |
| Notificaciones  | Alertas automáticas, badge navbar        | Todos               |
| API REST        | Endpoints clientes/facturas/contratos    | Via API Key         |
| Informes        | Reportes exportables PDF/CSV             | Admin, Gestión      |
| Monitoreo SNMP  | Dispositivos, escaneo, ping/snmpget     | Admin, Gestión      |
| Backup BD       | mysqldump, descargar, restaurar          | Admin               |
| Auditoría       | Visor de bitácora con filtros            | Admin               |
| Portal Cliente  | Login, dashboard, pagos, tickets         | Clientes            |
| Plantillas Email| CRUD de plantillas para notificaciones   | Admin               |
| Health Dashboard| Estado PHP/MySQL/disco/backups           | Admin               |

## Dark Mode
- Toggle en navbar con icono luna/sol
- CSS variables: `body.dark-mode` cambia `--body-bg`, `--card-bg`, `--border-color`, `--text-primary`, `--text-secondary`, `--navbar-bg`, `--input-bg`, `--table-stripe`, `--table-hover`
- Persistencia via `localStorage.getItem('redreport-dark')`

## Security features active
- Session timeout: 30 min (`verificar_sesion()` in both sesion.php)
- Account lockout: 5 failed attempts → 15 min block (`verificar_bloqueo()` before login)
- CSRF tokens on all POST operations + verification
- XSS protection via `htmlspecialchars()` / `hescape()`
- Security headers: `X-Frame-Options: DENY`, `X-Content-Type-Options: nosniff`, `Referrer-Policy`
- Audit log: `bitacora()` writes IP, user, action, table, record ID, detail to `tb_bitacora`
- API Key auth for REST endpoints

## DB connection
- `app/config/conexion.php` uses constants from config.php
- Never use `new PDO()` directly — always `require_once '../app/config/conexion.php'` and use `$pdo`

## CSS
- Single file: `public/css/redreport.css` (~763 lines)
- Dark sidebar: `#0f172a`, brand: `#2563eb`
- Stat cards: 8 color variants (blue, green, orange, teal, purple, red, pink, indigo)

## Tasks
### Completed
- Full AdminLTE 3 → Bootstrap 5 migration (40+ view files)
- Professional CSS redesign (dark sidebar, gradient stats, modern cards)
- Coverage map module (Leaflet/OSM, zone CRUD, admin panel, company marker)
- Equipment inventory module (CRUD, filtering, stock minimum alerts, return-to-stock)
- Installations module (assignment, geolocation, equipment registration, photo uploads)
- Billing module (create invoice, items, auto-number FAC-XXXXX, PDF, mark paid/void, dashboard stats, cartera)
- Recurring billing (generate invoices for active contracts without current month invoice)
- Payment history (tb_pagos: monto, metodo, referencia, comprobante PDF)
- Sales module (dashboard, plans CRUD, contracts, sales registry, commissions)
- Service orders (tb_ordenes: CRUD, assignment, states, priority, solucion)
- Support tickets (tb_tickets: categories, assignment, resolution, audit)
- Notifications system (automatic alerts: pending installations, overdue invoices, low stock)
- Role Ventas (id_rol=4) added with dedicated sidebar and permissions
- Custom roles & permissions (tb_modulos + tb_permisos, admin UI in configuracion/permisos.php)
- Security system (CSRF, lockout 5/15min, bitácora, session timeout, XSS fixes)
- Profile module (edit profile, change password)
- Settings module (admin panel, writes .env)
- Company data module (tb_empresa: form + map picker)
- Client service status (Activo/Suspendido/Cortado), client detail page (ficha.php)
- Dashboard with real KPIs, Chart.js, top morosos, ordenes/tickets recientes
- REST API (endpoints: clientes CRUD, facturas, contratos, planes, dashboard)
- DataTables export buttons (Excel, CSV, PDF, Copy, Print)
- APP_URL auto-detection from __DIR__ + HTTP_X_FORWARDED_PROTO
- DB schema unified (redreport.sql: 24+ tablas con PKs, FKs, indices, datos iniciales)
- Syntax verified: 240+ PHP files pass `php -l`
- **Dark Mode**: Toggle luna/sol en navbar, CSS variables, localStorage persistencia
- **Auditoría**: Visor de bitácora (tb_bitacora) con filtros por usuario/acción/fecha
- **Búsqueda Global**: Ctrl+K o icono search en navbar, AJAX busca clientes/facturas/tickets/ordenes
- **Importar CSV**: Subida de CSV con preview, mapeo de columnas, validación y carga masiva de clientes
- **Plantillas Email**: CRUD completo de plantillas (tb_plantillas_email) con variables dinámicas
- **Health Dashboard**: Estado PHP/MySQL/disco/backups con stat cards y progress bars
- **Timeline Cliente**: Actividad reciente (facturas/pagos/tickets/ordenes) en ficha.php con línea de tiempo
- **Sidebar dinámico**: Resalta módulo activo según URL, submenú se abre automáticamente
- **Mapa de Calor**: Leaflet.heat con toggle Zonas/Calor, pesos por estado de servicio
- **2FA Google Authenticator**: TOTP class, setup QR, verificación en login, ±1 step clock skew
- **Prueba Email SMTP**: Botón en Configuración con test de envío PHPMailer
- **Mantenimiento BD**: Optimizar/Reparar/Analizar tablas con progreso vía AJAX
- **Visor de Logs**: PHP error log viewer con DataTable, descarga y limpieza
- **Documentación API**: Página interactiva con todos los endpoints REST documentados
- **Cola de Correos**: Sistema de colas asíncronas (tb_email_queue) con reprocesamiento
- **App Móvil PWA**: `movil/` con manifest.json + sw.js, login unificado (empleado/cliente), bottom nav tipo app nativa, instalador views (dashboard, instalación con GPS+fotos+firma+equipo, órdenes CRUD), cliente views (dashboard, facturas, tickets). Dark mode automático vía `prefers-color-scheme`

### In Progress
- *(none)*

### Done
- **CodeCanyon package files**: `changelog.txt`, `license.txt`, `screenshots/` creados
- **README.md profesional**: Rewrite completo con features, requisitos, instalación, stack, estructura, roles, API
- **Web installer**: `install/index.php` con wizard de configuración (requisitos → DB → admin → instalación). Detecta si .env existe, prueba conexión DB, importa SQL, configura admin
- **`verificar_acceso()` helper**: Nueva función en `seguridad.php` para role-based access control centralizado. Acepta array de roles permitidos, redirige a index si no autorizado
- **Role-based access checks**: Agregados a 50+ archivos (todas entry points y control files):
  - Clientes (1,2), Facturación (1,2), Ventas (1,2,4), Inventario (1,2)
  - Órdenes (1,2,3), Tickets (1,2), Informes (1,2), Monitoreo (1,2)
  - Usuarios (1), Backup (1), Auditoría (1), Configuración (1) — ya existía
  - Control files: clientes, facturacion, ventas, ordenes, tickets, monitoreo, inventario, usuarios
- **Sidebar gates fijadas**: "Usuarios y Seguridad" ahora solo visible para Admin (rol 1)
- **Broken gate en auditoría/index.php**: `verificar_sesion(...)` reemplazado por `verificar_acceso([1])`
- **Auth check en busqueda_global.php**: session_start + id_usuario check (AJAX search era accesible sin auth)
- **Syntax check**: 40+ archivos editados verificados con `php -l`, 0 errores
- **PWA icons reales**: `icon-192.png` y `icon-512.png` creados con Python/Pillow (wifi icon #2563eb)
- **manifest.json**: icons array agregado para PWA install prompt
- **Service Worker registration**: `navigator.serviceWorker.register('sw.js')` en `movil/login.php`
- **subir_foto.php**: CSRF bypass corregido (return value verificado), AJAX soporte agregado (JSON response)
- **realizar.php**: Photo upload con drag-and-drop, preview instantáneo, subida AJAX sin recarga, galería dinámica
- **PHP 8.x compatibilidad**: Escaneo completo — 0 funciones deprecadas/eliminadas encontradas en código de producción

## Relevant Files
- `redreport.sql` — 24 tables: tb_clientes, tb_ips, tb_red, tb_rol, tb_usuarios, tb_bitacora, tb_tipos_equipo, tb_equipos, tb_cobertura_zonas, tb_facturas, tb_factura_items, tb_planes, tb_contratos, tb_ventas, tb_empresa, tb_instalacion_fotos, tb_pagos, tb_ordenes, tb_tickets, tb_notificaciones, tb_modulos, tb_permisos, tb_plantillas_email
- `app/config/seguridad.php` — hescape(), CSRF, bitácora
- `app/config/config.php` — APP_URL from __DIR__, getenv() fallback, HTTPS detection
- `notificaciones/` — notification center, automatic alerts, AJAX badge counter
- `ordenes/` — service orders CRUD with estados y asignacion
- `tickets/` — support tickets with categorias y resolucion
- `api/` — REST API with API Key auth
- `configuracion/permisos.php` — role permissions UI
- `facturacion/controles/enviar_factura_email.php` — SMTP invoice sending
- `facturacion/pdf_comprobante.php` — payment receipt PDF
- `facturacion/registrar_pago.php` — payment form with metodo/referencia
- `instalaciones/controles/subir_foto.php` — photo upload for installations
- `movil/` — PWA mobile app: manifest.json, sw.js, login unificado, instalador dashboard/instalacion/ordenes, cliente dashboard/facturas/tickets. Bottom nav, dark mode automático
