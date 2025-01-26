<?php

namespace App\Livewire\Collection;

use Livewire\Component;
use App\Models\Album;
use App\Models\Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class Albums extends Component
{

    public function getImageFromAlbum(Album $album)
    {
        $key = "album-thumbnail-".$album->id;
        $imageUuid = Cache::get($key);
        $image = Image::find($imageUuid);
        if($image === null)
        {
            $image = $album->images()->first();
            Cache::set($key, $image->uuid, 3600);
        }
        return $image;
    }

    public function render()
    {
        return view('livewire.collection.albums',
            [
                'albums' => Album::ownedOrShared()->paginate(20),
            ]);
    }
}
