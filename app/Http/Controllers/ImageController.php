<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\ImageCategory;
use App\Models\ImageTag;
use App\Models\ImageUpload;
use App\Repository\ImageRepository;
use App\Repository\TagRepository;
use App\Services\ImageService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
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

    public function getImage(string $uuid)
    {
        $image = Image::where('uuid', $uuid)->first();

        if (!Auth::hasUser()) {
            return redirect(route('login'));
        }

        if (!isset($image) || Auth::user()->id != $image->owner_id) {
            redirect()->back();
        }

        try {
            $data = file_get_contents(storage_path('app/' . $image->getImagePath()));
            return response()->make(Crypt::decryptString($data, false), 200, ['Content-Type' => 'image/' . $image->format . ';base64']);
        } catch (Exception $e) {
            return response()->noContent('404');
        }
    }

    public function getThumbnail(string $uuid)
    {
        $image = Image::where('uuid', $uuid)->first();

        if (!Auth::hasUser()) {
            return redirect(route('login'));
        }

        if (!isset($image) || Auth::user()->id != $image->owner_id) {
            redirect()->back();
        }


        try {
            $data = Cache::remember('thumbnail-' . $image->uuid, 3600, function () use ($image) {
                return Crypt::decryptString(file_get_contents(storage_path('app/' . $image->getThumbnailPath())));
            });
            return response()->make($data, 200, ['Content-Type' => 'image/webp;base64']);
        } catch (Exception $e) {
            return response()->noContent('404');
        }
    }

    public function getTempImage(string $uuid)
    {
        $image = ImageUpload::where('uuid', $uuid)->first();

        if (!Auth::hasUser()) {
            return redirect(route('login'));
        }

        if (Auth::user()->id != $image->owner_id) {
            redirect()->back();
        }

        $data = Crypt::decryptString(file_get_contents($image->fullPath(), false));
        return response()->make($data, 200, ['Content-Type' => 'image/' . $image->format . ';base64']);
    }
}
