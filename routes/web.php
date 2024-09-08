<?php

use App\Livewire\ImageCollection;
use App\Livewire\ImageShow;
use App\Livewire\ImageUpload;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth'])->group(function () {
    Route::view('/', 'home')->name('home');

    Route::get('image/upload', ImageUpload::class)
        ->name('image.upload');

    Route::get('show/images', ImageCollection::class)
        ->name('images');

    Route::get('show/image/{image}', ImageShow::class)
        ->name('image.show');

    Route::view('profile', 'profile')
        ->name('profile');

});


require __DIR__.'/auth.php';
