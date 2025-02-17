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
use PhpParser\Node\Stmt\Continue_;

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

    #[Js]
    public function changeSelectMode(){
        return <<<'JS'
            $wire.editMode = !$wire.editMode;

            if($wire.editMode == false)
            {
                $wire.selectedImages = [];
            }
        JS;
    }

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
        foreach($this->selectedImages as $uuid => $selected)
        {
            if(!$selected) continue;

            $imageService = app(ImageService::class);
            $image = Image::find($uuid);
            if($imageService->canDeleteImage(Auth::user(), $image))
            {
                $imageService->deleteImage($image);
            }
        }
    }

    public function filter()
    {
        throw new Exception(count($this->selectedImages));
    }

    public function render()
    {
        return view('livewire.collection.images',
            [
                'images' => Tags::sortTags(Image::ownedOrShared(Auth::user()->id), $this->tags)->paginate(20)
            ]);
    }
}
