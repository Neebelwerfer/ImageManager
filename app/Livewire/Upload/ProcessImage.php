<?php

namespace App\Livewire\Upload;

use App\DTO\ImageTraitDTO;
use App\Models\Album;
use App\Models\ImageCategory;
use App\Models\ImageUpload;
use App\Models\Tags;
use App\Models\Traits;
use App\Services\TagService;
use App\Support\Enums\ImageUploadStates;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Component;

class ProcessImage extends Component
{
    public ImageUpload $imageUpload;

    #[Modelable]
    public $imageData = [];

    public $traits = [];
    public $error = false;
    public $state = "waiting";

    #[On('categorySelected')]
    public function categorySelected($category)
    {
        if($category == -1) {
            $this->imageData['category'] = [];
            $this->imageData['isDirty'] = true;
            return;
        }
        $category = ImageCategory::find($category);
        $this->imageData['category'] = ['name' => $category->name, 'id' => $category->id];
        $this->imageData['isDirty'] = true;
    }


    #[On('albumSelected')]
    public function albumSelected($albumId)
    {
        if(isset($this->imageData['albums'][$albumId])) return;

        $album = Album::ownedOrShared(Auth::user()->id)->find($albumId);
        $this->imageData['albums'][$albumId] = ['name' => $album->name, 'id' => $albumId];
        $this->imageData['isDirty'] = true;
    }

    #[On('tagSelected')]
    public function tagSelected($tagData)
    {
        $id = $tagData['id'];
        $personal = $tagData['personal'];
        if (isset($this->imageData['tags'][$id])) {
            return;
        }

        $tag = Tags::find($id);
        $this->imageData['tags'][$id] = ['name' => $tag->name, 'personal' => $personal, 'id' => $id];
        $this->imageData['isDirty'] = true;
    }

    public function setupTraits() {
        $traits = Traits::owned(Auth::user()->id)->get();
        foreach($traits as $trait) {
            $at = new ImageTraitDTO($trait, Auth::user()->id, $trait->default);
            $this->traits[$trait->id] = $at;
        }
    }

    public function removeImage()
    {
        $this->dispatch('imageDeleted', ['uuid' => $this->imageData['uuid']]);
    }

    public function accept()
    {
        $this->imageUpload->setState(ImageUploadStates::Waiting);
        $this->state = ImageUploadStates::Waiting->value;
        $this->dispatch('imageUploadUpdated', ['uuid' => $this->imageUpload->uuid]);
    }

    public function mount($uuid)
    {
        $res = ImageUpload::find($uuid);

        if($res === null || $res->user_id != Auth::user()->id)
        {
            return $this->error = true;
        }

        $this->imageUpload = $res;
        $this->state = $res->state;
    }

    public function render()
    {
        return view('livewire.upload.process-image');
    }
}
