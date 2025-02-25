<?php

namespace App\Jobs\Upload;

use App\Models\ImageUpload;
use App\Models\Upload;
use App\Models\User;
use App\Services\ImageService;
use App\Support\Enums\UploadStates;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Illuminate\Support\Str;
use SapientPro\ImageComparator\ImageComparator;

class ProcessUpload implements ShouldQueue, ShouldQueueAfterCommit, ShouldBeUnique
{
    use Queueable;


    public $timeout = 600;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly User $user,
        public readonly Upload $upload,
    ){}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Broadcast::on('upload.' . $this->user->id)->as('newUpload')->with(['ulid' => $this->upload->ulid])->send();

        // try {


        // } catch (\Throwable $th) {
        //     Log::error($th->getMessage());
        //     return;
        // }
        $this->upload->setState(UploadStates::Waiting);
    }

    public function failed() : void
    {
        DB::rollBack();
    }

    public function uniqueId(): string
    {
        return $this->user->id;
    }
}
