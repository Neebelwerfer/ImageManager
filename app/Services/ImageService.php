<?php

namespace App\Services;

use App\Models\Image;
use App\Models\ImageCategory;
use App\Models\ImageTag;
use App\Models\ImageTraits;
use App\Models\ImageUpload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;
use Intervention\Image\ImageManager;
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

    public function addTags(Image $image, array $tags)
    {
        $image->tags()->saveMany($tags);
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
            $thumbnail_path = 'thumbnails/' . $uuidSplit;
            $fileName = $imageModel->uuid . '.webp';
            $full_thumbnail_path = 'thumbnails/' . $uuidSplit . '/' . $fileName;
            if(!Storage::disk('local')->exists($thumbnail_path)) {
                Storage::disk('local')->makeDirectory($thumbnail_path);
            }
            $imageInfo->save(storage_path('app') . '/' . $full_thumbnail_path);

            $imagePath = $uuidSplit . '/' . $imageModel->uuid . '.' . $image->extension;

            if(!Storage::disk('local')->exists('images/' . $uuidSplit)) {
                Storage::disk('local')->makeDirectory('images/' . $uuidSplit);
            }

            $imageScaled->save(storage_path('app') . '/' . 'images/' . $imagePath);

            if(isset($data['category']) && $data['category'] >= 0) {
                $imageModel->category_id = $data['category'];
            }

            $imageModel->save();
            $tags = [];
            foreach ($data['tags'] as $tag) {
                $tagResponse = ImageTag::find($tag);
                if(isset($tagResponse)) {
                    $tags[$tag] = $tagResponse;
                }
            }

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

            $this->addTags($imageModel, $tags);

        } catch (\Exception $e) {
            if(isset($imageModel)) {
                $imageModel->delete();
            }
            else {
                Storage::disk('local')->delete($full_thumbnail_path);
                Storage::disk('local')->delete('images/' . $imagePath);
            }
            $image->delete();

            session()->flash('status', 'Something went wrong');
            session()->flash('error', true);
            session()->flash('error_message', $e->getMessage());
            return;
        }

        $image->delete();
        session()->flash('status', 'Image uploaded successfully!');
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

}
