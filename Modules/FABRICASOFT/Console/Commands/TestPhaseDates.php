<?php

namespace Modules\FABRICASOFT\Console\Commands;

use Illuminate\Console\Command;
use Modules\FABRICASOFT\Entities\ProjectPhase;

class TestPhaseDates extends Command
{
    protected $signature = 'fabricasoft:test-phase-dates {project_id}';
    protected $description = 'Test phase date functionality';

    public function handle()
    {
        $projectId = $this->argument('project_id');

        $this->info("Testing phase dates for Project ID: {$projectId}");

        try {
            $phases = ProjectPhase::where('project_id', $projectId)->get();

            foreach ($phases as $phase) {
                $this->line("\nPhase ID: {$phase->id}");
                $this->line("  Name: {$phase->phase_name}");
                $this->line("  Status: {$phase->status}");
                $this->line("  Start Date: " . ($phase->start_date ? $phase->start_date->format('d/m/Y H:i:s') : 'NULL'));
                $this->line("  End Date: " . ($phase->end_date ? $phase->end_date->format('d/m/Y H:i:s') : 'NULL'));
                $this->line("  Duration: " . ($phase->duration_days ?? 'NULL') . " días");
            }

            return 0;

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }
}

