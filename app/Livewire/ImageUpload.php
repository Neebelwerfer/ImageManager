<?php

namespace App\Livewire;

use App\Models\ImageCategory;
use App\Models\ImageTag;
use App\Services\ImageService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
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

    public $category;

    public $tags = [];
    public $traits = [];

    public $hash;

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

    public function save(ImageService $imageService)
    {
        $this->validate();

        $this->hash = $imageService->getHashFromUploadedImage($this->image);
        $duplicates = $imageService->compareHashes($this->hash);

        if(count($duplicates) > 0) {
            $this->dispatch('openModal', 'modal.upload.duplicate-images', ['duplicates' => $duplicates]);
            return;
        }

        return $this->upload($imageService);
    }

    #[On('accepted')]
    public function upload(ImageService $imageService) {
        $data = [
            'category' => $this->category->id ?? null,
            'tags' => array_keys($this->tags),
            'hash' => $this->hash
        ];

        return $imageService->create($this->image, $data, $this->traits);
    }

    #[On('cancelled')]
    public function cancelled() {
        $this->image->delete();
        return redirect()->route('image.upload');
    }

    public function render()
    {
        return view('livewire.image-upload');
    }
}
