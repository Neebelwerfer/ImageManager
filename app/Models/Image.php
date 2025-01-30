<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
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
        return 'thumbnails/' . $split . '/' . $this->uuid;
    }

    public function getImagePath() : string
    {
        $split = Image::splitUUID($this->uuid);
        return 'images/' . $split . '/' . $this->uuid;
    }

    public function category() : BelongsTo
    {
        return $this->belongsTo(ImageCategory::class);
    }

    public function tags() : BelongsToMany
    {
        return $this->belongsToMany(Tags::class)->withPivot('added_by', 'personal')->ownOrPublic();
    }

    public function traits() : HasMany
    {
        return $this->hasMany(ImageTraits::class)->where('owner_id', Auth::user()->id);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function albums() : BelongsToMany
    {
        return $this->belongsToMany(Album::class, 'album_images', 'image_uuid', 'album_id');
    }

    public function shared_resources() : HasMany
    {
        return $this->hasMany(SharedResources::class, 'resource_uuid', 'uuid')->where('type', 'image');
    }

    public function scopeOwned($query)
    {
        $query->where('owner_id', Auth::user()->id);
    }

    public function scopeShared($query)
    {
        $query->whereHas('shared_resources', function ($query) {
            $query->where('shared_with_user_id', Auth::user()->id)->select('resource_uuid');
        });
    }

    public function scopeOwnedOrShared($query)
    {
        $query->where('owner_id', Auth::user()->id)->orwhereHas('shared_resources', function ($query) {
            $query->where('shared_with_user_id', Auth::user()->id)->select('resource_uuid');
        });
    }

    protected static function booted(): void
    {
        static::deleting(function (Image $image) {
            Storage::disk('local')->delete($image->getImagePath());
            Storage::disk('local')->delete($image->getThumbnailPath());
        });
    }

    public static function splitUUID(string $uuid) : string
    {
        $split = substr($uuid, 0, 1).'/'.substr($uuid, 1, 1).'/'.substr($uuid, 2, 1).'/'.substr($uuid, 3, 1);
        return $split;
    }
}
