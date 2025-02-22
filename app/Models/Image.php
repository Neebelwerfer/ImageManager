<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'category_id',
    ];

    public $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    public function getThumbnailPath() : string
    {
        $split = Image::splitUUID($this->uuid);
        return 'thumbnails/' . $split . '/' . hash('sha1', $this->uuid);
    }

    public function getImagePath() : string
    {
        $split = Image::splitUUID($this->uuid);
        return 'images/' . $split . '/' . hash('sha1', $this->uuid);
    }

    public function category() : BelongsTo
    {
        return $this->belongsTo(ImageCategory::class);
    }

    public function tags() : BelongsToMany
    {
        return $this->belongsToMany(Tags::class)->withPivot('added_by', 'personal', 'shared_image');
    }

    public function traits() : HasMany
    {
        return $this->hasMany(ImageTraits::class)->withPivot('added_by', 'shared_image');
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function albums() : BelongsToMany
    {
        return $this->belongsToMany(Album::class, 'album_images', 'image_uuid', 'album_id')->withPivot('added_by', 'shared_image');
    }

    public function sharedImages() : HasMany
    {
        return $this->hasMany(SharedImages::class, 'image_uuid', 'uuid');
    }

    public function scopeOwned($query, $user_id)
    {
        $query->where('owner_id', $user_id);
    }

    public function scopeShared($query, $user_id)
    {
        $query->whereHas('sharedImages', function ($query) use ($user_id) {
            $query->where('shared_with_user_id', $user_id);
        });
    }

    public function scopeOwnedOrShared($query, $user_id)
    {
        $query->where('owner_id', $user_id)->orwhereHas('sharedImages', function ($query) use($user_id) {
            $query->where('shared_with_user_id', $user_id)->select('image_uuid');
        });
    }

    protected static function booted(): void
    {
        static::deleting(function (Image $image) {
            Storage::disk('local')->delete($image->getImagePath());
            Storage::disk('local')->delete($image->getThumbnailPath());
            Cache::forget('image-hashes.user-'. $image->owner_id);
        });
    }

    public static function splitUUID(string $uuid) : string
    {
        $split = substr($uuid, 0, 1).'/'.substr($uuid, 1, 1).'/'.substr($uuid, 2, 1).'/'.substr($uuid, 3, 1);
        return $split;
    }
}
