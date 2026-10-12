<?php

use App\Http\Controllers\AnatomicalNavigatorController;
use App\Http\Controllers\Api\V1\DiagnosticController;
use App\Http\Controllers\Api\V1\DiagnosticSessionController;
use App\Http\Controllers\Api\V1\DiseaseComparisonController;
use App\Http\Controllers\Api\V1\DiseaseController;
use App\Http\Controllers\Api\V1\FindingSuggestionController;
use App\Http\Controllers\Api\V1\OutbreakController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('v1')->group(function () {

    Route::post(
        '/diagnose',
        [DiagnosticController::class, 'diagnose']
    );

    Route::get(
        '/diseases/{slug}',
        [DiseaseController::class, 'show']
    );
});





Route::get(
    '/v1/findings/suggestions',
    [FindingSuggestionController::class, 'index']
);


Route::prefix('v1/session')
    ->group(function () {

        Route::post(
            '/start',
            [DiagnosticSessionController::class, 'start']
        );

        Route::post(
            '/answer',
            [DiagnosticSessionController::class, 'answer']
        );

        Route::get(
            '/{session}',
            [DiagnosticSessionController::class, 'show']
        );
    });

Route::post(
    '/v1/outbreak/analyze',
    [OutbreakController::class, 'analyze']
);

Route::post(
    '/v1/diseases/compare',
    [DiseaseComparisonController::class, 'compare']
);

Route::get('/test-route', function () {
    return response()->json([
        'message' => 'API Works'
    ]);
});

Route::get(

    '/v1/anatomy/{structure}/diseases',

    [AnatomicalNavigatorController::class, 'diseases']

);