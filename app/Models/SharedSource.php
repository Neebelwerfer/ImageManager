<?php

namespace App\Models;

use App\Support\Shared\AccessLevel;
use App\Support\Shared\Type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SharedSource extends Model
{
    public $timestamps = false;
    public $table = 'shared_source';

    protected $fillable = [
        'shared_image',
        'shared_by_user_id',
        'source'
    ];

    public function shared_by() : BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_by_user_id');
    }

}
