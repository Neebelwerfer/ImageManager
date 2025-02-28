<?php

namespace App\Http\Controllers;

use App\Models\ImageUpload;
use App\Models\Upload;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Throwable;

use function Psy\debug;

class UploadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

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

        // Bus::chain([
        //     new ProcessUpload(Auth::user(), $upload, $this->data),
        //     new CheckUploadForDuplicates(Auth::user(), $this->upload)
        // ])->dispatch();

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
            return response('Could not find upload for ulid: ' . $ulid, 404);
        }
        $upload->delete();
    }

    public function uploadImages(Request $request, ImageService $imageService)
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

        foreach ($request->file('images') as $image)
        {
            $extension = $image->getClientOriginalExtension();
            $uuid = str::uuid()->toString();

            log::debug($image->getRealPath());

            $model = new ImageUpload(
                [
                    'uuid' => $uuid,
                    'upload_ulid' => $ulid,
                    'user_id' => Auth::user()->id,
                    'extension' => $extension,
                    'hash' =>  $imageService->createImageHash($image->getRealPath())
            ]);

            $thumbnail = ImageManager::imagick()->read($image);
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
            Storage::disk('local')->put('temp/' . $model->uuid, Crypt::encryptString($image->get()));
        }
    }
}
