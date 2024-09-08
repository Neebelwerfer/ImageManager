<?php

use App\Livewire\ImageCollection;
use App\Livewire\ImageUpload;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'home')->name('home');

Route::get('image/upload', ImageUpload::class)
    ->middleware(['auth'])
    ->name('image.upload');

Route::get('show/images', ImageCollection::class)
    ->middleware(['auth'])
    ->name('images');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
