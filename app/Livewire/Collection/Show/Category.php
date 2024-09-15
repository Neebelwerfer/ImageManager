<?php

namespace App\Livewire\Collection\Show;

use App\Models\ImageCategory;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.collection')]
class Category extends Component
{

    #[Url('grid')]
    public $gridView = true;
    #[Url('i')]
    public $count = 0;

    public $showOptions = false;
    public $category;
    public $images;
    public $image;

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
        $this->images = $this->category->images->sortBy('rating', SORT_NUMERIC, true)->values();
    }

    public function mount($categoryID)
    {
        $this->category = ImageCategory::find($categoryID);
        if(!isset($this->category)) {
            abort(404);
        }
        $this->images = $this->category->images->sortBy('rating', SORT_NUMERIC, true)->values();
    }

    public function render()
    {
        if($this->images->count() > 0 or $this->dirty) {
            if($this->count > $this->images->count() - 1)
            {
                $this->count = $this->images->count() - 1;
            }
            $this->image = $this->images[$this->count];
            $this->dispatch('imageUpdated', $this->image->uuid);
            $this->dirty = false;
        }

        return view('livewire.collection.show.category');
    }
}
