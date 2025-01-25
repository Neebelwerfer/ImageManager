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
        'owner_id',
    ];

    public function images() : HasMany
    {
        return $this->hasMany(Image::class, 'category_id', 'id');
    }

    public function tags() : HasManyThrough
    {
        return $this->hasManyThrough(Tags::class, Image::class, 'category_id', 'image_id');
    }

    public function shared_resources() : HasMany
    {
        return $this->hasMany(SharedResources::class, 'resource_id', 'id')->where('type', 'category');
    }

    public function scopeOwned($query)
    {
        $query->where('owner_id', Auth::user()->id);
    }

    public function scopeShared($query)
    {
        $query->whereHas('shared_resources', function ($query) {
            $query->where('shared_with_user_id', Auth::user()->id)->where('type', 'category')->select('resource_id');
        });
    }

    public function scopeOwnedOrShared($query)
    {
        $query->where('owner_id', Auth::user()->id)->orwhereHas('shared_resources', function ($query) {
            $query->where('type', 'category')->where('shared_with_user_id', Auth::user()->id);
        });
    }
}
