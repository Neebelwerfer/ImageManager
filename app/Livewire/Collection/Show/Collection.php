<?php

namespace App\Livewire\Collection\Show;

use App\Models\Album;
use App\Models\Image;
use App\Component\CollectionView;
use App\Models\ImageCategory;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;

#[Layout('layouts.collection')]
class Collection extends CollectionView
{
    public $minRating = 0;

    public $showOptions = false;
    public $collection;

    public $collectionType;

    public function mount($collectionType, $collectionID = null)
    {
        $this->showBackButton = true;

        $this->collectionType = $collectionType;
        switch($collectionType) {
            case 'categories':
                $this->collection = ImageCategory::find($collectionID);
                break;
            case 'albums':
                $this->collection = Album::find($collectionID);
                break;
            default:
                abort(404, 'Collection not found');
        }

        if(!isset($this->collection)) {
            abort(404, 'Collection not found');
        }

        if(Auth::user()->id != $this->collection->owner_id) {
            abort(403, 'Forbidden');
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
        return $this->collection->images->where('rating', '>=', $this->minRating)->sortBy('rating', SORT_NUMERIC, true)->values()->paginate(20);
    }

}
