<?php

namespace App\Livewire;

use App\Http\Controllers\ImageController;
use App\Models\ImageCategory;
use App\Models\ImageTag;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

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

    #[On('categorySelected')]
    public function categorySelected($category)
    {
        if($category == -1) {
            $this->category = null;
            return;
        }
        $this->category = ImageCategory::find($category);
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

    public function mount(){

        if(session('uploaded') !== null) {
            $this->image = TemporaryUploadedFile::unserializeFromLivewireRequest(session('uploaded'));;
        }
    }

    #[Computed()]
    public function categories()
    {
        return ImageCategory::where('owner_id', Auth::user()->id)->get();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'rating' => $this->rating,
            'category' => $this->category->id ?? null,
            'tags' => array_keys($this->tags),
        ];

        $imageController = app()->make(ImageController::class);

        return $imageController->create($this->image, $data);
    }

    public function render()
    {
        return view('livewire.image-upload');
    }
}
