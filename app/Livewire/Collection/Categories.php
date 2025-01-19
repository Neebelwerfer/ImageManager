<?php

namespace App\Livewire\Collection;

use App\Models\ImageCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Categories extends Component
{
    public $name = '';

    public function getImageFromCategory(ImageCategory $category)
    {
        $image = $category->images->sortBy('rating', SORT_NUMERIC, true)->first();
        return $image;
    }

    public function render()
    {
        return view('livewire.collection.categories',
            [
                'categories' => ImageCategory::ownedOrShared()->where('name', 'like', '%' . $this->name . '%')->paginate(20)
            ]);
    }
}
