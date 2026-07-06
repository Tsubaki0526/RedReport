# RedReport — Sistema de Gestión para ISP

**Sistema integral de gestión empresarial** diseñado específicamente para proveedores de servicios de Internet (ISP). Administra clientes, facturación recurrente, inventario, instalaciones en campo, órdenes de servicio, tickets de soporte, ventas y monitoreo de red — todo desde una plataforma unificada con **app móvil PWA incluida**.

---

## Características que Diferencian a RedReport

### Gestión de Clientes
- CRUD completo con ficha detallada y timeline de actividad
- Control de IPs y equipos de red por cliente
- Estados de servicio: Activo / Suspendido / Cortado
- Geolocalización en mapa con Leaflet y OpenStreetMap
- Importación masiva desde CSV con preview y mapeo de columnas
- Portal de cliente para consultas y pagos

### Facturación Profesional
- Facturas con items dinámicos y numeración automática (FAC-XXXXX)
- Cálculo automático de IVA (19%)
- **Facturación recurrente**: genera facturas mensuales automáticamente para contratos activos
- Cartera de clientes con filtros por estado
- Pagos con múltiples métodos: Efectivo, Transferencia, Tarjeta, Cheque
- PDF descargable con Dompdf
- Envío de facturas por email con cola asíncrona
- Comprobantes de pago en PDF

### Ventas y Contratos
- Dashboard comercial con indicadores clave
- Planes de internet configurables (velocidad, precio)
- Contratos digitales con firma
- Registro de ventas con comisiones
- Tipos de venta: nuevo, renovación, upgrade

### Inventario de Equipos
- Control por tipo de equipo (ONT, Router, Fuente, Patch Cord, etc.)
- Asignación a clientes con trazabilidad
- Alertas de stock mínimo
- Devolución a almacén
- Filtros por estado (Disponible, Asignado, Dañado, Garantía)

### Instalaciones en Campo
- Asignación de instaladores con geolocalización
- **Fotos con drag-and-drop y subida AJAX**
- **Firma digital** del cliente (SignaturePad)
- Registro de equipos instalados
- App móvil PWA para instaladores en ruta

### Órdenes de Servicio
- CRUD completo con numeración automática (ORD-XXXXXX)
- Asignación a técnicos con prioridad (Baja, Media, Alta, Urgente)
- Estados: Abierta, En Proceso, Completada, Cancelada
- Registro de solución

### Tickets de Soporte
- Categorías: Fallo de conexión, Equipo, Facturación, Otro
- Asignación automática con prioridad
- Historial completo con resolución y auditoría
- Portal de cliente para apertura de tickets

### Mapa de Cobertura
- Visualización con **Leaflet + OpenStreetMap**
- Zonas poligonales de cobertura
- **Mapa de calor** con Leaflet.heat
- Geolocalización de clientes y empresa
- Administración de zonas desde el panel

### Monitoreo SNMP
- Escaneo de dispositivos de red
- Ping y estado en tiempo real
- Historial de checks

### Informes y Exportación
- Reportes exportables a **PDF, CSV, Excel**
- Filtros por fechas, responsables, tipos
- DataTables con botones de exportación
- Copia, impresión desde el navegador

### Seguridad Empresarial
- **CSRF tokens** en todos los formularios POST
- **2FA Google Authenticator** (TOTP)
- Bloqueo de cuenta tras 5 intentos fallidos (15 min)
- Timeout de sesión a los 30 minutos
- Headers HTTP seguros (X-Frame-Options, X-Content-Type-Options, Referrer-Policy)
- Bitácora completa de auditoría con filtros
- API Key para endpoints REST
- XSS mitigado con hescape() / htmlspecialchars

### App Móvil PWA
- **Sin instalación en Play Store** — funciona desde el navegador
- **Instalador:** Dashboard, GPS + fotos + firma, órdenes CRUD
- **Cliente:** Dashboard, facturas, tickets, registro de fallas
- Bottom navigation tipo app nativa
- Dark mode automático
- Service Worker para funcionamiento offline parcial

### Y mucho más...
- **Dashboard** con KPIs reales, gráficos Chart.js, top morosos
- **Búsqueda global** (Ctrl+K) en clientes, facturas, tickets, órdenes
- **Notificaciones automáticas**: instalaciones pendientes, facturas vencidas, stock bajo
- **Plantillas email** con variables dinámicas
- **Modo oscuro** con persistencia en localStorage
- **API REST** completa con 9+ endpoints
- **Health Dashboard** (PHP, MySQL, disco, backups)
- **Backup de BD** desde la interfaz web
- **Mantenimiento de tablas** (optimizar, reparar, analizar)
- **Visor de logs** PHP
- **Cola de correos** con reprocesamiento automático
- **Instalador web** con wizard de 4 pasos

---

## Roles y Permisos

| Rol | ID | Descripción |
|-----|----|-------------|
| **Administrador** | 1 | Acceso total al sistema |
| **Gestión** | 2 | Reportes, clientes, inventario, facturación |
| **Instalador** | 3 | Instalaciones, mapa, órdenes, tickets |
| **Ventas** | 4 | Ventas, planes, contratos |

Sistema de permisos granular vía `tb_modulos + tb_permisos` con interfaz de administración web. Puedes personalizar qué puede ver, crear, editar y eliminar cada rol por módulo.

---

## Requisitos del Servidor

