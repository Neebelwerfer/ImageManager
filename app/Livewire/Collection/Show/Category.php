<?php

namespace App\Livewire\Collection\Show;

use App\Models\Album;
use App\Models\ImageCategory;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

//TODO: Make this generic for all types of collections
#[Layout('layouts.collection')]
class Category extends Component
{

    #[Url('g')]
    public $gridView = true;
    #[Url('i')]
    public $count = 0;
    #[Url('r')]
    public $minRating = 0;

    public $showOptions = false;
    public $collection;
    public $image;
    public $type;
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
        $this->image->delete();
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

    #[Computed(cache: true)]
    public function images()
    {
        return $this->collection->images->where('rating', '>=', $this->minRating)->sortBy('rating', SORT_NUMERIC, true)->values();
    }

    public function mount($collectionID)
    {
        // switch($this->type) {
        //     case 'category':
        //         $this->collection = ImageCategory::find($categoryID);
        //         break;
        //     case 'album':
        //         $this->collection = Album::find($categoryID);
        //         break;
        //     default:
        //         abort(404);
        // }
        $this->collection = ImageCategory::find($collectionID);
        if(!isset($this->collection)) {
            abort(404);
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

            if($this->dirty) {
                $this->image = $this->images[$this->count];
                $this->dispatch('imageUpdated', $this->image->uuid);
                $this->dirty = false;
            }
        }

        return view('livewire.collection.show.grid-and-single');
    }
}
