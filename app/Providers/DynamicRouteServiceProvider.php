<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Livewire\DynamicRouting;
use Illuminate\Support\Facades\Route;

class DynamicRouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $routes = DynamicRouting::getRoutes();
        $middleware = DynamicRouting::$middleware??'web';
        $method = DynamicRouting::$method??'get';
       // dd($routes);

        foreach ($routes as $route=>$action) {
            Route::get($route, $action)
                ->name($route['name'])->middleware($middleware);
        }
    }
}
