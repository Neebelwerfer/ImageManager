<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImageTraits extends Model
{
    protected $fillable = [
        'image_uuid',
        'trait_id',
        'owner_id',
        'value',
        'shared_image'
    ];

    public function image() : BelongsTo
    {
        return $this->belongsTo(Image::class, 'image_uuid');
    }

    public function trait() : BelongsTo
    {
        return $this->belongsTo(Traits::class, 'trait_id');
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function getString()
    {
        $trait = $this->trait;
        if($trait->type === 'boolean') {
            return $trait->name. ': ' . ($this->value === '1' ? 'True' : 'False');
        }

        return $trait->name.': '. $this->value;
    }
}
