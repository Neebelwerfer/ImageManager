<?php

namespace App\Livewire\Modal\Image;

use App\Livewire\Modal\Upload\EditRelations;
use App\Models\Album;
use App\Models\ImageCategory;
use App\Models\ImageTag;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;
use LivewireUI\Modal\ModalComponent;

class CreateRelationClassification extends ModalComponent
{

    public $type = 'category';

    #[Validate('required|string|max:255')]
    public $name;

    public function save()
    {
        $this->validate();

        if($this->checkIfExists($this->name)) {
            return $this->addError('name', 'Name already exists');
        }

        if($this->type == 'category') {
            ImageCategory::create([
                'name' => $this->name,
                'owner_id' => Auth::user()->id,
            ]);
        } else if ($this->type == 'tag') {
            ImageTag::create([
                'name' => $this->name,
                'owner_id' => Auth::user()->id,
            ]);
        } else if ($this->type == 'album') {
            Album::create([
                'name' => $this->name,
                'owner_id' => Auth::user()->id,
            ]);
        }

        $this->closeModalWithEvents([
            EditRelations::class => 'refresh'
        ]);
    }

    public function checkIfExists($name) {
        if($this->type == 'category') {
            return ImageCategory::where('name', $name)->where('owner_id', Auth::user()->id)->exists();
        } else if ($this->type == 'tag') {
            return ImageTag::where('name', $name)->where('owner_id', Auth::user()->id)->exists();

        } else if ($this->type == 'album') {
            return Album::where('name', $name)->where('owner_id', Auth::user()->id)->exists();
        } else {
            return false;
        }
    }

    public function mount(string $type)
    {
        $this->type = $type;
    }

    public function render()
    {
        return view('livewire.modal.image.create-relation-classification');
    }
}
