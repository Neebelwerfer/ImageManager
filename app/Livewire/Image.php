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
        <div class="{{ $classes }} cursor-zoom-in" wire:click="toggleZoom" >
            <img class="object-scale-down"  src="{{ asset($image->path) }}"  alt="{{ $image->name }}">

            @teleport('body')
                <div class="absolute inset-0 z-50 cursor-zoom-out @if(!$zoom) hidden @endif" wire:click="toggleZoom">
                    <div class="flex justify-center h-full py-2 bg-gray-700/95">
                        <img class="object-scale-down"  src="{{ asset($image->path) }}"  alt="{{ $image->name }}">
                    </div>
                </div>
            @endteleport
        </div>
        HTML;
    }
}
