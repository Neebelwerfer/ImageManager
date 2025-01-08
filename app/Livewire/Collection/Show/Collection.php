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
use Livewire\WithPagination;

#[Layout('layouts.collection')]
class Collection extends Component
{

    use WithPagination;

    #[Url('g')]
    public $gridView = true;
    #[Url('i')]
    public $count = 0;
    public $minRating = 0;

    public $showOptions = false;
    public $collection;

    public $singleImage;

    public function setGridView($value)
    {
        $this->gridView = $value;
    }

    public function nextImage()
    {
        $this->count++;
        if($this->count > count($this->images())) {
            $this->count = count($this->images());
        }

        if($this->count == count($this->images())) {
            if($this->gotNext())
            {
                $this->count = 0;
                $this->updatePage(true);
            }
        }
    }

    public function show($count)
    {
        $this->count = $count;
        $this->gridView = false;
    }

    public function gotPrevious()
    {
        if($this->count > 0 || !$this->images->onFirstPage()) {
            return true;
        }
        return false;
    }

    public function gotNext()
    {
        if($this->count < count($this->images()) - 1 || !$this->images->onLastPage()) {
            return true;
        }
        return false;
    }

    public function previousImage()
    {
        $this->count--;
        if($this->count < 0) {
            $this->count = 0;
            if($this->gotPrevious()) {
                $this->count = 19;
                $this->updatePage(false);
            }
        }
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
        $this->dispatch('reloadPage');
    }

    public function updateImages()
    {
        unset($this->images);
    }

    public function updatePage(bool $increment){
        if($increment) {
            $this->nextPage();
        } else {
            $this->previousPage();
        }
        $this->updateImages();
    }

    #[Computed(cache: true)]
    public function images()
    {
        $key = Auth::user()->id . '-' . $this->collection->id;

        return $this->collection->images->where('rating', '>=', $this->minRating)->sortBy('rating', SORT_NUMERIC, true)->values()->paginate(20);
    }

    public function mount($collectionType, $collectionID = null)
    {
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

    public function render()
    {
        if($this->images->count() > 0) {
            if($this->count > $this->images->count() - 1)
            {
                $this->count = $this->images->count() - 1;
            }

            if($this->singleImage == null || $this->singleImage != $this->images()[$this->count]) {
                $this->singleImage = $this->images()[$this->count];
                $this->dispatch('imageUpdated', $this->singleImage->uuid);
            }
        }
        return view('livewire.collection.show.grid-and-single');
    }
}
