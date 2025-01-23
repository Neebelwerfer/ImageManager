<?php

namespace App\Models;

use App\Support\Shared\AccessLevel;
use App\Support\Shared\Type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SharedResources extends Model
{
    public $table = 'shared_resources';

    protected $fillable = [
        'resource_id',
        'resource_uuid',
        'shared_by_user_id',
        'shared_with_user_id',
        'level',
        'type',
    ];

    public function casts()
    {
        return [
            'level' => AccessLevel::class,
            'type' => Type::class,
        ];
    }

    public function shared_with() : BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_with_user_id');
    }

    public function shared_by() : BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_by_user_id');
    }

}
