<?php

namespace App\Livewire\Manage;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.manage')]
class Albums extends Component
{
    public function render()
    {
        return view('livewire.manage.albums');
    }
}
