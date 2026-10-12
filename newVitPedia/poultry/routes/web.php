<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\BodyMapController;
use App\Http\Controllers\ClinicalCaseController;
use App\Http\Controllers\ClinicalSearchController;
use App\Http\Controllers\DiagnosticPageController;
use App\Http\Controllers\Api\v1\DiseaseApiController;
use App\Http\Controllers\DiseaseExplorerController;
use App\Http\Controllers\DiseaseComparisonPageController;
use App\Http\Controllers\DiseaseKnowledgeGraphController;
use App\Http\Controllers\FindingExplorerController;
use App\Http\Controllers\AnatomicalNavigatorController;
use App\Http\Controllers\LesionAtlasController;

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

Route::get('/diagnostic', [DiagnosticPageController::class, 'index'])
    ->name('diagnostic.index');

Route::get('/clinical-search', [ClinicalSearchController::class, 'index'])
    ->name('clinical.search');

Route::get('/body-map', [BodyMapController::class, 'index'])
    ->name('body.map');

Route::get('/lesion-atlas', [LesionAtlasController::class, 'index'])
    ->name('lesion.atlas');

Route::get('/disease-comparison', [DiseaseComparisonPageController::class, 'index'])
    ->name('disease.comparison');


/*
|--------------------------------------------------------------------------
| Disease Routes
|--------------------------------------------------------------------------
*/

Route::prefix('diseases')->name('diseases.')->group(function () {

    Route::get('/{slug}', [DiseaseExplorerController::class, 'show'])
        ->name('show');

    Route::get('/{slug}/graph', [DiseaseKnowledgeGraphController::class, 'show'])
        ->name('graph');
});

Route::get(
    '/diseases',
    [DiseaseExplorerController::class, 'index']
)->name('diseases.index');


/*
|--------------------------------------------------------------------------
| Findings Routes
|--------------------------------------------------------------------------
*/

Route::get('/findings/{slug}', [FindingExplorerController::class, 'show'])
    ->name('findings.show');


/*
|--------------------------------------------------------------------------
| Anatomy Routes
|--------------------------------------------------------------------------
*/

Route::prefix('anatomy')->name('anatomy.')->group(function () {

    Route::get('/', [AnatomicalNavigatorController::class, 'index'])
        ->name('index');

    Route::get('/{slug}', [AnatomicalNavigatorController::class, 'show'])
        ->name('show');
});


/*
|--------------------------------------------------------------------------
| Clinical Cases Routes
|--------------------------------------------------------------------------
*/

Route::prefix('cases')->name('cases.')->group(function () {

    Route::get('/', [ClinicalCaseController::class, 'index'])
        ->name('index');

    Route::get('/{slug}', [ClinicalCaseController::class, 'show'])
        ->name('show');
});


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    Route::get('/diseases/{slug}', [DiseaseApiController::class, 'show']);
});

// Route::fallback(fn () => view('errors.404'));