<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\SetPartTheme;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__.'/../routes/web.php',
            __DIR__.'/../routes/large-animals.php',
            __DIR__.'/../routes/poultry.php',
        ],
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'localize' => LaravelLocalizationRoutes::class,
            'localizationRedirect' => LaravelLocalizationRedirectFilter::class,
            'localeSessionRedirect' => LocaleSessionRedirect::class,
            'localeViewPath' => LaravelLocalizationViewPath::class,
            'set.part.theme' => SetPartTheme::class,
            'ensure.user.is.admin' => EnsureUserIsAdmin::class,
        ]);

        $middleware->appendToGroup('web', [
            LocaleSessionRedirect::class,
            LaravelLocalizationRedirectFilter::class,
            SetPartTheme::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
