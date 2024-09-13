<?php

use App\Livewire\Collection;
use App\Livewire\ImageShow;
use App\Livewire\ImageUpload;
use App\Livewire\Manage;
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

    Route::get('show/image/{image}', ImageShow::class)
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

});


require __DIR__.'/auth.php';
