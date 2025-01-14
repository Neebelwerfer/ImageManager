<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

#[Layout('layouts.app')]
class Collection extends Component
{

    #[Url('type')]
    public $type;

    public function setType($type)
    {
        $this->type = $type;
    }

    public function mount($collection = null)
    {
        $this->type = $collection;
    }

    public function render()
    {
        return view('livewire.collection');
    }
}
