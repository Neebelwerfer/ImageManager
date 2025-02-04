<?php

namespace App\Livewire\Upload;

use App\Jobs\Upload\CheckForDuplicates;
use App\Jobs\Upload\ProcessImage;
use App\Jobs\Upload\ScanForDuplicates;
use App\Livewire\Upload;
use App\Models\ImageCategory;
use App\Models\ImageUpload;
use App\Models\Tags;
use App\Models\Traits;
use App\Models\User;
use App\Services\ImageService;
use App\Support\Enums\UploadState;
use App\Support\Traits\AddedTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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
    public $hash;


    #[On('echo:upload.{imageUpload.uuid},.imageProcessed')]
    public function imageProcessed()
    {
        $this->state = $this->imageUpload->state;
    }

    #[On('echo:upload.{imageUpload.uuid},.begunProcessing')]
    public function begunProcessing()
    {
        $this->state = $this->imageUpload->state;
    }

    #[On('echo:upload.{imageUpload.uuid},.processingFailed')]
    public function processingFailed()
    {
        $this->state = $this->imageUpload->state;
    }

    #[On('echo:upload.{imageUpload.uuid},.foundDuplicates')]
    public function foundDuplicates()
    {
        $this->state = $this->imageUpload->state;
        unset($this->duplicates);
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

        $data = [
            'category' => $this->category->id ?? null,
            'tags' => $tags,
            'traits' => $this->traits,
            'dimensions' => $this->ImageMetadata['dimensions']
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
        $traits = Traits::personalOrGlobal()->get();
        foreach($traits as $trait) {
            $at = new AddedTrait($trait, $trait->default);
            $this->traits[$trait->id] = $at;
        }
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
            $img = ImageManager::gd()->read(storage_path('app/') . $this->imageUpload->path());

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
        $data = json_decode($this->imageUpload->data, true);
        if(isset($data) && !empty($data))
        {

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
        else if($this->state === "foundDuplicates")
            $this->foundDuplicates();
    }

    public function render()
    {
        return view('livewire.upload.process-upload');
    }
}
