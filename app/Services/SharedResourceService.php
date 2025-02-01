<?php

namespace App\Services;

use App\Models\SharedResources;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SharedResourceService
{
    public function __construct() {
        //
    }

    public function Share(User $sharedTo, string $type, $id, $accessLevel)
    {
        $shared_resource = new SharedResources();

        if($type === 'image')
            $shared_resource->resource_uuid = $id;
        else
            $shared_resource->resource_id = $id;

        $shared_resource->type = $type;
        $shared_resource->shared_by_user_id = Auth::user()->id;
        $shared_resource->shared_with_user_id = $sharedTo->id;
        $shared_resource->level = $accessLevel;
        $shared_resource->save();
    }

    public function isShared(User $sharedTo, string $type, $id) : bool
    {
        return SharedResources::where('type', $type)->where('shared_with_user_id', $sharedTo->id)->where(function($query) use($type, $id) {
            if($type === 'image')
                $query->where('resource_uuid', $id);
            else
                $query->where('resource_id', $id);
        })->exists();
    }

    public function stopSharing(User $sharedTo, string $type, $id)
    {

    }
}
