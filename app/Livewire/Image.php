<?php

namespace App\Livewire;

use App\Models\Image as ModelsImage;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;

#[Lazy()]
class Image extends Component
{
    public $image;

    public $classes = '';

    #[On('imageUpdated')]
    public function imageUpdated($imageUUID)
    {
        $this->image = ModelsImage::find($imageUUID);
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
        <div class="{{ $classes }}">
            <img class="object-scale-down" src="{{ asset($image->path) }}"  alt="{{ $image->name }}">
        </div>
        HTML;
    }
}
