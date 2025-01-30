<?php

namespace App\Services;

use App\Events\ImageTagEdited;
use App\Models\Image;
use App\Models\ImageCategory;
use App\Models\ImageTraits;
use App\Models\ImageUpload;
use App\Models\Tags;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
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
    public function create(ImageUpload $image, array $data, array $traits)
    {
        $user = Auth::user();

        $imageModel = new Image($data);
        try {

            $imageInfo = ImageManager::imagick()->read($image->fullPath());
            $imageScaled = ImageManager::gd()->read($image->fullPath());

            $imageModel->uuid = $image->uuid;
            $imageModel->owner_id = $user->id;
            $imageModel->width = $imageScaled->width();
            $imageModel->image_hash = $data['hash'];
            $imageModel->height = $imageScaled->height();
            $imageModel->format = $image->extension;
            $uuidSplit = substr($imageModel->uuid, 0, 1).'/'.substr($imageModel->uuid, 1, 1).'/'.substr($imageModel->uuid, 2, 1).'/'.substr($imageModel->uuid, 3, 1);

            $imageInfo->scaleDown(256, 256);

            $path = $uuidSplit;
            $name = $imageModel->uuid;
            $this->storeImageAndThumbnail($imageScaled, $imageInfo, $path, $name);

            if(isset($data['category']) && $data['category'] >= 0) {
                $imageModel->category_id = $data['category'];
            }

            $imageModel->save();

            if(count($traits) > 0) {
                foreach ($traits as $trait) {
                    $t = new ImageTraits(
                        [
                            'image_uuid' => $imageModel->uuid,
                            'trait_id' => $trait->getTrait()->id,
                            'owner_id' => $user->id,
                            'value' => $trait->getValue()
                        ]
                    );
                    $t->save();
                }
            }


            foreach($data['tags'] as $tagData)
            {
                $this->addTag($imageModel, $tagData['tag'], $tagData['personal']);
            }

        } catch (\Exception $e) {
            if(isset($imageModel)) {
                $imageModel->delete();
            }
            else {
                Storage::disk('local')->delete('thumbnails/' . $path . '/' . $name);
                Storage::disk('local')->delete('images/' . $path . '/' . $name);
            }

            session()->flash('status', 'Something went wrong');
            session()->flash('error', true);
            session()->flash('error_message', $e->getMessage());
            return;
        }

        $image->delete();
        session()->flash('status', 'Image uploaded successfully!');
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
        $sameSizeImages = $this->lazyIndex();

        $hits = [];

        $counter = 0;
        foreach ($sameSizeImages as $sameSizeImage) {
            if ($this->comparator->compareHashStrings($sameSizeImage->image_hash, $newHash) > $threshold) {
                $hits[$counter] = $sameSizeImage->uuid;
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
