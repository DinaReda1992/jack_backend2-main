<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespaceApi = 'App\Http\Controllers\Api';
    protected $namespaceWarehouse = 'App\Http\Controllers\Api\Warehouse';
    protected $namespaceWeb = 'App\Http\Controllers\Website';
    protected $namespacePanel = 'App\Http\Controllers\Panel';
    protected $namespaceProvider = 'App\Http\Controllers\Providers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWarehouseRoutes();

        $this->mapWebRoutes();

        $this->mapPanelRoutes();

        $this->mapProvidersRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespaceWeb)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::middleware('api')
             ->namespace($this->namespaceApi)
             ->group(base_path('routes/api.php'));
    }

    protected function mapWarehouseRoutes()
    {
        Route::middleware('api')
             ->namespace($this->namespaceWarehouse)
             ->group(base_path('routes/warehouse.php'));
    }

    protected function mapPanelRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespacePanel)
            ->group(base_path('routes/panel.php'));
    }

    protected function mapProvidersRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespaceProvider)
            ->group(base_path('routes/provider.php'));

    }
}
