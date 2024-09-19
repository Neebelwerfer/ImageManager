<?php

namespace App\Livewire\Collection;

use App\Models\Image;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Images extends Component
{
    use WithPagination;

    public $name = '';

    #[Url('r')]
    public $rating = 0;

    public function search()
    {
        $this->dispatch('reloadPage');
    }

    public function render()
    {
        return view('livewire.collection.images',
            [
                'images' => Image::where('rating', '>=', $this->rating)->where('owner_id', Auth::user()->id)->orderby('rating', 'desc')->paginate(20)
            ]);
    }
}
