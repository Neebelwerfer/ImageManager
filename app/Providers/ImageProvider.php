<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repository\ImageRepository;
use App\Repository\TagRepository;
use App\Services\AlbumService;
use App\Services\CategoryService;
use App\Services\ImageService;
use App\Services\SharedResourceService;
use App\Services\TagService;
use App\Services\TraitService;

class ImageProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(AlbumService::class, function ($app) {
            return new AlbumService();
        });

        $this->app->singleton(CategoryService::class, function ($app) {
            return new CategoryService();
        });

        $this->app->singleton(TagService::class, function ($app) {
            return new TagService();
        });

        $this->app->singleton(ImageService::class, function ($app) {
            return new ImageService();
        });

        $this->app->singleton(TraitService::class, function ($app) {
            return new TraitService();
        });

        $this->app->singleton(SharedResourceService::class, function ($app) {
            return new SharedResourceService();
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
