<?php

namespace Modules\FABRICASOFT\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class CheckRouteMiddleware extends Command
{
    protected $signature = 'fabricasoft:check-route-middleware {route_name}';
    protected $description = 'Verificar middleware aplicado a una ruta específica';

    public function handle()
    {
        $routeName = $this->argument('route_name');
        
        $this->info("Verificando middleware para la ruta: {$routeName}");
        
        try {
            $route = Route::getRoutes()->getByName($routeName);
            if (!$route) {
                $this->error("Ruta no encontrada: {$routeName}");
                return 1;
            }
            
            $this->line("URI: {$route->uri()}");
            $this->line("Método: " . implode('|', $route->methods()));
            $this->line("Controlador: " . $route->getActionName());
            
            $this->line('');
            $this->info("Middleware aplicado:");
            foreach ($route->middleware() as $middleware) {
                $this->line("  - {$middleware}");
            }
            
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
