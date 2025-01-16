<?php

namespace App\Services;

use App\Models\Image;


class ImageService
{
    public function deleteImage(Image $image)
    {
        $image->delete();
    }


}
