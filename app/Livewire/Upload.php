<?php

namespace App\Livewire;

use App\Jobs\Upload\ProcessUpload;
use App\Models\ImageCategory;
use App\Models\Tags;
use App\Models\ImageUpload;
use App\Models\Traits;
use App\Models\Upload as UploadModel;
use App\Services\ImageService;
use App\Support\Traits\AddedTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
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
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

#[Layout('layouts.app')]
class Upload extends Component
{
    use WithFileUploads;

    // 100MB Max
    #[Validate(['images.*' => 'image'])]
    public $images = [];

    public $chunks = [];

    public $uploading = false;
    public $fileCount = 0;
    public $processing = false;

    #[On('UploadCancelled')]
    public function UploadCancelled()
    {
    }


    #[On('ChunkComplete')]
    public function ChunckComplete($index)
    {
        $i = count($this->images);
        foreach($this->chunks[$index] as $image)
        {
            $this->images[$i] = $image;
            $i += 1;
        }

        if(count($this->images) === $this->fileCount)
        {
            $this->processing = true;
            $this->dispatch('UploadFinished')->self();
        }
    }

    #[On('UploadFinished')]
    public function onUploadFinished() {

        $uploadModel = UploadModel::create(
            [
                'ulid' => Str::ulid()->toString(),
                'user_id' => Auth::user()->id
            ]
        );

        $data = [];
        foreach ($this->images as $key => $image)
        {
            $data[$key] = ['path' => $image->getRealPath(), 'extension' => $image->extension()];
        }

        Broadcast::on('upload.' . Auth::user()->id)->as('newUpload')->with(['ulid' => $uploadModel->ulid])->send();
        ProcessUpload::dispatch(Auth::user(), $uploadModel, $data);
        return $this->redirectRoute('upload.multiple', ['ulid' => $uploadModel->ulid], navigate: true);
    }

    public function render()
    {
        return view('livewire.upload');
    }
}
