<?php

namespace App\Livewire\Collection\Show;

use App\Models\ImageCategory;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.collection')]
class Category extends Component
{

    #[Url('grid')]
    public $gridView = true;
    #[Url('i')]
    public $count = 0;

    public $category;
    public $images;
    public $image;

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
    }

    public function show($count)
    {
        $this->count = $count;
        $this->gridView = false;
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
    }

    public function mount($categoryID)
    {
        $this->category = ImageCategory::find($categoryID);
        if(!isset($this->category)) {
            abort(404);
        }
        $this->images = $this->category->images->values();
    }

    public function render()
    {
        $this->image = $this->images[$this->count];
        return view('livewire.collection.show.category');
    }
}
