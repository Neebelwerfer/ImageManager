<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class LoginActivity extends Model
{
    use Prunable;

    public $table = 'login_activity';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'time',
        'ip',
        'is_successful',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prunable()
    {
        return $this->where('time', '<', now()->subDays(30));
    }
}
