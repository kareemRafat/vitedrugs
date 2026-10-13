<?php

use App\Http\Controllers\Knowledge\SearchController;
use App\Http\Controllers\Poultry\CompareController;
use App\Http\Controllers\Poultry\DiagnosticController;
use App\Http\Controllers\Poultry\DiseaseController;
use App\Http\Controllers\Poultry\FilterController;
use App\Http\Controllers\Poultry\PoultryLandingController;
use App\Http\Controllers\Poultry\ProductController;
use App\Http\Controllers\Poultry\SignController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => 'localeViewPath',
], function () {
    Route::prefix('poultry')->group(function () {
        Route::get('/', PoultryLandingController::class)->name('poultry.home');

        Route::get('diagnosis', [DiagnosticController::class, 'index'])->name('poultry.diagnosis');
        Route::get('diagnosis/suggestions', [DiagnosticController::class, 'suggestions'])->name('poultry.diagnosis.suggestions');
        Route::post('diagnosis', [DiagnosticController::class, 'diagnose'])->name('poultry.diagnosis.run');
        Route::get('diagnosis/results', [DiagnosticController::class, 'showResults'])->name('poultry.diagnosis.results');
        Route::post('diagnosis/results', [DiagnosticController::class, 'refinedResults'])->name('poultry.diagnosis.results.store');

        Route::get('filter', [FilterController::class, 'index'])->name('poultry.filter');
        Route::get('filter/results', [FilterController::class, 'showResults'])->name('poultry.filter.results');
        Route::post('filter/results', [FilterController::class, 'store'])->name('poultry.filter.results.store');
        Route::get('filter/suggestions', [FilterController::class, 'suggestions'])->name('poultry.filter.suggestions');

        Route::get('compare', CompareController::class)->name('poultry.comparison');

        Route::get('products', ProductController::class)->name('poultry.products.index');

        Route::get('diseases/{slug}', [DiseaseController::class, 'show'])->name('poultry.diseases.show');

        Route::get('signs/{sign}', [SignController::class, 'show'])->name('poultry.signs.show');

        Route::get('search', [SearchController::class, 'index'])->name('poultry.search');
    });
});
