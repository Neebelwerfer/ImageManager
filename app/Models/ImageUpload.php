<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Support\Facades\Storage;

class ImageUpload extends Model
{
    use Prunable;

    protected $fillable = [
        'uuid',
        'extension',
        'user_id',
    ];

    public $primaryKey = 'uuid';
    public $incrementing = false;
    public $keyType = 'string';

    public function path() : string
    {
        return 'temp/' . $this->uuid . '.' . $this->extension;
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
        Storage::disk('local')->delete('app/' . $this->path());
    }
}
