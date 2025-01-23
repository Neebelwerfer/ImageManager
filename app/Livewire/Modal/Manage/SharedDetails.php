<?php

namespace App\Livewire\Modal\Manage;

use App\Models\SharedResources;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;
use LivewireUI\Modal\ModalComponent;

class SharedDetails extends ModalComponent
{
    public SharedResources $resource;

    #[Locked]
    public $type;

    public function stopSharing()
    {
        $this->resource->delete();
        $this->dispatch('resfreshPage');
        $this->closeModal();
    }


    #[Computed()]
    public function data(): array
    {
        $sharedBy = User::where('id', $this->resource->shared_by_user_id)->first()->name;
        return [
            'sharedBy' => $sharedBy,
            'accessLevel' => $this->resource->level
        ];
    }

    public function mount($type, $id)
    {
        $this->resource = SharedResources::where('resource_id', $id)->where('type', $type)->where('shared_with_user_id', Auth::user()->id)->first();
    }

    public function render()
    {
        return view('livewire.modal.manage.shared-details');
    }
}
