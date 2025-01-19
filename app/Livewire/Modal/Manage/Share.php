<?php

namespace App\Livewire\Modal\Manage;

use App\Models\Album;
use App\Models\ImageCategory;
use App\Models\SharedResources;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Component;
use LivewireUI\Modal\ModalComponent;

class Share extends ModalComponent
{

    #[Validate('required', 'email')]
    public $email;
    #[Validate('required')]
    public $accessLevel = 'view';

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

                if(isset($sharedTo) && $sharedTo->id != Auth::user()->id) {
                    $shared_resource = new SharedResources();
                    $shared_resource->resource_id = $category->id;
                    $shared_resource->type = 'category';
                    $shared_resource->shared_by_user_id = Auth::user()->id;
                    $shared_resource->shared_with_user_id = $sharedTo->id;
                    $shared_resource->level = $this->accessLevel;
                    $shared_resource->save();
                    return $this->closeModal();
                }
            }
        }
        else if($this->type == 'album') {
            $album = Album::owned()->find($this->id);

            if(isset($album)) {
                $sharedTo = User::where('email', $this->email)->first();

                if(isset($sharedTo) && $sharedTo->id != Auth::user()->id) {
                    $shared_resource = new SharedResources();
                    $shared_resource->resource_id = $album->id;
                    $shared_resource->type = 'album';
                    $shared_resource->shared_by_user_id = Auth::user()->id;
                    $shared_resource->shared_with_user_id = $sharedTo->id;
                    $shared_resource->level = $this->accessLevel;
                    $shared_resource->save();
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
