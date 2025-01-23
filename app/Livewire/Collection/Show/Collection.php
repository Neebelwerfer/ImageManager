<?php

namespace App\Livewire\Collection\Show;

use App\Models\Image;
use App\Component\CollectionView;
use App\Models\Album;
use App\Models\ImageCategory;
use App\Models\SharedResources;
use App\Support\Shared\AccessLevel;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;

#[Layout('layouts.collection')]
class Collection extends CollectionView
{
    public $minRating = 0;

    public $showOptions = false;

    #[Locked()]
    public $collectionID;

    #[Locked()]
    public AccessLevel $accessLevel = AccessLevel::view;

    public function mount($collectionType, $collectionID = null)
    {
        $this->showBackButton = true;

        $this->collectionType = $collectionType;
        $this->collectionID = $collectionID;


        if($collectionType != 'categories' && $collectionType != 'albums') {
            abort(404, 'Collection not found');
        }

        if($collectionType == 'categories') {
            if(ImageCategory::ownedOrShared()->find($collectionID) == null) {
                abort(404, 'Category not found');
            }

            if(!ImageCategory::owned()->exists($collectionID)) {
                $resource = SharedResources::where('resource_id', $collectionID)->where('type', 'category')->first();
                $this->accessLevel = $resource->level;
            }
        }

        if($collectionType == 'albums') {
            if(Album::ownedOrShared()->find($collectionID) == null) {
                abort(404, 'Category not found');
            }

            if(!Album::owned()->exists($collectionID)) {
                $resource = SharedResources::where('resource_id', $collectionID)->where('type', 'category')->first();
                $this->accessLevel = $resource->level;
            }
        }

        $this->updateImages();
    }

    public function goBack()
    {
        return redirect()->route('collection.show', $this->collectionType);
    }

    #[Computed()]
    public function images()
    {
        if($this->collectionType == 'categories') {
            return Image::whereHas('category', function ($query) {
                $query->where('category_id', $this->collectionID);
            })->paginate(20);
        }
        else if($this->collectionType == 'albums') {
            return Image::whereHas('albums', function ($query) {
                $query->where('album_id', $this->collectionID);
            })->paginate(20);
        }
    }

}
