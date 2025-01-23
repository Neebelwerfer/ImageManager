<?php

namespace App\Livewire\Trait;

use App\Support\Traits\AddedTrait;
use Livewire\Component;

class Show extends Component
{
    public AddedTrait $trait;

    public $value = '';

    public function updated()
    {
        $this->dispatch('traitUpdated', $this->trait->getTrait()->id, $this->value);
    }

    public function mount($trait)
    {
        $this->trait = $trait;
        $this->value = $trait->getValue();
    }

    public function render()
    {
        return view('livewire.trait.show');
    }
}
