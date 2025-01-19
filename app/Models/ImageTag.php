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
use Illuminate\Support\Facades\Auth;

#[ScopedBy(OwnerOnly::class)]
class ImageTag extends Model
{
    use HasFactory, Prunable;

    protected $fillable = [
        'name',
    ];

    public function ownership() : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tag_ownership', 'tag_id', 'owner_id');
    }

    public function images() : BelongsToMany
    {
        return $this->belongsToMany(Image::class);
    }

    public function prunable(): Builder
    {
        return static::withoutGlobalScopes()->whereDoesntHave('ownership' , function ($query) {
            $query->where('owner_id', Auth::user()->id);
        });
    }
}
