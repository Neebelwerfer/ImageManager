<?php

use App\Models\Image;
use App\Models\ImageUpload;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::call(function () {
    $softDeletedImages = Image::onlyTrashed()->get();
    foreach ($softDeletedImages as $image) {
        $image->forceDelete();
    }
})->everyTenMinutes();

Schedule::command('model:prune')->daily();

Schedule::command('model:prune', [
    '--model' => ImageUpload::class
])->hourly();
