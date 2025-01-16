<?php

namespace App\Services;

use App\Models\Image;
use App\Models\ImageCategory;
use App\Models\ImageTag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;
use Intervention\Image\ImageManager;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Str;
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
    public function create(TemporaryUploadedFile $image, array $data)
    {
        $user = Auth::user();

        $imageModel = new Image($data);
        $imageModel->uuid = Str::uuid();
        $imageModel->owner_id = $user->id;

        try {
            $imageInfo = ImageManager::imagick()->read($image);
            $imageScaled = ImageManager::gd()->read($image);


            $uuidSplit = substr($imageModel->uuid, 0, 1).'/'.substr($imageModel->uuid, 1, 1).'/'.substr($imageModel->uuid, 2, 1).'/'.substr($imageModel->uuid, 3, 1);
            $imageModel->width = $imageScaled->width();
            $imageModel->height = $imageScaled->height();
            $imageModel->format = $image->extension();
            $imageInfo->scaleDown(256, 256);

            $thumbnail_path = 'thumbnails/' . $uuidSplit;
            $fileName = $imageModel->uuid . '.webp';
            $full_thumbnail_path = 'thumbnails/' . $uuidSplit . '/' . $fileName;
            if(!Storage::disk('local')->exists($thumbnail_path)) {
                Storage::disk('local')->makeDirectory($thumbnail_path);
            }
            $imageInfo->save(storage_path('app') . '/' . $full_thumbnail_path);


            // Check if image already exists via image hash
            // Currently only compares images with same width and height
            $imageModel->image_hash = $this->createImageHash(storage_path('app') . '/' . $full_thumbnail_path);
            $hits = $this->compareHashes($imageModel->image_hash);

            if (count($hits) > 0) {
                Storage::disk('local')->delete($full_thumbnail_path);
                return redirect()->route('image.upload')->with(['status' => 'Image already exists!', 'uploaded' => $image->serializeForLivewireResponse(), 'error' => true]);
            }

            $imagePath = $uuidSplit . '/' . $imageModel->uuid . '.' . $image->extension();

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
                $tagResponse = ImageTag::where('owner_id', Auth::user()->id)->find($tag);
                if(isset($tagResponse)) {
                    $tags[$tag] = $tagResponse;
                }
            }

            $this->addTags($imageModel, $tags);

        } catch (\Exception $e) {
            if(isset($imageModel)) {
                $imageModel->delete();
                $image->delete();
            }
            else {
                Storage::disk('local')->delete($full_thumbnail_path);
                Storage::disk('local')->delete('images/' . $imagePath);

            }
            return redirect()->route('image.upload')->with(['status' => 'Something went wrong', 'error' => true, 'error_message' => $e->getMessage()]);
        }

        $image->delete();
        return redirect()->route('image.upload')->with('status', 'Image uploaded successfully!');
    }

    private function compareHashes($newHash, $threshold = 95) : array
    {
        $sameSizeImages = $this->lazyIndex();

        $hits = [];

        $counter = 0;
        foreach ($sameSizeImages as $sameSizeImage) {
            if ($this->comparator->compareHashStrings($sameSizeImage->image_hash, $newHash) > $threshold) {
                $hits[$counter] = $sameSizeImage;
                $counter++;
            }
        }
        return $hits;
    }

    private function createImageHash($thumbnail_path) : string
    {
        $hash = $this->comparator->hashImage($thumbnail_path);
        return $this->comparator->convertHashToBinaryString($hash);
    }

}
