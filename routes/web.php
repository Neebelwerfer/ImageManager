<?php

use App\Livewire\Collection;
use App\Livewire\ImageShow;
use App\Livewire\ImageUpload;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth'])->group(function () {
    Route::view('/', 'home')->name('home');

    Route::get('image/upload', ImageUpload::class)
        ->name('image.upload');

    Route::get('collection', Collection::class)
        ->name('collection');

    Route::get('show/image/{image}', ImageShow::class)
        ->name('image.show');

    Route::view('profile', 'profile')
        ->name('profile');

});


require __DIR__.'/auth.php';
