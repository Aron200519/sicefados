<?php

namespace Modules\FABRICASOFT\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class RecreateFabricasoftRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fabricasoft:recreate-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recrear los roles de FABRICASOFT';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Eliminando roles existentes de FABRICASOFT...');
        
        // Eliminar roles existentes
        $existingRoles = Role::where('slug', 'like', 'fabricasoft.%')->get();
        foreach ($existingRoles as $role) {
            $role->delete();
            $this->line("  - Eliminado: {$role->slug}");
        }
        
        $this->info('Ejecutando seeder para crear nuevos roles...');
        
        // Ejecutar el seeder
        $this->call('module:seed', ['module' => 'FABRICASOFT']);
        
        $this->info('Verificando roles creados...');
        
        // Verificar roles creados
        $newRoles = Role::where('slug', 'like', 'fabricasoft.%')->get();
        if ($newRoles->count() > 0) {
            $this->info('Roles creados exitosamente:');
            foreach ($newRoles as $role) {
                $this->line("  - {$role->slug}: {$role->name}");
            }
        } else {
            $this->error('Error al crear los roles.');
        }

        return 0;
    }
}


