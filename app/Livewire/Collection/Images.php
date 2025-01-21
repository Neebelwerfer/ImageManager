<?php

namespace App\Livewire\Collection;

use App\Component\CollectionView;
use App\Models\Image;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.collection')]
class Images extends Component
{
    use WithPagination;

    public $minRating = 0;

    public $showOptions = false;
    public $collection;

    public function render()
    {
        return view('livewire.collection.images',
            [
                'images' => Image::owned()->orderby('rating', 'desc')->paginate(20)
            ]);
    }
}
