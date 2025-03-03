<?php

namespace App\Jobs\Upload;

use App\Models\Image;
use App\Models\ImageCategory;
use App\Models\Upload;
use App\Models\Upload\UploadErrors;
use App\Models\User;
use App\Services\AlbumService;
use App\Services\CategoryService;
use App\Services\ImageService;
use App\Services\TagService;
use App\Support\Enums\ImageUploadStates;
use App\Support\Enums\UploadStates;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Throwable;

class ProcessMultipleImages implements ShouldQueue, ShouldBeUnique, ShouldBeEncrypted
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly User $user,
        public readonly Upload $upload
    ) {
    }

    public $timeout = 600;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->upload->setState(UploadStates::Processing);
        $imageService = app(ImageService::class);
        $tagService = app(TagService::class);
        $categoryService = app(CategoryService::class);
        $albumService = app(AlbumService::class);


        $imageUploads = $this->upload->images;
        $error = false;

        foreach ($imageUploads as $imageUpload)
        {
            $data = json_decode($imageUpload->data, true);

            $name = $imageUpload->uuid;
            $uuidSplit = substr($name, 0, 1).'/'.substr($name, 1, 1).'/'.substr($name, 2, 1).'/'.substr($name, 3, 1);
            $path = '/' . $uuidSplit;

            $image = new Image();
            try
            {
                DB::beginTransaction();
                $image->uuid = $imageUpload->uuid;
                $image->owner_id = $this->user->id;
                $image->width = $data['dimensions']['width'];
                $image->height = $data['dimensions']['height'];
                $image->image_hash = $imageUpload->hash;
                $image->format = $imageUpload->extension;
                $image->save();

                $decryptedImage = Crypt::decryptString(file_get_contents($imageUpload->fullPath()), false);

                $imageScaled = ImageManager::gd()->read($decryptedImage);

                if($data['category'] !== []) {
                    $category = ImageCategory::find($data['category']['id']);
                    $categoryService->addImage($this->user, $image, $category);
                }

                if(isset($data['traits']) && count($data['traits']) > 0) {
                    foreach ($data['traits'] as $trait_id => $trait_value) {
                        $imageService->addTrait($image->uuid, $trait_id, $image->owner_id, $trait_value);
                    }
                }

                foreach($data['tags'] as $id => $data)
                {
                    $tagName = $data['name'];
                    $personal = $data['personal'];
                    $tag = $tagService->getOrCreate($tagName);
                    if(isset($tag))
                        $imageService->addTag($this->user, $image, $tag, $personal);
                }

                if(isset($data['albums']))
                {
                    foreach($data['albums'] as $albumID => $data)
                    {
                        $albumService->addImage($this->user, $image, $albumID);
                    }
                }

                if($imageScaled->height() > $imageScaled->width())
                {
                    $imageScaled->scaleDown(1080, 1920);
                }
                else
                {
                    $imageScaled->scaleDown(1920, 1080);
                }

                $imageService->storeImageAndThumbnail($imageScaled, $path, $name);

                DB::commit();
            }
            catch(Throwable $e)
            {
                DB::rollBack();
                Log::error($e->getMessage());
                $error = true;
                $hasedName = hash('sha1', $name);
                Storage::disk('local')->delete('images/' . $path . '/' . $hasedName);
                Storage::disk('local')->delete('originalImages/' . $path . '/' . $hasedName);
                Storage::disk('local')->delete('images/' . $path . '/' . $hasedName . '.thumbnail');

                UploadErrors::create([
                    'image_upload_uuid' => $imageUpload->uuid,
                    'message' => $e
                ]);
                continue;
            }
        }

        if($error)
            $this->upload->setState(UploadStates::Waiting);
        else
            $this->upload->setState(UploadStates::Done);

        Cache::forget('image-hashes.user-' . $image->owner_id);
    }

    /**
     * Handle a job failure.
     */
    public function failed(?Throwable $exception): void
    {
        $this->upload->setState(UploadStates::Waiting);
    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return $this->upload->ulid;
    }
}
