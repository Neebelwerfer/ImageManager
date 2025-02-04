<?php

namespace App\Jobs\Upload;

use App\Events\Upload\FoundDuplicates;
use App\Models\ImageUpload;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ScanForDuplicates implements ShouldQueue, ShouldBeEncrypted
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user,
        public ImageUpload $imageUpload,
    )
    {
        $user->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle(ImageService $imageService): void
    {
        $this->imageUpload->state = "scanning";
        $this->imageUpload->save();
        $res = $imageService->compareHashes($this->user->id, $this->imageUpload->hash);

        if(count($res) > 0)
        {
            $this->imageUpload->duplicates = json_encode($res);
            $this->imageUpload->state = "foundDuplicates";
            $this->imageUpload->save();
            broadcast(new FoundDuplicates($this->user, $this->imageUpload->uuid));
            return;
        }
        ProcessImage::dispatch($this->user, $this->imageUpload);
    }
}
