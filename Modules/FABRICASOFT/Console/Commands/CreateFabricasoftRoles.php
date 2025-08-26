<?php

namespace Modules\FABRICASOFT\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateFabricasoftRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fabricasoft:create-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear los roles de FABRICASOFT directamente en la base de datos';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Creando roles de FABRICASOFT...');
        
        try {
            // Crear roles directamente en la base de datos
            $roles = [
                [
                    'name' => 'FABRICASOFT Admin',
                    'slug' => 'fabricasoft.admin',
                    'description' => 'Administrador del sistema FABRICASOFT',
                    'description_english' => 'FABRICASOFT System Administrator',
                    'app_id' => 1, // ID de la aplicación FABRICASOFT
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'name' => 'FABRICASOFT Desarrollador',
                    'slug' => 'fabricasoft.desarrollador',
                    'description' => 'Desarrollador de software en FABRICASOFT',
                    'description_english' => 'Software Developer in FABRICASOFT',
                    'app_id' => 1, // ID de la aplicación FABRICASOFT
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'name' => 'FABRICASOFT Cliente Interno',
                    'slug' => 'fabricasoft.cliente_interno',
                    'description' => 'Cliente interno del sistema FABRICASOFT',
                    'description_english' => 'Internal Client of FABRICASOFT System',
                    'app_id' => 1, // ID de la aplicación FABRICASOFT
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'name' => 'FABRICASOFT Cliente Externo',
                    'slug' => 'fabricasoft.cliente_externo',
                    'description' => 'Cliente externo del sistema FABRICASOFT',
                    'description_english' => 'External Client of FABRICASOFT System',
                    'app_id' => 1, // ID de la aplicación FABRICASOFT
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ];

            foreach ($roles as $roleData) {
                DB::table('roles')->updateOrInsert(
                    ['slug' => $roleData['slug']],
                    $roleData
                );
                $this->line("  - Creado/Actualizado: {$roleData['slug']}");
            }
            
            $this->info('Roles creados exitosamente!');
            
        } catch (\Exception $e) {
            $this->error('Error al crear los roles: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
