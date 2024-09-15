<?php

namespace App\Livewire;

use App\Models\Image;
use App\Models\ImageCategory;
use App\Models\ImageTag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
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

    public function mount($image)
    {
        if(!isset($image) or empty($image)) {
            abort(404);
        }

        $this->image = Image::find($image);
        if(!isset($this->image)) {
            abort(404);
        }

        if(Auth::user()->id != $this->image->owner_id) {
            abort(403);
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
