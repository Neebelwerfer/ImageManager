<?php

namespace App\Repository;

use App\Models\ImageTag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TagRepository
{
    public function find($id)
    {
        $image = ImageTag::where('owner_id', Auth::user()->id)->find($id);

        if(isset($image)) {
            return $image;
        }
    }

    public function all()
    {
        return ImageTag::where('owner_id', Auth::user()->id)->get();
    }

    public function delete($id)
    {
        $image = $this->find($id);
        $image->delete();
    }
}
