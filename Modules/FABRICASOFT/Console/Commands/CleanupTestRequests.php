<?php

namespace Modules\FABRICASOFT\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupTestRequests extends Command
{
    protected $signature = 'fabricasoft:cleanup-test-requests';
    protected $description = 'Eliminar todas las solicitudes de prueba del sistema';

    public function handle()
    {
        $this->info('🧹 Limpiando solicitudes de prueba...');
        
        // Buscar solicitudes de prueba
        $testRequests = DB::table('fabricasoft_preregistrations')
            ->where(function($query) {
                $query->where('email', 'like', '%test%')
                      ->orWhere('full_name', 'like', '%Test%')
                      ->orWhere('organization', 'like', '%Test%')
                      ->orWhere('email', 'like', '%@test.com')
                      ->orWhere('email', 'like', '%@example.com')
                      ->orWhere('full_name', 'like', '%Usuario%')
                      ->orWhere('full_name', 'like', '%Test%')
                      ->orWhere('organization', 'like', '%Test%')
                      ->orWhere('organization', 'like', '%Org%');
            })
            ->get(['id', 'full_name', 'email', 'organization', 'created_at']);
        
        if ($testRequests->count() == 0) {
            $this->info('✅ No se encontraron solicitudes de prueba para eliminar.');
            return 0;
        }
        
        $this->line("📋 Solicitudes de prueba encontradas: {$testRequests->count()}");
        
        // Mostrar las solicitudes que se van a eliminar
        $this->table(
            ['ID', 'Nombre', 'Email', 'Organización', 'Fecha'],
            $testRequests->map(function($request) {
                return [
                    $request->id,
                    $request->full_name,
                    $request->email,
                    $request->organization,
                    $request->created_at
                ];
            })
        );
        
        // Confirmar eliminación
        if (!$this->confirm('¿Estás seguro de que quieres eliminar estas solicitudes de prueba?')) {
            $this->info('❌ Operación cancelada.');
            return 0;
        }
        
        // Eliminar las solicitudes
        $deletedCount = DB::table('fabricasoft_preregistrations')
            ->where(function($query) {
                $query->where('email', 'like', '%test%')
                      ->orWhere('full_name', 'like', '%Test%')
                      ->orWhere('organization', 'like', '%Test%')
                      ->orWhere('email', 'like', '%@test.com')
                      ->orWhere('email', 'like', '%@example.com')
                      ->orWhere('full_name', 'like', '%Usuario%')
                      ->orWhere('full_name', 'like', '%Test%')
                      ->orWhere('organization', 'like', '%Test%')
                      ->orWhere('organization', 'like', '%Org%');
            })
            ->delete();
        
        $this->info("✅ Se eliminaron {$deletedCount} solicitudes de prueba exitosamente.");
        
        // Mostrar estadísticas actuales
        $totalRequests = DB::table('fabricasoft_preregistrations')->count();
        $this->line("📊 Total de solicitudes restantes: {$totalRequests}");
        
        return 0;
    }
}
