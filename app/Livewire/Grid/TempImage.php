<?php

namespace App\Livewire\Grid;

use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy(isolate: false)]
class TempImage extends Component
{
    public $image;

    public function mount($image)
    {
        $this->image = $image;
    }

    public function placeholder()
    {
        return <<<'HTML'
        <div>
            <x-spinning-loader />
        </div>
        HTML;
    }

    public function render()
    {
        return <<<'HTML'
        <div>
            <img class="object-scale-down px-1" style="width: 190px; height: 215px;" src="{{ url('temp/'.$image->uuid) }}">
        </div>
        HTML;
    }
}
