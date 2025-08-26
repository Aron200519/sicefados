<?php

namespace Modules\FABRICASOFT\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class DebugAuth extends Command
{
    protected $signature = 'fabricasoft:debug-auth';
    protected $description = 'Debuggear el estado de autenticación y roles';

    public function handle()
    {
        $this->info('=== DEBUG DE AUTENTICACIÓN ===');
        
        // Verificar usuario admin
        $adminUser = User::where('email', 'dt2345160@gmail.com')->first();
        if (!$adminUser) {
            $this->error('❌ Usuario dt2345160@gmail.com no encontrado');
            return;
        }
        
        $this->info("✅ Usuario encontrado: ID {$adminUser->id}, Email: {$adminUser->email}");
        
        // Verificar roles del usuario
        $userRoles = DB::table('role_user')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('role_user.user_id', $adminUser->id)
            ->select('roles.slug', 'roles.name')
            ->get();
            
        if ($userRoles->count() > 0) {
            $this->info('Roles del usuario:');
            foreach ($userRoles as $role) {
                $this->line("  - {$role->slug}: {$role->name}");
            }
        } else {
            $this->error('❌ El usuario no tiene roles asignados');
        }
        
        // Verificar específicamente el rol admin
        $hasAdminRole = DB::table('role_user')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('role_user.user_id', $adminUser->id)
            ->where('roles.slug', 'fabricasoft.admin')
            ->exists();
            
        if ($hasAdminRole) {
            $this->info('✅ El usuario TIENE el rol fabricasoft.admin');
        } else {
            $this->error('❌ El usuario NO TIENE el rol fabricasoft.admin');
        }
        
        // Verificar método hasCustomRole
        if (method_exists($adminUser, 'hasCustomRole')) {
            $hasCustomRole = $adminUser->hasCustomRole('fabricasoft.admin');
            $this->info("hasCustomRole('fabricasoft.admin'): " . ($hasCustomRole ? 'true' : 'false'));
        } else {
            $this->error('❌ Método hasCustomRole no existe en el modelo User');
        }
        
        // Verificar proyecto
        $project = DB::table('fabricasoft_projects')->where('id', 1)->first();
        if ($project) {
            $this->info("✅ Proyecto encontrado: ID {$project->id}, Scrum Master: {$project->scrum_master_id}");
            $this->info("¿Es el usuario el Scrum Master?: " . ($adminUser->id == $project->scrum_master_id ? 'SÍ' : 'NO'));
        } else {
            $this->error('❌ Proyecto con ID 1 no encontrado');
        }
        
        $this->info('=== FIN DEL DEBUG ===');
    }
}

