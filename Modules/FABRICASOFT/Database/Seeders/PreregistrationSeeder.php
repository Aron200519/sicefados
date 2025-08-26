<?php

namespace Modules\FABRICASOFT\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PreregistrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $preregistrations = [
            [
                'full_name' => 'Juan Pérez',
                'email' => 'juan.perez@empresa.com',
                'phone' => '+57 300 123 4567',
                'organization' => 'Empresa ABC Ltda',
                'software_type' => 'Sistema de Gestión Empresarial',
                'project_description' => 'Necesitamos un sistema integral de gestión empresarial que incluya control de inventarios, facturación, gestión de clientes y reportes financieros. La empresa tiene 3 sucursales y necesitamos una solución web que permita acceso remoto y sincronización en tiempo real.',
                'additional_requirements' => 'Integración con sistemas contables existentes, reportes personalizables, interfaz intuitiva para usuarios no técnicos',
                'client_type' => 'cliente_externo',
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'full_name' => 'María González',
                'email' => 'maria.gonzalez@startup.co',
                'phone' => '+57 310 987 6543',
                'organization' => 'StartupTech',
                'software_type' => 'Aplicación Móvil',
                'project_description' => 'Estamos desarrollando una aplicación móvil para delivery de comida que incluya sistema de pagos, geolocalización, gestión de restaurantes y seguimiento de pedidos en tiempo real. Necesitamos tanto la app para clientes como para restaurantes.',
                'additional_requirements' => 'MVP en 3 meses para presentar a inversionistas, integración con pasarelas de pago, notificaciones push',
                'client_type' => 'cliente_externo',
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'full_name' => 'Carlos Rodríguez',
                'email' => 'carlos.rodriguez@consultoria.com',
                'phone' => '+57 315 456 7890',
                'organization' => 'Consultoría Empresarial',
                'software_type' => 'CRM Personalizado',
                'project_description' => 'Buscamos un CRM personalizado para nuestra consultoría que incluya gestión de clientes, seguimiento de proyectos, facturación automática y reportes de rentabilidad. Debe integrarse con nuestros sistemas contables existentes.',
                'additional_requirements' => 'Interfaz fácil de usar para consultores con diferentes niveles de experiencia tecnológica, reportes personalizables',
                'client_type' => 'cliente_externo',
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'full_name' => 'Ana Martínez',
                'email' => 'ana.martinez@institucion.edu',
                'phone' => '+57 320 111 2222',
                'organization' => 'Instituto de Educación Superior',
                'software_type' => 'Sistema Académico',
                'project_description' => 'Nuestra institución requiere un sistema de gestión académica integral para estudiantes, profesores y administradores. Debe incluir matrículas, calificaciones, horarios, comunicación institucional y reportes académicos.',
                'additional_requirements' => 'Accesible desde dispositivos móviles, compatible con estándares educativos nacionales, módulo de videoconferencias',
                'client_type' => 'cliente_externo',
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'full_name' => 'Luis Hernández',
                'email' => 'luis.hernandez@restaurante.com',
                'phone' => '+57 325 333 4444',
                'organization' => 'Restaurante El Sabor',
                'software_type' => 'Sistema de Restaurante',
                'project_description' => 'Necesitamos un sistema completo para nuestro restaurante que incluya gestión de mesas, toma de pedidos, control de inventario, facturación y reportes de ventas. Debe ser fácil de usar para el personal del restaurante.',
                'additional_requirements' => 'Interfaz táctil para tablets, impresión de tickets, integración con delivery',
                'client_type' => 'cliente_externo',
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($preregistrations as $preregistration) {
            DB::table('fabricasoft_preregistrations')->insert($preregistration);
        }
    }
}
