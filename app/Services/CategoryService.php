<?php

namespace App\Services;

use App\Jobs\StopSharingCategory;
use App\Models\ImageCategory;
use App\Models\SharedCollections;
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


    public function share(User $sharedBy, $category_id, User $sharedTo, $accessLevel) : bool
    {
        if($sharedTo->id === $sharedBy->id) return false;

        $cat = ImageCategory::owned($sharedBy->id)->find($category_id);
        if(isset($cat)) {
            if(isset($sharedTo) && !$this->isShared($sharedTo, $category_id)) {
                app(SharedResourceService::class)->ShareCategory($sharedBy, $sharedTo, $category_id, $accessLevel);
                return true;
            }
        }
        return false;
    }

    public function stopSharing(User $sharedBy, User $sharedTo, $category_id)
    {
        $cat = ImageCategory::owned($sharedBy->id)->find($category_id);
        if(isset($cat))
        {
            if(!$cat->is_shared) return;
            $sharedCategory = SharedCollections::where('type', 'category')->where('shared_by_user_id', $sharedBy)->where('shared_with_user_id', $sharedTo)->first();
            if(!isset($sharedCategory)) throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Could not find shared category');
            StopSharingCategory::dispatch();
        }
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
