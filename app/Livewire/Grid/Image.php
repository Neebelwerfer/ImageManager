<?php

namespace App\Livewire\Grid;

use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy()]
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
            <img class="object-scale-down mt-1" style="width: 256px; height: 290px;" src="{{ asset($image->thumbnail_path()) }}">
        </div>
        HTML;
    }
}
