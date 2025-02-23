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

    #[On('echo:upload.{upload.user_id},.stateUpdated')]
    public function stateUpdated($data)
    {
        if($data['ulid'] != $this->upload->ulid) return;

        $this->state = $data['state'];

        if($data['state'] === UploadStates::FoundDuplicates->value)
        {

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
        $this->upload->delete();
        return $this->redirectRoute('upload', navigate: true);
    }

    #[Computed()]
    public function ImageUploads()
    {
        if($this->state !== UploadStates::FoundDuplicates->value)
            return ImageUpload::where('upload_ulid', $this->upload->ulid)->orderBy('created_at', 'desc')->paginate(1);
        else
            return ImageUpload::where('upload_ulid', $this->upload->ulid)->where('state', 'foundDuplicates')->orderBy('created_at', 'desc')->paginate(1);
    }

    public function render()
    {
        return view('livewire.upload.process-multiple');
    }
}
