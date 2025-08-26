<?php

namespace Modules\FABRICASOFT\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AssignFabricasoftRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fabricasoft:assign-role {email} {role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asignar rol de FABRICASOFT a un usuario por email';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->argument('email');
        $roleSlug = $this->argument('role');

        // Buscar el usuario por email
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("Usuario con email '{$email}' no encontrado.");
            return 1;
        }

        // Buscar el rol
        $role = DB::table('roles')->where('slug', $roleSlug)->first();
        if (!$role) {
            $this->error("Rol '{$roleSlug}' no encontrado.");
            return 1;
        }

        // Verificar si ya tiene el rol
        $existingRole = DB::table('role_user')
            ->where('user_id', $user->id)
            ->where('role_id', $role->id)
            ->first();

        if ($existingRole) {
            $this->warn("El usuario '{$user->nickname}' ya tiene el rol '{$role->name}'.");
            return 0;
        }

        // Asignar el rol
        DB::table('role_user')->insert([
            'user_id' => $user->id,
            'role_id' => $role->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->info("Rol '{$role->name}' asignado exitosamente al usuario '{$user->nickname}' ({$user->email}).");
        return 0;
    }
}

