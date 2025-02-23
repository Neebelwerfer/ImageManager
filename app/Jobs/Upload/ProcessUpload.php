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
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
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
        public readonly array $data
    ){}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $imageService = app(ImageService::class);

        $imageUploads = [];
        DB::beginTransaction();
        try {
            //code...
            foreach ($this->data as $key => $data)
            {
                $img = ImageManager::gd()->read(file_get_contents($data['path']));

                $upload = new ImageUpload(
                    [
                        'uuid' => str::uuid(),
                        'upload_ulid' => $this->upload->ulid,
                        'user_id' => $this->user->id,
                        'extension' => $data['extension'],
                        'hash' => $imageService->createImageHash($img->core()->native())
                    ]);
                $upload->save();
                $imageUploads[$key] = $upload;

                $cryptImage = Crypt::encrypt((string) $img->encodeByMediaType(), false);
                Storage::disk('local')->put('temp/'. $upload->uuid, $cryptImage);
                unlink($data['path']);
            }



            //Remove Obvious duplicates
            $comparator = new ImageComparator;
            $toDelete = [];
            foreach ($imageUploads as $key => $imageUpload)
            {
                if(array_key_exists($key, $toDelete)) continue;

                foreach($imageUploads as $otherKey => $otherUpload)
                {
                    if($otherUpload->uuid == $imageUpload->uuid || array_key_exists($otherKey, $toDelete)) continue;

                    if($comparator->compareHashStrings($imageUpload->hash, $otherUpload->hash) === 100)
                    {
                        $toDelete[$otherKey] = true;
                        $otherUpload->delete();
                        unset($imageUploads[$otherKey]);
                    }
                }
            }

        } catch (\Throwable $th) {
            DB::rollBack();
            return;
        }
        DB::commit();
        $this->upload->setState(UploadStates::Waiting);
    }

    public function failed() : void
    {
        DB::rollBack();

        foreach ($this->data as $data)
        {
            unlink($data['path']);
        }
    }

    public function uniqueId(): string
    {
        return $this->user->id;
    }
}
