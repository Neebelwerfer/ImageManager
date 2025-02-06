<?php

namespace App\Services;

use App\Models\Album;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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


        $album = Album::owned($sharedBy)->find($id);
        if(isset($album)) {
            if(isset($sharedTo) && !$this->isShared($sharedTo, $id)) {
                app(SharedResourceService::class)->ShareAlbum($sharedBy, $sharedTo, $id, $accessLevel);
                return true;
            }
        }
        return false;
    }

    public function stopSharing($sharedTo, $id)
    {

    }
}
