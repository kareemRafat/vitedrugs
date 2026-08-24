<?php

use App\Http\Controllers\ActiveIngredientController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DiseaseController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductSubmissionController;
use App\Http\Controllers\ProfileController;
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
    Route::get('/', LandingController::class)->name('home');

    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'create'])->name('login');
        Route::post('/login', [LoginController::class, 'store']);
        Route::get('/register', [RegisterController::class, 'create'])->name('register');
        Route::post('/register', [RegisterController::class, 'store']);
    });

    Route::post('/logout', [LoginController::class, 'destroy'])
        ->middleware('auth:web')
        ->name('logout');

    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
        ->middleware('signed')
        ->name('verification.verify');

    Route::middleware('auth:web')->group(function () {
        Route::get('/email/verify', [VerificationController::class, 'notice'])
            ->name('verification.notice');
        Route::post('/email/verification-notification', [VerificationController::class, 'resend'])
            ->middleware('throttle:6,1')
            ->name('verification.resend');

        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::get('/profile/security', [ProfileController::class, 'security'])->name('profile.security');
        Route::put('/profile/security', [ProfileController::class, 'updateSecurity'])->name('profile.security.update');
        Route::get('/profile/submissions', [ProfileController::class, 'submissions'])->name('profile.submissions');
        Route::get('/profile/submissions/{product}', [ProfileController::class, 'showSubmission'])
            ->name('profile.submissions.show');
        Route::get('/profile/submissions/{product}/edit', [ProductSubmissionController::class, 'edit'])
            ->name('profile.submissions.edit');
        Route::put('/profile/submissions/{product}', [ProductSubmissionController::class, 'update'])
            ->name('profile.submissions.update');
    });

    Route::view('/about', 'app.pages.about')->name('about');
    Route::get('/contact', [ContactController::class, 'create'])->name('contact');
    Route::view('/privacy-policy', 'app.pages.privacy-policy')->name('privacy-policy');
    Route::view('/terms-of-service', 'app.pages.terms-of-service')->name('terms-of-service');

    Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/{blog:slug}', [BlogController::class, 'show'])->name('blog.show');

    Route::prefix('drugs')->group(function () {
        Route::get('/', LandingController::class)->name('drugs.home');

        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/create-submission', [ProductSubmissionController::class, 'create'])->name('products.submission.create');
        Route::post('/products/create-submission', [ProductSubmissionController::class, 'store'])
            ->middleware('throttle:3,60')
            ->name('products.submission.store');
        Route::get('/products/compare', function () {
            return view('app.products.compare');
        })->name('products.compare');
        Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

        Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
        Route::get('/companies/{company:slug}', [CompanyController::class, 'show'])->name('companies.show');

        Route::get('/diseases', [DiseaseController::class, 'index'])->name('diseases.index');
        Route::get('/diseases/{disease:slug}', [DiseaseController::class, 'show'])->name('diseases.show');

        Route::get('/active-ingredients', [ActiveIngredientController::class, 'index'])->name('active-ingredients.index');
        Route::get('/active-ingredients/{activeIngredient:slug}', [ActiveIngredientController::class, 'show'])->name('active-ingredients.show');

        Route::get('/search', [SearchController::class, 'index'])->name('search');
    });
});

Route::fallback(function () {
    $segments = request()->segments();
    $defaultLocale = LaravelLocalization::getDefaultLocale();

    $locale = null;
    if ($segments !== [] && LaravelLocalization::checkLocaleInSupportedLocales($segments[0])) {
        $locale = array_shift($segments);
    }

    // Legacy drugs URLs ({locale?}/products, {locale?}/diseases, ...) moved under /drugs.
    $legacyDrugsPrefixes = ['products', 'companies', 'diseases', 'active-ingredients', 'search'];
    if ($segments !== [] && in_array($segments[0], $legacyDrugsPrefixes, true)) {
        if ($locale === null) {
            return redirect('/drugs/'.implode('/', $segments), 301);
        }

        if ($locale === $defaultLocale && config('laravellocalization.hideDefaultLocaleInURL')) {
            return redirect('/drugs/'.implode('/', $segments), 301);
        }

        return redirect('/'.$locale.'/drugs/'.implode('/', $segments), 301);
    }

    if ($locale !== null) {
        abort(404);
    }

    $currentLocale = LaravelLocalization::getCurrentLocale();

    $isHiddenDefault = $currentLocale === $defaultLocale
        && config('laravellocalization.hideDefaultLocaleInURL');

    if ($isHiddenDefault) {
        abort(404);
    }

    return redirect('/'.$currentLocale.'/'.request()->path());
});
