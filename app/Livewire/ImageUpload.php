<?php

namespace App\Livewire;

use App\Models\Image;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Intervention\Image\ImageManager;

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

        $imageModel->name = $this->name;
        $imageModel->rating = $this->rating;
        $imageModel->path = $this->image->store('images/'.$imageModel->uuid.'.'.$this->image->extension(), 'public');

        $thumbnail = ImageManager::imagick()->read($imageModel->path);
        $thumbnail->resize(100, 100);
        $imageModel->thumbnail = 'thumbnails/'.$imageModel->uuid.'.jpg';
        $thumbnail->save(public_path($imageModel->thumbnail));

        $imageModel->save();

        $this->emit('saved');

    }

    public function render()
    {
        return view('livewire.image-upload');
    }
}
