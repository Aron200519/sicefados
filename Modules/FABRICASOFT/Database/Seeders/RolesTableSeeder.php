<?php

namespace Modules\FABRICASOFT\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Crear roles para FABRICASOFT usando la estructura existente
        $roles = [
            [
                'name' => 'FABRICASOFT Admin',
                'slug' => 'fabricasoft.admin',
                'description' => 'Administrador del sistema FABRICASOFT',
                'description_english' => 'FABRICASOFT System Administrator',
                'full_access' => 'Si',
                'app_id' => 1,
            ],
            [
                'name' => 'FABRICASOFT Analista',
                'slug' => 'fabricasoft.analista',
                'description' => 'Analista de software en FABRICASOFT',
                'description_english' => 'Software Analyst in FABRICASOFT',
                'full_access' => 'No',
                'app_id' => 1,
            ],
            [
                'name' => 'FABRICASOFT Desarrollador',
                'slug' => 'fabricasoft.desarrollador',
                'description' => 'Desarrollador de software en FABRICASOFT',
                'description_english' => 'Software Developer in FABRICASOFT',
                'full_access' => 'No',
                'app_id' => 1,
            ],
            [
                'name' => 'FABRICASOFT Cliente Interno',
                'slug' => 'fabricasoft.cliente_interno',
                'description' => 'Cliente interno del sistema FABRICASOFT',
                'description_english' => 'Internal Client of FABRICASOFT System',
                'full_access' => 'No',
                'app_id' => 1,
            ],
            [
                'name' => 'FABRICASOFT Cliente Externo',
                'slug' => 'fabricasoft.cliente_externo',
                'description' => 'Cliente externo del sistema FABRICASOFT',
                'description_english' => 'External Client of FABRICASOFT System',
                'full_access' => 'No',
                'app_id' => 1,
            ]
        ];

        foreach ($roles as $roleData) {
            DB::table('roles')->updateOrInsert(
                ['slug' => $roleData['slug']],
                $roleData
            );
        }

        $this->command->info('FABRICASOFT roles created successfully!');
    }
}
