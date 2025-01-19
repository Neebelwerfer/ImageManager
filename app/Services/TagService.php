<?php

namespace App\Services;

use App\Models\ImageTag;
use Illuminate\Support\Facades\Auth;

class TagService
{
    public function __construct() {
        //
    }

    public function create($name) : ImageTag
    {
        $tag = ImageTag::withoutGlobalScopes()->where('name', $name)->first();

        if(isset($tag)) {
            if(!$tag->ownership()->where('owner_id', Auth::user()->id)->exists()) {
                $tag->ownership()->attach(Auth::user()->id);
            }
            return $tag;
        }

        $tag = ImageTag::create([
            'name' => $name,
        ]);

        $tag->ownership()->attach(Auth::user()->id);

        return $tag;
    }

    public function delete($id) : void
    {
        $tag = ImageTag::find($id);
        if(isset($tag)) {
            $tag->ownership()->detach(Auth::user()->id);
        }
    }
}

