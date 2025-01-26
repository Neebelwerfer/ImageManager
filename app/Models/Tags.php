<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tags extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public static function IsNegativeTag($tag)
    {
        return Str::contains($tag, '-');
    }

    public function images() : BelongsToMany
    {
        return $this->belongsToMany(Image::class);
    }
}
