<?php

namespace App\Services;

use App\Models\Album;
use App\Models\Image;
use App\Models\ImageCategory;
use App\Models\SharedCollections;
use App\Models\SharedImages;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AlbumService
{
    public function __construct() {
        //
    }

    public function isShared($sharedTo, $id) : bool
    {
        return app(SharedResourceService::class)->isCollectionShared($sharedTo, 'album', $id);
    }


    public function share(User $sharedBy, $id, User $sharedTo, $accessLevel) : bool
    {
        if($sharedTo->id === $sharedBy->id) return false;


        $album = Album::owned($sharedBy->id)->find($id);
        if(isset($album)) {
            if(isset($sharedTo) && !$this->isShared($sharedTo, $id)) {
                app(SharedResourceService::class)->ShareAlbum($sharedBy, $sharedTo, $id, $accessLevel);
                return true;
            }
        }
        return false;
    }

    public function stopSharing(User $sharedBy, User $sharedTo, $album_id)
    {
        $album = Album::owned($sharedBy->id)->find($album_id);
        if(isset($album))
        {
            if(!$album->is_shared) return;
            $sharedAlbum = SharedCollections::where('type', 'album')->where('shared_by_user_id', $sharedBy->id)->where('shared_with_user_id', $sharedTo->id)->first();
            if(!isset($sharedAlbum)) throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Could not find shared album');
            $sharedAlbum->delete();

            //StopSharingCategory::dispatch($sharedBy, $sharedTo, $sharedCategory);
        }
    }

    public function addImage(User $user, Image $image, Album|int $album)
    {
        if(gettype($album) == "integer")
        {
            $album = Album::ownedOrShared($user->id)->find($album);
            if($album === null)
            {
                throw new Exception('Could not find the album to add image to');
            }
        }

        DB::beginTransaction();
        try
        {

            $shared_image = null;
            if($user->id != $image->owner_id){
                $shared = SharedImages::where('shared_with_user_id', $user->id)->where('image_uuid', $image->uuid)->first();
                if(isset($shared))
                {
                    $shared_image = $shared->id;
                }
            }

            $album->images()->attach($image, ['added_by' => $user->id, 'shared_image' => $shared_image]);

            // if($album->is_shared)
            // {
            //     $users = [];
            //     foreach ($album->sharedCollections()->get() as $sharedCollection)
            //     {
            //         if($sharedCollection->shared_with_user_id != $user->id && !isset($users[$sharedCollection->shared_with_user_id]))
            //         {
            //             $users[$sharedCollection->shared_with_user_id] = $sharedCollection;
            //             app(SharedResourceService::class)->ShareImage($user, User::find($sharedCollection->shared_with_user_id), $image->uuid, $sharedCollection->level, 'album');
            //         }
            //     }
            //     if($album->owner_id != $user->id)
            //     {
            //         app(SharedResourceService::class)->ShareImage($user, User::find($album->owner_id), $image->uuid, $sharedCollection->level, 'album');
            //     }
            // }
        }
        catch (\Exception $e)
        {
            Log::debug($e);
            Log::error('Something went wrong adding image to album', ['album' => $album->id, 'image' => $image->uuid]);
            DB::rollBack();
        }
        DB::commit();
    }

    public function removeImage(User $user, Image $image, Album|int $album)
    {
        if(gettype($album) == "integer")
        {
            $album = Album::ownedOrShared($user->id)->find($album);
            if($album === null)
            {
                throw new Exception('Could not find the album to remove image from');
            }
        }

        DB::beginTransaction();
        try
        {
            $album->images()->detach($image);
            // if($album->is_shared)
            // {
            //     $users = [];
            //     foreach ($album->sharedCollections()->get() as $sharedCollection)
            //     {
            //         if($sharedCollection->shared_with_user_id != $user->id && !isset($users[$sharedCollection->shared_with_user_id]))
            //         {
            //             $users[$sharedCollection->shared_with_user_id] = $sharedCollection;
            //             app(SharedResourceService::class)->StopSharingImage($user, User::find($sharedCollection->shared_with_user_id), $image->uuid, 'album');
            //         }
            //     }
            //     if($album->owner_id != $user->id)
            //     {
            //         app(SharedResourceService::class)->StopSharingImage($user, User::find($album->owner_id), $image->uuid, 'album');
            //     }
            // }
        }
        catch (Exception $e)
        {
            Log::error('Failed to remove image from category', ['image' => $image->uuid, 'album' => $album->id, 'is_shared' => $album->is_shared]);
            Log::error($e->getMessage());
            DB::rollBack();
        }
        DB::commit();
    }

}
