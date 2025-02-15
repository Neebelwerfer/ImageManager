<?php

namespace App\Livewire;

use App\Models\ImageCategory;
use App\Models\Tags;
use App\Models\ImageUpload;
use App\Models\Traits;
use App\Services\ImageService;
use App\Support\Traits\AddedTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;

#[Layout('layouts.app')]
class Upload extends Component
{
    use WithFileUploads;

    // 100MB Max
    #[Validate('image')]
    public $image;

    #[Locked]
    public $completedImageUploads = [];

    #[Locked]
    public $imageUploads = [];

    public function onUploadFinished() {
        $img = ImageManager::gd()->read($this->image);


        $upload = new ImageUpload(
            [
                'uuid' => str::uuid(),
                'user_id' => Auth::user()->id,
                'extension' => $this->image->extension(),
                'hash' => app(ImageService::class)->createImageHash($img->core()->native())
            ]);
        $upload->save();

        $this->image->storeAs('temp/', $upload->uuid . '.' . $this->image->extension());
        $this->image->delete();
        $this->image = null;

        return $this->redirectRoute('upload.process', ['uuid' => $upload->uuid], navigate: true);
    }

    public function retrieveImageUploads()
    {
        $uploads = ImageUpload::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();
        $this->imageUploads = [];
        $this->completedImageUploads = [];
        foreach ($uploads as $upload)
        {
            if($upload->state === "done")
                $this->completedImageUploads[$upload->uuid] = ['state' => $upload->state, 'startTime' => $upload->created_at->diffForHumans()];
            else
                $this->imageUploads[$upload->uuid] = ['state' => $upload->state, 'startTime' => $upload->created_at->diffForHumans()];
        }
    }

    public function mount(){
        $this->retrieveImageUploads();
    }

    public function getStateColour($state)
    {
        switch($state){
            case 'waiting':
                return 'border-gray-600';
            case 'scanning':
            case 'processing':
                return 'border-orange-500';
            case 'foundDuplicates':
            case 'error':
                return 'border-red-600';
        }
    }

    public function render()
    {
        return view('livewire.upload');
    }
}
