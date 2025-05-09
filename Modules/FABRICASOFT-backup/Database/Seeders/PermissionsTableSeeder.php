<?php

namespace Modules\FABRICASOFT\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\SICA\Entities\App;
use Modules\SICA\Entities\Permission;
use Modules\SICA\Entities\Role;

class PermissionsTableSeeder extends Seeder {
    public function run()
    {
        // Obtener aplicación FABRICASOFT
        $app = App::where('name', 'FABRICASOFT')->first();

        /** ============================================
         *  PERMISOS PARA ADMINISTRADOR
         *  ============================================ */
        $permissions_admin = [];

        // Acceso al panel de administrador
        $permissions_admin[] = Permission::updateOrCreate(
            ['slug' => 'fabricasoft.admin.welcome'],
            [
                'name' => 'Acceso al Rol de Administrador',
                'description' => 'Acceso al Rol de Administrador',
                'description_english' => 'Access to the Administrator Role',
                'app_id' => $app->id,
            ]
        )->id;
        // Asignar permisos al rol administrador
        $rol_admin = Role::where('slug', 'fabricasoft.admin')->first();
        $rol_admin->permissions()->syncWithoutDetaching($permissions_admin);

        /** ============================================
         *  PERMISOS PARA APRENDIZ
         *  ============================================ */
        $permissions_intern = [];

        // Acceso al panel del pasante
        $permissions_intern[] = Permission::updateOrCreate(
            ['slug' => 'fabricasoft.apprentices'],
            [
                'name' => 'Acceso al Rol de Aprendiz',
                'description' => 'Acceso al Rol de Aprendiz',
                'description_english' => 'Access to the Apprentices Role',
                'app_id' => $app->id,
            ]
        )->id;

        // Asignar permisos al rol pasante
        $rol_intern = Role::where('slug', 'fabricasoft.apprentices')->first();
        $rol_intern->permissions()->syncWithoutDetaching($permissions_intern);
    }
}
