<?php

namespace App\Livewire\Modal\Manage;

use App\Models\Album;
use App\Models\SharedResources;
use Livewire\Attributes\Computed;
use LivewireUI\Modal\ModalComponent;

class EditAlbum extends ModalComponent
{

    public Album $album;

    public $name;

    public function save()
    {
        $this->album->update(['name' => $this->name]);
        $this->closeModal();
    }

    public function delete()
    {
        $this->album->delete();
        $this->closeModal();
    }

    public function removeShared($id) {
        SharedResources::find($id)->delete();
        $this->closeModal();
    }

    #[Computed()]
    public function sharedWith() {
        return SharedResources::where('resource_id', $this->album->id)->where('type', 'album')->get();
    }

    public function mount($album)
    {
        $this->album = Album::find($album);
    }

    public function render()
    {
        return view('livewire.modal.manage.edit-album');
    }
}
