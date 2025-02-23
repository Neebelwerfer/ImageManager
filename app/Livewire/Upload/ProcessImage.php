<?php

namespace App\Livewire\Upload;

use App\DTO\ImageTraitDTO;
use App\Models\Album;
use App\Models\ImageCategory;
use App\Models\ImageUpload;
use App\Models\Tags;
use App\Models\Traits;
use App\Services\TagService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class ProcessImage extends Component
{
    public ImageUpload $imageUpload;

    public $category;
    public $tags = [];
    public $traits = [];
    public $albums = [];
    public $hash;

    public $error = false;
    public $state = "waiting";

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

    #[Computed()]
    public function ImageMetadata()
    {
        if($this->imageUpload == null) return null;

        return Cache::remember('imageUpload-'.$this->imageUpload->uuid, 3600, function () {
            $data = [];
            $decryptedImage = Crypt::decrypt(file_get_contents($this->imageUpload->fullPath()), false);
            $img = ImageManager::gd()->read($decryptedImage);

            $data['dimensions'] = ['height' => $img->size()->height(), 'width' => $img->size()->width()];
            $data['extension'] = Str::upper($this->imageUpload->extension);
            $data['size'] = number_format(Storage::disk('local')->size($this->imageUpload->path()) / 1024 / 1024, 2);
            Cache::set('image-upload-'.$this->imageUpload->uuid, $data, now()->addHour());
            return $data;
        });
    }

    public function save()
    {
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
    }

    public function setupTraits() {
        $traits = Traits::owned(Auth::user()->id)->get();
        foreach($traits as $trait) {
            $at = new ImageTraitDTO($trait, Auth::user()->id, $trait->default);
            $this->traits[$trait->id] = $at;
        }
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

    #[Computed()]
    public function duplicates()
    {
        return json_decode($this->imageUpload->duplicates);
    }

    public function removeImage()
    {
        $this->imageUpload->delete();
        $this->error = true;
    }

    public function mount($uuid)
    {
        $res = ImageUpload::find($uuid);

        if($res === null || $res->user_id != Auth::user()->id)
        {
            return $this->error = true;
        }

        $this->imageUpload = $res;
        $this->state = $res->state;

        if($this->state == "waiting"){
            $this->SetupData();
        }
    }

    public function render()
    {
        return view('livewire.upload.process-image');
    }
}
