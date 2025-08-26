<?php

return [
    /*
    |--------------------------------------------------------------------------
    | FABRICASOFT Module Configuration
    |--------------------------------------------------------------------------
    |
    | Aquí puedes configurar las opciones del módulo FABRICASOFT
    |
    */

    // Emails de administradores que recibirán notificaciones
    'admin_emails' => env('FABRICASOFT_ADMIN_EMAILS', [
        'admin@fabricasoft.com',
        'dt2345160@gmail.com' // Email del usuario actual
    ]),

    // Configuración de notificaciones
    'notifications' => [
        'email_enabled' => env('FABRICASOFT_EMAIL_NOTIFICATIONS', true),
        'admin_notifications' => env('FABRICASOFT_ADMIN_NOTIFICATIONS', true),
    ],

    // Configuración de paginación
    'pagination' => [
        'solicitudes_per_page' => 15,
        'dashboard_solicitudes' => 10,
    ],

    // Estados de solicitudes
    'solicitud_statuses' => [
        'pending' => 'Pendiente',
        'approved' => 'Aprobada',
        'rejected' => 'Rechazada',
    ],

    // Tipos de software disponibles
    'software_types' => [
        'Sistema Web' => 'Sistema Web',
        'Aplicación Móvil' => 'Aplicación Móvil',
        'Sistema de Escritorio' => 'Sistema de Escritorio',
        'E-commerce' => 'E-commerce',
        'CRM' => 'CRM',
        'ERP' => 'ERP',
        'Otro' => 'Otro',
    ],

    // Configuración de exportación
    'export' => [
        'csv_enabled' => true,
        'excel_enabled' => false,
        'pdf_enabled' => false,
    ],

    // Configuración de logs
    'logging' => [
        'enabled' => true,
        'level' => 'info',
        'channels' => ['daily', 'stack'],
    ],
];
