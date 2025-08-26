<?php

namespace Modules\FABRICASOFT\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CreateAnalystUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fabricasoft:create-analyst {email} {name} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear usuario analista de FABRICASOFT';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->argument('email');
        $name = $this->argument('name');
        $password = $this->argument('password');

        // Verificar si el usuario ya existe
        if (User::where('email', $email)->exists()) {
            $this->error("Usuario con email '{$email}' ya existe.");
            return 1;
        }

        // Crear primero el registro de persona
        $personId = DB::table('people')->insertGetId([
            'document_type' => 'Cédula de ciudadanía',
            'document_number' => DB::table('people')->max('document_number') + 1,
            'first_name' => $name,
            'first_last_name' => 'FABRICASOFT',
            'second_last_name' => 'ANALYST',
            'eps_id' => 6,
            'population_group_id' => 22,
            'pension_entity_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Crear el usuario
        $user = User::create([
            'nickname' => $name,
            'person_id' => $personId,
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        // Buscar el rol de analista
        $role = DB::table('roles')->where('slug', 'fabricasoft.analista')->first();
        if (!$role) {
            $this->error("Rol 'fabricasoft.analista' no encontrado.");
            return 1;
        }

        // Asignar el rol
        DB::table('role_user')->insert([
            'user_id' => $user->id,
            'role_id' => $role->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->info("Usuario analista creado exitosamente:");
        $this->line("  - Nombre: {$user->name}");
        $this->line("  - Email: {$user->email}");
        $this->line("  - Contraseña: {$password}");
        $this->line("  - Rol: {$role->name}");

        return 0;
    }
}
