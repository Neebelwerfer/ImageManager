<?php

namespace App\Jobs\Upload;

use App\Models\Upload;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class CleanupUpload implements ShouldQueue
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
        $images = $this->upload->images;
        foreach($images as $image)
        {
            $image->delete();
        }
        $this->upload->delete();
    }
    public function failed(?Throwable $exception) {
        Log::error($exception->getMessage());
    }
}
