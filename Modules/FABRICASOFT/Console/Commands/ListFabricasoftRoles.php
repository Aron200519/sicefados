<?php

namespace Modules\FABRICASOFT\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class ListFabricasoftRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fabricasoft:list-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listar todos los roles de FABRICASOFT';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Roles de FABRICASOFT disponibles:');
        $this->line('');

        $roles = Role::where('slug', 'like', 'fabricasoft.%')->get();
        
        if ($roles->count() > 0) {
            foreach ($roles as $role) {
                $this->line("  - {$role->slug}: {$role->name}");
            }
        } else {
            $this->warn('No se encontraron roles de FABRICASOFT.');
            $this->info('Ejecutando seeder...');
            
            // Ejecutar el seeder
            $this->call('module:seed', ['module' => 'FABRICASOFT']);
            
            // Verificar nuevamente
            $roles = Role::where('slug', 'like', 'fabricasoft.%')->get();
            if ($roles->count() > 0) {
                $this->info('Roles creados exitosamente:');
                foreach ($roles as $role) {
                    $this->line("  - {$role->slug}: {$role->name}");
                }
            } else {
                $this->error('Error al crear los roles.');
            }
        }

        return 0;
    }
}


