<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Album extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'owner_id'
    ];

    public function images() : BelongsToMany
    {
        return $this->belongsToMany(Image::class, 'album_images', 'album_id', 'image_uuid');
    }

    public function tags() : HasManyThrough
    {
        return $this->hasManyThrough(ImageTag::class, Image::class, 'category_id', 'image_id');
    }
}
