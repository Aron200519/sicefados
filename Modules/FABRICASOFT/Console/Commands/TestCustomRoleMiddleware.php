<?php

namespace Modules\FABRICASOFT\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TestCustomRoleMiddleware extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fabricasoft:test-roles {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test custom role middleware for a specific user';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Probando roles para usuario: {$email}");
        
        // Buscar usuario por email
        $user = DB::table('users')->where('email', $email)->first();
        
        if (!$user) {
            $this->error("Usuario no encontrado");
            return 1;
        }
        
        $this->info("Usuario encontrado: ID {$user->id}, Nombre: {$user->nickname}");
        $this->info("Campo 'name' en BD: " . ($user->name ?? 'NULL'));
        $this->info("Campo 'nickname' en BD: " . ($user->nickname ?? 'NULL'));
        
        // Verificar roles
        $roles = DB::table('role_user')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('role_user.user_id', $user->id)
            ->select('roles.id', 'roles.name', 'roles.slug')
            ->get();
            
        $this->info("Roles encontrados:");
        foreach ($roles as $role) {
            $this->line("- ID: {$role->id}, Nombre: {$role->name}, Slug: {$role->slug}");
        }
        
        // Verificar rol específico
        $hasRole = DB::table('role_user')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('role_user.user_id', $user->id)
            ->where('roles.slug', 'fabricasoft.cliente_externo')
            ->exists();
            
        $this->info("¿Tiene rol 'fabricasoft.cliente_externo'? " . ($hasRole ? 'SÍ' : 'NO'));
        
        return 0;
    }
}
