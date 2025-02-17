<?php

namespace App\Livewire\Upload;

use App\DTO\ImageTraitDTO;
use App\Jobs\Upload\CheckForDuplicates;
use App\Jobs\Upload\ProcessImage;
use App\Jobs\Upload\ScanForDuplicates;
use App\Livewire\Upload;
use App\Models\Album;
use App\Models\ImageCategory;
use App\Models\ImageUpload;
use App\Models\Tags;
use App\Models\Traits;
use App\Models\User;
use App\Services\ImageService;
use App\Services\TagService;
use App\Support\Enums\UploadState;
use App\Support\Enums\UploadStates;
use App\Support\Traits\AddedTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ProcessUpload extends Component
{
    public ImageUpload $imageUpload;

    public $state = "waiting";

    public $category;
    public $tags = [];
    public $traits = [];
    public $albums = [];
    public $hash;

    #[On('echo:upload.{imageUpload.uuid},.stateUpdated')]
    public function stateUpdated($data)
    {
        $this->state = $data['state'];

        if($data['state'] === UploadStates::FoundDuplicates->value)
        {
            unset($this->duplicates);
        }
    }

    public function process() {
        $this->state = "processing";
        ProcessImage::dispatchAfterResponse(Auth::user(), $this->imageUpload);
    }

    #[On('categorySelected')]
    public function categorySelected($category)
    {
        if($category == -1) {
            $this->category = null;
            return;
        }
        $this->category = ImageCategory::find($category);
    }


    #[On('albumSelected')]
    public function albumSelected($albumId)
    {
        $this->albums[$albumId] = Album::ownedOrShared(Auth::user()->id)->find($albumId);
    }

    #[On('tagSelected')]
    public function tagSelected($tagData)
    {
        $id = $tagData['id'];
        $personal = $tagData['personal'];
        if (isset($this->tags[$id])) {
            return;
        }

        $this->tags[$id] = ['tag' => Tags::find($id), 'personal' => $personal];
    }

    public function submit()
    {
        if($this->imageUpload->state !== "waiting")
            return;


        $tags = [];
        foreach ($this->tags as $id => $data)
        {
            $tags[$data['tag']->name] = $data['personal'];
        }

        $traits = [];
        foreach ($this->traits as $id => $imageTraitDTO)
        {
            $traits[$id] = $imageTraitDTO->getValue();
        }

        $data = [
            'category' => $this->category->id ?? null,
            'tags' => $tags,
            'traits' => $traits,
            'dimensions' => $this->ImageMetadata['dimensions'],
            'albums' => array_keys($this->albums)
        ];

        $this->imageUpload->data = json_encode($data);
        $this->imageUpload->save();
        $this->state = "scanning";
        ScanForDuplicates::dispatchAfterResponse(Auth::user(), $this->imageUpload);
    }

    public function removeTag($tagID)
    {
        unset($this->tags[$tagID]);
    }

    #[On('traitUpdated')]
    public function traitUpdated($id, $value) {
        $this->traits[$id]->setValue($value);
    }


    public function setupTraits() {
        $traits = Traits::owned(Auth::user()->id)->get();
        foreach($traits as $trait) {
            $at = new ImageTraitDTO($trait, Auth::user()->id, $trait->default);
            $this->traits[$trait->id] = $at;
        }
    }

    public function retry()
    {
        $this->imageUpload->state = "waiting";
        $this->imageUpload->save();
        $this->state = $this->imageUpload->state;
        $this->SetupData();

    }

    public function cancel() {
        $this->imageUpload->delete();
        return $this->redirectRoute('upload', navigate: true);
    }

    public function navigate($toImage = false)
    {
        if($toImage)
        {
            return $this->redirectRoute('image.show', ['imageUuid' => $this->imageUpload->uuid]);
        }
        return $this->redirectRoute('upload', navigate:true);
    }

    #[Computed()]
    public function duplicates()
    {
        return json_decode($this->imageUpload->duplicates);
    }

    #[Computed()]
    public function ImageMetadata()
    {
        if($this->imageUpload == null) return null;
        $cache = Cache::get('image-upload-'.$this->imageUpload->uuid);
        if($cache === null)
        {
            $data = [];
            $decryptedImage = Crypt::decrypt(file_get_contents($this->imageUpload->fullPath()), false);
            $img = ImageManager::gd()->read($decryptedImage);

            $data['dimensions'] = ['height' => $img->size()->height(), 'width' => $img->size()->width()];
            $data['extension'] = Str::upper($this->imageUpload->extension);
            $data['size'] = number_format(Storage::disk('local')->size($this->imageUpload->path()) / 1024 / 1024, 2);
            Cache::set('image-upload-'.$this->imageUpload->uuid, $data, now()->addHour());
            return $data;
        }
        return $cache;
    }

    public function SetupData()
    {
        $this->setupTraits();

        $data = json_decode($this->imageUpload->data, true);
        if(isset($data) && !empty($data))
        {
            if(isset($data["category"]) && is_numeric($data['category']))
            {
                $this->category = ImageCategory::ownedOrShared(Auth::user()->id)->find($data['category']);
            }

            if(isset($data["tags"]))
            {
                foreach($data['tags'] as $name => $personal)
                {
                    $this->tagSelected(['id' => app(TagService::class)->getOrCreate($name)->id, 'personal' => $personal]);
                }
            }

            if(isset($data['traits']))
            {
                foreach($data['traits'] as $id => $value)
                {
                    $this->traits[$id]->setValue($value);
                }
            }

            if(isset($data['albums']))
            {
                foreach ($data['albums'] as $albumId)
                {
                    $this->albumSelected($albumId);
                }
            }
        }
    }

    public function boot()
    {

    }

    public function mount($uuid)
    {
        $res = ImageUpload::find($uuid);

        if($res === null || $res->user_id != Auth::user()->id)
        {
            return $this->redirectRoute('upload', navigate: true);
        }

        $this->imageUpload = $res;
        $this->state = $res->state;

       if($this->state === "waiting")
            $this->SetupData();
    }

    public function render()
    {
        return view('livewire.upload.process-upload');
    }
}
