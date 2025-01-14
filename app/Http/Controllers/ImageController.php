<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\ImageCategory;
use App\Models\ImageTag;
use App\Repository\ImageRepository;
use App\Repository\TagRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use SapientPro\ImageComparator\ImageComparator;

class DuplicateImageException extends Exception
{
    public array $duplicates = [];

    public function __construct(string $message, array $duplicates = [])
    {
        parent::__construct($message);
        $this->duplicates = $duplicates;
    }
}

class ImageController extends Controller
{
    private ImageRepository $imageRepository;
    private TagRepository $tagRepository;
    private ImageComparator $comparator;

    public function __construct()
    {
        $this->imageRepository = App::make(ImageRepository::class);
        $this->tagRepository = App::make(TagRepository::class);
        $this->comparator = new ImageComparator();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->imageRepository->index();
    }

    /**
     * Display a listing of the resource in pages.
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function pagedIndex() {
        return $this->imageRepository->pagedIndex();
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

        if(!Auth::hasUser()) {
            return redirect(route('login'));
        }

        if(!isset($image) || Auth::user()->id != $image->owner_id) {
            abort(404);
        }

        return response()->file(storage_path('app/' . $image->getImagePath()));
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

    public function addTags(Image $image, array $tags)
    {
        $image->tags()->saveMany($tags);
    }

    public function removeTags(Image $image, array $tags)
    {
        $image->tags()->detach($tags);
        $image->push();
    }

    public function addCategory(Image $image, ImageCategory $category)  {
        $image->categories()->save($category);
    }

    public function removeCategory(Image $image, ImageCategory $category) {
        $image->categories()->detach($category);
        $image->push();
    }



    /**
     * Adds image to database and creates thumbnail
     *
     * @param TemporaryUploadedFile $image
     * @param array $data
     * @return void
     */
    public function create(TemporaryUploadedFile $image, array $data)
    {
        $user = Auth::user();

        try {
            $imageInfo = ImageManager::imagick()->read($image);
            $imageScaled = ImageManager::gd()->read($image);

            $imageModel = new Image($data);
            $imageModel->uuid = Str::uuid();
            $imageModel->owner_id = $user->id;
            $uuidSplit = substr($imageModel->uuid, 0, 1).'/'.substr($imageModel->uuid, 1, 1).'/'.substr($imageModel->uuid, 2, 1).'/'.substr($imageModel->uuid, 3, 1);
            $imageModel->width = $imageScaled->width();
            $imageModel->height = $imageScaled->height();
            $imageModel->format = $image->extension();
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
            $imageModel->image_hash = $this->createImageHash(storage_path('app') . '/' . $full_thumbnail_path);
            $hits = $this->compareHashes($imageModel->image_hash);

            if (count($hits) > 0) {
                Storage::disk('local')->delete($full_thumbnail_path);
                return redirect()->route('image.upload')->with(['status' => 'Image already exists!', 'uploaded' => $image->serializeForLivewireResponse(), 'error' => true]);
            }

            $imagePath = $uuidSplit . '/' . $imageModel->uuid . '.' . $image->extension();

            if(!Storage::disk('local')->exists('images/' . $uuidSplit)) {
                Storage::disk('local')->makeDirectory('images/' . $uuidSplit);
            }

            $imageScaled->save(storage_path('app') . '/' . 'images/' . $imagePath);

            $imageModel->save();
            $tags = [];
            foreach ($data['tags'] as $tag) {
                $tagResponse = $this->tagRepository->find($tag);
                if(isset($tagResponse)) {
                    $tags[$tag] = $tagResponse;
                }
            }

            $this->addCategory($imageModel, $data['category']);

            $this->addTags($imageModel, $tags);
        } catch (\Exception $e) {
            Storage::disk('local')->delete($full_thumbnail_path);
            Storage::disk('local')->delete('images/' . $imagePath);
            return redirect()->route('image.upload')->with(['status' => 'Something went wrong', 'error' => true, 'error_message' => $e->getMessage()]);
        }

        return redirect()->route('image.upload')->with('status', 'Image uploaded successfully!');
    }

    private function compareHashes($newHash, $threshold = 95) : array
    {
        $sameSizeImages = $this->imageRepository->lazyIndex();

        $hits = [];

        $counter = 0;
        foreach ($sameSizeImages as $sameSizeImage) {
            if ($this->comparator->compareHashStrings($sameSizeImage->image_hash, $newHash) > $threshold) {
                $hits[$counter] = $sameSizeImage;
                $counter++;
            }
        }
        return $hits;
    }

    private function createImageHash($thumbnail_path) : string
    {
        $hash = $this->comparator->hashImage($thumbnail_path);
        return $this->comparator->convertHashToBinaryString($hash);
    }
}
