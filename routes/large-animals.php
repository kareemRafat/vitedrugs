<?php

use App\Http\Controllers\Knowledge\SearchController as KnowledgeSearchController;
use App\Http\Controllers\LargeAnimals\CompareController;
use App\Http\Controllers\LargeAnimals\DiagnosticController;
use App\Http\Controllers\LargeAnimals\DiseaseArticleController;
use App\Http\Controllers\LargeAnimals\DiseaseController;
use App\Http\Controllers\LargeAnimals\DrugLandingController;
use App\Http\Controllers\LargeAnimals\FilterController;
use App\Http\Controllers\LargeAnimals\MedicalArticleController;
use App\Http\Controllers\LargeAnimals\MicroorganismController;
use App\Http\Controllers\LargeAnimals\ProductController;
use App\Http\Controllers\LargeAnimals\SignController;
use App\Http\Controllers\LargeAnimals\SpecializationController;
use App\Http\Controllers\LargeAnimals\TestArticleController;
use App\Http\Controllers\LargeAnimals\VeterinaryProjectController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => 'localeViewPath',
], function () {

    Route::prefix('large-animals')->group(function () {
        Route::get('/', DrugLandingController::class)->name('large-animals.home');

        Route::get('search', [KnowledgeSearchController::class, 'index'])->name('large-animals.search');

        Route::get('diagnosis', [DiagnosticController::class, 'index'])->name('large-animals.diagnosis');
        Route::get('diagnosis/suggestions', [DiagnosticController::class, 'suggestions'])->name('large-animals.diagnosis.suggestions');
        Route::post('diagnosis', [DiagnosticController::class, 'diagnose'])->name('large-animals.diagnosis.run');
        Route::get('diagnosis/results', [DiagnosticController::class, 'showResults'])->name('large-animals.diagnosis.results');
        Route::post('diagnosis/results', [DiagnosticController::class, 'refinedResults'])->name('large-animals.diagnosis.results.store');

        Route::get('filter', [FilterController::class, 'index'])->name('large-animals.filter');
        Route::get('filter/results', [FilterController::class, 'showResults'])->name('large-animals.filter.results');
        Route::post('filter/results', [FilterController::class, 'store'])->name('large-animals.filter.results.store');
        Route::get('filter/suggestions', [FilterController::class, 'suggestions'])->name('large-animals.filter.suggestions');

        Route::get('compare', CompareController::class)->name('large-animals.comparison');

        Route::get('products', ProductController::class)->name('large-animals.products.index');

        // Before `{slug}` so the literal path cannot be read as a slug.
        Route::get('diseases', [DiseaseController::class, 'index'])->name('large-animals.diseases.index');
        Route::get('diseases/{slug}', [DiseaseController::class, 'show'])->name('large-animals.diseases.show');
        Route::get('diseases/{slug}/article', [DiseaseArticleController::class, 'show'])->name('large-animals.diseases.article');

        Route::get('signs/{sign}', [SignController::class, 'show'])->name('large-animals.signs.show');

        Route::get('articles', [MedicalArticleController::class, 'index'])->name('large-animals.medical-articles.index');
        Route::get('articles/{slug}', [MedicalArticleController::class, 'show'])->name('large-animals.medical-articles.show');

        Route::get('microorganisms', [MicroorganismController::class, 'index'])->name('large-animals.microorganisms.index');
        Route::get('microorganisms/{slug}', [MicroorganismController::class, 'show'])->name('large-animals.microorganisms.show');

        Route::get('specializations', [SpecializationController::class, 'index'])->name('large-animals.specializations.index');
        Route::get('specializations/{group}', [SpecializationController::class, 'show'])->name('large-animals.specializations.show');

        Route::get('projects', [VeterinaryProjectController::class, 'index'])->name('large-animals.projects.index');
        Route::get('projects/{slug}', [VeterinaryProjectController::class, 'show'])->name('large-animals.projects.show');

        if (app()->environment('local', 'testing')) {
            Route::get('article-test', [TestArticleController::class, 'show'])->name('large-animals.article-test');
        }
    });
});
