<?php

namespace App\Livewire\Modal\Manage;

use App\DTO\ImageTraitDTO;
use App\Livewire\Modal\Image\Details;
use App\Models\ImageTraits;
use App\Models\Traits;
use App\Services\TraitService;
use Exception;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Component;
use LivewireUI\Modal\ModalComponent;

class EditTraitValue extends ModalComponent
{
    public ImageTraits $imageTrait;
    public Traits $trait;

    public string $value;

    public function mount($imageTrait)
    {
        $this->imageTrait = $imageTrait;
        $this->trait = $this->imageTrait->trait;
        $this->value = $this->imageTrait->value;
    }

    public function save()
    {
        $validationString = app(TraitService::class)->getValidationRules($this->trait);

        $validation = $this->validate([
            'value' => $validationString,
        ]);

        if(isset($validation['value']))
        {
            $this->imageTrait->value = $this->value;
            $this->imageTrait->save();

            $this->closeModalWithEvents(
                [
                    Details::class => 'traitUpdated'
                ]
            );
        }
    }

    public function render()
    {
        return view('livewire.modal.manage.edit-trait-value');
    }
}
