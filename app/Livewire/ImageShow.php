<?php

namespace App\Livewire;

use App\Models\Image;
use App\Models\ImageCategory;
use App\Models\ImageTag;
use Illuminate\Support\Facades\Auth;
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

    #[On('tagSelected')]
    public function tagSelected($tag)
    {
        $tag = ImageTag::find($tag);

        if(isset($tag) and $this->image->tags()->find($tag->id) == null) {
            $this->image->tags()->save($tag);
            return;
        }
    }

    public function removeTag($tagID)
    {
        $tag = ImageTag::find($tagID);

        if(!isset($tag)) {
            return;
        }

        $this->image->tags()->detach($tag);
    }

    public function toggleTag()
    {
        $this->showTags = !$this->showTags;
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

    public function back()
    {
        return $this->redirect(url()->previous(), true);
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

        if(Auth::user()->id != $this->image->owner_id) {
            abort(403);
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
