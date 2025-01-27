<?php

namespace App\Livewire\Modal\Image;

use App\Services\TagService;
use LivewireUI\Modal\ModalComponent;

class AddTag extends ModalComponent
{
    public $name;
    public bool $personal = false;

    protected TagService $tagService;

    public function save()
    {
        $tag = $this->tagService->getOrCreate($this->name);
        $this->dispatch('tagSelected', ['id' => $tag->id, 'personal' => $this->personal]);
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
