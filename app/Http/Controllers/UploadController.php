<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
        $upload = Upload::create(
            [
                'ulid' => Str::ulid()->toString(),
                'user_id' => Auth::user()->id
            ]
        );

        return response()->make('', 200, ['ulidt' => $upload->ulid]);
    }

    public function uploadImage(Request $request)
    {
        if(!$request->has('ulid'))
        {
            return response('Need to specify ulid of upload to proceed', status:404);
        }

        $upload = Upload::find($request->get('ulid'));
        if(!isset($upload) || $upload->user_id != Auth::user())
        {
            return response('Could not find upload for ulid: ' . $request->get('ulid'), 404);
        }
    }
}
