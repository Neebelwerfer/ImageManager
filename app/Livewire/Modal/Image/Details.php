<?php

namespace App\Livewire\Modal\Image;

use App\Models\Album;
use App\Models\Image;
use App\Models\ImageCategory;
use App\Models\ImageTag;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use LivewireUI\Modal\ModalComponent;

class Details extends ModalComponent
{
    public Image $image;

    #[On('categorySelected')]
    public function categorySelected($category)
    {
        $category = ImageCategory::find($category);

        if(isset($category) && Auth::user()->id == $category->owner_id) {
            $this->image->update(['category_id' => $category->id]);
        }
    }

    #[On('tagSelected')]
    public function tagSelected($tag)
    {
        if(Auth::user()->id != $this->image->owner_id) {
            return;
        }

        $res = ImageTag::find($tag);

        if(isset($res)) {
            if($this->image->tags()->find($res->id) != null) {
                return;
            }
            $this->image->tags()->save($res);
        }
    }

    #[On('albumSelected')]
    public function albumSelected($album)
    {
        if(Auth::user()->id != $this->image->owner_id) {
            return;
        }
        $res = Album::where('owner_id', Auth::user()->id)->find($album);

        if(!isset($res)) return;

        if($this->image->albums()->find($res->id) != null) {
            return;
        }

        $this->image->albums()->attach($res->id);
    }

    public function removeTag($tagID)
    {
        if(Auth::user()->id != $this->image->owner_id) {
            return;
        }
        $tag = ImageTag::find($tagID);
        $this->image->tags()->detach($tag);
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
        return $this->image->albums()->where('owner_id', Auth::user()->id)->get();
    }

    public function show()
    {
        $this->redirect(route('image.show', $this->image->uuid));
    }

    public function mount(string $imageUuid)
    {
        $this->image = Image::where('owner_id', Auth::user()->id)->where('uuid', $imageUuid)->first();

        if(!isset($this->image)) {
            $this->closeModal();
        }
    }

    public function render()
    {
        return view('livewire.modal.image.details');
    }
}
