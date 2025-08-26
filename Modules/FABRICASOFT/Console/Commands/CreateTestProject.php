<?php

namespace Modules\FABRICASOFT\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateTestProject extends Command
{
    protected $signature = 'fabricasoft:create-test-project';
    protected $description = 'Create a test project manually to verify functionality';

    public function handle()
    {
        $this->info('=== CREANDO PROYECTO DE PRUEBA ===');
        
        try {
            DB::beginTransaction();
            
            // Crear el proyecto
            $this->info('1. Creando proyecto...');
            $projectId = DB::table('fabricasoft_projects')->insertGetId([
                'preregistration_id' => 6,
                'project_name' => 'Página Web de E-commerce - carlos juan',
                'description' => 'Crear una página web para la venta y compra de productos con carrito de compras',
                'status' => 'planning',
                'scrum_master_id' => 91, // Analista FABRICASOFT
                'start_date' => now(),
                'estimated_end_date' => now()->addDays(84), // 12 semanas
                'project_goals' => 'Desarrollar una plataforma de e-commerce funcional con carrito de compras',
                'success_criteria' => 'Página web funcionando con sistema de ventas y carrito de compras operativo',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $this->info("   ✅ Proyecto creado con ID: {$projectId}");
            
            // Crear fases del proyecto
            $this->info('2. Creando fases del proyecto...');
            $phases = [
                [
                    'project_id' => $projectId,
                    'phase_name' => 'Especificaciones del documento del software',
                    'description' => 'Fase inicial de análisis de requisitos y planificación del proyecto',
                    'type' => 'milestone',
                    'status' => 'planned',
                    'duration_days' => 14,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'project_id' => $projectId,
                    'phase_name' => 'Sprint 1 - Desarrollo Base',
                    'description' => 'Primer sprint para establecer la arquitectura base del sistema',
                    'type' => 'sprint',
                    'status' => 'planned',
                    'duration_days' => 21,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'project_id' => $projectId,
                    'phase_name' => 'Sprint 2 - Funcionalidades Core',
                    'description' => 'Segundo sprint para implementar funcionalidades principales',
                    'type' => 'sprint',
                    'status' => 'planned',
                    'duration_days' => 21,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'project_id' => $projectId,
                    'phase_name' => 'Sprint 3 - Refinamiento y Testing',
                    'description' => 'Tercer sprint para refinar funcionalidades y realizar testing',
                    'type' => 'sprint',
                    'status' => 'planned',
                    'duration_days' => 21,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'project_id' => $projectId,
                    'phase_name' => 'Entrega Final',
                    'description' => 'Entrega del producto final al cliente',
                    'type' => 'deliverable',
                    'status' => 'planned',
                    'duration_days' => 7,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];
            
            foreach ($phases as $phase) {
                DB::table('fabricasoft_project_phases')->insert($phase);
            }
            
            $this->info("   ✅ 5 fases creadas para el proyecto");
            
            DB::commit();
            
            $this->info('3. Verificando proyecto creado...');
            $project = DB::table('fabricasoft_projects')->where('id', $projectId)->first();
            $phasesCount = DB::table('fabricasoft_project_phases')->where('project_id', $projectId)->count();
            
            $this->info("   - Proyecto ID: {$project->id}");
            $this->info("   - Nombre: {$project->project_name}");
            $this->info("   - Estado: {$project->status}");
            $this->info("   - Scrum Master ID: {$project->scrum_master_id}");
            $this->info("   - Fases: {$phasesCount}");
            
            $this->info('✅ PROYECTO CREADO EXITOSAMENTE');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Error creando proyecto: " . $e->getMessage());
            Log::error('Error creando proyecto de prueba', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
