<?php

use App\Http\Controllers\ImageController;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Users;
use App\Livewire\Collection;
use App\Livewire\Collection\Show\Collection as ShowCollection;
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

    Route::get('collection/images/{image}', ImageShow::class)
        ->name('image.show');

    Route::get('collection', Collection::class)
        ->name('collection');

    Route::get('collection/{collection}', Collection::class)
        ->name('collection.show');

    Route::get('collection/{collectionType}/{collectionID?}', ShowCollection::class)
        ->name('collection.type.show');

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

    Route::get('images/{image}', [ImageController::class, 'getImage']);
    Route::get('thumbnail/{thumbnail}', [ImageController::class, 'getThumbnail']);

    Route::get('image/{imageUuid}', ImageShow::class)
        ->name('image.show');


});


Route::middleware(['auth', EnsureUserIsAdmin::class])->group(function () {
    Route::get('admin', Dashboard::class)
        ->name('admin');

    Route::get('admin/users', Users::class)
        ->name('admin.users');
});

require __DIR__.'/auth.php';

Route::fallback(function () {
    return view('404');
});
