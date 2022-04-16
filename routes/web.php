<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['verify' => true]);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('admin/keyword/list', [App\Http\Controllers\KeywordsController::class, 'list']);
Route::post('admin/import/keyword',  [App\Http\Controllers\KeywordsController::class, 'import']);
Route::get('admin/keyword/{id}/related_keywords', [App\Http\Controllers\KeywordsController::class, 'related_keywords']);