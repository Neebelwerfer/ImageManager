<?php

namespace App\Repository;

use App\Models\Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ImageRepository
{
    public function find($id)
    {
        $image = Image::find($id);

        if(isset($image)) {
            if(Auth::user()->id != $image->owner_id) {
                abort(403, 'Forbidden');
            }
            return $image;
        }
    }

    public function all()
    {
        return Image::where('owner_id', Auth::user()->id)->get();
    }

    public function delete($id)
    {
        $image = $this->find($id);
        $image->delete();
    }
}
