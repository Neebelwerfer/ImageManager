<?php

namespace App\Livewire\Collection;

use App\Models\Image;
use App\Models\ImageCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Categories extends Component
{
    public $name = '';

    public function getImageFromCategory(ImageCategory $category)
    {
        $key = "category-thumbnail-".$category->id;
        $imageUuid = Cache::get($key);
        $image = Image::find($imageUuid);
        if($image === null)
        {
            $image = $category->images()->first();
            if($image=== null)
            {
                return null;
            }
            Cache::set($key, $image->uuid, 3600);
        }
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
