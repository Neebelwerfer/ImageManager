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
        return $this->hasManyThrough(Tags::class, Image::class, 'category_id', 'image_id');
    }

    public function sharedCollections() : HasMany
    {
        return $this->hasMany(SharedCollections::class, 'resource_id', 'id')->where('type', '=', 'album');
    }

    public function scopeOwned($query, $user_id)
    {
        $query->where('owner_id', $user_id);
    }

    public function scopeShared($query, $user_id)
    {
        $query->whereHas('sharedCollections', function ($query) use($user_id) {
            $query->where('shared_with_user_id', $user_id)->select('resource_id');
        });
    }

    public function scopeOwnedOrShared($query, $user_id)
    {
        $query->where('owner_id', Auth::user()->id)->orwhereHas('sharedCollections', function ($query) use($user_id) {
            $query->where('shared_with_user_id', $user_id);
        });
    }
}
