<?php

namespace App\Jobs;

use App\Models\Image;
use App\Models\ImageCategory;
use App\Models\SharedCollections;
use App\Models\SharedImages;
use App\Models\User;
use App\Services\SharedResourceService;
use Exception;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShareCategoryImages implements ShouldQueue, ShouldBeEncrypted, ShouldBeUnique
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly User $sharedBy,
        public readonly User $sharedTo,
        public readonly ImageCategory $imageCategory,
        public readonly string $accessLevel
    )
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            DB::beginTransaction();

            $shared_collection = new SharedCollections();
            $shared_collection->resource_id = $this->imageCategory->id;
            $shared_collection->type = 'category';
            $shared_collection->shared_by_user_id = $this->sharedBy->id;
            $shared_collection->shared_with_user_id = $this->sharedTo->id;
            $shared_collection->level = $this->accessLevel;
            $shared_collection->save();

            $this->imageCategory->is_shared = true;
            $this->imageCategory->save();

            $catImages = Image::where('category_id', $this->imageCategory->id)->select('uuid')->get();

            foreach ($catImages as $image)
            {
                $shared_image = SharedImages::with('SharedSource')->where('image_uuid', $image->uuid)->where('shared_by_user_id', $this->sharedBy->id)->where('shared_with_user_id', $this->sharedTo->id)->firstOrCreate([
                    'image_uuid' => $image->uuid,
                    'shared_by_user_id' => $this->sharedBy->id,
                    'shared_with_user_id' => $this->sharedTo->id,
                    'level' => $this->accessLevel
                ]);

                if(!$shared_image->sharedSources()->where('source', 'category')->exists())
                {
                    app(SharedResourceService::class)->AddSourceToSharedImage($this->sharedBy, $shared_image, 'category');
                }
                else {
                    Log::warning('Shared image in category already have category as source', ['image_uuid' => $image->uuid, 'category' => $this->imageCategory->id]);
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $this->fail($e);
        }
    }
}
