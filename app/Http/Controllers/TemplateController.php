<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;
use App\Models\Template;
use Illuminate\Support\Facades\Blade;
use Z3d0X\FilamentFabricator\Models\Page;

class TemplateController extends Controller
{
    public function preview($filamentFabricatorPage = null)
    {
        //dd($filamentFabricatorPage);
         // Handle root (home) page
         if (blank($filamentFabricatorPage)) {
            $pageUrls = FilamentFabricator::getPageUrls();

            $pageId = array_search('/', $pageUrls);

            /** @var Page $filamentFabricatorPage */
            $filamentFabricatorPage = FilamentFabricator::getPageModel()::query()
                ->where('id', $pageId)
                ->firstOrFail();
        }

        $filamentFabricatorPage=Template::find($filamentFabricatorPage);

        /** @var ?class-string<Layout> $layout */
        $layout = FilamentFabricator::getLayoutFromName($filamentFabricatorPage?->layout);

        if (! isset($layout)) {
            throw new \Exception("Filament Fabricator: Layout \"{$filamentFabricatorPage->layout}\" not found");
        }

        /** @var string $component */
        $component = $layout::getComponent();

        return Blade::render(
            <<<'BLADE'
            <x-dynamic-component
                :component="$component"
                :page="$page"
            />
            BLADE,
            ['component' => $component, 'page' => $filamentFabricatorPage]
        );
    }

    public function preview_page(Request $request, $filamentFabricatorPage = null)
    {
        $preview=$request->get('status');
        //dd($filamentFabricatorPage);
         // Handle root (home) page
         if (blank($filamentFabricatorPage)) {
            $pageUrls = FilamentFabricator::getPageUrls();

            $pageId = array_search('/', $pageUrls);

            /** @var Page $filamentFabricatorPage */
            $filamentFabricatorPage = FilamentFabricator::getPageModel()::query()
                ->where('id', $pageId)
                ->firstOrFail();
        }

        $filamentFabricatorPage=Page::find($filamentFabricatorPage);

        /** @var ?class-string<Layout> $layout */
        $layout = FilamentFabricator::getLayoutFromName($filamentFabricatorPage?->layout);
        if (! isset($layout)) {
            throw new \Exception("Filament Fabricator: Layout \"{$filamentFabricatorPage->layout}\" not found");
        }

        /** @var string $component */
        $component = $layout::getComponent();

        return Blade::render(
            <<<'BLADE'
            <x-dynamic-component
                :component="$component"
                :page="$page"
                :preview="$preview"
            />
            BLADE,
            ['component' => $component, 'page' => $filamentFabricatorPage, 'preview'=>$preview]
        );
    }
}
