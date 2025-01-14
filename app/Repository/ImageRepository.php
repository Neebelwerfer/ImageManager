<?php

namespace App\Repository;

use App\Models\Image;
use App\Support\ImagePaths;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;

class ImageRepository
{
    public function index() : Collection
    {
        return Image::where('owner_id', Auth::user()->id)->get();
    }

    public function pagedIndex($pageSize = 20)
    {
        return Image::where('owner_id', Auth::user()->id)->paginate($pageSize);
    }

    public function lazyIndex() : LazyCollection
    {
        return Image::where('owner_id', Auth::user()->id)->lazy();
    }

    public function find($id)
    {
        $image = Image::find($id);

        if(isset($image)) {
            if(Auth::user()->id != $image->owner_id) {
                return null;
            }
            return $image;
        }

        return null;
    }

    public function findOrFail($id)
    {
        $image = Image::find($id);

        if(isset($image)) {
            if(Auth::user()->id != $image->owner_id) {
                abort(403, 'Forbidden');
            }
            return $image;
        }

        return abort(404, 'Image not found');
    }

    public function all()
    {
        return Image::where('owner_id', Auth::user()->id)->get();
    }

    public function delete($id)
    {
        $image = $this->find($id);

        if(!isset($image))
        {
            return;
        }

        Storage::disk('local')->delete($image->getImagePath());
        Storage::disk('local')->delete($image->getThumbnailPath());

        $image->delete();
    }
}
