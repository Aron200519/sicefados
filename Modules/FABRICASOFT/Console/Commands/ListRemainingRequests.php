<?php

namespace Modules\FABRICASOFT\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ListRemainingRequests extends Command
{
    protected $signature = 'fabricasoft:list-requests';
    protected $description = 'Listar todas las solicitudes restantes';

    public function handle()
    {
        $this->info('📋 Listando solicitudes restantes...');
        
        $requests = DB::table('fabricasoft_preregistrations')
            ->orderBy('id')
            ->get(['id', 'full_name', 'email', 'organization', 'workflow_status', 'created_at']);
        
        if ($requests->count() == 0) {
            $this->info('✅ No hay solicitudes en el sistema.');
            return 0;
        }
        
        $this->line("📊 Total de solicitudes: {$requests->count()}");
        
        $this->table(
            ['ID', 'Nombre', 'Email', 'Organización', 'Estado', 'Fecha'],
            $requests->map(function($request) {
                return [
                    $request->id,
                    $request->full_name,
                    $request->email,
                    $request->organization,
                    $request->workflow_status ?? 'N/A',
                    $request->created_at
                ];
            })
        );
        
        return 0;
    }
}
