<?php

namespace App\Livewire\Modal\Search;

use App\Models\Traits;
use Illuminate\Support\Facades\Auth;
use LivewireUI\Modal\ModalComponent;

class AddTrait extends ModalComponent
{
    public function selectEntry($id)
    {
        $this->dispatch('traitSelected', $id);
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.modal.search.add-trait',
        [
            'traits' => Traits::owned(Auth::user()->id)->paginate(20)
        ]);
    }
}
