<?php

namespace App\Jobs;

use App\Models\Image;
use App\Models\SharedCollections;
use App\Models\SharedImages;
use App\Models\SharedSource;
use App\Models\User;
use App\Services\SharedResourceService;
use Exception;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\Continue_;

class StopSharingCategory implements ShouldQueue, ShouldBeEncrypted, ShouldBeUnique
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly User $sharedBy,
        public readonly User $sharedTo,
        public readonly SharedCollections $sharedCategory,
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            DB::beginTransaction();
            $catImages = Image::where('category_id', $this->sharedCategory->resource_id)->get();

            foreach ($catImages as $image)
            {
                $shared_image = SharedImages::where('image_uuid', $image->uuid)->where('shared_by_user_id', $this->sharedBy->id)->where('shared_with_user_id', $this->sharedTo->id)->first();
                if(!isset($shared_image))
                {
                    Log::debug('Found image not shared in shared category', ['image_uuid' => $image->uuid, 'category_id' => $this->sharedCategory->resource_id]);
                    continue;
                }

                app(SharedResourceService::class)->RemoveSourceFromSharedImage($this->sharedBy, $shared_image, 'category');

                if($shared_image->sharedSources()->count() == 0)
                {
                    $shared_image->delete();
                }
            }

            $this->sharedCategory->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::debug($e->getMessage());
            $this->fail($e);
        }
    }
}
