<?php

namespace App\Component;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.collection')]
abstract class CollectionView extends Component
{

    use WithPagination;

    #[Url('g')]
    public $gridView = true;
    #[Url('i')]
    public $count = 0;
    public $minRating = 0;

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
        $this->singleImage->delete();
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
                $this->dispatch('imageUpdated', $this->singleImage->uuid);
            }
        }
        return view('livewire.collection.show.grid-and-single');
    }
}
