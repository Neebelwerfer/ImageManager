<?php

namespace App\Livewire\Collection;

use App\Component\CollectionView;
use App\Models\Image;
use App\Models\Tags;
use App\Services\ImageService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\Attributes\Js;
use Livewire\WithPagination;

#[Layout('layouts.collection')]
class Images extends Component
{
    use WithPagination;

    #[Url('tags', except:'')]
    public $tags = '';
    public $minRating = 0;

    public $showOptions = false;
    public $collection;

    public $editMode = false;
    public $selectedImages = [];

    public function addSelectedToCategory()
    {
        foreach($this->selectedImages as $uuid => $selected)
        {
            if(!$selected) continue;

            $image = Image::find($uuid);
            // $imageService = app(ImageService::class);
            // if($imageService->canDeleteImage(Auth::user(), $image))
            // {
            //     $imageService->deleteImage($image);
            // }
        }
    }

    public function addSelectedToAlbum()
    {
        foreach($this->selectedImages as $uuid => $selected)
        {
            if(!$selected) continue;

            $image = Image::find($uuid);
            // $imageService = app(ImageService::class);
            // if($imageService->canDeleteImage(Auth::user(), $image))
            // {
            //     $imageService->deleteImage($image);
            // }
        }
    }

    public function deleteSelected()
    {
        $countNotDeleted = 0;
        foreach($this->selectedImages as $uuid => $selected)
        {
            if(!$selected) continue;

            $imageService = app(ImageService::class);
            $image = Image::find($uuid);
            if($imageService->canDeleteImage(Auth::user(), $image))
            {
                $imageService->deleteImage($image);
                unset($this->selectedImages[$uuid]);
            }
            else
            {
                $countNotDeleted++;
            }
        }
        if($countNotDeleted > 0)
        {

        }
    }

    public function filter()
    {
        unset($this->images);
    }

    #[Computed()]
    public function images()
    {
        $images = Tags::sortTags(Image::ownedOrShared(Auth::user()->id), $this->tags)->paginate(20);

        $this->selectedImages = [];
        foreach($images->items() as $image)
        {
            $this->selectedImages[$image->uuid] = false;
        }

        return $images;
    }

    public function mount()
    {

    }

    public function render()
    {
        return view('livewire.collection.images');
    }
}
