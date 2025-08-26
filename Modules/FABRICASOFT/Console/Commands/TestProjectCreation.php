<?php

namespace Modules\FABRICASOFT\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TestProjectCreation extends Command
{
    protected $signature = 'fabricasoft:test-project-creation';
    protected $description = 'Test project creation and diagnose issues';

    public function handle()
    {
        $this->info('=== DIAGNÓSTICO DE CREACIÓN DE PROYECTOS ===');
        
        // Verificar solicitud
        $this->info('1. Verificando solicitud ID 6...');
        $solicitud = DB::table('fabricasoft_preregistrations')->where('id', 6)->first();
        
        if (!$solicitud) {
            $this->error('❌ Solicitud ID 6 no encontrada');
            return;
        }
        
        $this->info('✅ Solicitud encontrada');
        $this->info("   - Estado: {$solicitud->workflow_status}");
        $this->info("   - Nombre: {$solicitud->full_name}");
        $this->info("   - Email: {$solicitud->email}");
        
        // Verificar tablas
        $this->info('2. Verificando tablas...');
        $projectsCount = DB::table('fabricasoft_projects')->count();
        $phasesCount = DB::table('fabricasoft_project_phases')->count();
        $membersCount = DB::table('fabricasoft_project_team_members')->count();
        
        $this->info("   - Proyectos: {$projectsCount}");
        $this->info("   - Fases: {$phasesCount}");
        $this->info("   - Miembros: {$membersCount}");
        
        // Verificar si ya existe un proyecto para esta solicitud
        $existingProject = DB::table('fabricasoft_projects')
            ->where('preregistration_id', 6)
            ->first();
            
        if ($existingProject) {
            $this->warn('⚠️  Ya existe un proyecto para esta solicitud');
            $this->info("   - ID del proyecto: {$existingProject->id}");
            $this->info("   - Nombre: {$existingProject->project_name}");
            $this->info("   - Estado: {$existingProject->status}");
        } else {
            $this->info('✅ No hay proyecto existente para esta solicitud');
        }
        
        // Verificar usuarios disponibles
        $this->info('3. Verificando usuarios disponibles...');
        $users = DB::table('users')->get();
        $this->info("   - Total usuarios: " . $users->count());
        
        foreach ($users as $user) {
            $name = $user->name ?? $user->nickname ?? 'Sin nombre';
            $this->info("     - ID: {$user->id}, Nombre: {$name}, Email: {$user->email}");
        }
        
        $this->info('=== FIN DEL DIAGNÓSTICO ===');
    }
}
