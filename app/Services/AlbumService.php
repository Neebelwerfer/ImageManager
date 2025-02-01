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
        return app(SharedResourceService::class)->isShared($sharedTo, 'album', $id);
    }


    public function share($id, User $sharedTo, $accessLevel) : bool
    {
        $album = Album::owned()->find($id);
        if(isset($album)) {
            if(isset($sharedTo) && $sharedTo->id != Auth::user()->id && !$this->isShared($sharedTo, $id)) {
                app(SharedResourceService::class)->Share($sharedTo, 'album', $id, $accessLevel);
                $album->is_shared = true;
                return true;
            }
        }
        return false;
    }

    public function stopSharing($sharedTo, $id)
    {

    }
}
