<?php

namespace App\Livewire\Modal\Manage;

use App\Models\Album;
use App\Models\ImageCategory;
use App\Models\SharedResources;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use LivewireUI\Modal\ModalComponent;

class EditCollection extends ModalComponent
{
    public $collection;

    public $name;

    #[Locked]
    public $collectionID;
    #[Locked]
    public $collectionType;

    public function save()
    {
        $this->collection->update(['name' => $this->name]);
        $this->dispatch('collectionEdited', $this->name);
        $this->closeModal();
    }

    public function delete()
    {
        $this->collection->delete();
        $this->dispatch('collectionDeleted', $this->name);
        $this->closeModal();
    }

    public function mount($collectionId, $collectionType)
    {
        $this->collectionID = $collectionId;
        switch($collectionType)
        {
            case 'category':
            case 'categories':
                $this->collection = ImageCategory::find($collectionId);
                $this->collectionType = "category";
                break;
            case 'album':
            case 'albums':
                $this->collection = Album::find($collectionId);
                $this->collectionType = "album";
                break;
        }
        if(!isset($this->collection))
            $this->closeModal();
    }

    public function render()
    {
        return view('livewire.modal.manage.edit-collection');
    }
}
