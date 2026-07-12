<?php

use App\Http\Controllers\ActiveIngredientController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DiseaseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::get('/sitemap.xml', [SitemapController::class, 'index'])
    ->name('sitemap');

Route::view('/robots.txt', 'robots')
    ->name('robots');

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => 'localeViewPath',
], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'create'])->name('login');
        Route::post('/login', [LoginController::class, 'store']);
        Route::get('/register', [RegisterController::class, 'create'])->name('register');
        Route::post('/register', [RegisterController::class, 'store']);
    });

    Route::post('/logout', [LoginController::class, 'destroy'])
        ->middleware('auth')
        ->name('logout');

    Route::middleware('auth')->group(function () {
        Route::get('/email/verify', [VerificationController::class, 'notice'])
            ->name('verification.notice');
        Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
            ->middleware('signed')
            ->name('verification.verify');
        Route::post('/email/verification-notification', [VerificationController::class, 'resend'])
            ->middleware('throttle:6,1')
            ->name('verification.resend');
    });

    Route::view('/about', 'app.pages.about')->name('about');
    Route::get('/contact', [ContactController::class, 'create'])->name('contact');
    Route::post('/contact', [ContactController::class, 'store']);
    Route::view('/privacy-policy', 'app.pages.privacy-policy')->name('privacy-policy');
    Route::view('/terms-of-service', 'app.pages.terms-of-service')->name('terms-of-service');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

    Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
    Route::get('/companies/{company:slug}', [CompanyController::class, 'show'])->name('companies.show');

    Route::get('/diseases', [DiseaseController::class, 'index'])->name('diseases.index');
    Route::get('/diseases/{disease:slug}', [DiseaseController::class, 'show'])->name('diseases.show');

    Route::get('/active-ingredients', [ActiveIngredientController::class, 'index'])->name('active-ingredients.index');
    Route::get('/active-ingredients/{activeIngredient:slug}', [ActiveIngredientController::class, 'show'])->name('active-ingredients.show');

    Route::get('/search', [SearchController::class, 'index'])->name('search');
});

Route::fallback(function () {
    $locale = LaravelLocalization::getCurrentLocale();
    $path = request()->path();

    if (LaravelLocalization::checkLocaleInSupportedLocales(request()->segment(1))) {
        abort(404);
    }

    return redirect('/'.$locale.'/'.$path);
});
