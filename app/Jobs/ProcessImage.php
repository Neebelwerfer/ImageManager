<?php

namespace App\Jobs;

use App\Models\Image;
use App\Models\ImageTraits;
use App\Services\ImageService;
use Exception;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class ProcessImage implements ShouldQueue, ShouldBeUnique, ShouldBeEncrypted
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly Image $image,
        public readonly string $tempPath,
        public readonly array $data,
        public readonly array $traits
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try
        {
            $imageService = app(ImageService::class);
            $imageInfo = ImageManager::imagick()->read($this->tempPath);
            $imageScaled = ImageManager::gd()->read($this->tempPath);

            $name = $this->image->uuid;
            $uuidSplit = substr($name, 0, 1).'/'.substr($name, 1, 1).'/'.substr($name, 2, 1).'/'.substr($name, 3, 1);
            $path = $this->image->owner_id . '/' . $uuidSplit;


            if(isset($data['category']) && $data['category'] >= 0) {
                $this->image->category_id = $data['category'];
            }

            if(count($this->traits) > 0) {
                foreach ($this->traits as $trait) {
                    $t = new ImageTraits(
                        [
                            'image_uuid' => $this->image->uuid,
                            'trait_id' => $trait->getTrait()->id,
                            'owner_id' => $this->image->owner_id,
                            'value' => $trait->getValue()
                        ]
                    );
                    $t->save();
                }
            }

            foreach($this->data['tags'] as $tagData)
            {
                $imageService->addTag($this->image, $tagData['tag'], $tagData['personal']);
            }

            $imageInfo->scaleDown(256, 256);
            $imageService->storeImageAndThumbnail($imageScaled, $imageInfo, $path, $name);
        }
        catch(Exception $e)
        {
            if(isset($this->image)) {
                $this->image->delete();
            }
            else {
                Storage::disk('local')->delete('thumbnails/' . $path . '/' . $name);
                Storage::disk('local')->delete('images/' . $path . '/' . $name);
            }
            return;
        }
        finally
        {
            File::delete($this->tempPath);
        }

        Broadcast::on('Image.'.$this->image->uuid)->as('imageProcessed')->sendNow();
        Cache::forget('image-hashes.user-' . $this->image->owner_id);
    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return $this->image->uuid;
    }
}
