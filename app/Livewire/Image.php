<?php

namespace App\Livewire;

use App\Models\Image as ModelsImage;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;

#[Lazy()]
class Image extends Component
{
    #[Reactive()]
    public $image;

    public $classes = '';

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
        <div class="{{ $classes }}">
            <img class="object-scale-down"  src="{{ asset($image->path) }}"  alt="{{ $image->name }}">
        </div>
        HTML;
    }
}
