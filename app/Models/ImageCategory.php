<?php

namespace App\Models;

use App\Models\Scopes\OwnerOnly;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Auth;

class ImageCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
    ];

    public function images() : HasMany
    {
        return $this->hasMany(Image::class, 'category_id', 'id');
    }

    public function tags() : HasManyThrough
    {
        return $this->hasManyThrough(ImageTag::class, Image::class, 'category_id', 'image_id');
    }

    public function sharedWith() : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'shared_categories', 'category_id', 'user_id');
    }

    public function scopeOwned($query)
    {
        $query->where('user_id', Auth::user()->id);
    }

    public function scopeShared($query)
    {
        $query->whereHas('sharedWith', function ($query) {
            $query->where('user_id', Auth::user()->id);
        });
    }

    public function scopeOwnedOrShared($query)
    {
        $query->where('user_id', Auth::user()->id)->orwhereHas('sharedWith', function ($query) {
            $query->where('user_id', Auth::user()->id);
        });
    }
}
