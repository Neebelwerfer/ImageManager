<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'is_admin',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function lastLogin()
    {

        $login = $this->loginActivity()->where('is_successful', true)->orderBy('time', 'desc')->first();
        if(isset($login)) {
            return $login->time->diffForHumans() ?? 'n/a';
        }
        return 'n/a';
    }

    public function images() : HasMany
    {
        return $this->hasMany(Image::class, 'owner_id', 'id');
    }

    public function categories() : HasMany
    {
        return $this->hasMany(ImageCategory::class, 'owner_id', 'id');
    }

    public function tags() : HasMany
    {
        return $this->hasMany(ImageTag::class, 'owner_id', 'id');
    }

    public function loginActivity() : HasMany
    {
        return $this->hasMany(LoginActivity::class, 'user_id', 'id');
    }
}
