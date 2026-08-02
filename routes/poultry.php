<?php

use App\Http\Controllers\Poultry\PoultryLandingController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => 'localeViewPath',
], function () {
    Route::prefix('poultry')->group(function () {
        Route::get('/', PoultryLandingController::class)->name('poultry.home');
    });
});
