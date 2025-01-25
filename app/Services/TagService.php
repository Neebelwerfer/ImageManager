<?php

namespace App\Services;

use App\Models\Tags;

class TagService
{
    public function __construct() {
        //
    }

    public function find($id) : ?Tags
    {
        return Tags::find($id);
    }

    public function getOrCreate($name) : Tags
    {
        $tag = Tags::where('name', $name)->first();

        if(isset($tag)) {
            return $tag;
        }

        $tag = Tags::create([
            'name' => $name,
        ]);

        return $tag;
    }
}

