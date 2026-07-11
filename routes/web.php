<?php

use App\Http\Controllers\ActiveIngredientController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DiseaseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])
    ->name('sitemap');

Route::view('/robots.txt', 'robots')
    ->name('robots');

Route::view('/about', 'pages.about')
    ->name('about');

Route::view('/contact', 'pages.contact')
    ->name('contact');

Route::view('/privacy-policy', 'pages.privacy-policy')
    ->name('privacy-policy');

Route::view('/terms-of-service', 'pages.terms-of-service')
    ->name('terms-of-service');

Route::get('/products', [ProductController::class, 'index'])
    ->name('products.index');

Route::get('/products/{product:slug}', [ProductController::class, 'show'])
    ->name('products.show');

Route::get('/companies', [CompanyController::class, 'index'])
    ->name('companies.index');

Route::get('/companies/{company:slug}', [CompanyController::class, 'show'])
    ->name('companies.show');

Route::get('/diseases', [DiseaseController::class, 'index'])
    ->name('diseases.index');

Route::get('/diseases/{disease:slug}', [DiseaseController::class, 'show'])
    ->name('diseases.show');

Route::get('/active-ingredients', [ActiveIngredientController::class, 'index'])
    ->name('active-ingredients.index');

Route::get(
    '/active-ingredients/{activeIngredient:slug}',
    [ActiveIngredientController::class, 'show']
)->name('active-ingredients.show');

Route::get('/search', [SearchController::class, 'index'])
    ->name('search');

// Route::get('/{page}', 'AdminController@index');
