<?php

namespace App\Livewire\Upload;

use App\Jobs\Upload\CleanupUpload;
use App\Jobs\Upload\ProcessMultipleImages;
use App\Models\ImageUpload;
use App\Models\Upload;
use App\Support\Enums\ImageUploadStates;
use App\Support\Enums\UploadStates;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ProcessMultiple extends Component
{
    use WithPagination;

    public Upload $upload;

    public $state = "waiting";
    public $selectedUUID = "";
    public $count = 0;
    public $selectedImages = [];
    public $editMode = false;

    #[On('echo:upload.{upload.user_id},.stateUpdated')]
    public function stateUpdated($data)
    {
        if($data['ulid'] != $this->upload->ulid) return;

        $this->state = $data['state'];
        if($this->state === "waiting")
        {
            $this->dispatch('reloadPage');
        }
    }

    public function next(){
        $this->count += 1;
        if($this->count > count($this->images) - 1) $this->count = (count($this->images) - 1);
    }

    public function previous(){
        if($this->count === 0)
            return;
        $this->count -= 1;
    }

    public function select($count)
    {
        if($count != $this->count)
            $this->count = $count;

        $this->js('document.getElementById("process").scrollIntoView();');
    }

    #[Computed(persist: true, seconds: 600)]
    public function images()
    {
        $images = ImageUpload::where('upload_ulid', $this->upload->ulid)->whereNot('state', 'done')->where('user_id', Auth::user()->id)->orderBy('uuid', 'desc')->get()->values();
        $this->selectedImages = [];
        foreach($images as $image)
        {
            $this->selectedImages[$image->uuid] = false;
        }
        return $images;
    }

    public function mount($ulid)
    {
        $res = Upload::find($ulid);

        if(!isset($res) || $res->user_id != Auth::user()->id)
        {
            return $this->redirectRoute('upload', navigate: true);
        }

        $this->upload = $res;

        $this->state = $this->upload->state;
    }

    #[On('imageUploadUpdated')]
    public function imageUploadUpdated()
    {
        unset($this->images);
    }


    public function finalizeUpload()
    {
        foreach($this->images as $image)
        {
            if($image->state == ImageUploadStates::FoundDuplicates->value)
            {
                $this->js("alert('Cant upload while images need response')");
                return;
            }
        }

        $this->state = UploadStates::Processing->value;
        ProcessMultipleImages::dispatch(Auth::user(), $this->upload);
    }

    public function deleteSelected()
    {
        $countNotDeleted = 0;
        foreach($this->selectedImages as $uuid => $selected)
        {
            if(!$selected) continue;

            $image = ImageUpload::find($uuid);
            if($image->user_id === Auth::user()->id)
            {
                $image->delete();
                unset($this->selectedImages[$uuid]);
            }
            else
            {
                $countNotDeleted++;
            }
        }
        if($countNotDeleted > 0)
        {
            $this->js("alert(" . $countNotDeleted . ' images were not deleted');
        }
    }

    public function uploadCancel()
    {
        CleanupUpload::dispatch(Auth::user(), $this->upload);
        return $this->redirectRoute('upload', navigate: true);
    }

    public function render()
    {
        return view('livewire.upload.process-multiple');
    }
}
