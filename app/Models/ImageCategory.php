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

#[ScopedBy(OwnerOnly::class)]
class ImageCategory extends Model
{
    use HasFactory, Prunable;

    protected $fillable = [
        'name',
    ];

    public function images() : HasMany
    {
        return $this->hasMany(Image::class, 'category_id', 'id');
    }

    public function ownership() : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'category_ownership', 'category_id', 'owner_id');
    }

    public function tags() : HasManyThrough
    {
        return $this->hasManyThrough(ImageTag::class, Image::class, 'category_id', 'image_id');
    }

    public function prunable(): Builder
    {
        return static::withoutGlobalScopes()->whereDoesntHave('ownership' , function ($query) {
            $query->where('owner_id', Auth::user()->id);
        });
    }
}
