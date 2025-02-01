<?php

namespace App\Services;

use App\Models\ImageCategory;
use App\Models\SharedResources;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CategoryService
{
    public function __construct() {

    }

    public function create($name) : ImageCategory
    {
        $cat = ImageCategory::owned()->where('name', $name)->first();

        if(isset($cat)) {
            return $cat;
        }

        $cat = ImageCategory::create([
            'name' => $name,
            'owner_id' => Auth::user()->id
        ]);

        return $cat;
    }

    public function isShared($sharedTo, $id) : bool
    {
        return app(SharedResourceService::class)->isShared($sharedTo, 'category', $id);
    }


    public function share($id, User $sharedTo, $accessLevel) : bool
    {
        $cat = ImageCategory::owned()->find($id);
        if(isset($cat)) {
            if(isset($sharedTo) && $sharedTo->id != Auth::user()->id && !$this->isShared($sharedTo, $id)) {
                app(SharedResourceService::class)->Share($sharedTo, 'category', $id, $accessLevel);
                $cat->is_shared = true;
                return true;
            }
        }
        return false;
    }

    public function stopSharing($sharedTo, $id)
    {

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
