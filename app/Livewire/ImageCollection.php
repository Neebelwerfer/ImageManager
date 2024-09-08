<?php

namespace App\Livewire;

use App\Models\Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;

#[Layout('layouts.app')]
class ImageCollection extends Component
{

    #[Computed]
    public function images()
    {
        $user = Auth::user();
        return Image::where('owner_id', $user->id)->get();
    }

    public function mount()
    {

    }

    public function render()
    {
        return view('livewire.image-collection');
    }
}
