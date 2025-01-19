<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Support\Carbon;

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


    protected function casts(): array
    {
        return [
            'time' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prunable()
    {
        return $this->where('time', '<', now()->subDays(30));
    }
}
