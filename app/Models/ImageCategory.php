<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class ImageCategory extends Model
{
    use HasFactory;


    public function images() : HasMany
    {
        return $this->hasMany(Image::class, 'category_id', 'id');
    }

    public function tags() : HasManyThrough
    {
        return $this->hasManyThrough(ImageTag::class, Image::class, 'category_id', 'image_id');
    }
}
