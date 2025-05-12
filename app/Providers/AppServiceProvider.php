<?php

namespace App\Providers;

use App\Models\Page;
//use App\Models\Menu;
use App\Livewire\MenuBuilder;
use App\Models\MenuItem;
use App\Filament\Resources\MenuResource;
use App\Filament\Resources\MenuItemResource;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Z3d0X\FilamentFabricator\Models\Page as VendorPage;
use Z3d0X\FilamentFabricator\Http\Controllers\PageController as VendorPageController;
use App\Http\Controllers\PageController;
use Biostate\FilamentMenuBuilder\Models\Menu as VendorMenu;
use Biostate\FilamentMenuBuilder\Models\MenuItem as VendorMenuItem;
use Biostate\FilamentMenuBuilder\Filament\Resources\MenuResource as VendorMenuResource;
use Biostate\FilamentMenuBuilder\Filament\Resources\MenuItemResource as VendorMenuItemResource;
use Biostate\FilamentMenuBuilder\Http\Livewire\MenuBuilder as VendorMenuBuilder;
use SolutionForest\FilamentAccessManagement\Resources\UserResource as VendorUserResource;
use App\Filament\Resources\UserResource;

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
       // $loader->alias(VendorMenu::class, Menu::class);
        $loader->alias(VendorMenuItem::class, MenuItem::class);
        $loader->alias(VendorMenuItemResource::class, MenuItemResource::class);
        $loader->alias(VendorMenuResource::class, MenuResource::class);
        $loader->alias(VendorMenuBuilder::class, MenuBuilder::class);
        $loader->alias(VendorUserResource::class, UserResource::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
