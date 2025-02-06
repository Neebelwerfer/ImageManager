<?php

namespace App\Services;

use App\Jobs\ShareCategoryImages;
use App\Models\Album;
use App\Models\Image;
use App\Models\ImageCategory;
use App\Models\SharedCollections;
use App\Models\SharedImages;
use App\Models\SharedResources;
use App\Models\SharedSource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SharedResourceService
{
    public function __construct() {
        //
    }

    public function isCollectionShared(User $sharedTo, $collectionType, $collectionId)
    {
        return SharedCollections::where('type', $collectionType)->where('resource_id', $collectionId)->where('shared_with_user_id', $sharedTo->id)->exists();
    }

    public function isImageShared(User $sharedTo, $imageUuid)
    {
        return SharedImages::where('image_uuid', $imageUuid)->where('shared_with_user_id', $sharedTo->id);
    }

    public function ShareCategory(User $sharedBy, User $sharedTo, $id, $accessLevel)
    {
        $category = ImageCategory::find($id);
        if(!isset($category))
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Could Not find Model');


        ShareCategoryImages::dispatch($sharedBy, $sharedTo, $category, $accessLevel);
    }

    public function ShareAlbum(User $sharedBy, User $sharedTo, $id, $accessLevel)
    {
        $type = 'album';
        $album = Album::find($id);
        if(!isset($album))
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Could Not find Model');

        $album->is_shared = true;
        $album->save();

        $shared_collection = new SharedCollections();
        $shared_collection->resource_id = $id;
        $shared_collection->type = $type;
        $shared_collection->shared_by_user_id = $sharedBy->id;
        $shared_collection->shared_with_user_id = $sharedTo->id;
        $shared_collection->level = $accessLevel;
        $shared_collection->save();
    }


    public function ShareImage(User $sharedBy, User $sharedTo, $uuid, $accessLevel, $source)
    {
        if($source === null) return;

        $shared_image = new SharedImages();
        $shared_image->image_uuid = $uuid;
        $shared_image->shared_by_user_id = $sharedBy->id;
        $shared_image->shared_with_user_id = $sharedTo->id;
        $shared_image->level = $accessLevel;
        $shared_image->save();

        $shared_image->refresh();

        $this->AddSourceToSharedImage($sharedBy, $shared_image, $source);
    }

    public function StopSharingImage(User $sharedBy, User $sharedTo, $image_uuid, $source)
    {
        $sharedImages = SharedImages::where('image_uuid', $image_uuid)->where('shared_with_user_id', $sharedTo->id)->get();
        foreach ($sharedImages as $sharedImage)
        {
            $this->RemoveSourceFromSharedImage($sharedBy, $sharedImage, $source);

            if(SharedSource::where('shared_image', $sharedImage->id)->where('shared_by_user_id', $sharedBy->id)->count() == 0)
            {
                $sharedImage->delete();
            }
        }
    }

    public function AddSourceToSharedImage(User $sharedBy, SharedImages $sharedImages, $source)
    {
        SharedSource::create([
            'shared_image' => $sharedImages->id,
            'shared_by_user_id' => $sharedBy->id,
            'source' => $source
        ]);
    }

    public function RemoveSourceFromSharedImage(User $sharedBy, SharedImages $sharedImage, $source)
    {
        $res = SharedSource::where('shared_image', $sharedImage->id)->where('source', $source)->where('shared_by_user_id', $sharedBy->id)->first();
        if(isset($res))
        {
            $res->delete();
        }
        else {
            Log::debug('Could not find source of shared image');
        }
    }
}
