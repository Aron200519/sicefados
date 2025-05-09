<?php

namespace Modules\FABRICASOFT\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Modules\SICA\Entities\Role;
use Modules\SICA\Entities\App;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $app = App::where('name', 'FABRICASOFT')->firstOrFail();

        $roleadmin = Role::updateOrCreate(['slug' => 'fabricasoft.admin'], [
            'name' => 'Administrador',
            'description' => 'Administrador del sistema de Fabricasoft',
            'description_english' => 'Fabricasoft system administrator',
            'full_access' => 'No',
            'app_id' => $app->id,
        ]);

        $useradministrador = User::where('nickname', 'Deimar')->firstOrFail();
        $useradministrador->roles()->syncWithoutDetaching([$roleadmin->id]);

        $roleintern = Role::updateOrCreate(['slug' => 'fabricasoft.apprentices'], [
            'name' => 'Aprendiz',
            'description' => 'Rol aprendiz de la aplicación FABRICASOFT',
            'description_english' => 'Apprentice role of the FABRICASOFT application',
            'full_access' => 'No',
            'app_id' => $app->id,
        ]);

        $userintern = User::where('nickname', 'Deimar')->firstOrFail();

        $userintern->roles()->syncWithoutDetaching([$roleintern->id]);
    }
}