| Requisito | Versión / Detalle |
|-----------|------------------|
| **PHP** | 7.4+ (recomendado 8.0+) |
| **Base de datos** | MySQL 5.7+ o MariaDB 10.3+ |
| **Extensiones PHP** | PDO, pdo_mysql, GD, MBString, DOM, cURL, JSON |
| **Composer** | Necesario para instalar dependencias (PHPMailer, Dompdf) |
| **Servidor web** | Apache con mod_rewrite o nginx |
| **HTTPS** | Recomendado para producción |

---

## Stack Tecnológico

| Capa | Tecnología |
|------|-----------|
| **Backend** | PHP 7.4+ nativo (sin framework) |
| **Base de datos** | MySQL / MariaDB |
| **Frontend** | Bootstrap 5.3, Font Awesome 6, DataTables 1.13, Chart.js 4, SweetAlert2 |
| **Mapas** | Leaflet + OpenStreetMap, Leaflet.heat |
| **PDF** | Dompdf |
| **Email** | PHPMailer con cola asíncrona |
| **2FA** | Google Authenticator (TOTP) |
| **App Móvil** | PWA nativa (Service Worker, GPS, SignaturePad) |
| **Monitoreo** | SNMP (php-snmp) |

---

## Instalación

### Método 1: Instalador Web (Recomendado)

```bash
# 1. Copiar archivos al servidor web
# 2. Instalar dependencias
composer install

# 3. Acceder vía navegador a:
#    http://tudominio.com/install/
#    El wizard te guiará en 4 pasos:
#    Requisitos → Base de datos → Admin + URL → Instalación
```

### Método 2: Manual

```bash
# 1. Copiar archivos al servidor
composer install
cp .env.example .env

# 2. Crear base de datos e importar SQL
mysql -u root -p tu_bd < redreport.sql

# 3. Editar .env con tus datos
# 4. Acceder a http://tudominio.com/
```

**Usuarios predefinidos:**
| Usuario | Contraseña | Rol |
|---------|-----------|-----|
| `administrador` | admin | Administrador |
| `gestor` | 123456 | Gestión |
| `instalador1` | 123456 | Instalador |
| `vendedor1` | 123456 | Ventas |

---

## Estructura del Proyecto

```
RedReport/
├── app/config/          # Configuración, conexión PDO, seguridad
├── api/                 # API REST con autenticación por API Key
├── auditoria/           # Visor de bitácora
├── backup/              # Respaldos de base de datos
├── clientes/            # Módulo de clientes
├── configuracion/       # Panel de administración del sistema
├── docs/                # Documentación técnica para compradores
├── facturacion/         # Facturación, pagos, cartera
├── informes/            # Reportes exportables
├── instalaciones/       # Gestión de instalaciones en campo
├── install/             # Instalador web wizard
├── inventario/          # Control de inventario de equipos
├── login/               # Autenticación y recuperación de contraseña
├── mapa/                # Mapa de cobertura Leaflet
├── monitoreo/           # Monitoreo SNMP de dispositivos
├── movil/               # App móvil PWA
├── notificaciones/      # Centro de notificaciones
├── ordenes/             # Órdenes de servicio
├── portal/              # Portal del cliente
├── public/              # Assets estáticos (css, img, js)
├── tickets/             # Tickets de soporte técnico
├── usuarios/            # Gestión de usuarios y roles
├── ventas/              # Ventas, planes y contratos
├── vendor/              # Dependencias Composer
├── redreport.sql        # Esquema completo con datos demo
└── .env.example         # Template de configuración
```

---

## Capturas de Pantalla

![Dashboard](screenshots/dashboard.jpg)
*Panel principal con KPIs, gráficos y actividad reciente*

![Clientes](screenshots/clientes.jpg)
*Gestión de clientes con timeline y fichas detalladas*

![Facturación](screenshots/facturacion.jpg)
*Facturación profesional con PDF y cartera*

![App Móvil](screenshots/movil.jpg)
*App móvil PWA con bottom navigation*

---

## API REST

Endpoints disponibles (requieren API Key vía header `X-API-Key`):

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/clientes` | Listar clientes |
| POST | `/api/clientes` | Crear cliente |
| GET | `/api/clientes/{id}` | Ver cliente |
| PUT | `/api/clientes/{id}` | Actualizar cliente |
| DELETE | `/api/clientes/{id}` | Eliminar cliente |
| GET | `/api/facturas` | Listar facturas |
| GET | `/api/contratos` | Listar contratos |
| GET | `/api/planes` | Listar planes |
| GET | `/api/dashboard` | Resumen de indicadores |

Documentación interactiva en `configuracion/api/documentacion.php`.

---

## Soporte Técnico

- **Documentación:** Revisa `docs/documentacion.md` para guía completa de instalación y configuración
- **Reportar bugs:** Abre un issue en el repositorio oficial
- **Email:** jorge.valdeblanquez.tech@gmail.com

---

## Licencia

Este software se distribuye bajo licencia de CodeCanyon. Consulte `license.txt` para términos de uso detallados.

**Lo que incluye la licencia regular:**
- Uso en un solo proyecto o dominio
- Actualizaciones gratuitas de por vida
- Soporte técnico por 6 meses (renovable)

---

*RedReport v2.0.0 — Sistema de Gestión para ISP. Hecho con PHP nativo, Bootstrap 5 y pasión por los ISP.*
