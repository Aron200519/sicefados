<?php

namespace Modules\FABRICASOFT\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Modules\FABRICASOFT\Entities\Preregistration;

class TestAnalystDashboard extends Command
{
    protected $signature = 'fabricasoft:test-analyst-dashboard {email}';
    protected $description = 'Probar el dashboard del analista';

    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Probando dashboard del analista: {$email}");
        
        // Buscar el usuario
        $user = \App\Models\User::where('email', $email)->first();
        if (!$user) {
            $this->error("Usuario no encontrado: {$email}");
            return 1;
        }
        
        $this->line("Usuario encontrado: {$user->nickname} (ID: {$user->id})");
        
        // Verificar si tiene rol de analista
        $hasAnalystRole = DB::table('role_user')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('role_user.user_id', $user->id)
            ->where('roles.slug', 'fabricasoft.analista')
            ->exists();
            
        if (!$hasAnalystRole) {
            $this->error("El usuario no tiene rol de analista");
            return 1;
        }
        
        $this->info("✅ Usuario tiene rol de analista");
        
        // Obtener solicitudes asignadas
        $assignedRequests = Preregistration::where('assigned_analyst_id', $user->id)
            ->orderBy('assigned_at', 'desc')
            ->get();
            
        $this->line("Solicitudes asignadas: {$assignedRequests->count()}");
        
        foreach ($assignedRequests as $request) {
            $this->line("  - ID: {$request->id} - {$request->full_name} - Estado: {$request->workflow_status} - Análisis: {$request->analysis_status}");
        }
        
        // Calcular estadísticas
        $stats = [
            'total_assigned' => $assignedRequests->count(),
            'pending_analysis' => $assignedRequests->where('analysis_status', 'pending')->count(),
            'in_progress' => $assignedRequests->where('analysis_status', 'in_progress')->count(),
            'completed' => $assignedRequests->where('analysis_status', 'completed')->count(),
        ];
        
        $this->line('');
        $this->info('Estadísticas del dashboard:');
        $this->line("  - Total asignadas: {$stats['total_assigned']}");
        $this->line("  - Pendientes de análisis: {$stats['pending_analysis']}");
        $this->line("  - En progreso: {$stats['in_progress']}");
        $this->line("  - Completadas: {$stats['completed']}");
        
        // Verificar que la vista se pueda renderizar
        try {
            $view = View::make('fabricasoft::analyst.dashboard', compact('assignedRequests', 'stats'));
            $html = $view->render();
            
            $this->line('');
            $this->info('✅ Vista del dashboard renderizada correctamente');
            
            // Verificar elementos clave
            if (strpos($html, 'Solicitudes Asignadas Recientes') !== false) {
                $this->info('✅ Tabla de solicitudes encontrada');
            }
            
            if (strpos($html, $user->nickname) !== false) {
                $this->info('✅ Información del usuario encontrada');
            }
            
            if (strpos($html, 'No tienes solicitudes asignadas') !== false || $assignedRequests->count() > 0) {
                $this->info('✅ Lógica de solicitudes funcionando');
            }
            
        } catch (\Exception $e) {
            $this->error('Error al renderizar la vista: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
