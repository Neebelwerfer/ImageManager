<?php

namespace App\Livewire\Collection\Show;

use App\Models\ImageCategory;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.collection')]
class Category extends Component
{

    public $category;

    public function mount($categoryID)
    {
        $this->category = ImageCategory::find($categoryID);
        if(!isset($this->category)) {
            abort(404);
        }
    }

    public function render()
    {
        return view('livewire.collection.show.category');
    }
}
