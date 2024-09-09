<?php

namespace App\Livewire\Collection;

use App\Models\Image;
use Livewire\Component;
use Livewire\WithPagination;

class Images extends Component
{
    use WithPagination;

    public $name = '';
    public $rating = -1;

    public function render()
    {
        return view('livewire.collection.images',
            [
                'images' => Image::paginate(20)
            ]);
    }
}
