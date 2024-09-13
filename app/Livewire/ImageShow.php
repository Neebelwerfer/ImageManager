<?php

namespace App\Livewire;

use App\Models\Image;
use App\Models\ImageCategory;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.app')]
class ImageShow extends Component
{
    public $image;

    public $showCategory = false;
    public $showTags = false;

    #[On('closeModal')]
    public function closeModal()
    {
        $this->showCategory = false;
        $this->showTags = false;
    }

    #[On('categorySelected')]
    public function categorySelected($category)
    {
        if($category == -1) {
            $this->image->category_id = null;
            $this->image->save();
            return;
        }

        $category = ImageCategory::find($category);

        if(isset($category)) {
            $this->image->category_id = $category->id;
            $this->image->save();
            return;
        }
    }

    public function removeCategory()
    {
        $this->image->category_id = null;
        $this->image->save();
    }

    public function toggleCategoryModal()
    {
        $this->showCategory = !$this->showCategory;
    }

    public function mount($image)
    {
        if(!isset($image) or empty($image)) {
            abort(404);
        }
        $this->image = Image::find($image);
        if(!isset($this->image)) {
            abort(404);
        }
    }

    public function delete()
    {
        $this->image->delete();
        return $this->redirect(route('collection.show', 'images'), true);
    }

    public function render()
    {
        return view('livewire.image-show');
    }
}
