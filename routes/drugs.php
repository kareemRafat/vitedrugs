<?php

use App\Http\Controllers\Drugs\DiagnosticController;
use App\Http\Controllers\Drugs\DiseaseArticleController;
use App\Http\Controllers\Drugs\DiseaseComparisonController;
use App\Http\Controllers\Drugs\DiseaseController;
use App\Http\Controllers\Drugs\DrugLandingController;
use App\Http\Controllers\Drugs\FilterController;
use App\Http\Controllers\Drugs\MedicalArticleController;
use App\Http\Controllers\Drugs\MicroorganismController;
use App\Http\Controllers\Drugs\SearchController;
use App\Http\Controllers\Drugs\SpecializationController;
use App\Http\Controllers\Drugs\TestArticleController;
use App\Http\Controllers\Drugs\VeterinaryProjectController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => 'localeViewPath',
], function () {

    Route::prefix('drugs')->group(function () {
        Route::get('/', DrugLandingController::class)->name('drugs.home');

        Route::get('search', [SearchController::class, 'index'])->name('drugs.search');

        Route::get('diagnosis', [DiagnosticController::class, 'index'])->name('drugs.diagnosis');
        Route::post('diagnosis', [DiagnosticController::class, 'diagnose'])->name('drugs.diagnosis.run');
        Route::post('diagnosis/results', [DiagnosticController::class, 'refinedResults'])->name('drugs.diagnosis.results');

        Route::get('filter', [FilterController::class, 'index'])->name('drugs.filter');
        Route::get('filter/suggestions', [FilterController::class, 'suggestions'])->name('drugs.filter.suggestions');

        Route::get('compare', [DiseaseComparisonController::class, 'index'])->name('drugs.comparison');
        Route::match(['get', 'post'], 'compare/results', [DiseaseComparisonController::class, 'compare'])->name('drugs.comparison.results');

        Route::get('diseases/{slug}', [DiseaseController::class, 'show'])->name('drugs.diseases.show');
        Route::get('diseases/{slug}/article', [DiseaseArticleController::class, 'show'])->name('drugs.diseases.article');

        Route::get('articles', [MedicalArticleController::class, 'index'])->name('drugs.medical-articles.index');
        Route::get('articles/{slug}', [MedicalArticleController::class, 'show'])->name('drugs.medical-articles.show');

        Route::get('microorganisms', [MicroorganismController::class, 'index'])->name('drugs.microorganisms.index');
        Route::get('microorganisms/{slug}', [MicroorganismController::class, 'show'])->name('drugs.microorganisms.show');

        Route::get('specializations', [SpecializationController::class, 'index'])->name('drugs.specializations.index');
        Route::get('specializations/{group}', [SpecializationController::class, 'show'])->name('drugs.specializations.show');

        Route::get('projects', [VeterinaryProjectController::class, 'index'])->name('drugs.projects.index');
        Route::get('projects/{slug}', [VeterinaryProjectController::class, 'show'])->name('drugs.projects.show');

        if (app()->environment('local', 'testing')) {
            Route::get('article-test', [TestArticleController::class, 'show'])->name('drugs.article-test');
        }
    });
});
