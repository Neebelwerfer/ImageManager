<?php

namespace App\Livewire\Modal\Manage;

use App\Livewire\Manage\Traits as AdminTraits;
use App\Models\Traits;
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
    #[Validate('boolean')]
    public bool $global = false;
    #[Validate('required')]
    public string $default = '';

    public function CreateTrait()
    {
        $this->validate();

        if($this->max <= $this->min) {
            return $this->addError('max', 'Max must be greater than min');
        }

        if(!Auth::user()->is_admin && $this->global) {
            return $this->addError('global', 'You must be an admin to create a global trait');
        }

        Traits::create([
            'name' => $this->name,
            'type' => $this->type,
            'min' => $this->min,
            'max' => $this->max,
            'global' => $this->global,
            'owner_id' => Auth::user()->id,
            'default' => $this->default
        ]);

        $this->closeModalWithEvents([
            AdminTraits::class => 'refresh'
        ]);
    }

    public function render()
    {
        return view('livewire.modal.manage.create-trait');
    }
}
