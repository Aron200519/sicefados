<?php

namespace Modules\FABRICASOFT\Console\Commands;

use Illuminate\Console\Command;
use Modules\FABRICASOFT\Entities\ProjectPhase;
use Modules\FABRICASOFT\Entities\Project;

class TestPhaseView extends Command
{
    protected $signature = 'fabricasoft:test-phase-view {project_id}';
    protected $description = 'Test if the view is receiving correct phase data';

    public function handle()
    {
        $projectId = $this->argument('project_id');

        $this->info("Testing phase view data for Project ID: {$projectId}");

        try {
            $project = Project::with([
                'preregistration',
                'request',
                'scrumMaster',
                'teamMembers.user',
                'phases'
            ])->findOrFail($projectId);

            $this->info("Project found: {$project->name}");
            $this->info("Total phases: " . $project->phases->count());

            foreach ($project->phases as $phase) {
                $this->line("\nPhase ID: {$phase->id}");
                $this->line("  Name: {$phase->phase_name}");
                $this->line("  Status: {$phase->status}");
                $this->line("  Notes: " . ($phase->notes ?? 'NULL'));
                $this->line("  External Link: " . ($phase->external_link ?? 'NULL'));
                $this->line("  Document Path: " . ($phase->document_path ?? 'NULL'));
                $this->line("  Order: {$phase->order}");
            }

            // Verificar fases completadas vs actuales
            $completedPhases = $project->phases->where('status', 'completed')->sortBy('order');
            $currentPhase = $project->phases->where('status', '!=', 'completed')->sortBy('order')->first();

            $this->info("\nCompleted phases: " . $completedPhases->count());
            if ($currentPhase) {
                $this->info("Current phase: {$currentPhase->phase_name} (ID: {$currentPhase->id})");
            }

            return 0;

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }
}
