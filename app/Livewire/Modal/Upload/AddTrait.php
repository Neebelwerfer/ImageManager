<?php

namespace App\Livewire\Modal\Upload;

use App\Models\Traits;
use Livewire\Component;
use LivewireUI\Modal\ModalComponent;

class AddTrait extends ModalComponent
{
    public function selectTrait(Traits $trait)
    {
        $this->dispatch('traitSelected', $trait->id);
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.modal.upload.add-trait',
            [
                'traits' => Traits::personalOrGlobal()->paginate(20),
            ]);
    }
}
