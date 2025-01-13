<?php

namespace App\Support;

use App\Models\Image;

class ImagePaths
{
    public static function splitUuid(string $uuid) : string
    {
        $split = substr($uuid, 0, 1).'/'.substr($uuid, 1, 1).'/'.substr($uuid, 2, 1).'/'.substr($uuid, 3, 1);
        return $split;
    }

    public static function getThumbnailPath(Image $image) : string
    {
        $split = ImagePaths::splitUuid($image->uuid);
        return 'thumbnails/' . $split . '/' . $image->uuid . '.webp';
    }

    public static function getImagePath(Image $image) : string
    {
        $split = ImagePaths::splitUuid($image->uuid);
        return 'images/' . $split . '/' . $image->uuid . '.webp';
    }
}
