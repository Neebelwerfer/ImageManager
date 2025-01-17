<?php

namespace App\Livewire;

use App\Models\Image;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.app')]
class ImageShow extends Component
{
    public $image;

    public function back()
    {
        return $this->redirect(url()->previous(), true);
    }

    public function mount($imageUuid)
    {
        if(!isset($imageUuid) or empty($imageUuid)) {
            abort(404, 'Image not found');
        }

        $this->image = Image::find($imageUuid);
        if(!isset($this->image)) {
            abort(404, 'Image not found');
        }

        if(Auth::user()->id != $this->image->owner_id) {
            abort(403, 'Forbidden');
        }
    }

    #[On('deleteImage')]
    public function delete()
    {
        $this->image->delete();
        return $this->redirect(route('collection.show', 'images'), true);
    }

    public function render()
    {
        return view('livewire.image-show');
    }
}
