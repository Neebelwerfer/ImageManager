<?php

namespace App\Livewire\Modal\Image;

use App\Models\Image;
use Illuminate\Support\Facades\Auth;
use LivewireUI\Modal\ModalComponent;

class Details extends ModalComponent
{
    public Image $image;

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
