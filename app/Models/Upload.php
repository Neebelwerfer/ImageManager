<?php

namespace App\Models;

use App\Models\Upload\UploadErrors;
use App\Support\Enums\UploadStates;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Storage;

class Upload extends Model
{
    use Prunable;

    protected $fillable = [
        'ulid',
        'user_id',
        'active_upload_uuid'
    ];

    public $primaryKey = 'ulid';
    public $incrementing = false;
    public $keyType = 'string';


    public function images() : HasMany
    {
        return $this->hasMany(ImageUpload::class, 'upload_ulid', 'ulid');
    }

    public function active_upload() : HasOne
    {
        return $this->hasOne(ImageUpload::class, 'active_upload_uuid', 'uuid');
    }

    public function prunable()
    {
        return $this->where('created_at', '<', now()->subMinutes(30));
    }

    public function setState(UploadStates $state)
    {
        $this->state = $state->value;
        $this->save();
        Broadcast::on('upload.' . $this->user_id)->as('stateUpdated')->with(['ulid' => $this->ulid,'state' => $state])->send();
    }

    protected static function booted(): void
    {
        static::deleting(function (Upload $Upload) {
            Broadcast::on('upload.' . $Upload->user_id)->as('uploadDeleted')->with(['ulid' => $Upload->ulid])->send();
            foreach($Upload->images as $image)
            {
                $image->delete();
            }
        });
    }
}
