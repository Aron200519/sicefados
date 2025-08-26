<?php

namespace Modules\FABRICASOFT\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssignClientRoleToAll extends Command
{
    protected $signature = 'fabricasoft:assign-client-role-to-all {--dry-run : Solo mostrar qué se haría sin ejecutar cambios} {--force : Ejecutar sin confirmación}';
    protected $description = 'Asignar rol de cliente interno a TODOS los usuarios que no lo tengan';

    public function handle()
    {
        $this->info('🔍 Iniciando asignación de rol de cliente interno a TODOS los usuarios...');
        
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
        
        // Verificar cuáles ya tienen el rol de cliente interno
        $usersWithRole = DB::table('role_user')
            ->where('role_id', $clientRole->id)
            ->pluck('user_id')
            ->toArray();
        
        $this->info("📈 Usuarios que YA tienen rol de cliente interno: " . count($usersWithRole));
        
        // Usuarios que necesitan el rol
        $usersNeedingRole = $allUsers->whereNotIn('id', $usersWithRole);
        $this->info("📈 Usuarios que NECESITAN el rol de cliente interno: " . $usersNeedingRole->count());
        
        if ($usersNeedingRole->isEmpty()) {
            $this->info('🎉 ¡Todos los usuarios ya tienen el rol de cliente interno!');
            return 0;
        }
        
        $this->info("\n👥 Usuarios que recibirán el rol de cliente interno:");
        foreach ($usersNeedingRole as $user) {
            $this->line("  - {$user->nickname} ({$user->email})");
        }
        
        if ($this->option('dry-run')) {
            $this->info("\n🔍 MODO SIMULACIÓN - No se realizarán cambios");
            $this->info("Para ejecutar realmente, omite la opción --dry-run");
            return 0;
        }
        
        // Confirmar la operación (omitir si se usa --force)
        if (!$this->option('force') && !$this->confirm('¿Estás seguro de que quieres asignar el rol de cliente interno a TODOS estos usuarios?')) {
            $this->info('❌ Operación cancelada por el usuario.');
            return 0;
        }
        
        $this->info("\n🚀 Ejecutando asignación de roles...");
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($usersNeedingRole as $user) {
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
                
                Log::info('FABRICASOFT rol de cliente interno asignado a usuario', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'role_id' => $clientRole->id,
                    'role_slug' => $clientRole->slug,
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
            $this->info("\n🎉 ¡Operación completada exitosamente!");
            $this->info("💡 Ahora TODOS los usuarios tienen acceso al sistema FABRICASOFT como clientes internos.");
            $this->info("💡 Los usuarios mantienen sus roles originales y ahora también tienen el de cliente interno.");
        }
        
        return 0;
    }
}
