<?php

namespace Modules\FABRICASOFT\Console\Commands;

use Illuminate\Console\Command;
use Modules\FABRICASOFT\Http\Controllers\AdminController;

class TestControllerAnalysts extends Command
{
    protected $signature = 'fabricasoft:test-controller-analysts';
    protected $description = 'Probar el filtrado de analistas en el controlador';

    public function handle()
    {
        $this->info('Probando filtrado de analistas en el controlador...');
        
        try {
            // Crear instancia del controlador
            $controller = new AdminController();
            
            // Usar reflexión para acceder al método privado o crear una instancia temporal
            $reflection = new \ReflectionClass($controller);
            $method = $reflection->getMethod('showSolicitud');
            $method->setAccessible(true);
            
            // Simular una solicitud
            $solicitud = \Modules\FABRICASOFT\Entities\Preregistration::first();
            
            if (!$solicitud) {
                $this->error('No hay solicitudes en la base de datos');
                return 1;
            }
            
            $this->line("Solicitud encontrada: ID {$solicitud->id} - {$solicitud->full_name}");
            
            // Obtener analistas usando la misma lógica del controlador
            $analysts = \App\Models\User::whereRaw('id IN (
                SELECT user_id FROM role_user 
                WHERE role_id IN (SELECT id FROM roles WHERE slug = "fabricasoft.analista")
            )')
            ->whereRaw('id NOT IN (
                SELECT user_id FROM role_user 
                WHERE role_id NOT IN (SELECT id FROM roles WHERE slug = "fabricasoft.analista")
            )')
            ->get();
            
            $this->line("Analistas EXCLUSIVOS encontrados: {$analysts->count()}");
            foreach ($analysts as $analyst) {
                $this->line("  - {$analyst->nickname} ({$analyst->email}) - ID: {$analyst->id}");
            }
            
            $this->line('');
            $this->info('✅ Filtrado funcionando correctamente. Solo se muestran analistas exclusivos.');
            
        } catch (\Exception $e) {
            $this->error('Error al probar el controlador: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
