<?php

namespace App\Livewire\Collection\Show;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\Album as AlbumModel;
use Livewire\Attributes\Url;

#[Layout('layouts.collection')]
class Album extends Component
{
    #[Url('grid')]
    public $gridView = true;
    #[Url('i')]
    public $count = 0;

    public $album;
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
    public function mount($albumID)
    {
        $this->album = AlbumModel::find($albumID);
        if(!isset($this->album)) {
            abort(404);
        }
        $this->images = $this->album->images->values();

    }

    public function render()
    {
        if($this->images->count() != 0) {
            $this->image = $this->images[$this->count];
        }
        return view('livewire.collection.show.grid-and-single');
    }
}
