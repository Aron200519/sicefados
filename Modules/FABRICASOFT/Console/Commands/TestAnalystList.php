<?php

namespace Modules\FABRICASOFT\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestAnalystList extends Command
{
    protected $signature = 'fabricasoft:test-analyst-list';
    protected $description = 'Probar la obtención de la lista de analistas';

    public function handle()
    {
        $this->info('Probando obtención de lista de analistas...');
        
        // Método 1: Usando whereRaw (como en el controlador) - Muestra usuarios con múltiples roles
        $analysts1 = \App\Models\User::whereRaw('id IN (
            SELECT user_id FROM role_user 
            WHERE role_id IN (SELECT id FROM roles WHERE slug = "fabricasoft.analista")
        )')->get();
        
        $this->line("Método 1 (whereRaw): {$analysts1->count()} usuarios con rol analista (incluyendo múltiples roles)");
        foreach ($analysts1 as $analyst) {
            $this->line("  - {$analyst->nickname} ({$analyst->email}) - ID: {$analyst->id}");
        }
        
        $this->line('');
        
        // Método 2: Usando DB::table directamente - Muestra usuarios con múltiples roles
        $analysts2 = DB::table('users')
            ->join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('roles.slug', 'fabricasoft.analista')
            ->select('users.id', 'users.nickname', 'users.email')
            ->get();
        
        $this->line("Método 2 (DB::table): {$analysts2->count()} usuarios con rol analista (incluyendo múltiples roles)");
        foreach ($analysts2 as $analyst) {
            $this->line("  - {$analyst->nickname} ({$analyst->email}) - ID: {$analyst->id}");
        }
        
        $this->line('');
        
        // Método 3: Solo usuarios con EXCLUSIVAMENTE el rol de analista
        $exclusiveAnalysts = DB::table('users')
            ->whereRaw('id IN (
                SELECT user_id FROM role_user 
                WHERE role_id IN (SELECT id FROM roles WHERE slug = "fabricasoft.analista")
            )')
            ->whereRaw('id NOT IN (
                SELECT user_id FROM role_user 
                WHERE role_id NOT IN (SELECT id FROM roles WHERE slug = "fabricasoft.analista")
            )')
            ->select('id', 'nickname', 'email')
            ->get();
        
        $this->line("Método 3 (EXCLUSIVAMENTE analista): {$exclusiveAnalysts->count()} usuarios SOLO con rol analista");
        foreach ($exclusiveAnalysts as $analyst) {
            $this->line("  - {$analyst->nickname} ({$analyst->email}) - ID: {$analyst->id}");
        }
        
        $this->line('');
        
        // Método 4: Verificar roles de cada usuario
        $this->info('Verificando roles de cada usuario:');
        foreach ($analysts1 as $user) {
            $roles = DB::table('role_user')
                ->join('roles', 'role_user.role_id', '=', 'roles.id')
                ->where('role_user.user_id', $user->id)
                ->select('roles.name', 'roles.slug')
                ->get();
            
            $roleNames = $roles->pluck('name')->implode(', ');
            $this->line("  - {$user->nickname}: {$roleNames}");
        }
        
        $this->line('');
        
        // Método 5: Roles disponibles
        $this->info('Roles disponibles en la base de datos:');
        $roles = DB::table('roles')->where('slug', 'like', 'fabricasoft%')->get();
        foreach ($roles as $role) {
            $this->line("  - {$role->name} ({$role->slug}) - ID: {$role->id}");
        }
        
        return 0;
    }
}
