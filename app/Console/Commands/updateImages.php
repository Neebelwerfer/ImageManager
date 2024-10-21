<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class updateImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:images';

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
        $images = \App\Models\Image::all();
        foreach ($images as $image) {

            $uuidSplit = substr($image->uuid, 0, 4).'/'.substr($image->uuid, 4, 4).'/'.substr($image->uuid, 9, 4).'/'.substr($image->uuid, 14, 4);
            echo 'Moved '.$image->uuid.' to '.$uuidSplit."\n";

            if(!Storage::disk('local')->exists('thumbnails/'.$uuidSplit)) {
                Storage::disk('local')->makeDirectory('thumbnails/'.$uuidSplit);
            }
            Storage::disk('local')->move('thumbnails/'.$image->uuid.'.webp', 'thumbnails/'.$uuidSplit.'/'.$image->uuid.'.webp');


            if(!Storage::disk('local')->exists('images/'.$uuidSplit))
            {
                Storage::disk('local')->makeDirectory('images/'.$uuidSplit);
            }


            if(Storage::disk('local')->exists('images/'.$image->uuid.'.jpg'))
            {
                $ext = 'jpg';
            }
            else if(Storage::disk('local')->exists('images/'.$image->uuid.'.png'))
            {
                $ext = 'png';
            }
            else if(Storage::disk('local')->exists('images/'.$image->uuid.'.webp'))
            {
                $ext = 'webp';
            }

            Storage::disk('local')->move('images/'.$image->uuid.'.'.$ext, 'images/'.$uuidSplit.'/'.$image->uuid.'.'.$ext);

            $image->path = 'images/'.$uuidSplit.'/'.$image->uuid.'.'.$ext;

            $image->save();
        }
    }
}
