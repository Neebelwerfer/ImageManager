<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Auth;

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

    public function shared_resources() : HasMany
    {
        return $this->hasMany(SharedResources::class, 'resource_id', 'id')->where('type', 'album');
    }

    public function scopeOwned($query)
    {
        $query->where('owner_id', Auth::user()->id);
    }

    public function scopeShared($query)
    {
        $query->whereHas('shared_resources', function ($query) {
            $query->where('shared_with_user_id', Auth::user()->id)->select('resource_id');
        });
    }

    public function scopeOwnedOrShared($query)
    {
        $query->where('owner_id', Auth::user()->id)->orwhereHas('shared_resources', function ($query) {
            $query->where('shared_with_user_id', Auth::user()->id);
        });
    }
}
