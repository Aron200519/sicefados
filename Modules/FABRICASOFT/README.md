# Módulo FABRICASOFT

## Descripción
Módulo de gestión de proyectos de software para FABRICASOFT. Este módulo proporciona funcionalidades para la gestión de proyectos, tareas y seguimiento de avances, incluyendo un sistema de pre-registro para nuevos usuarios.

## Roles de Usuario

### 1. Administrador
- **Acceso**: `/fabricasoft/admin`
- **Funcionalidades**:
  - Gestión de usuarios
  - Gestión de proyectos
  - Configuraciones del sistema
  - Reportes y estadísticas
  - Supervisión general
  - Revisión de solicitudes de pre-registro

### 2. Desarrollador
- **Acceso**: `/fabricasoft/desarrollador`
- **Funcionalidades**:
  - Visualización de proyectos asignados
  - Gestión de tareas
  - Seguimiento de fases
  - Reporte de avances
  - Subida de archivos

### 3. Cliente Interno
- **Acceso**: `/fabricasoft/cliente-interno`
- **Funcionalidades**:
  - Gestión de proyectos internos
  - Solicitud de nuevos proyectos
  - Modificación de requisitos
  - Seguimiento de avances
  - Reportes internos

### 4. Cliente Externo
- **Acceso**: `/fabricasoft/cliente-externo`
- **Funcionalidades**:
  - Visualización de proyectos contratados
  - Seguimiento de progreso
  - Solicitud de soporte
  - Acceso a contratos
  - Comunicación con el equipo

## Estructura del Módulo

```
Modules/FABRICASOFT/
├── Config/
│   └── config.php                 # Configuración del módulo
├── Database/
│   ├── Migrations/
│   │   └── 2025_08_22_000000_create_fabricasoft_preregistrations_table.php
│   └── Seeders/
│       └── PreregistrationSeeder.php
├── Entities/
│   └── Preregistration.php        # Modelo para pre-registros
├── Http/
│   ├── Controllers/
│   │   ├── FABRICASOFTController.php  # Controlador principal
│   │   └── PreregistroController.php  # Controlador de pre-registro
│   └── Middleware/                # Middlewares personalizados
├── Providers/
│   ├── FABRICASOFTServiceProvider.php  # Service Provider principal
│   └── RouteServiceProvider.php   # Proveedor de rutas
├── Resources/
│   └── views/
│       ├── index.blade.php        # Vista principal (bienvenida)
│       ├── preregistro.blade.php  # Formulario de pre-registro
│       ├── preregistro_success.blade.php  # Página de éxito
│       ├── admin/
│       │   └── dashboard.blade.php    # Dashboard administrador
│       ├── desarrollador/
│       │   └── dashboard.blade.php    # Dashboard desarrollador
│       ├── cliente_interno/
│       │   └── dashboard.blade.php    # Dashboard cliente interno
│       └── cliente_externo/
│           └── dashboard.blade.php    # Dashboard cliente externo
├── Routes/
│   ├── web.php                    # Rutas web
│   └── api.php                    # Rutas API
├── module.json                    # Configuración del módulo
└── README.md                      # Este archivo
```

## Funcionalidades del Sistema

### Página de Bienvenida
- **URL**: `/fabricasoft`
- **Acceso**: Público (sin autenticación)
- **Descripción**: Página principal con información del sistema, opciones de acceso y descripción de roles

### Sistema de Pre-Registro
- **URL**: `/fabricasoft/preregistro`
- **Acceso**: Público (sin autenticación)
- **Funcionalidades**:
  - Formulario de solicitud de acceso
  - Captura de información personal y del proyecto
  - Selección de tipo de cliente (interno/externo)
  - Validación de datos
  - Confirmación de envío

### Dashboards por Rol
- **Acceso**: Requiere autenticación y rol específico
- **Funcionalidades**: Cada rol tiene acceso a funcionalidades específicas según sus permisos

## Base de Datos

### Tabla: `fabricasoft_preregistrations`
- **Propósito**: Almacenar solicitudes de pre-registro
- **Campos principales**:
  - `id`: Identificador único
  - `first_name`, `last_name`: Nombre y apellido
  - `email`: Correo electrónico (único)
  - `phone`: Teléfono (opcional)
  - `organization`: Organización/Empresa
  - `position`: Cargo/Puesto (opcional)
  - `client_type`: Tipo de cliente (cliente_interno/cliente_externo)
  - `project_description`: Descripción del proyecto
  - `estimated_start`: Fecha estimada de inicio
  - `estimated_duration`: Duración estimada en meses
  - `additional_comments`: Comentarios adicionales
  - `status`: Estado (pending/approved/rejected)
  - `admin_notes`: Notas del administrador
  - `reviewed_at`: Fecha de revisión
  - `reviewed_by`: Usuario que revisó
  - `created_at`, `updated_at`: Timestamps

## Instalación

1. Asegúrate de que el módulo esté en el directorio `Modules/FABRICASOFT/`
2. Ejecuta las migraciones: `php artisan migrate`
3. Opcional: Ejecuta los seeders: `php artisan db:seed --class=Modules\\FABRICASOFT\\Database\\Seeders\\PreregistrationSeeder`
4. El módulo se registrará automáticamente a través de su Service Provider
5. Las rutas estarán disponibles bajo el prefijo `/fabricasoft`

## Configuración

El módulo incluye configuraciones para:
- Estados de proyectos
- Roles de usuario
- Configuraciones generales del sistema
- Estados de pre-registro

## Uso

### Acceso Principal
- **URL**: `/fabricasoft`
- **Descripción**: Página principal con acceso a todas las funcionalidades

### Pre-Registro
- **URL**: `/fabricasoft/preregistro`
- **Descripción**: Formulario para solicitar acceso al sistema

### Rutas por Rol
- **Admin**: `/fabricasoft/admin`
- **Desarrollador**: `/fabricasoft/desarrollador`
- **Cliente Interno**: `/fabricasoft/cliente-interno`
- **Cliente Externo**: `/fabricasoft/cliente-externo`

## Dependencias

- Laravel Framework
- Sistema de autenticación
- Sistema de roles y permisos
- Bootstrap CSS (para las vistas)

## Desarrollo

Para agregar nuevas funcionalidades:

1. **Controladores**: Crear en `Http/Controllers/`
2. **Vistas**: Crear en `Resources/views/`
3. **Rutas**: Agregar en `Routes/web.php` o `Routes/api.php`
4. **Entidades**: Crear en `Entities/`
5. **Migraciones**: Crear en `Database/Migrations/`
6. **Seeders**: Crear en `Database/Seeders/`

## Notas

- La página principal y el pre-registro son accesibles sin autenticación
- Los dashboards requieren autenticación y verificación de roles
- El sistema de pre-registro incluye validación de datos y logging
- Las vistas utilizan el layout principal de la aplicación (`layouts.app`)
- Los nombres de las tablas de base de datos están en inglés

## Versión
1.0.0
