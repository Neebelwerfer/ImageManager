<?php

namespace App\Livewire\Grid;

use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy(isolate: false)]
class Image extends Component
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
            <img class="object-scale-down px-1 mt-1" style="width: 255px; height: 290px;" src="{{ url('thumbnail/'.$image->uuid) }}">
        </div>
        HTML;
    }
}
