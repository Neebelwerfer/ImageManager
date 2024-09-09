<?php

namespace App\Livewire;

use App\Models\Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;

#[Layout('layouts.app')]
class Collection extends Component
{

    public $type;

    public function setType($type)
    {
        $this->type = $type;
    }

    public function render()
    {
        return view('livewire.collection');
    }
}
