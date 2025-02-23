<?php

namespace App\Jobs\Upload;

use App\Events\Upload\FoundDuplicates;
use App\Models\ImageUpload;
use App\Models\Upload;
use App\Models\User;
use App\Services\ImageService;
use App\Support\Enums\ImageUploadStates;
use App\Support\Enums\UploadStates;
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
        public Upload $Upload,
    )
    {
        $user->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle(ImageService $imageService): void
    {
        $this->Upload->setState(UploadStates::Scanning);
        $images = $this->Upload->images;

        $foundDuplicates = false;
        foreach($images as $imageUpload)
        {
            $imageUpload->setState(ImageUploadStates::Scanning);
            $res = $imageService->compareHashes($this->user->id, $imageUpload->hash);

            if(count($res) > 0)
            {
                $imageUpload->duplicates = json_encode($res);
                $imageUpload->setState(ImageUploadStates::FoundDuplicates);
                $foundDuplicates = true;
            }
        }

        if(!$foundDuplicates)
        {
            ProcessMultipleImages::dispatch($this->user, $this->Upload);
        }
        else
        {
            $this->Upload->setState(UploadStates::FoundDuplicates);
        }
    }
}
