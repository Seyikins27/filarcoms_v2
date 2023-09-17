<?php

use Illuminate\Support\Facades\Route;

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
