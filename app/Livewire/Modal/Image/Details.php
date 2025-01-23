<?php

namespace App\Livewire\Modal\Image;

use App\Models\Album;
use App\Models\Image;
use App\Models\ImageCategory;
use App\Models\ImageTag;
use App\Models\Traits;
use App\Support\Traits\DisplayTrait;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use LivewireUI\Modal\ModalComponent;

class Details extends ModalComponent
{
    public Image $image;

    #[On('categorySelected')]
    public function categorySelected($category)
    {
        $category = ImageCategory::ownedOrShared()->find($category);

        if(isset($category)) {
            $this->image->update(['category_id' => $category->id]);
        }
        else {
            $this->image->update(['category_id' => null]);
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

    #[Computed()]
    public function traits()
    {
        $res = [];
        $traits = Traits::personalOrGlobal()->get();
        $imageTrait = $this->image->traits();

        foreach($traits as $trait) {
            $dT = new DisplayTrait($trait->id, $trait->name, $trait->type, $trait->default);
            foreach($imageTrait as $imageTrait) {
                if($imageTrait->id == $trait->id) {
                    $dT->setValue($imageTrait->value);
                }
            }
            $res[$trait->id] = $dT;
        }

        return $res;
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
