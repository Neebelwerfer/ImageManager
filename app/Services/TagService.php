<?php

namespace App\Services;

use App\Models\ImageTag;
use Illuminate\Support\Facades\Auth;

class TagService
{
    public function __construct() {
        //
    }

    public function find($id) : ?ImageTag
    {
        return ImageTag::find($id);
    }

    public function create($name) : ImageTag
    {
        $tag = ImageTag::where('name', $name)->first();

        if(isset($tag)) {
            return $tag;
        }

        $tag = ImageTag::create([
            'name' => $name,
            'user_id' => Auth::user()->id
        ]);

        return $tag;
    }

    public function delete($id) : void
    {
        $cat = ImageTag::owned()->find($id);
        if(isset($cat)) {
            $cat->delete();
        }
    }
}

