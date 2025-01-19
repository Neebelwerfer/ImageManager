<?php

namespace App\Services;

use App\Models\ImageCategory;
use App\Models\User;
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

    public function share($id, $email) : void
    {
        $cat = ImageCategory::owned()->find($id);
        if(isset($cat)) {
            $sharedTo = User::where('email', $email)->first();

            if(isset($sharedTo) && $sharedTo->id != Auth::user()->id && $sharedTo->sharedCategories()->find($cat->id) == null) {

            }
        }
    }

    public function delete($id) : void
    {
        $cat = ImageCategory::owned()->find($id);
        if(isset($cat)) {
            $cat->delete();
        }
    }

    public function find($id) : ?ImageCategory
    {
        return ImageCategory::owned()->find($id);
    }

    public function findWhereOwnedAndShared($id) : ?ImageCategory
    {
        return ImageCategory::ownedOrShared()->find($id);
    }
}
