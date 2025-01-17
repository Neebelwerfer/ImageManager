<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\ImageCategory;
use App\Models\ImageTag;
use App\Repository\ImageRepository;
use App\Repository\TagRepository;
use App\Services\ImageService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use SapientPro\ImageComparator\ImageComparator;

class ImageController extends Controller
{
    private ImageService $ImageService;

    public function __construct()
    {
        $this->ImageService = App::make(ImageService::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->ImageService->index();
    }

    /**
     * Display a listing of the resource in pages.
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function pagedIndex() {
        return $this->ImageService->pagedIndex();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $imageUuid)
    {
        return view('image.show', $imageUuid);
    }

    public function getImage(string $uuid) {
        $image = Image::where('uuid', $uuid)->first();

        if(!Auth::hasUser()) {
            return redirect(route('login'));
        }

        if(!isset($image) || Auth::user()->id != $image->owner_id) {
            redirect()->back();
        }

        return response()->file(storage_path('app/' . $image->getImagePath()));
    }

    public function getThumbnail(string $uuid) {
        $image = Image::where('uuid', $uuid)->first();

        if(!Auth::hasUser()) {
            return redirect(route('login'));
        }

        if(Auth::user()->id != $image->owner_id) {
            redirect()->back();
        }

        if(empty($image->thumbnail_path())) {
            abort(404);
        }


        return response()->file(storage_path('app') . '/' . $image->thumbnail_path());
    }

}
