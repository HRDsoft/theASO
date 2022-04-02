<?php

use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

// Route::get('admin/register', [App\Http\Controllers\Admin\Auth\RegisterController::class, 'showRegistrationForm'])->name('backpack.auth.register');
// Route::get('admin/register', 'App\Http\Controllers\Admin\Auth\RegisterController@showRegistrationForm')->name('backpack.auth.register');

Route::group(['middleware' => [config('backpack.base.web_middleware', 'web')]], function () {
    //routes here
    Route::get('admin/register', 'App\Http\Controllers\Admin\Auth\RegisterController@showRegistrationForm')->name('backpack.auth.register');
    Route::post('admin/register', 'App\Http\Controllers\Admin\Auth\RegisterController@register');
});

Route::group(['middleware' => ['verified']], function() {
    Route::group([
            'prefix'     => config('backpack.base.route_prefix', 'admin'),
            'middleware' => array_merge(
                (array) config('backpack.base.web_middleware', 'web'),
                (array) config('backpack.base.middleware_key', 'admin')
            ),
            'namespace'  => 'App\Http\Controllers\Admin',
        ], function () { // custom admin routes

            Route::crud('category', 'CategoryCrudController');
            Route::crud('keyword', 'KeywordCrudController');
            Route::crud('related-keyword', 'RelatedKeywordCrudController');
    }); // this should be the absolute last line of this file
    
});