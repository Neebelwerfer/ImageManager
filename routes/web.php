<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Users;

use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::middleware(['auth'])->group(function () {

    Route::view('profile', 'profile')
        ->name('profile');

});


Route::middleware(['auth', EnsureUserIsAdmin::class])->group(function () {
    Route::get('admin', Dashboard::class)
        ->name('admin');

    Route::get('admin/users', Users::class)
        ->name('admin.users');
});

require __DIR__.'/images.php';
require __DIR__.'/auth.php';
