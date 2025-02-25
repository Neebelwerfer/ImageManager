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
use SapientPro\ImageComparator\ImageComparator;

#[Layout('layouts.app')]
class Upload extends Component
{
    use WithFileUploads;

    // 100MB Max
    #[Validate(['images.*' => 'image'])]
    public $images = [];

    public $imageCount = 0;

    public $uploading = false;
    public $fileCount = 0;
    public $processing = false;
    public $progress = 0;
    public UploadModel $upload;

    public $hashes = [];

    public TemporaryUploadedFile $test;

    #[On('UploadCancelled')]
    public function UploadCancelled()
    {
        $this->upload->delete();
    }


    #[On('UploadStarted')]
    public function UploadStarted()
    {
        $this->upload = UploadModel::create(
            [
                'ulid' => Str::ulid()->toString(),
                'user_id' => Auth::user()->id
            ]
        );
    }

    #[On('ChunkComplete')]
    public function ChunckComplete($index)
    {
        $imageService = app(ImageService::class);

        foreach($this->images[$index] as $image)
        {
            $this->imageCount += 1;
            $path = $image->getRealPath();
            $hash = $imageService->createImageHash($path);

            //Remove obvious duplicates
            $comparator = new ImageComparator;
            $duplicate = false;
            foreach ($this->hashes as $otherHash)
            {
                if($comparator->compareHashStrings($hash, $otherHash) > 99.9)
                {
                    $duplicate = true;
                    break;
                }
            }
            if($duplicate)
            {
                $image->delete();
                continue;
            }
            $this->hashes[] = $hash;

            $model = ImageUpload::create(
                [
                    'uuid' => str::uuid(),
                    'upload_ulid' => $this->upload->ulid,
                    'user_id' => Auth::user()->id,
                    'extension' => $image->extension(),
                    'hash' =>  $hash
                ]);

            $dimension = $image->dimensions();

            $model->data = json_encode([
                'category' => null,
                'tags' => [],
                'traits' => [],
                'albums' => [],
                'dimensions' => ['width' => $dimension["0"], 'height' => $dimension['1']]
            ]);

            $model->save();

            Storage::disk('local')->put('temp/' . $model->uuid, Crypt::encryptString(file_get_contents($path)));
            $image->delete();
        }
    }

    #[On('UploadFinished')]
    public function onUploadFinished()
    {
        assert($this->upload !== null, 'Upload is null?');
        Broadcast::on('upload.' . Auth::user()->id)->as('newUpload')->with(['ulid' => $this->upload->ulid])->send();
        ProcessUpload::dispatch(Auth::user(), $this->upload);
        return $this->redirectRoute('upload.multiple', ['ulid' => $this->upload->ulid], navigate: true);
    }

    public function render()
    {
        return view('livewire.upload');
    }
}
