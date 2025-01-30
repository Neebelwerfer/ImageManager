<?php

use App\Http\Controllers\ImageController;
use App\Livewire\Collection;
use App\Livewire\Collection\Images;
use App\Livewire\Collection\Show\Collection as ShowCollection;
use App\Livewire\ImageShow;
use App\Livewire\Manage;
use App\Livewire\Manage\Albums;
use App\Livewire\Manage\Categories;
use App\Livewire\Manage\Tags;
use App\Livewire\Manage\Traits;
use App\Livewire\Upload;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('upload', Upload::class)
        ->name('upload');

    Route::get('manage', Manage::class)
        ->name('manage');

    Route::get('manage/images', null)
        ->name('manage.images');

    Route::get('manage/categories', Categories::class)
        ->name('manage.categories');

    Route::get('manage/tags', Tags::class)
        ->name('manage.tags');

    Route::get('manage/albums', Albums::class)
        ->name('manage.albums');

    Route::get('manage/traits', Traits::class)
        ->name('manage.traits');

    Route::get('images/{image}', [ImageController::class, 'getImage']);
    Route::get('thumbnail/{thumbnail}', [ImageController::class, 'getThumbnail']);
    Route::get('temp/{imageUuid}', [ImageController::class, 'getTempImage']);

    Route::get('collection/images/{imageUuid}', ImageShow::class)
        ->name('image.show');

    Route::get('collection/images', Images::class)
        ->name('collection');

    Route::get('collection/{collection}', Collection::class)
        ->name('collection.show');

    Route::get('collection/{collectionType}/{collectionID?}', ShowCollection::class)
        ->name('collection.type.show');
});
