<?php

namespace App\Livewire\Modal\Image;

use App\Services\TagService;
use Livewire\Attributes\Validate;
use Livewire\Component;

class AddTag extends Component
{
    #[Validate('required|min:1')]
    public $name;

    public bool $personal = false;

    protected TagService $tagService;

    public function save()
    {
        $this->validate();

        if(str_contains(trim($this->name), ' '))
        {
            $this->addError('name', "Tag can't have space in the middle");
            return;
        }


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
