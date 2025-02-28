<?php

namespace App\Http\Controllers;

use App\Jobs\Upload\CheckUploadForDuplicates;
use App\Jobs\Upload\ProcessUpload;
use App\Models\ImageUpload;
use App\Models\Upload;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Throwable;

class UploadController extends Controller
{
    public function uploadStart()
    {
        if(!Auth::hasUser())
        {
            return response('', 404);
        }

        $upload = Upload::create(
            [
                'ulid' => Str::ulid()->toString(),
                'user_id' => Auth::user()->id
            ]
        );

        return response()->make('', 200, ['ulid' => $upload->ulid]);
    }

    public function uploadComplete(Request $request)
    {
        if(!Auth::hasUser())
        {
            return response('', 403);
        }

        if(!$request->hasHeader('ulid'))
        {
            return response('Need to specify ulid of upload to proceed', 404);
        }

        $ulid = $request->header('ulid');
        $upload = Upload::find($ulid);
        if(!isset($upload))
        {
            return response('Could not find upload for ulid: ' . $ulid, 404);
        }

        Bus::chain([
             new ProcessUpload(Auth::user(), $upload),
             new CheckUploadForDuplicates(Auth::user(), $upload)
        ])->dispatch();

        return response()->make('Upload in Progress', 200, ['url' => route('upload.multiple', ['ulid' => $ulid])]);
    }

    public function uploadCancel(Request $request)
    {
        if(!$request->hasHeader('ulid'))
        {
            return response('Need to specify ulid of upload to proceed', 404);
        }
        if(!Auth::hasUser())
        {
            return response('', 403);
        }

        $ulid = $request->header('ulid');
        $upload = Upload::find($ulid);
        if(!isset($upload))
        {
            return response()->noContent();
        }
        $upload->delete();
    }

    public function uploadImages(Request $request)
    {
        if(!$request->hasHeader('ulid'))
        {
            return response('Need to specify ulid of upload to proceed', 404);
        }

        $ulid = $request->header('ulid');
        $upload = Upload::find($ulid);
        if(!isset($upload))
        {
            return response('Could not find upload for ulid: ' . $ulid, 404);
        }

        if($upload->user_id != Auth::user()->id)
        {
            return response('No access', 403);
        }

        if(!$request->hasFile('images'))
        {
            return response('No files detected', 404);
        }

        $process = [];
        foreach ($request->file('images') as $image)
        {
            $path = $image->getRealPath();
            $extension = $image->getClientOriginalExtension();
            $user_id = Auth::user()->id;
            $process[] = fn () => $this->handleImage($user_id, $path, $extension , $ulid);
        }

        try
        {
            DB::beginTransaction();
            Concurrency::run($process);
            DB::commit();
            return response('Images uploaded and handled');
        }
        catch (\Throwable $th)
        {
            Log::error('Failed to handle uploaded images', ['message' => $th->getMessage(), 'file' => $th->getFile() .':'. $th->getLine()]);
            return response('Error occured when handling uploaded images', 500);
        }
    }

    private function handleImage($user_id, $path, $extension, $ulid)
    {
        $imageService = app(ImageService::class);
        $uuid = str::uuid()->toString();

        $model = new ImageUpload(
            [
                'uuid' => $uuid,
                'upload_ulid' => $ulid,
                'user_id' => $user_id,
                'extension' => $extension,
                'hash' =>  $imageService->createImageHash($path)
        ]);

        $content = file_get_contents($path);
        $thumbnail = ImageManager::imagick()->read($content);
        $model->data = json_encode([
            'category' => null,
            'tags' => [],
            'traits' => [],
            'albums' => [],
            'dimensions' => ['width' => $thumbnail->width(), 'height' => $thumbnail->height()]
        ]);
        $model->save();

        $thumbnail->scaleDown(256, 256);
        Storage::disk('local')->put('temp/' . $model->uuid . '.thumbnail', Crypt::encrypt((string)$thumbnail->toWebp(), false));
        Storage::disk('local')->put('temp/' . $model->uuid, Crypt::encryptString($content));
    }
}
