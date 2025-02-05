<?php

namespace App\Services;

use App\Models\ImageCategory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CategoryService
{
    public function __construct() {

    }

    public function create(User $user, $name) : ImageCategory
    {
        $cat = ImageCategory::owned($user->id)->where('name', $name)->first();

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
        return app(SharedResourceService::class)->isCollectionShared($sharedTo, 'category', $id);
    }


    public function share(User $sharedBy, $id, User $sharedTo, $accessLevel) : bool
    {
        if($sharedTo->id === $sharedBy->id) return true;

        $cat = ImageCategory::owned($sharedBy->id)->find($id);
        if(isset($cat)) {
            if(isset($sharedTo) && !$this->isShared($sharedTo, $id)) {
                app(SharedResourceService::class)->ShareCollection($sharedBy, $sharedTo, 'category', $id, $accessLevel);
                $cat->is_shared = true;
                return true;
            }
        }
        return false;
    }

    public function stopSharing($sharedTo, $id)
    {

    }

    public function delete(User $user, $id) : void
    {
        $cat = ImageCategory::owned($user)->find($id);
        if(isset($cat)) {
            $cat->delete();
        }
    }

    public function find(User $user, $id) : ?ImageCategory
    {
        return ImageCategory::owned($user)->find($id);
    }

    public function findWhereOwnedAndShared(User $user, $id) : ?ImageCategory
    {
        return ImageCategory::ownedOrShared($user)->find($id);
    }
}
