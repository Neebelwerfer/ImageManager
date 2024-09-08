<?php

namespace App\Livewire;

use App\Models\Image;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Intervention\Image\ImageManager;
use Illuminate\Support\Str;

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

    public function save()
    {
        $this->validate();

        $imageModel = new Image();

        $imageModel->uuid = Str::uuid();
        $imageModel->name = $this->name;
        $imageModel->rating = $this->rating;
        $imageModel->path = $this->image->storeAs('images', $imageModel->uuid.'.'.$this->image->extension(), 'public');

        $thumbnail = ImageManager::imagick()->read(storage_path('app/public') . '/' . $imageModel->path);
        $thumbnail->toWebp();
        $thumbnail->scaleDown(512, 512);
        $imageModel->thumbnail = 'thumbnails/'.$imageModel->uuid.'.webp';
        $thumbnail->save(storage_path('app/public') . '/' .($imageModel->thumbnail));
    }

    public function render()
    {
        return view('livewire.image-upload');
    }
}
