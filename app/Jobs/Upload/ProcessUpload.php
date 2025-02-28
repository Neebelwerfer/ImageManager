<?php

namespace App\Jobs\Upload;

use App\Models\ImageUpload;
use App\Models\Upload;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use SapientPro\ImageComparator\ImageComparator;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;

class ProcessUpload implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly User $user,
        public readonly Upload $upload,
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Broadcast::on('upload.' . $this->user->id)->as('newUpload')->with(['ulid' => $this->upload->ulid])->send();

        $images = $this->upload->images;
        $hashes = [];
        DB::beginTransaction();
        $comparator = new ImageComparator();

        foreach($images as $image)
        {
            $hash = $image->hash;
            foreach ($hashes as $otherHash)
            {
                if($comparator->compareHashStrings($hash, $otherHash) >= 99)
                {
                    $image->delete();
                    continue;
                }
            }
            $hashes[] = $hash;
        }
        DB::commit();
    }
}
