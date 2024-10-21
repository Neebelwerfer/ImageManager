<?php

namespace App\Livewire\Collection\Show;

use App\Models\Album;
use App\Models\ImageCategory;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.collection')]
class Collection extends Component
{

    #[Url('g')]
    public $gridView = true;
    #[Url('i')]
    public $count = 0;
    #[Url('r')]
    public $minRating = 0;
    #[Url('page')]
    public $page = 0;

    public $showOptions = false;
    public $collection;

    public $singeleImage;
    public $dirty = false;

    public function setGridView($value)
    {
        $this->gridView = $value;
    }

    public function nextImage()
    {
        $this->count++;
        if($this->count >= count($this->images)) {
            $this->count = count($this->images) - 1;
        }
        $this->dirty = true;
    }

    public function show($count)
    {
        $this->count = $count;
        $this->gridView = false;
        $this->dirty = true;
    }

    public function gotPrevious()
    {
        if($this->count > 0) {
            return true;
        }
        return false;
    }

    public function gotNext()
    {
        if($this->count < count($this->images) - 1) {
            return true;
        }
        return false;
    }

    public function previousImage()
    {
        $this->count--;
        if($this->count < 0) {
            $this->count = 0;
        }
        $this->dirty = true;
    }

    #[On('deleteImage')]
    public function delete()
    {
        $this->singeleImage->delete();
        $this->previousImage();
        $this->updateImages();
    }

    public function filter()
    {
        $this->updateImages();
        $this->count = 0;
        $this->dirty = true;
        $this->dispatch('reloadPage');
    }

    public function updateImages()
    {
        unset($this->images);
    }

    #[Computed()]
    public function chunkedImages()
    {

        return $this->images->chunk(20);
    }

    #[Computed(cache: true)]
    public function images()
    {
        $key = Auth::user()->id . '-' . $this->collection->id;

        return $this->collection->images->where('rating', '>=', $this->minRating)->sortBy('rating', SORT_NUMERIC, true)->values();
    }

    public function mount($collectionType, $collectionID)
    {
        switch($collectionType) {
            case 'categories':
                $this->collection = ImageCategory::where('owner_id', Auth::user()->id)::find($collectionID);
                break;
            case 'albums':
                $this->collection = Album::where('owner_id', Auth::user()->id)::find($collectionID);
                break;
            default:
                abort(404, 'Collection not found');
        }

        if(!isset($this->collection)) {
            abort(404, 'Collection not found');
        }
        $this->updateImages();
    }

    public function render()
    {
        if($this->images->count() > 0) {
            if($this->count > $this->images->count() - 1)
            {
                $this->count = $this->images->count() - 1;
            }

            if($this->singeleImage == null) {
                $this->singeleImage = $this->images[$this->count];
            }

            if($this->dirty) {
                $this->singeleImage = $this->images[$this->count];
                $this->dispatch('imageUpdated', $this->singeleImage->uuid);
                $this->dirty = false;
            }
        }

        return view('livewire.collection.show.grid-and-single');
    }
}
