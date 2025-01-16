<?php

use App\Models\Image;
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
