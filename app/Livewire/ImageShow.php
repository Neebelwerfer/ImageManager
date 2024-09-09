<?php

namespace App\Livewire;

use App\Jobs\DeleteImages;
use App\Models\Image;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ImageShow extends Component
{

    public $image;

    public function mount($image)
    {
        if(!isset($image) or empty($image)) {
            abort(404);
        }
        $this->image = Image::find($image);
        if(!isset($this->image)) {
            abort(404);
        }
    }

    public function delete()
    {
        $this->image->delete();
        return $this->redirect(route('collection', ['type' => 'images']), true);
    }

    public function render()
    {
        return view('livewire.image-show');
    }
}
