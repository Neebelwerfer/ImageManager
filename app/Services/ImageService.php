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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
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

    public function index()
    {
        return Image::where('owner_id', Auth::user()->id)->all();
    }

    public function pagedIndex($pageSize = 20)
    {
        return $this->index()->paginate($pageSize);
    }

    public function lazyIndex() : LazyCollection
    {
        return Image::where('owner_id', Auth::user()->id)->lazy();
    }

    public function deleteImage(Image $image)
    {
        $image->delete();
    }

    public function addTag(User $user, Image $image, Tags $tag, bool $personal)
    {
        $image->tags()->attach($tag, ['added_by' => $user->id, 'personal' => $personal]);
        if(!$personal)
        {
            Broadcast(new ImageTagEdited($user, $image->uuid))->toOthers();
        }
    }

    public function removeCategory(Image $image, ImageCategory $category) {
        $image->categories()->detach($category);
        $image->push();
    }

    public function storeImageAndThumbnail(ImageInterface $image, ImageInterface $thumbnail, string $path, string $name)
    {
        $thumbnail_path = 'thumbnails/' . $path;
        $image_path = 'images/' . $path;
        if(!Storage::disk('local')->exists($thumbnail_path)) {
            Storage::disk('local')->makeDirectory($thumbnail_path);
        }
        if(!Storage::disk('local')->exists($image_path)) {
            Storage::disk('local')->makeDirectory($image_path);
        }

        $cryptThumb = Crypt::encrypt((string) $thumbnail->toWebp(), false);
        $cryptImage = Crypt::encrypt((string) $image->encodeByMediaType(), false);

        Storage::disk('local')->put($thumbnail_path . '/' . $name, $cryptThumb);
        Storage::disk('local')->put($image_path . '/' . $name,$cryptImage);
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
        $image = Image::owned(Auth::user()->id)->find($image_uuid);
        if(isset($image))
        {
            if(isset($sharedTo) && !$this->isShared($sharedTo, $image_uuid)) {
                app(SharedResourceService::class)->ShareImage($sharedBy, $sharedTo, $image_uuid, $accessLevel, 'image');
                $image->is_shared = true;
                return true;
            }
        }
        return false;
    }

    public function stopSharing($sharedTo, $id)
    {

    }

    public function removeTags(Image $image, array $tags)
    {
        $image->tags()->detach($tags);
        $image->push();
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
