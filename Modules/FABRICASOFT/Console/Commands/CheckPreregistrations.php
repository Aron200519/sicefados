<?php

namespace Modules\FABRICASOFT\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckPreregistrations extends Command
{
    protected $signature = 'fabricasoft:check-preregistrations';
    protected $description = 'Verificar solicitudes de preregistro';

    public function handle()
    {
        $this->info('Verificando solicitudes de preregistro...');
        
        $count = DB::table('fabricasoft_preregistrations')->count();
        $this->line("Total de solicitudes: {$count}");
        
        if ($count > 0) {
            $this->line('');
            $this->info('Primeras 3 solicitudes:');
            $requests = DB::table('fabricasoft_preregistrations')->select('*')->limit(3)->get();
            
            foreach ($requests as $request) {
                $this->line("ID: {$request->id}");
                $this->line("  Cliente: {$request->full_name}");
                $this->line("  Email: {$request->email}");
                $this->line("  Estado: {$request->workflow_status}");
                $this->line("  Analista: " . ($request->assigned_analyst_id ?? 'No asignado'));
                $this->line('---');
            }
        }
        
        return 0;
    }
}
