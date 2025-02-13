<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Traits extends Model
{
    protected $fillable = [
        'name',
        'type',
        'min',
        'max',
        'owner_id',
        'default'
    ];

    public $timestamps = false;

    public function scopeOwned($query, $owner_id)
    {
        return $query->where('owner_id', $owner_id);
    }
}
