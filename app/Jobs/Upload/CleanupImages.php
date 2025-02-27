<?php

namespace App\Jobs\Upload;

use App\Models\Upload;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class CleanupImages implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly User $user,
        public readonly array $data
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->data as $path)
        {
            unlink($path);
        }
    }

    public function failed(?Throwable $exception) {
        Log::error($exception->getMessage());
    }
}
