<?php

namespace App\Livewire\Collection;

use Livewire\Component;
use App\Models\Album;
use Illuminate\Support\Facades\Auth;

class Albums extends Component
{

    public function getImageFromAlbum(Album $album)
    {
        $image = $album->images()->first();
        return $image;
    }

    public function render()
    {
        return view('livewire.collection.albums',
            [
                'albums' => Album::where('owner_id', Auth::user()->id)->paginate(20),
            ]);
    }
}
