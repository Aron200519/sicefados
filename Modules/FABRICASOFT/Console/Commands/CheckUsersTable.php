<?php

namespace Modules\FABRICASOFT\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckUsersTable extends Command
{
    protected $signature = 'fabricasoft:check-users';
    protected $description = 'Verificar estructura de la tabla users';

    public function handle()
    {
        $this->info('Verificando estructura de la tabla users...');
        
        $columns = DB::select('DESCRIBE users');
        $this->line('Columnas disponibles:');
        foreach ($columns as $column) {
            $this->line("- {$column->Field} ({$column->Type})");
        }
        
        $this->line('');
        $this->info('Verificando tablas de roles...');
        $tables = DB::select('SHOW TABLES LIKE "%role%"');
        foreach ($tables as $table) {
            $tableName = array_values((array) $table)[0];
            $this->line("- Tabla: {$tableName}");
        }
        
        $this->line('');
        $this->info('Verificando tabla people...');
        $peopleColumns = DB::select('DESCRIBE people');
        $this->line('Columnas de people:');
        foreach ($peopleColumns as $column) {
            $this->line("- {$column->Field} ({$column->Type})");
        }
        
        $this->line('');
        $this->info('Primeras 3 personas:');
        $people = DB::table('people')->select('*')->limit(3)->get();
        foreach ($people as $person) {
            $this->line("ID: {$person->id}");
            foreach ($person as $key => $value) {
                if ($key !== 'id') {
                    $this->line("  {$key}: {$value}");
                }
            }
            $this->line('---');
        }
        
        $this->line('');
        $this->info('Primeros 3 usuarios:');
        $users = DB::table('users')->select('*')->limit(3)->get();
        
        foreach ($users as $user) {
            $this->line("ID: {$user->id}");
            foreach ($user as $key => $value) {
                if ($key !== 'id') {
                    $this->line("  {$key}: {$value}");
                }
            }
            $this->line('---');
        }
        
        return 0;
    }
}
