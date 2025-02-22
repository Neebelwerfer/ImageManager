<?php

namespace App\Jobs\Upload;

use App\Events\ImageProcessed;
use App\Models\Image;
use App\Models\ImageCategory;
use App\Models\ImageTraits;
use App\Models\ImageUpload;
use App\Models\Tags;
use App\Models\Traits;
use App\Models\Upload\UploadErrors;
use App\Models\User;
use App\Services\AlbumService;
use App\Services\CategoryService;
use App\Services\ImageService;
use App\Services\TagService;
use App\Support\Enums\UploadStates;
use Exception;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Throwable;

class ProcessImage implements ShouldQueue, ShouldBeUnique, ShouldBeEncrypted
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly User $user,
        public readonly ImageUpload $imageUpload
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $imageService = app(ImageService::class);
        $tagService = app(TagService::class);
        $categoryService = app(CategoryService::class);
        $albumService = app(AlbumService::class);

        $this->imageUpload->setState(UploadStates::Processing);

        $data = json_decode($this->imageUpload->data, true);

        $name = $this->imageUpload->uuid;
        $uuidSplit = substr($name, 0, 1).'/'.substr($name, 1, 1).'/'.substr($name, 2, 1).'/'.substr($name, 3, 1);
        $path = '/' . $uuidSplit;

        $image = new Image();
        try
        {
            DB::beginTransaction();
            $image->uuid = $this->imageUpload->uuid;
            $image->owner_id = $this->user->id;
            $image->width = $data['dimensions']['width'];
            $image->height = $data['dimensions']['height'];
            $image->image_hash = $this->imageUpload->hash;
            $image->format = $this->imageUpload->extension;
            $image->save();

            $decryptedImage = Crypt::decryptString(file_get_contents($this->imageUpload->fullPath()), false);

            $imageInfo = ImageManager::imagick()->read($decryptedImage);
            $imageOriginal = ImageManager::gd()->read($decryptedImage);
            $imageScaled = ImageManager::gd()->read($decryptedImage);

            if($data['category'] !== null) {
                $category = ImageCategory::find($data['category']);
                $categoryService->addImage($this->user, $image, $category);
            }

            if(isset($data['traits']) && count($data['traits']) > 0) {
                foreach ($data['traits'] as $trait_id => $trait_value) {
                    $imageService->addTrait($image->uuid, $trait_id, $image->owner_id, $trait_value);
                }
            }

            foreach($data['tags'] as $tagName => $personal)
            {
                $tag = $tagService->getOrCreate($tagName);
                if(isset($tag))
                    $imageService->addTag($this->user, $image, $tag, $personal);
            }

            foreach($data['albums'] as $albumID)
            {
                $albumService->addImage($this->user, $image, $albumID);
            }

            $imageInfo->scaleDown(256, 256);

            if($imageScaled->height() > $imageScaled->width())
            {
                $imageScaled->scaleDown(1080, 1920);
            }
            else
            {
                $imageScaled->scaleDown(1920, 1080);
            }

            $imageService->storeImageAndThumbnail($imageOriginal, $imageScaled, $imageInfo, $path, $name);
        }
        catch(Exception $e)
        {
            DB::rollBack();
            $hasedName = hash('sha1', $name);
            Storage::disk('local')->delete('thumbnails/' . $path . '/' . $hasedName);
            Storage::disk('local')->delete('originalImage/' . $path . '/' . $hasedName);
            Storage::disk('local')->delete('images/' . $path . '/' . $hasedName);

            $this->imageUpload->setState(UploadStates::Error);
            UploadErrors::create([
                'image_upload_uuid' => $this->imageUpload->uuid,
                'message' => $e
            ]);
            return;
        }

        DB::commit();
        $this->imageUpload->setState(UploadStates::Done);
        Cache::forget('image-hashes.user-' . $image->owner_id);
    }

    /**
     * Handle a job failure.
     */
    public function failed(?Throwable $exception): void
    {
        $this->imageUpload->setState(UploadStates::Error);
        UploadErrors::create([
            'image_upload_uuid' => $this->imageUpload->uuid,
            'message' => $exception
        ]);
    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return $this->imageUpload->uuid;
    }
}
