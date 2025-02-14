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
use Exception;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Cache;
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

        $this->imageUpload->state = "processing";
        $this->imageUpload->save();

        Broadcast::on('upload.' . $this->imageUpload->uuid)->as('begunProcessing')->sendNow();
        $data = json_decode($this->imageUpload->data, true);

        $name = $this->imageUpload->uuid;
        $uuidSplit = substr($name, 0, 1).'/'.substr($name, 1, 1).'/'.substr($name, 2, 1).'/'.substr($name, 3, 1);
        $path = '/' . $uuidSplit;

        $image = new Image();
        try
        {
            $image->uuid = $this->imageUpload->uuid;
            $image->owner_id = $this->user->id;
            $image->width = $data['dimensions']['width'];
            $image->height = $data['dimensions']['height'];
            $image->image_hash = $this->imageUpload->hash;
            $image->format = $this->imageUpload->extension;
            $image->save();

            $traits = Traits::where('owner_id', $this->user->id)->get();

            $imageInfo = ImageManager::imagick()->read($this->imageUpload->fullPath());
            $imageScaled = ImageManager::gd()->read($this->imageUpload->fullPath());

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
            $imageService->storeImageAndThumbnail($imageScaled, $imageInfo, $path, $name);
        }
        catch(Exception $e)
        {
            $image->delete();
            Storage::disk('local')->delete('thumbnails/' . $path . '/' . $name);
            Storage::disk('local')->delete('images/' . $path . '/' . $name);

            $this->imageUpload->state = "error";
            $this->imageUpload->save();
            UploadErrors::create([
                'image_upload_uuid' => $this->imageUpload->uuid,
                'message' => $e
            ]);
            Broadcast::on('upload.' . $this->imageUpload->uuid)->as('processingFailed')->send();
            return;
        }

        $this->imageUpload->state = "done";
        $this->imageUpload->save();
        broadcast(new ImageProcessed($image->uuid));
        Cache::forget('image-hashes.user-' . $image->owner_id);
    }

    /**
     * Handle a job failure.
     */
    public function failed(?Throwable $exception): void
    {
        $this->imageUpload->state = "error";
        $this->imageUpload->save();
        UploadErrors::create([
            'image_upload_uuid' => $this->imageUpload->uuid,
            'message' => $exception
        ]);
        Broadcast::on('upload.' . $this->imageUpload->uuid)->as('processingFailed')->send();
    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return $this->imageUpload->uuid;
    }
}
