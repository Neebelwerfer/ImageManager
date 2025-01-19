<?php

namespace App\Services;

use App\Models\ImageCategory;
use Illuminate\Support\Facades\Auth;

class CategoryService
{
    public function __construct() {
        //
    }

    public function create($name) : ImageCategory
    {
        $cat = ImageCategory::owned()->where('name', $name)->first();

        if(isset($cat)) {
            return $cat;
        }

        $cat = ImageCategory::create([
            'name' => $name,
            'user_id' => Auth::user()->id
        ]);

        return $cat;
    }

    public function delete($id) : void
    {
        $cat = ImageCategory::owned()->find($id);
        if(isset($cat)) {
            $cat->delete();
        }
    }
}
