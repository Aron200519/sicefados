<?php

namespace Modules\FABRICASOFT\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Modules\SICA\Entities\Role;

class AssignInternalClientRole extends Command
{
    protected $signature = 'fabricasoft:assign-internal-client-role {--dry-run : Solo mostrar qué se haría sin ejecutar cambios}';
    protected $description = 'Asignar rol de cliente interno a usuarios con roles de otros módulos';

    public function handle()
    {
        $this->info('🔍 Iniciando asignación de rol de cliente interno...');
        
        // Obtener el rol de cliente interno
        $internalClientRole = Role::where('slug', 'fabricasoft.cliente_interno')->first();
        
        if (!$internalClientRole) {
            $this->error('❌ El rol "fabricasoft.cliente_interno" no existe.');
            $this->info('💡 Ejecuta primero: php artisan fabricasoft:create-roles');
            return 1;
        }
        
        $this->info("✅ Rol encontrado: {$internalClientRole->name} (ID: {$internalClientRole->id})");
        
        // Obtener usuarios que tienen roles de otros módulos (no FABRICASOFT)
        $usersWithOtherRoles = DB::table('users')
            ->join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('roles.slug', 'not like', 'fabricasoft.%')
            ->select('users.id', 'users.email', 'users.nickname')
            ->distinct()
            ->get();
        
        if ($usersWithOtherRoles->isEmpty()) {
            $this->info('ℹ️ No hay usuarios con roles de otros módulos.');
            return 0;
        }
        
        $this->info("📊 Usuarios encontrados con roles de otros módulos: {$usersWithOtherRoles->count()}");
        
        // Mostrar usuarios que serán afectados
        $this->info("\n👥 Usuarios que recibirán el rol de cliente interno:");
        foreach ($usersWithOtherRoles as $user) {
            $this->line("  - {$user->nickname} ({$user->email})");
        }
        
        // Verificar si es solo simulación
        if ($this->option('dry-run')) {
            $this->info("\n🔍 MODO SIMULACIÓN - No se realizarán cambios");
            $this->info("Para ejecutar realmente, omite la opción --dry-run");
            return 0;
        }
        
        // Confirmar la operación (omitir si se usa --no-interaction)
        if (!$this->option('no-interaction') && !$this->confirm('¿Estás seguro de que quieres asignar el rol de cliente interno a estos usuarios?')) {
            $this->info('❌ Operación cancelada por el usuario.');
            return 0;
        }
        
        $this->info("\n🚀 Ejecutando asignación de roles...");
        
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($usersWithOtherRoles as $user) {
            try {
                // Verificar si el usuario ya tiene el rol de cliente interno
                $alreadyHasRole = DB::table('role_user')
                    ->where('user_id', $user->id)
                    ->where('role_id', $internalClientRole->id)
                    ->exists();
                
                if ($alreadyHasRole) {
                    $this->line("  ℹ️ {$user->nickname} ya tiene el rol de cliente interno");
                    continue;
                }
                
                // Asignar el rol
                DB::table('role_user')->insert([
                    'user_id' => $user->id,
                    'role_id' => $internalClientRole->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $this->line("  ✅ {$user->nickname} - Rol asignado exitosamente");
                $successCount++;
                
                // Log de la operación
                Log::info('FABRICASOFT rol de cliente interno asignado automáticamente', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'role_id' => $internalClientRole->id,
                    'role_slug' => $internalClientRole->slug,
                    'executed_by' => 'artisan_command',
                ]);
                
            } catch (\Exception $e) {
                $this->line("  ❌ {$user->nickname} - Error: " . $e->getMessage());
                $errorCount++;
                
                Log::error('Error asignando rol de cliente interno', [
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
            $this->info("\n🎉 Operación completada exitosamente!");
            $this->info("💡 Los usuarios ahora pueden acceder al sistema FABRICASOFT como clientes internos.");
        }
        
        return 0;
    }
}
