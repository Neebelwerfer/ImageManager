<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ImageTag extends Model
{
    use HasFactory;

    public function images() : BelongsToMany
    {
        return $this->belongsToMany(Image::class);
    }
}
