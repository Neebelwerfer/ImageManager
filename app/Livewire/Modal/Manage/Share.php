<?php

namespace App\Livewire\Modal\Manage;

use App\Models\ImageCategory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Component;
use LivewireUI\Modal\ModalComponent;

class Share extends ModalComponent
{

    #[Validate('required', 'email')]
    public $email;

    #[Locked()]
    public $type;
    #[Locked()]
    public $id;

    public function mount($type, $id)
    {
        $this->type = $type;
        $this->id = $id;
    }

    public function share()
    {
        $this->validate();

        if($this->type == 'category') {
            $category = ImageCategory::owned()->find($this->id);

            if(isset($category)) {
                $sharedTo = User::where('email', $this->email)->first();

                if(isset($sharedTo) && $sharedTo->id != Auth::user()->id && $sharedTo->sharedCategories()->find($category->id) == null) {
                    $sharedTo->sharedCategories()->attach($category->id);
                    return $this->closeModal();
                }
            }
        }
        return $this->addError('email', 'User not found');
    }

    public function render()
    {
        return view('livewire.modal.manage.share');
    }
}
