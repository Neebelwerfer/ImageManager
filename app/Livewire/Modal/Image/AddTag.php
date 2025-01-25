<?php

namespace App\Livewire\Modal\Image;

use App\Services\TagService;
use LivewireUI\Modal\ModalComponent;

class AddTag extends ModalComponent
{
    public $name;

    protected TagService $tagService;

    public function save()
    {
        $tag = $this->tagService->getOrCreate($this->name);
        $this->dispatch('tagSelected', $tag->id);
        $this->closeModal();
    }

    public function boot()
    {
        $this->tagService = app(TagService::class);
    }

    public function render()
    {
        return view('livewire.modal.image.add-tag');
    }
}
