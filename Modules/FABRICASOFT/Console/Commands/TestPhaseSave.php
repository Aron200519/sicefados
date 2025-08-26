<?php

namespace Modules\FABRICASOFT\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\FABRICASOFT\Entities\ProjectPhase;
use Illuminate\Support\Facades\Log;

class TestPhaseSave extends Command
{
    protected $signature = 'fabricasoft:test-phase-save {project_id} {phase_id}';
    protected $description = 'Test phase saving functionality directly';

    public function handle()
    {
        $projectId = $this->argument('project_id');
        $phaseId = $this->argument('phase_id');

        $this->info("Testing phase save for Project ID: {$projectId}, Phase ID: {$phaseId}");

        // Simular los datos que vendrían del formulario
        $testData = [
            'phase_id' => $phaseId,
            'phase_title' => 'Test Phase Title',
            'phase_description' => 'Test Phase Description',
            'phase_status' => 'completed',
            'phase_notes' => 'Test notes from command',
            'phase_link' => 'https://test.com',
        ];

        $this->info("Test data to save:");
        foreach ($testData as $key => $value) {
            $this->line("  {$key}: {$value}");
        }

        try {
            // Verificar que la fase existe
            $phase = ProjectPhase::where('project_id', $projectId)
                ->where('id', $phaseId)
                ->first();

            if (!$phase) {
                $this->error("Phase not found!");
                return 1;
            }

            $this->info("Phase found: {$phase->phase_name}");

            // Intentar actualizar directamente
            $updateData = [
                'phase_name' => $testData['phase_title'],
                'description' => $testData['phase_description'],
                'status' => $testData['phase_status'],
                'notes' => $testData['phase_notes'],
                'external_link' => $testData['phase_link'],
            ];

            $phase->update($updateData);

            $this->info("Phase updated successfully!");

            // Verificar que se guardó
            $updatedPhase = ProjectPhase::find($phaseId);
            $this->info("Updated phase data:");
            $this->line("  Notes: " . ($updatedPhase->notes ?? 'NULL'));
            $this->line("  External Link: " . ($updatedPhase->external_link ?? 'NULL'));
            $this->line("  Document Path: " . ($updatedPhase->document_path ?? 'NULL'));

            return 0;

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }
    }
}
