<?php

namespace App\Models;

use App\Models\Upload\UploadErrors;
use App\Support\Enums\UploadStates;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Storage;

class ImageUpload extends Model
{
    use Prunable;

    protected $fillable = [
        'uuid',
        'extension',
        'user_id',
        'hash'
    ];

    public $primaryKey = 'uuid';
    public $incrementing = false;
    public $keyType = 'string';

    public function error() : HasOne
    {
        return $this->hasOne(UploadErrors::class, 'image_upload_uuid', 'uuid');
    }

    public function path() : string
    {
        return 'temp/' . $this->uuid;
    }

    public function fullPath() : string
    {
        return Storage::disk('local')->path($this->path());
    }

    public function prunable()
    {
        return $this->where('created_at', '<', now()->subMinutes(30));
    }

    protected function pruning()
    {
        Storage::disk('local')->delete($this->path());
    }

    public function setState(UploadStates $state)
    {
        $this->state = $state->value;
        $this->save();
        Broadcast::on('upload.' . $this->uuid)->as('stateUpdated')->with(['state' => $state])->send();
    }

    protected static function booted(): void
    {
        static::deleting(function (ImageUpload $imageUpload) {
            Storage::disk('local')->delete($imageUpload->path());
        });
    }}
