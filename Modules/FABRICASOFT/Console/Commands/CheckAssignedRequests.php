<?php

namespace Modules\FABRICASOFT\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\FABRICASOFT\Entities\Preregistration;

class CheckAssignedRequests extends Command
{
    protected $signature = 'fabricasoft:check-assigned-requests {email?}';
    protected $description = 'Verificar solicitudes asignadas a analistas';

    public function handle()
    {
        $email = $this->argument('email');
        
        if ($email) {
            $this->info("Verificando solicitudes asignadas a: {$email}");
            
            $user = \App\Models\User::where('email', $email)->first();
            if (!$user) {
                $this->error("Usuario no encontrado: {$email}");
                return 1;
            }
            
            $solicitudes = Preregistration::where('assigned_analyst_id', $user->id)->get();
            
            $this->line("Solicitudes asignadas a {$user->nickname}: {$solicitudes->count()}");
            foreach ($solicitudes as $solicitud) {
                $this->line("  - ID: {$solicitud->id} - {$solicitud->full_name} - Estado: {$solicitud->workflow_status}");
            }
            
        } else {
            $this->info('Verificando todas las solicitudes asignadas...');
            
            $solicitudes = Preregistration::whereNotNull('assigned_analyst_id')->get();
            
            $this->line("Total de solicitudes asignadas: {$solicitudes->count()}");
            
            foreach ($solicitudes as $solicitud) {
                $analyst = \App\Models\User::find($solicitud->assigned_analyst_id);
                $this->line("  - ID: {$solicitud->id} - {$solicitud->full_name} - Analista: {$analyst->nickname} - Estado: {$solicitud->workflow_status}");
            }
        }
        
        return 0;
    }
}
