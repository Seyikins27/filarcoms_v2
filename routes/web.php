<?php


use App\Models\Blog;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('preview-template/{template}',[App\Http\Controllers\TemplateController::class,'preview'])->name('preview-template');
Route::get('preview-page/{page}',[App\Http\Controllers\TemplateController::class,'preview_page'])->name('preview-page');

if(Schema::hasTable('blogs') )
{
    foreach(Blog::where('active',1)->get() as $blog) {
        Route::get('blogs'."/".$blog->slug, [
            'uses' => 'App\Http\Controllers\BlogController@render_blog',
            'blog'=>$blog->id
        ])->name('blog-'.$blog->slug);
    }
}
