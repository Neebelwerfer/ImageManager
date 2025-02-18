<?php

namespace App\Livewire\Collection\Show;

use App\Models\Image;
use App\Component\CollectionView;
use App\DTO\Traits\SearchTraitDTO;
use App\DTO\Traits\TraitDTO;
use App\Models\Album;
use App\Models\ImageCategory;
use App\Models\SharedCollections;
use App\Models\Tags;
use App\Models\Traits;
use App\Support\Shared\AccessLevel;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;

#[Layout('layouts.collection')]
class Collection extends CollectionView
{
    public $minRating = 0;

    public $showOptions = false;

    #[Locked()]
    public $collectionID;
    #[Locked]
    public $collectionName;

    #[Locked]
    public $searchTraits = [];

    #[On('traitSelected')]
    public function traitSelected($id)
    {
        $trait = Traits::owned(Auth::user()->id)->find($id);
        $this->searchTraits[$id] = ['name' => $trait->name, 'value' => $trait->default, 'min' => $trait->min, 'max' => $trait->max];//new SearchTraitDTO($trait, $trait->default);
    }

    public function mount($collectionType, $collectionID = null)
    {
        $this->collectionType = $collectionType;
        $this->collectionID = $collectionID;

        if($collectionType != 'categories' && $collectionType != 'albums') {
            abort(404, 'Collection not found');
        }

        if($collectionType == 'categories') {
            $res = ImageCategory::ownedOrShared(Auth::user()->id)->find($collectionID);
            if( $res === null) {
                abort(404, 'Category not found');
            }

            $this->collectionName = $res->name;
        }

        if($collectionType == 'albums') {
            $res = Album::ownedOrShared(Auth::user()->id)->find($collectionID);
            if($res === null) {
                abort(404, 'album not found');
            }

            $this->collectionName = $res->name;
        }

        $this->updateImages();
    }

    public function goBack()
    {
        $route = $this->collectionType == 'albums' ? 'album' : 'category';
        return redirect()->route('collection.' . $route, );
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

            $images = Tags::sortTags($query, $this->tags)->paginate(20);
        }
        else if($this->collectionType == 'albums') {
            $query = Image::whereHas('albums', function ($query) {
                $query->where('album_id', $this->collectionID);
            });

            $images = Tags::sortTags($query, $this->tags)->paginate(20);
        }

        foreach($images->items() as $image)
        {
            $this->selectedImages[$image->uuid] = false;
        }

        return $images;
    }
}
