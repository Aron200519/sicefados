<?php

namespace Modules\FABRICASOFT\Console\Commands;

use Illuminate\Console\Command;
use Modules\FABRICASOFT\Entities\ProjectPhase;
use Illuminate\Support\Facades\Log;

class TestPhaseSaveDates extends Command
{
    protected $signature = 'fabricasoft:test-phase-save-dates {project_id} {phase_id}';
    protected $description = 'Test phase date saving functionality';

    public function handle()
    {
        $projectId = $this->argument('project_id');
        $phaseId = $this->argument('phase_id');

        $this->info("Testing phase date save for Project ID: {$projectId}, Phase ID: {$phaseId}");

        try {
            $phase = ProjectPhase::where('project_id', $projectId)
                ->where('id', $phaseId)
                ->first();

            if (!$phase) {
                $this->error("Phase not found!");
                return 1;
            }

            $this->info("Phase found: {$phase->phase_name}");
            $this->info("Current status: {$phase->status}");
            $this->info("Current start_date: " . ($phase->start_date ? $phase->start_date->format('d/m/Y H:i:s') : 'NULL'));
            $this->info("Current end_date: " . ($phase->end_date ? $phase->end_date->format('d/m/Y H:i:s') : 'NULL'));

            // Simular el guardado de la fase como completada
            $updateData = [
                'status' => 'completed',
                'end_date' => now(),
            ];

            // Si no tiene fecha de inicio, establecerla ahora
            if (!$phase->start_date) {
                $updateData['start_date'] = now();
                $this->info("Setting start_date to now because it was NULL");
            }

            $phase->update($updateData);

            $this->info("Phase updated successfully!");
            
            // Verificar los cambios
            $updatedPhase = ProjectPhase::find($phaseId);
            $this->info("Updated phase data:");
            $this->line("  Start Date: " . ($updatedPhase->start_date ? $updatedPhase->start_date->format('d/m/Y H:i:s') : 'NULL'));
            $this->line("  End Date: " . ($updatedPhase->end_date ? $updatedPhase->end_date->format('d/m/Y H:i:s') : 'NULL'));

            return 0;

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }
    }
}

