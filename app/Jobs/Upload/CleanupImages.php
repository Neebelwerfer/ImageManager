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
        public readonly Upload $upload,
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
        $this->upload->delete();
        foreach ($this->data as $data)
        {
            try{
                unlink($data['path']);
            } catch (\Throwable $th) {
                Log::error($th->getMessage());
            }
        }
    }
    public function failed(?Throwable $exception) {
        Log::error($exception->getMessage());
    }
}
