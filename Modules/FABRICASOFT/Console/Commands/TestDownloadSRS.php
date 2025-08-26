<?php

namespace Modules\FABRICASOFT\Console\Commands;

use Illuminate\Console\Command;
use Modules\FABRICASOFT\Entities\Preregistration;
use Illuminate\Support\Facades\Log;

class TestDownloadSRS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fabricasoft:test-download-srs {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba la funcionalidad de descarga de SRS';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $id = $this->argument('id');
        
        $this->info("🔍 Probando descarga de SRS para solicitud ID: $id");
        
        try {
            // Buscar la solicitud
            $solicitud = Preregistration::find($id);
            
            if (!$solicitud) {
                $this->error("❌ Solicitud con ID $id no encontrada");
                return 1;
            }
            
            $this->info("✅ Solicitud encontrada: " . $solicitud->full_name);
            $this->info("📧 Email: " . $solicitud->email);
            $this->info("📁 SRS File Path: " . ($solicitud->srs_file_path ?? 'NULL'));
            $this->info("🔍 Analysis Status: " . ($solicitud->analysis_status ?? 'NULL'));
            $this->info("📊 Workflow Status: " . ($solicitud->workflow_status ?? 'NULL'));
            
            if ($solicitud->srs_file_path) {
                $filePath = storage_path('app/public/' . $solicitud->srs_file_path);
                $this->info("🔍 Ruta completa del archivo: " . $filePath);
                
                if (file_exists($filePath)) {
                    $this->info("✅ Archivo existe en el servidor");
                    $this->info("📏 Tamaño del archivo: " . filesize($filePath) . " bytes");
                } else {
                    $this->error("❌ Archivo no existe en el servidor");
                }
            } else {
                $this->warn("⚠️ La solicitud no tiene archivo SRS");
            }
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
    }
}




