<?php

namespace App\Livewire\ImageShow;

use App\Models\Image;
use App\Models\ImageCategory;
use App\Models\ImageTag;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class Options extends Component
{
    public Image $image;

    public $showCategory = false;
    public $showTags = false;
    public $showRating = false;


    #[On('closeModal')]
    public function closeModal()
    {
        $this->showCategory = false;
        $this->showTags = false;
        $this->showRating = false;
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

    public function removeCategory()
    {
        $this->image->category_id = null;
        $this->image->save();
    }

    public function toggleTag()
    {
        $this->showTags = !$this->showTags;
    }

    public function toggleCategoryModal()
    {
        $this->showCategory = !$this->showCategory;
    }

    public function toggleRatingModal()
    {
        $this->showRating = !$this->showRating;
    }

    public function delete()
    {
        $this->dispatch('deleteImage');
    }

    #[On('imageUpdated')]
    public function imageUpdated($image)
    {
        $this->image = Image::find($image);
    }

    public function render()
    {

        return view('livewire.image-show.options');
    }
}
