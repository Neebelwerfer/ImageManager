<?php

namespace App\Livewire;

use App\Models\Image;
use App\Models\ImageCategory;
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

    // 1MB Max
    #[Validate('image|max:1024')]
    public $image;

    #[Validate('required|min:3')]
    public $name = '';

    #[Validate('required|min:0|max:10')]
    public $rating = 5;

    public $category;

    public $duplicate;

    public bool $showCategory = false;

    #[On('closeModal')]
    public function closeModal()
    {
        $this->showCategory = false;
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

    #[Computed()]
    public function categories()
    {
        return ImageCategory::where('owner_id', Auth::user()->id)->get();
    }

    private function compareImage($oldImage, $newImage) : bool
    {
        $comparator = new ImageComparator();
        $res = $comparator->compare($oldImage->thumbnail_path, $newImage->thumbnail_path);
        return $res > 90;
    }

    public function save()
    {
        $this->validate();

        $user = Auth::user();

        $imageModel = new Image();
        $imageModel->uuid = Str::uuid();
        $imageModel->name = $this->name;
        $imageModel->rating = $this->rating;
        $imageModel->owner_id = $user->id;

        if (isset($this->category)) {
            $imageModel->category_id = $this->category->id;
        }

        $thumbnail = ImageManager::imagick()->read($this->image);
        $imageModel->width = $thumbnail->width();
        $imageModel->height = $thumbnail->height();
        $thumbnail->toWebp();
        $thumbnail->scaleDown(512, 512);
        $imageModel->thumbnail_path = 'thumbnails/'.$imageModel->uuid.'.webp';
        $thumbnail->save(storage_path('app') . '/' . ($imageModel->thumbnail_path));

        $sameSizeImages = Image::where('owner_id', $user->id)->where('width', $imageModel->width)->where('height', $imageModel->height)->get();
        if(isset($sameSizeImages) && $sameSizeImages->count() > 0)
        {
            foreach ($sameSizeImages as $sameSizeImage)
            {
                if($this->compareImage($sameSizeImage, $imageModel))
                {
                    Storage::disk('local')->delete($imageModel->thumbnail_path);
                    return redirect()->route('image.upload')->with( ['status' => 'Image already exists!', 'duplicate' => $sameSizeImage->path] );
                }
            }
        }

        $imageModel->path = $this->image->storeAs('images', $imageModel->uuid.'.'.$this->image->extension(), 'local');



        $imageModel->save();

        return redirect()->route('image.upload')->with('status', 'Image uploaded successfully!');
    }

    public function render()
    {
        return view('livewire.image-upload');
    }
}
