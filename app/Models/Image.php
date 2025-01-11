<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Image extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'category_id',
        'rating',
    ];

    public $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    public function category() : BelongsTo
    {
        return $this->belongsTo(ImageCategory::class);
    }

    public function tags() : BelongsToMany
    {
        return $this->belongsToMany(ImageTag::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function thumbnail_path() : string
    {
        $thumbnail_path = substr($this->uuid, 0, 1).'/'.substr($this->uuid, 1, 1).'/'.substr($this->uuid, 2, 1).'/'.substr($this->uuid, 3, 1);
        return 'thumbnails/' . $thumbnail_path . '/' . $this->uuid . '.webp';
    }

    public function albums() : BelongsToMany
    {
        return $this->belongsToMany(Album::class, 'album_images', 'image_uuid', 'album_id');
    }

    protected static function booted(): void
    {
        static::deleting(function (Image $image) {
            Storage::disk('local')->delete($image->path);

            Storage::disk('local')->delete($image->thumbnail_path());
        });
    }

}
