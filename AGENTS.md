# RedReport — Summary

## Stack
- PHP 7.4+, MySQL, Bootstrap 5, Font Awesome 6, DataTables BS5, SweetAlert2, Chart.js, Leaflet/OSM, Dompdf
- jQuery (DataTables dependency), Popper.js (BS5)

## Architecture
- `app/config/config.php` — core config with `.env` loading via `parse_ini_file()`
- `app/config/conexion.php` — PDO singleton via `$pdo`
- `app/config/seguridad.php` — `csrf_field()`, `csrf_verify()`, `verificar_sesion()`, `verificar_bloqueo()`, `bitacora()`
- `sesion.php` / `gestion_soporte/sesion.php` — session start + security headers + timeout check
- `parte1.php` / `parte2.php` — layout templates (dark sidebar, BS5 CDN, DataTables)
- `.env` — DB_HOST, DB_NAME, DB_USER, DB_PASS, SMTP_*, APP_URL, APP_NAME, APP_ENV, APP_DEBUG

## Roles
| ID | Nombre         | Description                     |
|----|----------------|---------------------------------|
| 1  | Administrador  | Full access, admin settings     |
| 2  | Gestion        | Reports, clients, inventory     |
| 3  | Instalador     | Installations, map              |

## Key conventions
- All POST forms: `<?= csrf_field() ?>`
- All POST controllers: `csrf_verify()` + `bitacora($pdo, $id_usuario, ...)`
- Session vars: `$_SESSION['id_usuario']`, `['usuario']`, `['id_rol']`, `['ultimo_acceso']`, `['_csrf_token']`
- `APP_URL` auto-detects protocol + host + base path — no hardcoding needed
- `hescape($str)` shortcut for `htmlspecialchars($str, ENT_QUOTES, 'UTF-8')`
- Chart.js for bar charts, DataTables for tables, Leaflet for maps, Dompdf for PDFs

## Módulos
| Módulo          | Descripción                              | Acceso              |
|-----------------|------------------------------------------|---------------------|
| Clientes        | CRUD, IPs, red                           | Admin, Gestión      |
| Reportes        | Reportes generales, finalización         | Admin, Gestión      |
| Facturación     | Facturas mensuales, PDF, pagos           | Admin, Gestión      |
| Inventario      | Equipos (ONT, Router, etc.)              | Admin, Gestión      |
| Instalaciones   | Asignación, geolocalización, equipos     | Admin, Instalador   |
| Mapa            | Cobertura Leaflet/OSM, zonas             | Todos               |
| Usuarios        | CRUD, roles, perfil                      | Admin               |
| Configuración   | .env via UI                              | Admin               |

## Security features active
- Session timeout: 30 min (`verificar_sesion()` in both sesion.php)
- Account lockout: 5 failed attempts → 15 min block (`verificar_bloqueo()` before login)
- CSRF tokens on all POST operations
- XSS protection via `htmlspecialchars()` / `hescape()`
- Security headers: `X-Frame-Options: DENY`, `X-Content-Type-Options: nosniff`, `Referrer-Policy`
- Audit log: `bitacora()` writes IP, user, action, table, record ID, detail to `tb_bitacora`

## DB connection
- `app/config/conexion.php` uses constants from config.php
- Never use `new PDO()` directly — always `require_once '../app/config/conexion.php'` and use `$pdo`

## CSS
- Single file: `public/css/redreport.css` (~690 lines)
- Dark sidebar: `#0f172a`, brand: `#2563eb`
- Stat cards: 8 color variants (blue, green, orange, teal, purple, red, pink, indigo)
- Card header: `display: flex; align-items: center; justify-content: space-between`

## Tasks
### Completed
- Full AdminLTE 3 → Bootstrap 5 migration (40+ view files)
- Professional CSS redesign (dark sidebar, gradient stats, modern cards)
- Coverage map module (Leaflet/OSM, zone CRUD, admin panel)
- Equipment inventory module (CRUD, filtering by type/state)
- Installations module (assignment, geolocation, equipment registration)
- Billing module (create invoice, items, auto-number, PDF, mark paid/void, dashboard stats)
- Removed ISP-specific modules (Claro, Azteka, Dialnet, Liberty)
- Security system (CSRF, lockout, bitácora, session timeout, XSS fixes)
- Profile module (edit profile, change password)
- Settings module (admin panel, writes .env)
- All hardcoded URLs/credentials eliminated
- SMTP credentials in `.env` (recuperar_procesa.php uses SMTP_* constants)
- Navbar user dropdown (Perfil, Configuración, Cerrar sesión)
- DB schema cleanup (`redreport.sql` with all CREATE TABLE + Instalador role)
- Sidebar cleanup: all items now consistent (collapsible with arrow + submenu)

### Pending
- Add photo upload for equipment in `realizar.php`
- Equipment de-assignment / return-to-stock flow
- Test all modules on fresh install
- Send invoice by email from PDF module
