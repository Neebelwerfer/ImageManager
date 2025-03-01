<?php

use App\Http\Controllers\UploadController;
use App\Livewire\Upload;
use App\Livewire\Upload\ProcessMultiple;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('upload', Upload::class)
        ->name('upload');

    Route::get('upload/{ulid}', ProcessMultiple::class)
        ->name('upload.multiple');

    Route::post('media/upload', [UploadController::class, 'uploadImages'])
        ->name('media.upload');

    Route::post('media/upload/start',  [UploadController::class, 'uploadStart'])
        ->name('media.upload.start');

    Route::post('media/upload/cancel',  [UploadController::class, 'uploadCancel'])
        ->name('media.upload.cancel');

    Route::post('media/upload/complete',  [UploadController::class, 'uploadComplete'])
        ->name('media.upload.complete');
});
