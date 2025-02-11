<?php

namespace App\Providers;

use App\Models\Page;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Z3d0X\FilamentFabricator\Models\Page as VendorPage;
use Z3d0X\FilamentFabricator\Http\Controllers\PageController as VendorPageController;
use App\Http\Controllers\PageController;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $loader = AliasLoader::getInstance();
        $loader->alias(VendorPage::class, Page::class);
        $loader->alias(VendorPageController::class, PageController::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
