<?php

use App\Http\Controllers\Knowledge\SearchController;
use App\Http\Controllers\Poultry\DiagnosticController;
use App\Http\Controllers\Poultry\DiseaseController;
use App\Http\Controllers\Poultry\FilterController;
use App\Http\Controllers\Poultry\PoultryLandingController;
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

        Route::get('diseases/{slug}', [DiseaseController::class, 'show'])->name('poultry.diseases.show');

        Route::get('search', [SearchController::class, 'index'])->name('poultry.search');
    });
});
