<?php

namespace App\Livewire\Modal\Image;

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
        if (isset($this->tags[$tag])) {
            return;
        }

        $this->tags[$tag] = ImageTag::find($tag);
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
