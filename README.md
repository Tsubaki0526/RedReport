# RedReport

Sistema de gestión ISP para administración de clientes, reportes técnicos, inventario de equipos, instalaciones en campo y mapa de cobertura.

## Requisitos

- PHP 7.4+
- MySQL 5.7+ / MariaDB 10.3+
- Extensiones: PDO, MySQL, GD, MBString, DOM, cURL
- Composer

## Instalación

```bash
# 1. Clonar el repositorio
git clone <repo> /ruta/web
cd /ruta/web

# 2. Instalar dependencias PHP
composer install

# 3. Configurar entorno
cp .env.example .env
# Editar .env con datos de DB, SMTP y APP_URL

# 4. Importar base de datos
mysql -u root -p redreport < redreport.sql

# 5. Verificar permisos
chmod 755 logs/ .env
```

## Estructura

```
RedReport/
├── app/config/          # Configuración, conexión, seguridad
├── clientes/            # Módulo de clientes
├── claro_azteka_dialnet_cw/  # Reportes por operador
├── configuracion/       # Panel de configuración (admin)
├── gestion_soporte/     # Gestión de soporte (rol Gestión)
├── informes/            # Generación de informes PDF
├── instalaciones/       # Instalaciones en campo
├── inventario/          # Inventario de equipos
├── login/               # Autenticación y recuperación
├── logs/                # Logs de aplicación
├── mapa/                # Mapa de cobertura
├── public/              # Assets (css/, img/, js/)
├── usuarios/            # Gestión de usuarios y perfil
├── redreport.sql        # Esquema de base de datos
└── .env.example         # Template de configuración
```

## Roles

| Rol         | ID  | Acceso principal                    |
|-------------|-----|-------------------------------------|
| Administrador | 1  | Todo el sistema                     |
| Gestión     | 2   | Reportes, clientes, inventario      |
| Instalador  | 3   | Instalaciones, mapa                 |

## Funcionalidades

- **Clientes**: CRUD completo, listado con DataTables, búsqueda por múltiples campos
- **Reportes**: Registro y seguimiento por operador (Claro, Azteca, Dialnet, Liberty)
- **Informes**: Generación de informes PDF por operador
- **Mapa de cobertura**: Visualización Leaflet/OSM con zonas poligonales y marcadores de clientes por estado
- **Inventario**: Equipos por tipo y estado, con filtros dinámicos
- **Instalaciones**: Asignación a instaladores, geolocalización en campo, registro de equipos instalados
- **Seguridad**: CSRF tokens, bloqueo por intentos fallidos (5/15min), timeout de sesión (30min), bitácora de auditoría
- **Perfil**: Edición de datos personales y cambio de contraseña
- **Configuración**: Panel admin para ajustar DB, SMTP, APP_URL desde interfaz web

## Seguridad

- Contraseñas con hash (password_hash / password_verify)
- CSRF en todos los formularios POST
- XSS mitigado con htmlspecialchars en todas las salidas
- Sesión con timeout de 30 minutos
- Bloqueo de cuenta tras 5 intentos fallidos (15 min)
- Registro de auditoría en tb_bitacora
- Headers HTTP seguros (X-Frame-Options, X-Content-Type-Options, etc.)

## Licencia

Propietaria — RedReport ISP Management System
