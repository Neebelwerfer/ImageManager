<?php

namespace App\Services;

use App\DTO\ImageUploadDTO;
use App\Events\ImageTagEdited;
use App\Jobs\Upload\ProcessImage;
use App\Models\Image;
use App\Models\ImageCategory;
use App\Models\ImageTraits;
use App\Models\ImageUpload;
use App\Models\SharedImages;
use App\Models\Tags;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use SapientPro\ImageComparator\ImageComparator;

class ImageService
{
    private ImageComparator $comparator;


    public function __construct() {
        $this->comparator = new ImageComparator();
    }

    public function canDeleteImage(User $user, Image $image) : bool
    {
        return $image->owner_id = $user->id;
    }

    public function deleteImage(Image $image)
    {
        $image->delete();
    }

    public function addTag(User $user, Image $image, Tags $tag, bool $personal, SharedImages $sharedImage = null)
    {
        if($sharedImage === null && $image->owner_id != $user->id)
        {
            $sharedImage = SharedImages::where('image_uuid', $image->uuid)->where('shared_with_user_id', $user->id)->first();
            if($sharedImage == null)
            {
                Log::error('Could not find the shared image data entry', ['image' => $image->uuid, 'user' => $user->id]);
                throw new ModelNotFoundException('Could not find the shared image data entry');
            }
        }

        $image->tags()->attach($tag, ['added_by' => $user->id, 'personal' => $personal, 'shared_image' => $sharedImage == null ? null : $sharedImage->id]);
        if(!$personal)
        {
            Broadcast(new ImageTagEdited($user, $image->uuid))->toOthers();
        }
    }

    public function addTrait($uuid, $trait_id, $owner_id, $value, $shared_image_id = null)
    {
        ImageTraits::create(
            [
                'image_uuid' => $uuid,
                'trait_id' => $trait_id,
                'owner_id' => $owner_id,
                'value' => $value,
                'shared_image' => $shared_image_id
            ]
        );
    }

    public function removeCategory(Image $image, ImageCategory $category) {
        $image->categories()->detach($category);
        $image->push();
    }

    public function storeImageAndThumbnail(ImageInterface $originalImage, ImageInterface $image, ImageInterface $thumbnail, string $path, string $name)
    {
        $thumbnail_path = 'thumbnails/' . $path;
        $image_path = 'images/' . $path;
        $originalImage_path = 'originalImages/' . $path;

        if(!Storage::disk('local')->exists($thumbnail_path)) {
            Storage::disk('local')->makeDirectory($thumbnail_path);
        }
        if(!Storage::disk('local')->exists($image_path)) {
            Storage::disk('local')->makeDirectory($image_path);
        }
        if(!Storage::disk('local')->exists($originalImage_path)) {
            Storage::disk('local')->makeDirectory($originalImage_path);
        }

        $cryptThumb = Crypt::encrypt((string) $thumbnail->toWebp(), false);
        $cryptImage = Crypt::encrypt((string) $image->toWebp(), false);
        $cryptOriginalImage = Crypt::encrypt((string) $originalImage->encodeByMediaType(), false);
        $hashedName = hash('sha1', $name);

        Storage::disk('local')->put($thumbnail_path . '/' . $hashedName, $cryptThumb);
        Storage::disk('local')->put($image_path . '/' . $hashedName,$cryptImage);
        Storage::disk('local')->put($originalImage_path . '/' . $hashedName,$cryptOriginalImage);
    }

    public function compareHashes(int $user_id, $newHash, $threshold = 95) : array
    {
        $images = Cache::remember('image-hashes.user-'. $user_id, 3600, function () use ($user_id)
        {
            return Image::where('owner_id', $user_id)->select('image_hash', 'uuid')->get();
        });

        $hits = [];

        $counter = 0;
        foreach ($images as $image) {
            if ($this->comparator->compareHashStrings($image->image_hash, $newHash) > $threshold) {
                $hits[$counter] = $image->uuid;
                $counter++;
            }
        }
        return $hits;
    }

    public function getHashFromUploadedImage(string $path) : string
    {
        $img = ImageManager::gd()->read($path);
        return $this->createImageHash($img->core()->native());
    }

    public function createImageHash($image) : string
    {
        $hash = $this->comparator->hashImage($image, size:16);
        return $this->comparator->convertHashToBinaryString($hash);
    }

    public function isShared(User $sharedTo, $uuid) : bool
    {
        return app(SharedResourceService::class)->isImageShared($sharedTo, $uuid, 'image');
    }


    public function share(User $sharedBy, User $sharedTo, string $image_uuid, $accessLevel) : bool
    {
        if($sharedTo->id == $sharedBy->id) return true;
        $image = Image::owned($sharedBy->id)->find($image_uuid);
        if(isset($image))
        {
            if(isset($sharedTo) && !$this->isShared($sharedTo, $image_uuid)) {
                app(SharedResourceService::class)->ShareImage($sharedBy, $sharedTo, $image_uuid, $accessLevel, 'image');
                return true;
            }
        }
        return false;
    }

    public function stopSharing(User $sharedBy, User $sharedTo, $uuid)
    {
        app(SharedResourceService::class)->StopSharingImage($sharedBy, $sharedTo, $uuid, 'image');
    }

    public function removeTag(User $user, Image $image, $tagID)
    {
        $tag = $image->tags()->find($tagID);

        if($image->owner_id == $user->id || $tag->pivot->added_by === $user->id)
        {
            $image->tags()->detach($tag);
            if(!$tag->pivot->personal)
            {
                Broadcast(new ImageTagEdited($user, $image->uuid))->toOthers();
            }
        }
    }
}
