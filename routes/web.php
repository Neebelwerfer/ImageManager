<?php

use App\Livewire\Collection;
use App\Livewire\Collection\Show\Album;
use App\Livewire\Collection\Show\Category;
use App\Livewire\ImageShow;
use App\Livewire\ImageUpload;
use App\Livewire\Manage;
use App\Livewire\Manage\Albums;
use App\Livewire\Manage\Categories;
use App\Livewire\Manage\Tags;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::middleware(['auth'])->group(function () {

    Route::get('image/upload', ImageUpload::class)
        ->name('image.upload');

    Route::get('collection', Collection::class)
        ->name('collection');

    Route::get('collection/{collection}', Collection::class)
        ->name('collection.show');

    Route::get('collection/albums/{collectionID}', Album::class)
        ->name('collection.album');

    Route::get('collection/categories/{collectionID}', Category::class)
        ->name('collection.category');

    Route::get('collection/images/{image}', ImageShow::class)
        ->name('image.show');

    Route::view('profile', 'profile')
        ->name('profile');

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

});


require __DIR__.'/auth.php';
