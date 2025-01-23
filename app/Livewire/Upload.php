<?php

namespace App\Livewire;

use App\Models\ImageCategory;
use App\Models\ImageTag;
use App\Models\ImageUpload;
use App\Models\Traits;
use App\Services\ImageService;
use App\Support\Traits\AddedTrait;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;

#[Layout('layouts.app')]
class Upload extends Component
{
    use WithFileUploads;

    // 100MB Max
    #[Validate('image')]
    public $image;

    #[Url('uuid', except: '')]
    public string $uuid = '';

    public ?ImageUpload $imageUpload;

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

    #[On('traitUpdated')]
    public function traitUpdated($id, $value) {
        $this->traits[$id]->setValue($value);
    }

    #[On('accepted')]
    public function upload(ImageService $imageService) {
        $data = [
            'category' => $this->category->id ?? null,
            'tags' => array_keys($this->tags),
            'hash' => $this->hash
        ];

        return $imageService->create($this->imageUpload, $data, $this->traits);
    }

    public function onUploadFinished() {
        $upload = new ImageUpload(
            [
                'uuid' => str::uuid(),
                'user_id' => Auth::user()->id,
                'extension' => $this->image->extension()
            ]);
        $upload->save();

        $this->image->storeAs('temp/', $upload->uuid . '.' . $this->image->extension());
        $this->image->delete();
        $this->image = null;

        $this->uuid = $upload->uuid;
        $this->imageUpload = $upload;

        $this->setupTraits();
    }

    public function setupTraits() {
        $traits = Traits::personalOrGlobal()->get();
        foreach($traits as $trait) {
            $at = new AddedTrait($trait, $trait->default);
            $this->traits[$trait->id] = $at;
        }
    }

    public function onUploadStarted()
    {
        $this->cancel();
    }

    public function cancel() {
        if(isset($this->imageUpload))
        {
            $this->imageUpload->delete();
            $this->imageUpload = null;
        }
        $this->uuid = '';
        $this->traits = [];
    }

    public function boot()
    {
        if(!empty($this->uuid) && !isset($this->imageUpload))
        {
            $res = ImageUpload::where('user_id', Auth::user()->id)->where('uuid', $this->uuid)->first();
            if(isset($res)) {
                $this->imageUpload = $res;
                if($this->traits == []) {
                    $this->setupTraits();
                }
            }
            else
            {
                $this->cancel();
            }
        }
    }


    #[On('cancelled')]
    public function cancelled() {
        $this->cancel();
    }

    public function render()
    {
        return view('livewire.upload');
    }
}
