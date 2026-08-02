<?php

use App\Http\Controllers\Drugs\DrugLandingController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => 'localeViewPath',
], function () {

    Route::prefix('drugs')->group(function () {
        Route::get('/', DrugLandingController::class)->name('drugs.home');
    });
});
