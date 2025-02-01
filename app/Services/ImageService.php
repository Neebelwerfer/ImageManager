<?php

namespace App\Services;

use App\DTO\ImageUploadDTO;
use App\Events\ImageTagEdited;
use App\Jobs\ProcessImage;
use App\Models\Image;
use App\Models\ImageCategory;
use App\Models\ImageTraits;
use App\Models\ImageUpload;
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

    public function addTag(Image $image, Tags $tag, bool $personal)
    {
        $image->tags()->attach($tag, ['added_by' => Auth::user()->id, 'personal' => $personal]);
        if(!$personal)
        {
            Broadcast(new ImageTagEdited(Auth::user(), $image->uuid))->toOthers();
        }
    }

    public function removeTags(Image $image, array $tags)
    {
        $image->tags()->detach($tags);
        $image->push();
    }

    public function removeCategory(Image $image, ImageCategory $category) {
        $image->categories()->detach($category);
        $image->push();
    }

    /**
     * Adds image to database and creates thumbnail
     *
     * @param TemporaryUploadedFile $image
     * @param array $data
     * @return void
     */
    public function create(ImageUpload $image, array $data, array $traits) : bool
    {
        $user = Auth::user();

        $imageModel = new Image($data);
        try {

            $imageModel->uuid = $image->uuid;
            $imageModel->owner_id = $user->id;
            $imageModel->width = $data['dimensions']['width'];
            $imageModel->height = $data['dimensions']['height'];
            $imageModel->image_hash = $data['hash'];
            $imageModel->format = $image->extension;
            $imageModel->save();

            $imageModel->processing = true;
            ProcessImage::dispatch($imageModel, $image->fullPath(), $data, $traits);

        } catch (\Exception $e) {
            $imageModel->delete();

            session()->flash('status', 'Something went wrong');
            session()->flash('error', true);
            session()->flash('error_message', $e->getMessage());
            return false;
        }

        $image->delete();
        session()->flash('status', 'Image uploading!');
        session()->put('uuid', $imageModel->uuid);
        return true;
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

    public function compareHashes($newHash, $threshold = 95) : array
    {
        $images = Cache::remember('image-hashes.user-'. Auth::user()->id, 3600, function ()
        {
            return Image::owned()->select('image_hash', 'uuid')->get();
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

    public function getHashFromUploadedImage(ImageUpload $image) : string
    {
        $img = ImageManager::gd()->read($image->fullPath());
        return $this->createImageHash($img->core()->native());
    }

    private function createImageHash($image) : string
    {
        $hash = $this->comparator->hashImage($image);
        return $this->comparator->convertHashToBinaryString($hash);
    }

    public function isShared($sharedTo, $id) : bool
    {
        return app(SharedResourceService::class)->isShared($sharedTo, 'image', $id);
    }


    public function share($id, User $sharedTo, $accessLevel) : bool
    {
        $image = Image::owned()->find($id);
        if(isset($image)) {
            if(isset($sharedTo) && $sharedTo->id != Auth::user()->id && !$this->isShared($sharedTo, $id)) {
                app(SharedResourceService::class)->Share($sharedTo, 'image', $id, $accessLevel);
                $image->is_shared = true;
                return true;
            }
        }
        return false;
    }

    public function stopSharing($sharedTo, $id)
    {

    }
}
