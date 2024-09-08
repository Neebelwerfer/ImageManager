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
        $this->image = Image::find($image);
    }

    public function delete()
    {
        $this->image->delete();
        return redirect('/');
    }

    public function render()
    {
        return view('livewire.image-show');
    }
}
