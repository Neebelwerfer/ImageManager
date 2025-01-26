<?php

namespace App\Livewire\Collection;

use App\Component\CollectionView;
use App\Models\Image;
use App\Models\Tags;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.collection')]
class Images extends Component
{
    use WithPagination;

    #[Url('tags', except:'')]
    public $tags = '';
    public $minRating = 0;

    public $showOptions = false;
    public $collection;

    public function filter()
    {

    }

    public function render()
    {
        return view('livewire.collection.images',
            [
                'images' => Tags::sortTags(Image::ownedOrShared(), $this->tags)->paginate(20)
            ]);
    }
}
