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
        $cat = ImageCategory::withoutGlobalScopes()->where('name', $name)->first();

        if(isset($cat)) {
            if(!$cat->ownership()->where('owner_id', Auth::user()->id)->exists()) {
                $cat->ownership()->attach(Auth::user()->id);
            }
            return $cat;
        }

        $cat = ImageCategory::create([
            'name' => $name,
        ]);

        $cat->ownership()->attach(Auth::user()->id);

        return $cat;
    }

    public function delete($id) : void
    {
        $cat = ImageCategory::find($id);
        if(isset($cat)) {
            $cat->ownership()->detach(Auth::user()->id);
        }
    }
}
