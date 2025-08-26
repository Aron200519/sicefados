<?php

namespace Modules\FABRICASOFT\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\FABRICASOFT\Entities\Project;
use Modules\FABRICASOFT\Entities\ProjectPhase;

class UpdateProjectPhases extends Command
{
    protected $signature = 'fabricasoft:update-project-phases {project_id?}';
    protected $description = 'Actualizar las fases de un proyecto existente con el nuevo flujo de trabajo';

    public function handle()
    {
        $this->info('🔄 Actualizando fases de proyectos existentes...');
        
        // Primero actualizar las fases existentes en la base de datos
        $this->updateExistingPhases();
        
        // Luego crear nuevas fases si no existen
        $this->createNewPhases();
        
        $this->info('✅ Proceso de actualización completado.');
    }
    
    private function updateExistingPhases()
    {
        $this->info('📝 Actualizando nombres de fases existentes...');
        
        // Actualizar Fase 1: Definición de Necesidades -> Especificaciones del documento del software
        $updated = DB::table('fabricasoft_project_phases')
            ->where('phase_name', 'Definición de Necesidades')
            ->update(['phase_name' => 'Especificaciones del documento del software']);
            
        if ($updated > 0) {
            $this->info("✅ Actualizadas {$updated} fases de 'Definición de Necesidades' a 'Especificaciones del documento del software'");
        }
        
        // Actualizar Fase 2: Análisis y SRS -> Análisis y SRS (mantener igual)
        // Actualizar Fase 3: Diseño del Sistema -> Diseño del Sistema (mantener igual)
        // Actualizar Fase 4: Codificación -> Codificación (mantener igual)
        // Actualizar Fase 5: Pruebas -> Pruebas (mantener igual)
        // Actualizar Fase 6: Validación -> Validación (mantener igual)
        // Actualizar Fase 7: Mantenimiento -> Mantenimiento (mantener igual)
    }
    
    private function createNewPhases()
    {
        $this->info('🆕 Creando nuevas fases para proyectos que no las tengan...');
        
        $projects = DB::table('fabricasoft_projects')->get();
        
        foreach ($projects as $project) {
            $existingPhases = DB::table('fabricasoft_project_phases')
                ->where('project_id', $project->id)
                ->count();
                
            if ($existingPhases == 0) {
                $this->info("📋 Creando fases para el proyecto: {$project->project_name}");
                $this->createPhasesForProject($project->id);
            }
        }
    }
    
    private function createPhasesForProject($projectId)
    {
        $phases = [
            [
                'phase_name' => 'Especificaciones del documento del software',
                'description' => 'Recepción y análisis inicial de la solicitud del cliente. El admin revisa y aprueba la solicitud.',
                'type' => 'milestone',
                'status' => 'completed', // Se completa cuando se crea el proyecto
                'duration_days' => 3,
                'order' => 1,
            ],
            [
                'phase_name' => 'Análisis y SRS',
                'description' => 'El analista revisa la solicitud, se comunica con el cliente y crea el documento de Especificación de Requisitos del Software (SRS).',
                'type' => 'milestone',
                'status' => 'planned',
                'duration_days' => 10,
                'order' => 2,
            ],
            [
                'phase_name' => 'Diseño del Sistema',
                'description' => 'Diseño de la arquitectura, base de datos, interfaces y componentes del sistema basado en el SRS.',
                'type' => 'milestone',
                'status' => 'planned',
                'duration_days' => 14,
                'order' => 3,
            ],
            [
                'phase_name' => 'Codificación',
                'description' => 'Desarrollo e implementación del código según el diseño establecido. Los desarrolladores trabajan en las funcionalidades.',
                'type' => 'milestone',
                'status' => 'planned',
                'duration_days' => 21,
                'order' => 4,
            ],
            [
                'phase_name' => 'Pruebas',
                'description' => 'Ejecución de pruebas unitarias, de integración y del sistema para validar la funcionalidad.',
                'type' => 'milestone',
                'status' => 'planned',
                'duration_days' => 10,
                'order' => 5,
            ],
            [
                'phase_name' => 'Validación',
                'description' => 'Validación final con el cliente, pruebas de aceptación y ajustes finales antes de la entrega.',
                'type' => 'milestone',
                'status' => 'planned',
                'duration_days' => 7,
                'order' => 6,
            ],
            [
                'phase_name' => 'Mantenimiento',
                'description' => 'Soporte post-entrega, corrección de errores y mejoras continuas del sistema.',
                'type' => 'milestone',
                'status' => 'planned',
                'duration_days' => 30,
                'order' => 7,
            ],
        ];
        
        foreach ($phases as $phaseData) {
            DB::table('fabricasoft_project_phases')->insert(array_merge($phaseData, [
                'project_id' => $projectId,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
            $this->line("✅ Fase creada: {$phaseData['phase_name']}");
        }
    }
}
