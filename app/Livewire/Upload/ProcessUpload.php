<?php

namespace App\Livewire\Upload;

use App\Models\ImageCategory;
use App\Models\ImageUpload;
use App\Models\Tags;
use App\Models\Traits;
use App\Services\ImageService;
use App\Support\Traits\AddedTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ProcessUpload extends Component
{
    public ImageUpload $imageUpload;

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
    public function tagSelected($tagData)
    {
        $id = $tagData['id'];
        $personal = $tagData['personal'];
        if (isset($this->tags[$id])) {
            return;
        }

        $this->tags[$id] = ['tag' => Tags::find($id), 'personal' => $personal];
    }

    public function save(ImageService $imageService)
    {
        $this->hash = $imageService->getHashFromUploadedImage($this->imageUpload);
        $duplicates = $imageService->compareHashes($this->hash);

        if(count($duplicates) > 0) {
            $this->dispatch('openModal', 'modal.upload.duplicate-images', ['duplicates' => $duplicates]);
            return;
        }

        return $this->upload($imageService);
    }

    public function removeTag($tagID)
    {
        unset($this->tags[$tagID]);
    }

    #[On('traitUpdated')]
    public function traitUpdated($id, $value) {
        $this->traits[$id]->setValue($value);
    }

    #[On('accepted')]
    public function upload(ImageService $imageService) {
        $data = [
            'category' => $this->category->id ?? null,
            'tags' => $this->tags,
            'hash' => $this->hash,
            'dimensions' => $this->ImageMetadata['dimensions']
        ];

        if($imageService->create($this->imageUpload, $data, $this->traits))
        {
            $this->cancel();
        }
    }

    public function setupTraits() {
        $traits = Traits::personalOrGlobal()->get();
        foreach($traits as $trait) {
            $at = new AddedTrait($trait, $trait->default);
            $this->traits[$trait->id] = $at;
        }
    }

    #[On('cancelled')]
    public function cancel() {
        $this->imageUpload->delete();
        return $this->redirectRoute('upload', navigate: true);
    }

    #[Computed()]
    public function ImageMetadata()
    {
        if($this->imageUpload == null) return null;
        $cache = Cache::get('image-upload-'.$this->imageUpload->uuid);
        if($cache === null)
        {
            $data = [];
            $img = ImageManager::gd()->read(storage_path('app/') . $this->imageUpload->path());

            $data['dimensions'] = ['height' => $img->size()->height(), 'width' => $img->size()->width()];
            $data['extension'] = Str::upper($this->imageUpload->extension);
            $data['size'] = number_format(Storage::disk('local')->size($this->imageUpload->path()) / 1024 / 1024, 2);
            Cache::set('image-upload-'.$this->imageUpload->uuid, $data, now()->addHour());
            return $data;
        }
        return $cache;
    }

    public function mount($uuid)
    {
        $res = ImageUpload::find($uuid);

        if($res === null || $res->user_id != Auth::user()->id)
        {
            return $this->redirectRoute('upload', navigate: true);
        }

        $this->imageUpload = $res;
        $this->setupTraits();
    }

    public function render()
    {
        return view('livewire.upload.process-upload');
    }
}
