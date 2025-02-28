<?php

namespace App\Jobs\Upload;

use App\Models\ImageUpload;
use App\Models\Upload;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use SapientPro\ImageComparator\ImageComparator;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;

class ProcessUpload implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly User $user,
        public readonly Upload $upload,
        public readonly array $data
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Broadcast::on('upload.' . $this->user->id)->as('newUpload')->with(['ulid' => $this->upload->ulid])->send();
        foreach($this->upload->images as $image)
        {
            $path = $image->path();
            $thumbnail = ImageManager::imagick()->read(file_get_contents($path));
            $image->data = json_encode([
                'category' => null,
                'tags' => [],
                'traits' => [],
                'albums' => [],
                'dimensions' => ['width' => $thumbnail->width(), 'height' => $thumbnail->height()]
            ]);
            $image->save();

            $thumbnail->scaleDown(256, 256);
            Storage::disk('local')->put('temp/' . $image->uuid . '.thumbnail', Crypt::encrypt((string)$thumbnail->toWebp(), false));
            Storage::disk('local')->put('temp/' . $image->uuid, Crypt::encryptString(file_get_contents($path)));
        }
    }
}
