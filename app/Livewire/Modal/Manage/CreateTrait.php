<?php

namespace App\Livewire\Modal\Manage;

use App\Livewire\Manage\Traits as AdminTraits;
use App\Models\Traits;
use App\Services\TraitService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use LivewireUI\Modal\ModalComponent;

class CreateTrait extends ModalComponent
{
    #[Validate('required|string|min:3')]
    public string $name = '';
    #[Validate('required')]
    public string $type = "integer";
    #[Validate('min:0')]
    public int $min = 0;
    #[Validate('max:255|min:1')]
    public int $max = 255;
    #[Validate('required')]
    public string $default = '';

    public function CreateTrait()
    {
        $this->validate();

        if(Traits::owned(Auth::user()->id)->where('name', $this->name)->exists())
        {
            return $this->addError('name', 'Name already exists');
        }

        if($this->max <= $this->min) {
            return $this->addError('max', 'Max must be greater than min');
        }

        if($this->default > $this->max)
        {
            return $this->addError('default', 'Default should be equal or smaller than max');
        }

        if($this->default < $this->min)
        {
            return $this->addError('default', 'Default should be equal or bigger than min');
        }

        $data =
        [
            'name' => $this->name,
            'type' => $this->type,
            'min' => $this->min,
            'max' => $this->max,
            'default' => $this->default
        ];

        app(TraitService::class)->Create(Auth::user(), $data);

        $this->closeModal();

    }

    public function render()
    {
        return view('livewire.modal.manage.create-trait');
    }
}
