<?php

namespace App\Livewire\Modal\Error;

use LivewireUI\Modal\ModalComponent;

class SimpleErrorMessage extends ModalComponent
{
    public $message;

    public function mount($message)
    {
        $this->message = $message;
    }

    public function render()
    {
        return view('livewire.modal.error.simple-error-message');
    }
}
