<?php

namespace Modules\FABRICASOFT\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Modules\FABRICASOFT\Entities\Preregistration;

class TestModalFunctionality extends Command
{
    protected $signature = 'fabricasoft:test-modal';
    protected $description = 'Probar la funcionalidad del modal de asignar analista';

    public function handle()
    {
        $this->info('Probando funcionalidad del modal...');
        
        // Buscar una solicitud existente
        $solicitud = Preregistration::first();
        
        if (!$solicitud) {
            $this->error('No hay solicitudes en la base de datos');
            return 1;
        }
        
        $this->line("Solicitud encontrada: ID {$solicitud->id} - {$solicitud->full_name}");
        
        // Obtener analistas
        $analysts = \App\Models\User::whereRaw('id IN (
            SELECT user_id FROM role_user 
            WHERE role_id IN (SELECT id FROM roles WHERE slug = "fabricasoft.analista")
        )')->get();
        
        $this->line("Analistas disponibles: {$analysts->count()}");
        foreach ($analysts as $analyst) {
            $this->line("  - {$analyst->nickname} ({$analyst->email})");
        }
        
        // Verificar que la vista se pueda renderizar
        try {
            $view = View::make('fabricasoft::admin.show_solicitud', compact('solicitud', 'analysts'));
            $html = $view->render();
            
            // Verificar elementos clave en el HTML
            $this->line('');
            $this->info('Verificando elementos en el HTML...');
            
            if (strpos($html, 'modalAsignarAnalista') !== false) {
                $this->info('✅ Modal ID encontrado en el HTML');
            } else {
                $this->error('❌ Modal ID NO encontrado en el HTML');
            }
            
            if (strpos($html, 'onclick="asignarAnalista()"') !== false) {
                $this->info('✅ Función onclick encontrada en el botón');
            } else {
                $this->error('❌ Función onclick NO encontrada en el botón');
            }
            
            if (strpos($html, 'function asignarAnalista()') !== false) {
                $this->info('✅ Función JavaScript encontrada');
            } else {
                $this->error('❌ Función JavaScript NO encontrada');
            }
            
            if (strpos($html, 'bootstrap.Modal') !== false) {
                $this->info('✅ Referencia a Bootstrap Modal encontrada');
            } else {
                $this->error('❌ Referencia a Bootstrap Modal NO encontrada');
            }
            
            $this->line('');
            $this->info('HTML generado correctamente. El problema puede estar en el navegador.');
            
        } catch (\Exception $e) {
            $this->error('Error al renderizar la vista: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
