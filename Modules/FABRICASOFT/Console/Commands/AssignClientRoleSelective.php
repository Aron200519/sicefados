<?php

namespace Modules\FABRICASOFT\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssignClientRoleSelective extends Command
{
    protected $signature = 'fabricasoft:assign-client-role-selective {--dry-run : Solo mostrar qué se haría sin ejecutar cambios} {--force : Ejecutar sin confirmación}';
    protected $description = 'Asignar rol de cliente interno solo a usuarios que NO sean analistas, desarrolladores o admin';

    public function handle()
    {
        $this->info('🔍 Iniciando asignación selectiva de rol de cliente interno...');
        
        // Obtener el rol de cliente interno
        $clientRole = DB::table('roles')->where('slug', 'fabricasoft.cliente_interno')->first();
        
        if (!$clientRole) {
            $this->error('❌ El rol "fabricasoft.cliente_interno" no existe.');
            $this->info('💡 Ejecuta primero: php artisan fabricasoft:create-roles');
            return 1;
        }
        
        $this->info("✅ Rol encontrado: {$clientRole->name} (ID: {$clientRole->id})");
        
        // Obtener TODOS los usuarios del sistema
        $allUsers = DB::table('users')->select('id', 'email', 'nickname')->get();
        $this->info("📊 Total de usuarios en el sistema: {$allUsers->count()}");
        
        // Identificar usuarios que NO deben tener rol de cliente interno
        $excludedUsers = DB::table('users')
            ->join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->whereIn('roles.slug', [
                'fabricasoft.analista',
                'fabricasoft.desarrollador', 
                'fabricasoft.admin'
            ])
            ->select('users.id', 'users.nickname', 'users.email')
            ->distinct()
            ->get();
        
        $this->info("🚫 Usuarios EXCLUIDOS (analistas, desarrolladores, admin): " . $excludedUsers->count());
        foreach ($excludedUsers as $user) {
            $this->line("  - {$user->nickname} ({$user->email})");
        }
        
        // Usuarios que SÍ deben tener rol de cliente interno
        $usersNeedingRole = $allUsers->whereNotIn('id', $excludedUsers->pluck('id'));
        $this->info("📈 Usuarios que NECESITAN el rol de cliente interno: " . $usersNeedingRole->count());
        
        // Verificar cuáles ya tienen el rol de cliente interno
        $usersWithRole = DB::table('role_user')
            ->where('role_id', $clientRole->id)
            ->pluck('user_id')
            ->toArray();
        
        // Usuarios que realmente necesitan el rol (no lo tienen y no están excluidos)
        $usersToAssign = $usersNeedingRole->whereNotIn('id', $usersWithRole);
        $this->info("📈 Usuarios que RECIBIRÁN el rol de cliente interno: " . $usersToAssign->count());
        
        if ($usersToAssign->isEmpty()) {
            $this->info('🎉 ¡Todos los usuarios elegibles ya tienen el rol de cliente interno!');
            return 0;
        }
        
        $this->info("\n👥 Usuarios que recibirán el rol de cliente interno:");
        foreach ($usersToAssign as $user) {
            $this->line("  - {$user->nickname} ({$user->email})");
        }
        
        if ($this->option('dry-run')) {
            $this->info("\n🔍 MODO SIMULACIÓN - No se realizarán cambios");
            $this->info("Para ejecutar realmente, omite la opción --dry-run");
            return 0;
        }
        
        // Confirmar la operación (omitir si se usa --force)
        if (!$this->option('force') && !$this->confirm('¿Estás seguro de que quieres asignar el rol de cliente interno a estos usuarios?')) {
            $this->info('❌ Operación cancelada por el usuario.');
            return 0;
        }
        
        $this->info("\n🚀 Ejecutando asignación de roles...");
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($usersToAssign as $user) {
            try {
                // Verificar que no tenga ya el rol (doble verificación)
                $alreadyHasRole = DB::table('role_user')
                    ->where('user_id', $user->id)
                    ->where('role_id', $clientRole->id)
                    ->exists();
                
                if ($alreadyHasRole) {
                    $this->line("  ℹ️ {$user->nickname} ya tiene el rol de cliente interno");
                    continue;
                }
                
                // Asignar el rol
                DB::table('role_user')->insert([
                    'user_id' => $user->id,
                    'role_id' => $clientRole->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $this->line("  ✅ {$user->nickname} - Rol asignado exitosamente");
                $successCount++;
                
                Log::info('FABRICASOFT rol de cliente interno asignado selectivamente', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'role_id' => $clientRole->id,
                    'role_slug' => $clientRole->slug,
                    'executed_by' => 'artisan_command',
                ]);
                
            } catch (\Exception $e) {
                $this->line("  ❌ {$user->nickname} - Error: " . $e->getMessage());
                $errorCount++;
                
                Log::error('Error asignando rol de cliente interno selectivamente', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }
        
        $this->info("\n📊 Resumen de la operación:");
        $this->info("  ✅ Roles asignados exitosamente: {$successCount}");
        $this->info("  ❌ Errores: {$errorCount}");
        $this->info("  ℹ️ Total procesados: " . ($successCount + $errorCount));
        
        if ($successCount > 0) {
            $this->info("\n🎉 ¡Operación completada exitosamente!");
            $this->info("💡 Los usuarios elegibles ahora tienen acceso al sistema FABRICASOFT como clientes internos.");
            $this->info("💡 Los analistas, desarrolladores y admin mantienen solo sus roles especializados.");
        }
        
        return 0;
    }
}

