<?php

namespace App\Services;

use App\Models\Image;
use App\Models\ImageCategory;
use App\Models\SharedCollections;
use App\Models\SharedImages;
use App\Models\SharedResources;
use App\Models\SharedSource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SharedResourceService
{
    public function __construct() {
        //
    }

    public function ShareCollection(User $sharedBy, User $sharedTo, string $type, $id, $accessLevel)
    {
        $shared_collection = new SharedCollections();
        $shared_collection->resource_id = $id;
        $shared_collection->type = $type;
        $shared_collection->shared_by_user_id = $sharedBy->id;
        $shared_collection->shared_with_user_id = $sharedTo->id;
        $shared_collection->level = $accessLevel;
        $shared_collection->save();

        if($type === 'category')
        {
            $catImages = Image::where('category_id', $id)->select('uuid')->get();
            foreach ($catImages as $image)
            {
                $this->ShareImage($sharedBy, $sharedTo, $image->uuid, $accessLevel, $type);
            }
        }
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

    public function AddSourceToSharedImage(User $sharedBy, SharedImages $sharedImages, $source)
    {
        SharedSource::create([
            'shared_image' => $sharedImages->id,
            'shared_by_user_id' => $sharedBy->id,
            'source' => $source
        ]);
    }

    public function isCollectionShared(User $sharedTo, $collectionType, $collectionId)
    {
        return SharedCollections::where('type', $collectionType)->where('resource_id', $collectionId)->where('shared_with_user_id', $sharedTo->id)->exists();
    }

    public function isImageShared(User $sharedTo, $imageUuid)
    {
        return SharedImages::where('image_uuid', $imageUuid)->where('shared_with_user_id', $sharedTo->id);
    }
}
