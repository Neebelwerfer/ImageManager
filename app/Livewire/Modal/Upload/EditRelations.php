<?php

namespace App\Livewire\Modal\Upload;

use App\Livewire\ImageUpload;
use App\Models\Album;
use App\Models\ImageCategory;
use App\Models\ImageTag;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use LivewireUI\Modal\ModalComponent;

class EditRelations extends ModalComponent
{

    public $type = 'category';
    public $noneOption = false;

    public function mount(string $type = 'category', $noneOption = false)
    {
        $this->type = $type;
        $this->noneOption = $noneOption;
    }

    public function selectEntry(int $id)
    {

        $this->dispatch($this->getEventName($this->type), $id);
        $this->closeModal();
    }

    private function getEventName(string $type) {
        switch ($type) {
            case 'category':
                return 'categorySelected';
            case 'tag':
                return 'tagSelected';
            case 'album':
                return 'albumSelected';
        }
    }

    #[On('refresh')]
    public function refresh() {
        unset($this->entries);
    }

    #[Computed()]
    public function entries()
    {
        if($this->type == 'category') {
            return ImageCategory::all()->paginate(20);
        }
        else if ($this->type == 'tag') {
            return ImageTag::all()->paginate(20);
        }
        else if ($this->type == 'album') {
            return Album::where('owner_id', Auth::user()->id)->paginate(20);
        }
    }

    public function render()
    {
        return view('livewire.modal.upload.edit-relations');
    }
}
