<?php

namespace App\Jobs\Upload;

use App\Models\ImageUpload;
use App\Models\Upload;
use App\Models\User;
use App\Services\ImageService;
use App\Support\Enums\UploadStates;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Illuminate\Support\Str;


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

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $imageService = app(ImageService::class);

        $imageUploads = [];
        foreach ($this->data as $key => $data)
        {
            $img = ImageManager::gd()->read(file_get_contents($data['path']));

            $upload = new ImageUpload(
                [
                    'uuid' => str::uuid(),
                    'upload_ulid' => $this->upload->ulid,
                    'user_id' => $this->user->id,
                    'extension' => $data['extension'],
                    'hash' => $imageService->createImageHash($img->core()->native())
                ]);
            $upload->save();
            $imageUploads[$key] = $upload;

            $cryptImage = Crypt::encrypt((string) $img->encodeByMediaType(), false);
            Storage::disk('local')->put('temp/'. $upload->uuid, $cryptImage);
            unlink($data['path']);
        }

        $this->upload->setState(UploadStates::Waiting);
    }
}
