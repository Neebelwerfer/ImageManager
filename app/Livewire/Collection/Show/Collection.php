<?php

namespace App\Livewire\Collection\Show;

use App\Models\Image;
use App\Component\CollectionView;
use App\Models\Album;
use App\Models\ImageCategory;
use App\Models\SharedResources;
use App\Models\Tags;
use App\Support\Shared\AccessLevel;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;

#[Layout('layouts.collection')]
class Collection extends CollectionView
{
    public $minRating = 0;

    public $showOptions = false;

    #[Locked()]
    public $collectionID;
    #[Locked]
    public $collectionName;

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
            $res = ImageCategory::ownedOrShared()->find($collectionID);
            if( $res === null) {
                abort(404, 'Category not found');
            }

            if(!$res->owner_id === Auth::user()->id) {
                $resource = SharedResources::where('resource_id', $collectionID)->where('type', 'category')->first();
                $this->accessLevel = $resource->level;
            }
            $this->collectionName = $res->name;
        }

        if($collectionType == 'albums') {
            $res = Album::ownedOrShared()->find($collectionID);
            if($res === null) {
                abort(404, 'Category not found');
            }

            if(!$res->owner_id === Auth::user()->id) {
                $resource = SharedResources::where('resource_id', $collectionID)->where('type', 'category')->first();
                $this->accessLevel = $resource->level;
            }
            $this->collectionName = $res->name;
        }

        $this->updateImages();
    }

    public function goBack()
    {
        return redirect()->route('collection.show', $this->collectionType);
    }

    #[On('collectionEdited')]
    public function collectionEdited($name)
    {
        $this->collectionName = $name;
        $this->dispatch('reloadPage');
    }

    #[On('collectionDeleted')]
    public function collectionDeleted()
    {
        return $this->goBack();
    }


    #[Computed()]
    public function images()
    {
        if($this->collectionType == 'categories') {
            $query = Image::whereHas('category', function ($query) {
                $query->where('category_id', $this->collectionID);
            });

            return Tags::sortTags($query, $this->tags)->paginate(20);
        }
        else if($this->collectionType == 'albums') {
            $query = Image::whereHas('albums', function ($query) {
                $query->where('album_id', $this->collectionID);
            });

            return Tags::sortTags($query, $this->tags)->paginate(20);
        }
    }

}
