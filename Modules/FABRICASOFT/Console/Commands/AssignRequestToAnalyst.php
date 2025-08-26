<?php

namespace Modules\FABRICASOFT\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AssignRequestToAnalyst extends Command
{
    protected $signature = 'fabricasoft:assign-request {request_id} {analyst_email}';
    protected $description = 'Asignar solicitud a un analista';

    public function handle()
    {
        $requestId = $this->argument('request_id');
        $analystEmail = $this->argument('analyst_email');

        // Verificar que la solicitud existe
        $request = DB::table('fabricasoft_preregistrations')->where('id', $requestId)->first();
        if (!$request) {
            $this->error("Solicitud con ID {$requestId} no encontrada.");
            return 1;
        }

        // Verificar que el analista existe
        $analyst = DB::table('users')->where('email', $analystEmail)->first();
        if (!$analyst) {
            $this->error("Analista con email {$analystEmail} no encontrado.");
            return 1;
        }

        // Verificar que el analista tiene el rol correcto
        $role = DB::table('role_user')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('role_user.user_id', $analyst->id)
            ->where('roles.slug', 'fabricasoft.analista')
            ->first();

        if (!$role) {
            $this->error("El usuario {$analystEmail} no tiene el rol de analista.");
            return 1;
        }

        // Asignar la solicitud al analista
        DB::table('fabricasoft_preregistrations')
            ->where('id', $requestId)
            ->update([
                'assigned_analyst_id' => $analyst->id,
                'assigned_at' => now(),
                'workflow_status' => 'assigned',
                'analysis_status' => 'pending',
                'updated_at' => now(),
            ]);

        $this->info("Solicitud #{$requestId} asignada exitosamente al analista {$analyst->nickname} ({$analyst->email}).");
        return 0;
    }
}
