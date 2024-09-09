<?php

namespace App\Livewire\Collection;

use App\Models\Image;
use Livewire\Component;
use Livewire\WithPagination;

class Images extends Component
{
    use WithPagination;

    public $name = '';
    public $rating = 0;

    public function search()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.collection.images',
            [
                'images' => Image::where('name', 'like', '%' . $this->name . '%')->where('rating', '>=', $this->rating)->paginate(20)
            ]);
    }
}
