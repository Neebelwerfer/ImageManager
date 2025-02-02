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
use Livewire\Attributes\Url;

#[Layout('layouts.app')]
class Upload extends Component
{
    use WithFileUploads;

    // 100MB Max
    #[Validate('image')]
    public $image;

    public function onUploadFinished() {
        $upload = new ImageUpload(
            [
                'uuid' => str::uuid(),
                'user_id' => Auth::user()->id,
                'extension' => $this->image->extension()
            ]);
        $upload->save();

        $this->image->storeAs('temp/', $upload->uuid . '.' . $this->image->extension());
        $this->image->delete();
        $this->image = null;

        return $this->redirectRoute('upload.process', ['uuid' => $upload->uuid], navigate: true);
    }

    public function render()
    {
        return view('livewire.upload');
    }
}
