<?php

namespace App\Livewire\Upload;

use App\Models\ImageUpload;
use App\Models\Upload;
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

    #[On('echo:upload.{upload.user_id},.stateUpdated')]
    public function stateUpdated($data)
    {
        if($data['ulid'] != $this->upload->ulid) return;

        $this->state = $data['state'];

        if($data['state'] === UploadStates::FoundDuplicates->value)
        {

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
        if($this->state == UploadStates::FoundDuplicates)
        {
            return ImageUpload::where('upload_ulid', $this->upload->ulid)->where('state', 'foundDuplicates')->orderBy('uuid', 'desc')->where('user_id', Auth::user()->id)->get()->values();
        }
        else
        {
            return ImageUpload::where('upload_ulid', $this->upload->ulid)->where('user_id', Auth::user()->id)->orderBy('uuid', 'desc')->get()->values();
        }
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
