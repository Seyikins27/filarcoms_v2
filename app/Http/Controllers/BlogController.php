<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\Page;
use Illuminate\Support\Facades\Blade;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;
use Z3d0X\FilamentFabricator\Layouts\Layout;
use Z3d0X\FilamentFabricator\Services\PageRoutesService;

class BlogController extends Controller
{
    public function render_blog(Request $request, Blog $blog)
    {
        $page=Page::where('slug','?blog')->first();
        
        if(!$page->published)
        {
            abort(503);
        }
 
        $blog=Blog::where('id',$request->route()->getAction()['blog'])->first();

        $page->seo_tags=$blog->seo_tags;
        $page->meta_description=$blog->meta_description;
        $page->title=$blog->title;
        //dd($page);
        /** @var ?class-string<Layout> $layout */
        $layout = FilamentFabricator::getLayoutFromName($page?->layout);

        if (! isset($layout)) {
            throw new \Exception("Filament Fabricator: Layout \"{$page->layout}\" not found");
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
            ['component' => $component, 'page' => $page]
        );
    }
}
