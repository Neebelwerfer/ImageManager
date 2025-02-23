<?php

namespace App\Livewire\Upload;

use App\Models\ImageUpload;
use App\Models\Upload;
use App\Support\Enums\UploadStates;
use Exception;
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

    #[On('echo:upload.{upload.user_id},.stateUpdated')]
    public function stateUpdated($data)
    {
        if($data['ulid'] != $this->upload->ulid) return;

        $this->state = $data['state'];

        if($data['state'] === UploadStates::FoundDuplicates->value)
        {

        }
    }

    public function select($uuid)
    {
        if($uuid === $this->selectedUUID)
            $this->selectedUUID = "";
        else
            $this->selectedUUID = $uuid;
    }

    #[Computed()]
    public function images()
    {
        if($this->state == UploadStates::FoundDuplicates)
        {
            return ImageUpload::where('upload_ulid', $this->upload->ulid)->where('state', 'foundDuplicates')->where('user_id', Auth::user()->id)->get();
        }
        else
        {
            return ImageUpload::where('upload_ulid', $this->upload->ulid)->where('user_id', Auth::user()->id)->get();
        }
    }

    public function mount($ulid)
    {
        $this->upload = Upload::find($ulid);

        if(!isset($this->upload) || $this->upload->user_id != Auth::user()->id)
        {
            return $this->redirectRoute('upload', navigate: true);
        }

        $this->state = $this->upload->state;
    }

    public function finalizeUpload()
    {
        $this->state = UploadStates::Scanning->value;
    }

    public function uploadCancel()
    {
        foreach($this->upload->images as $imageUpload)
        {
            $imageUpload->delete();
        }
        $this->upload->delete();
        return $this->redirectRoute('upload', navigate: true);
    }

    public function render()
    {
        return view('livewire.upload.process-multiple');
    }
}
