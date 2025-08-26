<?php

namespace Modules\FABRICASOFT\Providers;

use Illuminate\Support\ServiceProvider;

class FABRICASOFTServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'FABRICASOFT';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'fabricasoft';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        // $this->registerFactories(); // Comentado temporalmente
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
        
        // Registrar comandos del módulo
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Modules\FABRICASOFT\Console\Commands\CreateFabricasoftRoles::class,
                \Modules\FABRICASOFT\Console\Commands\AssignFabricasoftRole::class,
                \Modules\FABRICASOFT\Console\Commands\ListFabricasoftRoles::class,
                \Modules\FABRICASOFT\Console\Commands\CreateAnalystUser::class,
                \Modules\FABRICASOFT\Console\Commands\CheckUsersTable::class,
                \Modules\FABRICASOFT\Console\Commands\CheckPreregistrations::class,
                \Modules\FABRICASOFT\Console\Commands\AssignRequestToAnalyst::class,
                \Modules\FABRICASOFT\Console\Commands\TestCustomRoleMiddleware::class,
                \Modules\FABRICASOFT\Console\Commands\CheckRouteMiddleware::class,
                \Modules\FABRICASOFT\Console\Commands\TestAnalystList::class,
                \Modules\FABRICASOFT\Console\Commands\TestModalFunctionality::class,
                \Modules\FABRICASOFT\Console\Commands\TestControllerAnalysts::class,
                \Modules\FABRICASOFT\Console\Commands\CheckAssignedRequests::class,
                \Modules\FABRICASOFT\Console\Commands\TestAnalystDashboard::class,
                            \Modules\FABRICASOFT\Console\Commands\CleanupTestRequests::class,
            \Modules\FABRICASOFT\Console\Commands\ListRemainingRequests::class,
            \Modules\FABRICASOFT\Console\Commands\TestProjectCreation::class,
            \Modules\FABRICASOFT\Console\Commands\TestAnalysts::class,
            \Modules\FABRICASOFT\Console\Commands\CreateTestProject::class,
            \Modules\FABRICASOFT\Console\Commands\DebugAuth::class,
            \Modules\FABRICASOFT\Console\Commands\UpdateProjectPhases::class,
            \Modules\FABRICASOFT\Console\Commands\TestPhaseSave::class,
            \Modules\FABRICASOFT\Console\Commands\TestPhaseView::class,
            \Modules\FABRICASOFT\Console\Commands\TestPhaseDates::class,
            \Modules\FABRICASOFT\Console\Commands\TestPhaseSaveDates::class,
                                        \Modules\FABRICASOFT\Console\Commands\AssignInternalClientRole::class,
                            \Modules\FABRICASOFT\Console\Commands\AssignClientRoleToAll::class,
                            \Modules\FABRICASOFT\Console\Commands\AssignClientRoleSelective::class,
                \Modules\FABRICASOFT\Console\Commands\TestDownloadSRS::class,
            ]);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
        }
    }

    /**
     * Register an additional directory for factories.
     *
     * @return void
     */
    /*
    public function registerFactories()
    {
        if (! app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(module_path($this->moduleName, 'Database/factories'));
        }
    }
    */

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }
}
