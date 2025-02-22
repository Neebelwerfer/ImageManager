<?php

namespace App\Console\Commands;

use App\Models\Image;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class UpdateImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $images = Image::get();

        foreach($images as $image)
        {
            $split = Image::splitUUID($image->uuid);
            $hashedUuid = hash('sha1', $image->uuid);
            $thumbPath = "thumbnails/" . $split . '/';
            $imagePath = "images/" . $split . '/';
            $originalImagePath = "originalImages/" . $split . '/';

            Storage::disk('local')->move($thumbPath . $image->uuid, $thumbPath . $hashedUuid);

            if(!Storage::exists($originalImagePath))
            {
                Storage::makeDirectory($originalImagePath);
            }

            Storage::disk('local')->move($imagePath . $image->uuid, $originalImagePath . $hashedUuid);

            $image_interface = ImageManager::gd()->read(Crypt::decryptString(file_get_contents(Storage::disk('local')->path($originalImagePath . $hashedUuid))));

            if($image_interface->height() > $image_interface->width())
            {
                $image_interface->scaleDown(1080, 1920);
            }
            else
            {
                $image_interface->scaleDown(1920, 1080);
            }


            $cryptImage = Crypt::encrypt((string) $image_interface->toWebp(), false);
            Storage::disk('local')->put($imagePath . $hashedUuid,$cryptImage);
        }
    }
}
