<?php

namespace App\Livewire;

use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;

#[Lazy()]
class Image extends Component
{
    #[Reactive()]
    public $image;

    public $zoom = false;

    public $classes = '';


    public function toggleZoom()
    {
        $this->zoom = !$this->zoom;
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
        <div class="@if(!$zoom) {{ $classes }} cursor-zoom-in @else absolute flex justify-center cursor-zoom-out bg-black/75 inset-0 z-50 scale-125 w-full @endif" wire:click="toggleZoom" >
            <img class="object-scale-down"  src="{{ asset($image->path) }}"  alt="{{ $image->name }}">
        </div>
        HTML;
    }
}
