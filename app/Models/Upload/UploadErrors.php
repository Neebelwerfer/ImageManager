<?php

namespace App\Models\Upload;

use App\Models\ImageUpload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UploadErrors extends Model
{
    public $timestamps = false;


    protected $fillable = [
        'image_upload_uuid',
        'message'
    ];


    public function imageUpload() : BelongsTo
    {
        return $this->belongsTo(ImageUpload::class, 'image_upload_uuid', 'uuid');
    }
}
