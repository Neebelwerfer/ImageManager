<?php

namespace App\Livewire;

use App\Models\Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ImageCollection extends Component
{
    use WithPagination;

    public function mount() {}

    public function render()
    {
        return view(
            'livewire.image-collection',
            [
                'images' => Image::where('owner_id', Auth::user()->id)->paginate(20),
            ]
        );
    }
}
