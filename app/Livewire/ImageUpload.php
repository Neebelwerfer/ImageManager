<?php

namespace App\Livewire;

use App\Models\Image;
use App\Models\ImageCategory;
use App\Models\ImageTag;
use GdImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Intervention\Image\ImageManager;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use SapientPro\ImageComparator\ImageComparator;

#[Layout('layouts.app')]
class ImageUpload extends Component
{
    use WithFileUploads;

    // 100MB Max
    #[Validate('image')]
    public $image;

    #[Validate('required|min:0|max:10')]
    public $rating = 5;

    public $category;

    public $tags = [];

    public $duplicate;

    public bool $showCategory = false;
    public bool $showTags = false;

    #[On('closeModal')]
    public function closeModal()
    {
        $this->showCategory = false;
        $this->showTags = false;
    }

    #[On('categorySelected')]
    public function categorySelected($category)
    {
        $this->category = ImageCategory::find($category);
    }

    public function toggleCategoryModal()
    {
        $this->showCategory = !$this->showCategory;
    }

    #[On('tagSelected')]
    public function tagSelected($tag)
    {
        if (isset($this->tags[$tag])) {
            return;
        }

        $this->tags[$tag] = ImageTag::find($tag);
    }

    public function removeTag($tagID)
    {
        unset($this->tags[$tagID]);
    }

    public function toggleTagsModal()
    {
        $this->showTags = !$this->showTags;
    }

    #[Computed()]
    public function categories()
    {
        return ImageCategory::where('owner_id', Auth::user()->id)->get();
    }

    public function save()
    {
        $this->validate();

        $user = Auth::user();

        $imageModel = new Image();
        $imageModel->uuid = Str::uuid();
        $imageModel->rating = $this->rating;
        $imageModel->owner_id = $user->id;

        if (isset($this->category)) {
            $imageModel->category_id = $this->category->id;
        }

        $comparator = new ImageComparator();

        // Try/catch block to ensure image is deleted if it already exists even if exception is thrown
        try {

            $imageInfo = ImageManager::imagick()->read($this->image);
            $imageScaled = ImageManager::imagick()->read($imageInfo);
            $imageModel->width = $imageInfo->width();
            $imageModel->height = $imageInfo->height();
            $thumbnail_path = 'thumbnails/' . $imageModel->uuid . '.webp';

            $imageScaled = $imageScaled->scaleDown(2560, 1440);

            $imageInfo->scaleDown(512, 512);
            $imageInfo->save(storage_path('app') . '/' . ($thumbnail_path));


            // Check if image already exists via image hash
            // Currently only compares images with same width and height
            $hash = $comparator->hashImage($thumbnail_path);
            $imageModel->image_hash = $comparator->convertHashToBinaryString($hash);
            $sameSizeImages = Image::where('owner_id', $user->id)->where('width', $imageModel->width)->where('height', $imageModel->height)->get();
            if (isset($sameSizeImages) && $sameSizeImages->count() > 0) {
                foreach ($sameSizeImages as $sameSizeImage) {
                    if ($comparator->compareHashStrings($sameSizeImage->image_hash, $imageModel->image_hash) > 95) {
                        Storage::disk('local')->delete($thumbnail_path);
                        return redirect()->route('image.upload')->with(['status' => 'Image already exists!', 'duplicate' => $sameSizeImage->path, 'hash' => $imageModel->image_hash, 'error' => true]);
                    }
                }
            }

            $imageModel->path = 'images/' . $imageModel->uuid . '.' . $this->image->extension();
            $imageScaled->save(storage_path('app') . '/' . $imageModel->path);
            $imageModel->save();

            foreach ($this->tags as $tag) {
                $imageModel->tags()->save($tag);
            }

        } catch (\Exception $e) {
            Storage::disk('local')->delete($thumbnail_path);
            return redirect()->route('image.upload')->with(['status' => 'Something went wrong', 'error' => true, 'error_message' => $e->getMessage()]);
        }


        return redirect()->route('image.upload')->with('status', 'Image uploaded successfully!');
    }

    public function render()
    {
        return view('livewire.image-upload');
    }
}
