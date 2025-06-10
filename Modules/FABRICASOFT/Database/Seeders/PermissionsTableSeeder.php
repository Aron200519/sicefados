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

        if (!$app) {
            $this->command->error('La aplicación FABRICASOFT no existe. Crea la app primero.');
            return;
        }

        /** ============================================
         *  PERMISOS PARA ADMINISTRADOR
         *  ============================================ */
        $permissions_admin = [];

        // Crear permiso
        $admin_permission = Permission::updateOrCreate(
            ['slug' => 'fabricasoft.admin.welcome'],
            [
                'name' => 'Acceso al Rol de Administrador',
                'description' => 'Acceso al Rol de Administrador',
                'description_english' => 'Access to the Administrator Role',
                'app_id' => $app->id,
            ]
        );

        $permissions_admin[] = $admin_permission->id;

        // Asignar permisos al rol administrador
        $rol_admin = Role::where('slug', 'fabricasoft.admin')->first();
        if ($rol_admin) {
            $rol_admin->permissions()->syncWithoutDetaching($permissions_admin);
        } else {
            $this->command->warn('Rol fabricasoft.admin no encontrado. Crea el rol antes de ejecutar el seeder.');
        }

        /** ============================================
         *  PERMISOS PARA APRENDIZ
         *  ============================================ */
        $permissions_intern = [];

        // Crear permiso
        $intern_permission = Permission::updateOrCreate(
            ['slug' => 'fabricasoft.apprentices'],
            [
                'name' => 'Acceso al Rol de Aprendiz',
                'description' => 'Acceso al Rol de Aprendiz',
                'description_english' => 'Access to the Apprentices Role',
                'app_id' => $app->id,
            ]
        );

        $permissions_intern[] = $intern_permission->id;

        // Asignar permisos al rol aprendiz
        $rol_intern = Role::where('slug', 'fabricasoft.apprentices')->first();
        if ($rol_intern) {
            $rol_intern->permissions()->syncWithoutDetaching($permissions_intern);
        } else {
            $this->command->warn('Rol fabricasoft.apprentices no encontrado. Crea el rol antes de ejecutar el seeder.');
        }

        $this->command->info('Permisos asignados correctamente.');
    }
}
