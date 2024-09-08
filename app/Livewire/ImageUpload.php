<?php

namespace App\Livewire;

use App\Models\Image;
use App\Models\ImageCategory;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Intervention\Image\ImageManager;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

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

        $imageModel->path = $this->image->storeAs('images', $imageModel->uuid.'.'.$this->image->extension(), 'local');

        $thumbnail = ImageManager::imagick()->read(storage_path('app') . '/' . $imageModel->path);
        $imageModel->width = $thumbnail->width();
        $imageModel->height = $thumbnail->height();

        $thumbnail->toWebp();
        $thumbnail->scaleDown(512, 512);
        $imageModel->thumbnail_path = 'thumbnails/'.$imageModel->uuid.'.webp';
        $thumbnail->save(storage_path('app') . '/' . ($imageModel->thumbnail_path));

        $imageModel->save();

        return redirect()->route('image.upload')->with('status', 'Image uploaded successfully!');
    }

    public function render()
    {
        return view('livewire.image-upload');
    }
}
