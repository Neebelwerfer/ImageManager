<?php

namespace App\Livewire;

use App\Jobs\Upload\CheckUploadForDuplicates;
use App\Jobs\Upload\CleanupImages;
use App\Jobs\Upload\ProcessUpload;
use App\Models\Upload as UploadModel;
use App\Services\ImageService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;
use Illuminate\Support\Str;
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
    public $progress = 0;
    public $currentChunk = 0;
    public UploadModel $upload;

    public $hashes = [];
    public $data = [];

    #[On('UploadCancelled')]
    public function UploadCancelled($url)
    {
        if(!$this->uploading) return;

        $this->uploading = false;
        $this->cleanup();
        $this->redirect($url);
    }

    public function cleanup()
    {
        $data = [];
        foreach($this->images as $chunk)
        {
            foreach($chunk as $image)
            {
                $data[] = $image->getRealPath();
            }
        }
        CleanupImages::dispatch(Auth::user(), $this->upload, $this->data);
    }

    #[On('UploadStarted')]
    public function UploadStarted()
    {
        $this->uploading = true;
        $this->data = [];
        $this->upload = UploadModel::create(
            [
                'ulid' => Str::ulid()->toString(),
                'user_id' => Auth::user()->id
            ]
        );
        Cache::set('uploading-' . $this->upload->ulid, true);
    }

    public function cleanupChunck($index){
        $data = [];
        foreach ($this->images[$index] as $image)
        {
            $data[] = $image->getRealPath();
        }
        CleanupImages::dispatch(Auth::user(), $data);
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
            $uuid = str::uuid()->toString();

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

            $dimension = $image->dimensions();
            $this->data[] = [
                'uuid' => $uuid,
                'path' => $path,
                'hash' => $hash,
                'extension' => $image->extension(),
                'dimensions' => ['width' => $dimension["0"], 'height' => $dimension['1']]
            ];
        }
    }

    #[On('UploadFinished')]
    public function onUploadFinished()
    {
        if(!$this->uploading)
        {
            return;
        }

        $this->uploading = false;
        assert($this->upload !== null, 'Upload is null?');

        Bus::chain([
            new ProcessUpload(Auth::user(), $this->upload, $this->data),
            new CheckUploadForDuplicates(Auth::user(), $this->upload)
        ])->dispatch();
        return $this->redirectRoute('upload.multiple', ['ulid' => $this->upload->ulid], navigate: true);
    }

    public function render()
    {
        return view('livewire.upload');
    }
}
