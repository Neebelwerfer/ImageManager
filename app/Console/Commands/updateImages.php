<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File as FacadesFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\File;

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

            $oldUuidSplit = substr($image->uuid, 0, 4).'/'.substr($image->uuid, 4, 4).'/'.substr($image->uuid, 9, 4).'/'.substr($image->uuid, 14, 4);
            $uuidSplit = substr($image->uuid, 0, 1).'/'.substr($image->uuid, 1, 1).'/'.substr($image->uuid, 2, 1).'/'.substr($image->uuid, 3, 1);

            echo 'Moved '.$image->uuid.' to '.$uuidSplit."\n";

            if(!Storage::disk('local')->exists('thumbnails/'.$uuidSplit)) {
                Storage::disk('local')->makeDirectory('thumbnails/'.$uuidSplit);
            }
            Storage::disk('local')->move('thumbnails/'. $oldUuidSplit.'/'. $image->uuid.'.webp', 'thumbnails/'.$uuidSplit.'/'.$image->uuid.'.webp');


            if(!Storage::disk('local')->exists('images/'.$uuidSplit))
            {
                Storage::disk('local')->makeDirectory('images/'.$uuidSplit);
            }

            $ext = FacadesFile::extension($image->path);

            Storage::disk('local')->move('images/' . $oldUuidSplit.'/' .$image->uuid.'.'.$ext, 'images/'.$uuidSplit.'/'.$image->uuid.'.'.$ext);

            $image->path = 'images/'.$uuidSplit.'/'.$image->uuid.'.'.$ext;

            $image->save();
        }
    }
}
