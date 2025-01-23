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
        'global',
        'owner_id',
        'default'
    ];

    public $timestamps = false;

    public function scopePersonal($query)
    {
        return $query->where('global', false)->where('owner_id', Auth::user()->id);
    }

    public function scopeGlobal($query)
    {
        return $query->where('global', true);
    }

    public function scopePersonalOrGlobal($query)
    {
        return $query->where('global', true)->orWhere('owner_id', Auth::user()->id);
    }
}
