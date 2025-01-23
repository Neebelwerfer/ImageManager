<?php

namespace App\Models;

use App\Models\Scopes\OwnerOnly;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ScopedBy(OwnerOnly::class)]
class ImageTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'owner_id',
    ];

    public function images() : BelongsToMany
    {
        return $this->belongsToMany(Image::class);
    }
}
