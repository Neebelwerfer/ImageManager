<?php

namespace App\Component;

use App\Models\SharedResources;
use App\Models\Tags;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.collection')]
abstract class CollectionView extends Component
{

    use WithPagination;

    #[Url('grid')]
    public $gridView = true;
    #[Url('i')]
    public $count = 0;
    #[Url('tags', except:'')]
    public $tags = '';

    public $singleImage;

    #[Locked()]
    public $collectionType;

    public bool $showBackButton = false;

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
        if(!isset($this->singleImage) && Auth::user()->id != $this->singleImage->owner_id) {
            return;
        }
        $this->gridView = true;
        $this->count = 0;
        $this->updateImages();
    }

    public function filter()
    {
        $this->updateImages();
        $this->count = 0;
    }

    public function goBack()
    {
        return redirect()->route('collection');
    }

    public function updateImages()
    {
        unset($this->images);
    }

    #[Computed()]
    public abstract function images();

    public function updatePage(bool $increment){
        if($increment) {
            $this->nextPage();
        } else {
            $this->previousPage();
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
            }
        }
        return view('livewire.collection.show.grid-and-single');
    }
}
