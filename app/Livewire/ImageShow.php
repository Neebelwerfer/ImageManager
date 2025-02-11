<?php

namespace App\Livewire;

use App\Models\Image;
use App\Models\SharedCollections;
use App\Models\SharedImages;
use App\Models\SharedResources;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;

#[Layout('layouts.app')]
class ImageShow extends Component
{
    public Image $image;

    public function back()
    {
        return $this->redirect(route('collection'), true);
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
            if(SharedImages::where('image_uuid', $this->image->uuid)->where('shared_with_user_id', Auth::user()->id)->first() == null) {
                abort(404, 'Image not found');
            }
        }
    }

    #[On('deleteImage')]
    public function delete()
    {
        $this->image->delete();
        return $this->redirect(route('collection'), true);
    }

    public function render()
    {
        return view('livewire.image-show');
    }
}
