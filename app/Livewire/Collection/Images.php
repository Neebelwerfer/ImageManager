<?php

namespace App\Livewire\Collection;

use App\Component\CollectionView;
use App\Models\Image;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.collection')]
class Images extends CollectionView
{
    use WithPagination;

    public $minRating = 0;

    public $showOptions = false;
    public $collection;

    public $collectionType = 'images';

    #[Computed()]
    public function images()
    {
        $key = Auth::user()->id.'-images';
        return Image::where('rating', '>=', $this->minRating)->where('owner_id', Auth::user()->id)->orderby('rating', 'desc')->paginate(20);
    }
}
