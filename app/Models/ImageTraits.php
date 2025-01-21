<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImageTraits extends Model
{
    protected $fillable = [
        'image_uuid',
        'trait_id',
        'owner_id',
        'value',
    ];

}
