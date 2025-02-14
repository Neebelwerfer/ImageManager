<?php

namespace App\Livewire\Modal\Image;

use App\DTO\ImageTraitDTO;
use App\Events\ImageTagEdited;
use App\Models\Album;
use App\Models\Image;
use App\Models\ImageCategory;
use App\Models\ImageTraits;
use App\Models\Tags;
use App\Models\Traits;
use App\Services\AlbumService;
use App\Services\CategoryService;
use App\Services\ImageService;
use App\Support\Traits\DisplayTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use LivewireUI\Modal\ModalComponent;

class Details extends ModalComponent
{
    public Image $image;

    public bool $owned = true;


    #[On('echo:Image.{image.uuid},.tagEdited')]
    public function tagEdited()
    {
        unset($this->tags);
    }

    #[On('categorySelected')]
    public function categorySelected($category)
    {
        $category = ImageCategory::ownedOrShared(Auth::user()->id)->find($category);

        if(isset($category)) {
            app(CategoryService::class)->addImage(Auth::user(), $this->image, $category);
        }
        else {
            app(CategoryService::class)->removeImage(Auth::user(), $this->image);
        }
    }

    #[On('tagSelected')]
    public function tagSelected($tagData)
    {
        $tag = $tagData['id'];

        $res = Tags::find($tag);

        if(isset($res)) {
            if($this->image->tags()->find($res->id) != null) {
                return;
            }

            app(ImageService::class)->addTag(Auth::user(), $this->image, $res, $tagData['personal']);
        }
    }

    #[On('albumSelected')]
    public function albumSelected($album)
    {
        if(Auth::user()->id != $this->image->owner_id) {
            return;
        }

        $res = Album::ownedOrShared(Auth::user()->id)->find($album);

        if(!isset($res)) return;

        if($this->image->albums()->find($res->id) != null) {
            return;
        }

        app(AlbumService::class)->addImage(Auth::user(), $this->image, $res);
    }

    #[On('traitUpdated')]
    public function traitUpdated()
    {
        unset($this->traits);
    }

    public function removeTag($tagID)
    {
        app(ImageService::class)->removeTag(Auth::user(), $this->image, $tagID);
    }

    public function deleteImage()
    {
        if(Auth::user()->id != $this->image->owner_id) {
            return;
        }
        $this->image->delete();
        $this->closeModal();
        $this->dispatch('deleteImage');
    }

    public function getAlbums()
    {
        return $this->image->albums()->owned(Auth::user()->id)->get();
    }

    public function show()
    {
        $this->redirect(route('image.show', $this->image->uuid));
    }

    #[Computed()]
    public function traits()
    {
        $res = [];
        $traits = Traits::owned(Auth::user()->id)->get();

        foreach($traits as $trait) {
            $imTrait = ImageTraits::where('image_uuid', $this->image->uuid)->where('owner_id', Auth::user()->id)->where('trait_id', $trait->id)->first();
            if($imTrait == null)
            {
                Log::error('Failed to find imageTraits for image', ['image' => $this->image->uuid, 'trait' => $trait->name]);
                break;
            }
            $dT = new ImageTraitDTO($trait, Auth::user()->id, $imTrait->value, $imTrait);
            $res[$trait->id] = $dT;
        }

        return $res;
    }

    #[Computed()]
    public function tags()
    {
        return $this->image->tags;
    }

    public function mount(string $imageUuid)
    {
        $this->image = Image::ownedOrShared(Auth::user()->id)->where('uuid', $imageUuid)->first();

        $this->owned = Auth::user()->id == $this->image->owner_id;

        if(!isset($this->image)) {
            $this->closeModal();
        }
    }

    public function render()
    {
        if($this->owned) {
            return view('livewire.modal.image.details');
        }
        else {
            return view('livewire.modal.image.shared-details');
        }
    }
}
