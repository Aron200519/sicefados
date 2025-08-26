<?php

namespace Modules\FABRICASOFT\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestAnalysts extends Command
{
    protected $signature = 'fabricasoft:test-analysts';
    protected $description = 'Test if there are analysts available for project creation';

    public function handle()
    {
        $this->info('=== VERIFICANDO ANALISTAS DISPONIBLES ===');
        
        // Verificar roles
        $this->info('1. Verificando roles...');
        $roles = DB::table('roles')->where('slug', 'like', '%analista%')->get();
        foreach ($roles as $role) {
            $this->info("   - ID: {$role->id}, Slug: {$role->slug}, Nombre: {$role->name}");
        }
        
        // Verificar usuarios con rol de analista
        $this->info('2. Verificando usuarios con rol de analista...');
        $analysts = DB::table('users')
            ->join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('roles.slug', 'fabricasoft.analista')
            ->select('users.id', 'users.nickname', 'users.email', 'roles.slug')
            ->get();
            
        if ($analysts->count() > 0) {
            $this->info("   ✅ Encontrados {$analysts->count()} analistas:");
            foreach ($analysts as $analyst) {
                $name = $analyst->nickname ?? 'Sin nombre';
                $this->info("     - ID: {$analyst->id}, Nombre: {$name}, Email: {$analyst->email}");
            }
        } else {
            $this->error("   ❌ No se encontraron analistas");
        }
        
        // Verificar usuarios EXCLUSIVOS (solo con rol de analista)
        $this->info('3. Verificando analistas EXCLUSIVOS...');
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
            
        if ($exclusiveAnalysts->count() > 0) {
            $this->info("   ✅ Encontrados {$exclusiveAnalysts->count()} analistas EXCLUSIVOS:");
            foreach ($exclusiveAnalysts as $analyst) {
                $name = $analyst->nickname ?? 'Sin nombre';
                $this->info("     - ID: {$analyst->id}, Nombre: {$name}, Email: {$analyst->email}");
            }
        } else {
            $this->error("   ❌ No se encontraron analistas EXCLUSIVOS");
        }
        
        $this->info('=== FIN DE LA VERIFICACIÓN ===');
    }
}
