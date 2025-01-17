<?php

namespace App\Livewire\Modal\Upload;

use LivewireUI\Modal\ModalComponent;

class DuplicateImages extends ModalComponent
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

    public function count()  {
        return count($this->duplicates);
    }

    public function close($accept) {

        if($accept) {
            $this->dispatch('accepted');
        }
        else{
            $this->dispatch('cancelled');
        }
        $this->closeModal();
    }

    public function render()
    {

        return view('livewire.modal.upload.duplicate-images');
    }
}
