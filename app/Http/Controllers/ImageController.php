<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\ImageTag;
use App\Repository\ImageRepository;
use App\Repository\TagRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use SapientPro\ImageComparator\ImageComparator;

class ImageController extends Controller
{
    private ImageRepository $imageRepository;
    private TagRepository $tagRepository;

    public function __construct()
    {
        $this->imageRepository = App::make(ImageRepository::class);
        $this->tagRepository = App::make(TagRepository::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return Image::where('owner_id', Auth::user()->id)->get();
    }

    /**
     * Display a listing of the resource in pages.
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function pagedIndex() {
        return Image::where('owner_id', Auth::user()->id)->paginate(20);
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
    public function show(Image $image)
    {
        return view('image.show', compact('image'));
    }

    public function getImage(string $uuid) {
        $image = Image::where('uuid', $uuid)->first();

        if(!isset($image)) {
            abort(404);
        }

        if(Auth::user()->id != $image->owner_id) {
            abort(403);
        }

        return response()->file(storage_path('app/' . $image->path));
    }

    public function getThumbnail(string $uuid) {
        $image = Image::where('uuid', $uuid)->first();

        if(!Auth::hasUser()) {
            return redirect(route('login'));
        }

        if(Auth::user()->id != $image->owner_id) {
            abort(404);
        }

        if(empty($image->thumbnail_path())) {
            abort(404);
        }


        return response()->file(storage_path('app') . '/' . $image->thumbnail_path());
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Image $image)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Image $image)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Image $image)
    {
        //
    }

    // Adds image to database and creates thumbnail
    // Returns imageModel
    public function create($image, $data)
    {
        $user = Auth::user();

        try {
            $comparator = new ImageComparator();
            $imageInfo = ImageManager::imagick()->read($image);
            $imageScaled = ImageManager::gd()->read($image);


            $imageModel = new Image($data);
            $imageModel->uuid = Str::uuid();
            $imageModel->owner_id = $user->id;
            $uuidSplit = substr($imageModel->uuid, 0, 1).'/'.substr($imageModel->uuid, 1, 1).'/'.substr($imageModel->uuid, 2, 1).'/'.substr($imageModel->uuid, 3, 1);
            $imageModel->width = $imageScaled->width();
            $imageModel->height = $imageScaled->height();
            $imageInfo->scaleDown(256, 256);


            $thumbnail_path = 'thumbnails/' . $uuidSplit;
            $fileName = $imageModel->uuid . '.webp';
            $full_thumbnail_path = 'thumbnails/' . $uuidSplit . '/' . $fileName;
            if(!Storage::disk('local')->exists($thumbnail_path)) {
                Storage::disk('local')->makeDirectory($thumbnail_path);
            }
            $imageInfo->save(storage_path('app') . '/' . $full_thumbnail_path);


            // Check if image already exists via image hash
            // Currently only compares images with same width and height
            $hash = $comparator->hashImage(storage_path('app') . '/' . $full_thumbnail_path);
            $imageModel->image_hash = $comparator->convertHashToBinaryString($hash);
            $sameSizeImages = Image::where('owner_id', $user->id)->where('width', $imageModel->width)->where('height', $imageModel->height)->get();
            if (isset($sameSizeImages) && $sameSizeImages->count() > 0) {
                foreach ($sameSizeImages as $sameSizeImage) {
                    if ($comparator->compareHashStrings($sameSizeImage->image_hash, $imageModel->image_hash) > 95) {
                        Storage::disk('local')->delete($full_thumbnail_path);
                        return redirect()->route('image.upload')->with(['status' => 'Image already exists!', 'duplicate' => $sameSizeImage->path, 'hash' => $imageModel->image_hash, 'error' => true]);
                    }
                }
            }

            $imageModel->path = 'images/' . $uuidSplit . '/' . $imageModel->uuid . '.' . $image->extension();

            if(!Storage::disk('local')->exists('images/' . $uuidSplit)) {
                Storage::disk('local')->makeDirectory('images/' . $uuidSplit);
            }

            $imageScaled->save(storage_path('app') . '/' . $imageModel->path);
            $imageModel->save();

            $tags = [];
            foreach ($data['tags'] as $tag) {
                $tagResponse = $this->tagRepository->find($tag);
                if(!isset($tagResponse)) {
                    $newTag = new ImageTag();
                    $newTag->name = $tag;
                    $newTag->owner_id = $user->id;
                    $newTag->save();
                    $tagResponse = $newTag;
                }
                $tags[$tag] = $tagResponse;
            }

            $this->addTags($imageModel, $tags);

        } catch (\Exception $e) {
            Storage::disk('local')->delete($full_thumbnail_path);
            return redirect()->route('image.upload')->with(['status' => 'Something went wrong', 'error' => true, 'error_message' => $e->getMessage()]);
        }

        return $imageModel;
    }

    public function addTags(Image $image, array $tags) {
        $image->tags()->saveMany($tags);
    }
}
