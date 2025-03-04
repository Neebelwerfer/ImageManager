<?php

namespace App\Livewire\Upload;

use App\DTO\ImageTraitDTO;
use App\Models\ImageUpload;
use App\Models\Tags;
use App\Models\Traits;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Component;

class ProcessImage extends Component
{
    public ImageUpload $imageUpload;

    #[Modelable]
    public $imageData = [];

    public $traits = [];
    public $error = false;
    public $state = "waiting";

    #[On('categorySelected')]
    public function categorySelected($selection)
    {
        if(array_diff_assoc($this->imageData['category'], $selection) == []) return;
        $this->imageData['category'] = $selection;
        $this->imageData['isDirty'] = true;
    }


    #[On('albumSelected')]
    public function albumSelected($selection)
    {
        $id = $selection['id'];
        $name = $selection['name'];

        if(isset($this->imageData['albums'][$id])) return;

        $this->imageData['albums'][$id] = ['name' => $name, 'id' => $id];
        $this->imageData['isDirty'] = true;
    }

    #[On('tagSelected')]
    public function tagSelected($data)
    {
        $name = $data['name'];
        $personal = $data['personal'];
        if (isset($this->imageData['tags'][$name])) {
            return;
        }

        $this->imageData['tags'][$name] = ['name' => $name, 'personal' => $personal];
        $this->imageData['isDirty'] = true;
    }

    public function setupTraits() {
        $traits = Traits::owned(Auth::user()->id)->get();
        foreach($traits as $trait) {
            $at = new ImageTraitDTO($trait, Auth::user()->id, $trait->default);
            $this->traits[$trait->id] = $at;
        }
    }

    public function accept()
    {
        $this->dispatch('imageDuplicatesDeleted', ['uuid' => $this->imageData['uuid']]);
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
    }

    public function render()
    {
        return view('livewire.upload.process-image');
    }
}
