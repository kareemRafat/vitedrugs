<?php

namespace App\Providers;

use App\Services\Knowledge\LesionAtlasService;
use App\Services\Knowledge\OrganAttachmentService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Both are resolved several times per request by the lesion atlas, and both
        // memoise an expensive computation behind the resolved instance: the atlas
        // walks the whole organ vocabulary in `assignments()`, and
        // `sectionCounts()` is asked for by the section headers and the filter
        // chips alike. `app()` does not cache, so without these bindings each
        // caller got a fresh object and the caches were empty every time — which
        // turned a 15-query page into 56.
        $this->app->singleton(OrganAttachmentService::class);
        $this->app->singleton(LesionAtlasService::class);
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
