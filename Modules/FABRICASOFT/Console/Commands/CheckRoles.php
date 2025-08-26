<?php

namespace Modules\FABRICASOFT\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckRoles extends Command
{
    protected $signature = 'fabricasoft:check-roles';
    protected $description = 'Verificar y crear roles de FABRICASOFT';

    public function handle()
    {
        $this->info('🔍 Verificando roles de FABRICASOFT...');

        try {
            // Verificar si existe la tabla roles
            if (!Schema::hasTable('roles')) {
                $this->error('❌ La tabla "roles" no existe. Verifica que Spatie Laravel Permission esté instalado.');
                return 1;
            }

            // Verificar roles existentes
            $existingRoles = DB::table('roles')->where('slug', 'like', 'fabricasoft.%')->get();
            
            $this->info('📋 Roles de FABRICASOFT encontrados:');
            if ($existingRoles->count() > 0) {
                foreach ($existingRoles as $role) {
                    $this->line("  ✅ {$role->name} (slug: {$role->slug})");
                }
            } else {
                $this->line("  ❌ No se encontraron roles de FABRICASOFT");
            }

            // Verificar si existen los roles necesarios
            $requiredRoles = [
                'fabricasoft.admin' => 'Administrador FABRICASOFT',
                'fabricasoft.analista' => 'Analista FABRICASOFT',
                'fabricasoft.desarrollador' => 'Desarrollador FABRICASOFT',
                'fabricasoft.cliente_interno' => 'Cliente Interno FABRICASOFT'
            ];

            $this->info("\n🔧 Creando roles faltantes...");
            
            foreach ($requiredRoles as $slug => $name) {
                $exists = DB::table('roles')->where('slug', $slug)->exists();
                
                if (!$exists) {
                    DB::table('roles')->insert([
                        'name' => $name,
                        'slug' => $slug,
                        'guard_name' => 'web',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $this->line("  ✅ Creado: {$name} ({$slug})");
                } else {
                    $this->line("  ℹ️  Ya existe: {$name} ({$slug})");
                }
            }

            // Verificar usuarios con roles
            $this->info("\n👥 Verificando usuarios con roles de FABRICASOFT...");
            
            $usersWithRoles = DB::table('users')
                ->join('role_user', 'users.id', '=', 'role_user.user_id')
                ->join('roles', 'role_user.role_id', '=', 'roles.id')
                ->where('roles.slug', 'like', 'fabricasoft.%')
                ->select('users.name', 'users.email', 'roles.name as role_name', 'roles.slug as role_slug')
                ->get();

            if ($usersWithRoles->count() > 0) {
                foreach ($usersWithRoles as $user) {
                    $this->line("  👤 {$user->name} ({$user->email}) - {$user->role_name}");
                }
            } else {
                $this->line("  ❌ No hay usuarios con roles de FABRICASOFT");
            }

            $this->info("\n✅ Verificación completada.");
            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
    }
}

