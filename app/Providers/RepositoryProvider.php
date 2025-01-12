<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repository\ImageRepository;
use App\Repository\TagRepository;

class RepositoryProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ImageRepository::class, function ($app) {
            return new ImageRepository();
        });

        $this->app->singleton(TagRepository::class, function ($app) {
            return new TagRepository();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
