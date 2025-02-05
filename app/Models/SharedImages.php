<?php

namespace App\Models;

use App\Support\Shared\AccessLevel;
use App\Support\Shared\Type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SharedImages extends Model
{
    public $table = 'shared_images';

    protected $fillable = [
        'image_uuid',
        'shared_by_user_id',
        'shared_with_user_id',
        'level',
    ];

    public function shared_with() : BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_with_user_id');
    }

    public function shared_by() : BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_by_user_id');
    }

}
