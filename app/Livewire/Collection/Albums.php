<?php

namespace App\Livewire\Collection;

use Livewire\Component;
use App\Models\Album;
use App\Models\Image;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

#[Layout('layouts.collection')]
class Albums extends Component
{

    #[Url('name', except:"")]
    public $name="";

    public function getImageFromAlbum(Album $album)
    {
        $key = "album-thumbnail-".$album->id;
        $imageUuid = Cache::get($key);
        $image = Image::find($imageUuid);
        if($image === null)
        {
            $image = $album->images()->first();
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
        return view('livewire.collection.albums',
            [
                'albums' => Album::ownedOrShared()->where('name', 'like', '%'.$this->name.'%')->paginate(20),
            ]);
    }
}
