<?php

namespace App\Jobs\Traits;

use App\Models\Image;
use App\Models\ImageTraits;
use App\Models\Traits;
use App\Models\User;
use App\Services\ImageService;
use Exception;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateTrait implements ShouldQueue, ShouldBeEncrypted
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly User $user,
        public readonly array $data,
    )
    {
}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::beginTransaction();
        try{
            $trait = Traits::create([
                'name' => $this->data['name'],
                'type' => $this->data['type'],
                'min' => $this->data['min'],
                'max' => $this->data['max'],
                'owner_id' => $this->user->id,
                'default' => $this->data['default']
            ]);


            foreach(Image::where('owner_id', $this->user->id)->get() as $image)
            {
                app(ImageService::class)->addTrait($image->uuid, $trait->id, $this->user->id, $trait->default);
            }

        } catch (Exception $e)
        {
            DB::rollBack();
            Log::error('Failed to create trait', ['message' => $e, 'trait_type' => $this->data['type']]);
        }

        DB::commit();
    }


    public function failed(): void
    {

    }
}
