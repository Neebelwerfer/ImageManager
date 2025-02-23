<?php

namespace App\Livewire;

use App\Models\ImageCategory;
use App\Models\Tags;
use App\Models\ImageUpload;
use App\Models\Traits;
use App\Models\Upload as UploadModel;
use App\Services\ImageService;
use App\Support\Traits\AddedTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
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

#[Layout('layouts.app')]
class Upload extends Component
{
    use WithFileUploads;

    // 100MB Max
    #[Validate(['images.*' => 'image'])]
    public $images = [];

    public function onUploadFinished() {

        $uploadModel = UploadModel::create(
            [
                'ulid' => Str::ulid()->toString(),
                'user_id' => Auth::user()->id
            ]
        );

        $imageUploads = [];

        foreach ($this->images as $key => $image)
        {

            $img = ImageManager::gd()->read($image);

            $upload = new ImageUpload(
                [
                    'uuid' => str::uuid(),
                    'upload_ulid' => $uploadModel->ulid,
                    'user_id' => Auth::user()->id,
                    'extension' => $image->extension(),
                    'hash' => app(ImageService::class)->createImageHash($img->core()->native())
                ]);
            $upload->save();
            $imageUploads[$key] = $upload;

            $cryptImage = Crypt::encrypt((string) $img->encodeByMediaType(), false);
            Storage::disk('local')->put('temp/'. $upload->uuid, $cryptImage);
            $image->delete();
        }

        return $this->redirectRoute('upload.multiple', ['ulid' => $uploadModel->ulid], navigate: true);
    }

    public function render()
    {
        return view('livewire.upload');
    }
}
