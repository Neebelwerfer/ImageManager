<?php

namespace App\Livewire\Collection\Show;

use App\Models\Image;
use App\Component\CollectionView;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;

#[Layout('layouts.collection')]
class Collection extends CollectionView
{
    public $minRating = 0;

    public $showOptions = false;

    #[Locked()]
    public $collectionType;
    #[Locked()]
    public $collectionID;

    public function mount($collectionType, $collectionID = null)
    {
        $this->showBackButton = true;

        $this->collectionType = $collectionType;
        $this->collectionID = $collectionID;

        if($collectionType != 'categories' && $collectionType != 'albums'  && $collectionType != 'images') {
            abort(404, 'Collection not found');
        }

        $this->updateImages();
    }

    public function goBack()
    {
        return redirect()->route('collection.show', $this->collectionType);
    }

    #[Computed()]
    public function images()
    {
        if($this->collectionType == 'categories') {
            return Image::owned()->where('category_id', $this->collectionID)->where('rating', '>=', $this->minRating)->orderby('rating', 'desc')->paginate(20);
        }
        else if($this->collectionType == 'albums') {
            return Image::owned()->whereHas('albums', function ($query) {
                $query->where('album_id', $this->collectionID);
            })->where('rating', '>=', $this->minRating)->orderby('rating', 'desc')->paginate(20);
        }
        else if($this->collectionType == 'images') {
            return Image::owned()->where('rating', '>=', $this->minRating)->orderby('rating', 'desc')->paginate(20);
        }
    }

}
