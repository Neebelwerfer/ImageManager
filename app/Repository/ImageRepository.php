<?php

namespace App\Repository;

use App\Models\Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use SapientPro\ImageComparator\ImageComparator;

class ImageRepository
{
    public function find($id)
    {
        $image = Image::find($id);

        if(isset($image)) {
            if(Auth::user()->id != $image->owner_id) {
                abort(403, 'Forbidden');
            }
            return $image;
        }
    }

    public function all()
    {
        return Image::where('owner_id', Auth::user()->id)->get();
    }

    public function delete($id)
    {
        $image = $this->find($id);
        $image->delete();
    }

    // Adds image to database and creates thumbnail
    // Returns image
    public function create($image, $data)
    {
        $user = Auth::user();

        $imageModel = new Image($data);
        $imageModel->uuid = Str::uuid();
        $imageModel->owner_id = $user->id;
        $uuidSplit = substr($imageModel->uuid, 0, 1).'/'.substr($imageModel->uuid, 1, 1).'/'.substr($imageModel->uuid, 2, 1).'/'.substr($imageModel->uuid, 3, 1);

        try {
            $comparator = new ImageComparator();
            $imageInfo = ImageManager::imagick()->read($image);
            $imageScaled = ImageManager::gd()->read($image);


            $imageModel->width = $imageScaled->width();
            $imageModel->height = $imageScaled->height();
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
            $hash = $comparator->hashImage(storage_path('app') . '/' . $full_thumbnail_path);
            $imageModel->image_hash = $comparator->convertHashToBinaryString($hash);
            $sameSizeImages = Image::where('owner_id', $user->id)->where('width', $imageModel->width)->where('height', $imageModel->height)->get();
            if (isset($sameSizeImages) && $sameSizeImages->count() > 0) {
                foreach ($sameSizeImages as $sameSizeImage) {
                    if ($comparator->compareHashStrings($sameSizeImage->image_hash, $imageModel->image_hash) > 95) {
                        Storage::disk('local')->delete($full_thumbnail_path);
                        return redirect()->route('image.upload')->with(['status' => 'Image already exists!', 'duplicate' => $sameSizeImage->path, 'hash' => $imageModel->image_hash, 'error' => true]);
                    }
                }
            }

            $imageModel->path = 'images/' . $uuidSplit . '/' . $imageModel->uuid . '.' . $image->extension();

            if(!Storage::disk('local')->exists('images/' . $uuidSplit)) {
                Storage::disk('local')->makeDirectory('images/' . $uuidSplit);
            }

            $imageScaled->save(storage_path('app') . '/' . $imageModel->path);
            $imageModel->save();

            if(isset($data['tags'])) {
                foreach ($data['tags'] as $tag) {
                    $imageModel->tags()->save($tag);
                }
            }

        } catch (\Exception $e) {
            Storage::disk('local')->delete($full_thumbnail_path);
            return redirect()->route('image.upload')->with(['status' => 'Something went wrong', 'error' => true, 'error_message' => $e->getMessage()]);
        }

        return $imageModel;
    }
}
