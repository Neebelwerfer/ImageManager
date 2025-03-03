<?php

namespace App\Livewire\Upload;

use Livewire\Attributes\Computed;
use Livewire\Component;

class DuplicateImages extends Component
{
    public $duplicates;
    public $count = 0;

    public function mount($duplicates)
    {
        $this->duplicates = $duplicates;
    }

    public function next()  {
        $this->count++;
        if($this->count >= count($this->duplicates)) {
            $this->count = 0;
        }
    }

    public function previous()  {
        $this->count--;
        if($this->count < 0) {
            $this->count = count($this->duplicates) - 1;
        }
    }

    public function hasNext()  {
        if($this->count < count($this->duplicates) - 1) {
            return true;
        }
        return false;
    }

    public function hasPrevious()  {
        if($this->count > 0) {
            return true;
        }
        return false;
    }

    #[Computed()]
    public function count()  {
        return count($this->duplicates);
    }

    public function render()
    {

        return view('livewire.upload.duplicate-images');
    }
}
