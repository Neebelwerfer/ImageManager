<?php

namespace App\Livewire\Modal\Manage;

use App\Models\Album;
use App\Models\Image;
use App\Models\ImageCategory;
use App\Models\SharedResources;
use App\Models\User;
use App\Services\AlbumService;
use App\Services\CategoryService;
use App\Services\ImageService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Component;
use LivewireUI\Modal\ModalComponent;

class Share extends ModalComponent
{

    #[Validate('required|email')]
    public $email;
    #[Validate('required')]
    public $accessLevel = 'view';

    #[Locked()]
    public $type;
    #[Locked()]
    public $id;

    protected CategoryService $categoryService;
    protected AlbumService $albumService;
    protected ImageService $imageService;

    public function mount($type, $id)
    {
        $this->type = $type;
        $this->id = $id;
    }

    public function boot()
    {
        $this->categoryService = app(CategoryService::class);
        $this->albumService = app(AlbumService::class);
        $this->imageService = app(ImageService::class);
    }

    public function share()
    {
        $this->validate();

        $sharedTo = User::where('email', $this->email)->first();
        if(!isset($sharedTo))
        {
            return $this->addError('email', 'User not found');
        }

        switch($this->type)
        {
            case 'category':
                $res = $this->categoryService->share(Auth::user(), $this->id, $sharedTo, $this->accessLevel);
                break;
            case 'album':
                $res = $this->albumService->share(Auth::user(), $this->id, $sharedTo, $this->accessLevel);
                break;
            case 'image':
                $res = $this->imageService->share(Auth::user(), $this->id, $sharedTo, $this->accessLevel);
                break;
            default:
                throw new \Exception('Trying to share unknown type');
        }

        if(!$res)
        {
            $this->dispatch('openModal', 'modal.error.simple-error-message', ['message' => 'This '. $this->type  .'is already shared with user!']);
            return;
        }
        $this->dispatch('updateShared');
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.modal.manage.share');
    }
}
