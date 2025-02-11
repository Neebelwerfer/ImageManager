<?php

namespace App\Livewire\Modal\Image;

use App\Events\ImageTagEdited;
use App\Models\Album;
use App\Models\Image;
use App\Models\ImageCategory;
use App\Models\Tags;
use App\Models\Traits;
use App\Services\AlbumService;
use App\Services\CategoryService;
use App\Services\ImageService;
use App\Support\Traits\DisplayTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
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
        // $traits = Traits::personalOrGlobal()->get();
        // $imageTrait = $this->image->traits();

        // foreach($traits as $trait) {
        //     $dT = new DisplayTrait($trait->id, $trait->name, $trait->type, $trait->default);
        //     foreach($imageTrait as $imageTrait) {
        //         if($imageTrait->id == $trait->id) {
        //             $dT->setValue($imageTrait->value);
        //         }
        //     }
        //     $res[$trait->id] = $dT;
        // }

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
