<?php

namespace App\Services;

use App\Jobs\StopSharingCategory;
use App\Models\Image;
use App\Models\ImageCategory;
use App\Models\SharedCollections;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    public function addImageToCategory(User $user, Image $image, ImageCategory $imageCategory)
    {
        DB::beginTransaction();
        try
        {
            $imageCategory->images()->save($image);
            if($imageCategory->is_shared)
            {
                $users = [];
                foreach ($imageCategory->sharedCollections()->get() as $sharedCollection)
                {
                    if($sharedCollection->shared_with_user_id != $user->id && !isset($users[$sharedCollection->shared_with_user_id]))
                    {
                        $users[$sharedCollection->shared_with_user_id] = $sharedCollection;
                        app(SharedResourceService::class)->ShareImage($user, User::find($sharedCollection->shared_with_user_id), $image->uuid, $sharedCollection->level, 'category');
                    }
                    if($imageCategory->owner_id != $user->id)
                    {
                        app(SharedResourceService::class)->ShareImage($user, User::find($imageCategory->owner_id), $image->uuid, $sharedCollection->level, 'category');
                    }
                }
            }
        }
        catch (\Exception $e)
        {
            Log::debug($e);
            Log::error('Something went wrong adding image to category', ['category' => $imageCategory->id, 'image' => $image->uuid]);
            DB::rollBack();
        }
        DB::commit();
    }

    public function removeImageFromCategory(User $user, Image $image, ImageCategory $imageCategory = null)
    {
        if($imageCategory === null)
        {
            $imageCategory = $image->category;
            if($imageCategory == null)
                throw new ModelNotFoundException("Could not find category to remove image from");
        }

        DB::beginTransaction();
        try
        {
            $image->category_id = null;
            $image->save();

            if($imageCategory->is_shared)
            {
                $users = [];
                foreach ($imageCategory->sharedCollections()->get() as $sharedCollection)
                {
                    if($sharedCollection->shared_with_user_id != $user->id && !isset($users[$sharedCollection->shared_with_user_id]))
                    {
                        $users[$sharedCollection->shared_with_user_id] = $sharedCollection;
                        app(SharedResourceService::class)->StopSharingImage($user, User::find($sharedCollection->shared_with_user_id), $image->uuid, $sharedCollection->level, 'category');
                    }
                    if($imageCategory->owner_id != $user->id)
                    {
                        app(SharedResourceService::class)->StopSharingImage($user, User::find($imageCategory->owner_id), $image->uuid, $sharedCollection->level, 'category');
                    }
                }
            }
        }
        catch (Exception $e)
        {
            Log::error('Failed to remove image from category', ['image' => $image->uuid, 'category' => $imageCategory->id, 'is_shared' => $imageCategory->is_shared]);
            Log::error($e->getMessage());
            DB::rollBack();
        }
        DB::commit();
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
