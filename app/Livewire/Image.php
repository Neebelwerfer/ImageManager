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

    public $vWidth = 'w-5/6';

    public $hWidth = 'w-3/4';

    public $vertical = true;

    public $width;


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

    public function cappedWidth()
    {
        if(!$this->vertical)
        {
            return min($this->image->width, 1000);
        }
        return $this->image->width*0.9;
    }

    public function render()
    {

        if($this->image->width > $this->image->height) {
            $this->width = $this->vWidth;
            $this->vertical = true;
        }
        else
        {
            $this->width = $this->hWidth;
            $this->vertical = false;
        }

        return <<<'HTML'
        <div class="{{ $classes }} {{ $width }} cursor-zoom-in" wire:click="toggleZoom" >
            <img class="object-scale-down" width="{{ $this->cappedWidth() }}"  src="{{ url('images/'.$image->uuid) }}"  alt="{{ $image->name }}">

            @teleport('body')
                <div class="absolute inset-0 z-50 cursor-zoom-out @if(!$zoom) hidden @endif" wire:click="toggleZoom">
                    <div class="flex justify-center h-full py-2 bg-gray-700/95">
                        <img class="object-scale-down"  src="{{ url('images/'.$image->uuid) }}"  alt="{{ $image->name }}">
                    </div>
                </div>
            @endteleport
        </div>
        HTML;
    }
}
