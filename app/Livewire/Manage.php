<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.manage')]
class Manage extends Component
{
    public function render()
    {
        return view('livewire.manage');
    }
}
