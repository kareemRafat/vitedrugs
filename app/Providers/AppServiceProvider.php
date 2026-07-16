<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $path = resource_path('views/app/components');

        app('livewire.finder')->addLocation(viewPath: $path);
        app('blade.compiler')->anonymousComponentPath($path);
        app('view')->addLocation($path);
    }
}
