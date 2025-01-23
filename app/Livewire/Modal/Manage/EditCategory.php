<?php

namespace App\Livewire\Modal\Manage;

use App\Models\ImageCategory;
use App\Models\SharedResources;
use Livewire\Attributes\Computed;
use Livewire\Component;
use LivewireUI\Modal\ModalComponent;

class EditCategory extends ModalComponent
{
    public ImageCategory $category;

    public $name;

    public function save()
    {
        $this->category->update(['name' => $this->name]);
        $this->closeModal();
    }

    public function delete()
    {
        $this->category->delete();
        $this->closeModal();
    }

    public function removeShared($id) {
        SharedResources::find($id)->delete();
        $this->closeModal();
    }

    #[Computed()]
    public function sharedWith() {
        return SharedResources::where('resource_id', $this->category->id)->where('type', 'category')->get();
    }

    public function mount($category)
    {
        $this->category = ImageCategory::find($category);
    }

    public function render()
    {
        return view('livewire.modal.manage.edit-category');
    }
}
