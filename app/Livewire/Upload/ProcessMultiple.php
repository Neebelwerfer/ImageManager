<?php

namespace App\Livewire\Upload;

use App\Jobs\Upload\CleanupUpload;
use App\Jobs\Upload\ProcessMultipleImages;
use App\Models\ImageUpload;
use App\Models\Upload;
use App\Support\Enums\ImageUploadStates;
use App\Support\Enums\UploadStates;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

use function PHPUnit\Framework\isNumeric;

#[Layout('layouts.app')]
class ProcessMultiple extends Component
{
    use WithPagination;

    public Upload $upload;

    public $state = "waiting";
    public $selectedImages = [];
    public $editMode = false;

    public $imagesSnapshot = [];
    public $images = [];
    public $count = 0;

    #[On('echo:upload.{upload.user_id},.stateUpdated')]
    public function stateUpdated($data)
    {
        if($data['ulid'] != $this->upload->ulid) return;

        $this->state = $data['state'];

        if($this->state = "waiting")
        {
            $this->images();
        }
    }

    public function next(){
        $this->count += 1;
        if($this->count > count($this->images) - 1) $this->count = (count($this->images) - 1);
    }

    public function previous(){
        if($this->count === 0)
            return;
        $this->count -= 1;
    }

    public function select($count)
    {
        if($count != $this->count)
            $this->count = $count;

        $this->js('document.getElementById("process").scrollIntoView();');
    }

    private function getProcessedData($index)
    {
        $image = $this->images[$index];
        $data = [];

        $data['category'] = $image['category'];
        $data['albums'] = $image['albums'];
        $data['tags'] = $image['tags'];
        $data['dimensions'] = $image['dimensions'];
        $data['size'] = $image['size'];

        return $data;
    }

    private function saveData($index)
    {
        $image = $this->images[$index];

        $imageUpload = ImageUpload::find($image['uuid']);
        $data = $this->getProcessedData($index);

        $this->images[$index]['isDirty'] = false;
        $imageUpload->data = json_encode($data);
        $imageUpload->save();
    }

    public function saveImageData()
    {
        DB::beginTransaction();
        foreach ($this->images as $key => $image)
        {
            if(!$image['isDirty']) continue;
            $this->saveData($key);
        }
        DB::commit();
        $this->js("alert('Saved image data')");
    }

    public function images()
    {
        $images = ImageUpload::where('upload_ulid', $this->upload->ulid)->where('user_id', Auth::user()->id)->orderBy('uuid', 'desc')->get();
        $this->selectedImages = [];

        $count = 0;
        foreach($images as $image)
        {
            $duplicates = json_decode($image->duplicates);
            $data = json_decode($image->data, true);

            $category = [];
            $tags = [];
            $albums = [];

            if(isset($data['category']))
            {
                $category = $data['category'];
            }
            if(isset($data['tags']))
            {
                $tags = $data['tags'];
            }
            if(isset($data['albums']))
            {
                $albums = $data['albums'];
            }

            $size = 0;
            if(isset($data['size']))
            {
                $size = $data['size'];
            }
            else
            {
                Log::error('Could not find size for uploaded file', ['uuid' => $image->uuid]);
            }


            $imageData = [
                'uuid' => $image->uuid,
                'state' => $image->state,
                'extension' => $image->extension,
                'category' => $category,
                'tags' => $tags,
                'albums' => $albums,
                'traits' => [],
                'isDirty' => false,
                'dimensions' => $data['dimensions'],
                'size' => $size,
                'duplicates' => $duplicates
            ];

            $this->images[$count] = $imageData;
            $this->imagesSnapshot[$count] = $imageData;

            $this->selectedImages[$count] = false;
            $count += 1;
        }
    }

    public function mount($ulid)
    {
        $res = Upload::find($ulid);

        if(!isset($res) || $res->user_id != Auth::user()->id)
        {
            return $this->redirectRoute('upload', navigate: true);
        }

        $this->upload = $res;

        $this->state = $this->upload->state;

        if($this->state === "waiting")
        {
            $this->images();
        }
    }

    #[On('imageUploadUpdated')]
    public function imageUploadUpdated()
    {
        unset($this->images);
    }

    #[On('imageDuplicatesDeleted')]
    public function imageDuplicatesDeleted($uuid)
    {
        $index = -1;
        foreach ($this->images as $key => $image)
        {
            if($image['uuid'] === $uuid)
            {
                $index = $key;
                break;
            }
        }

        if($index > -1)
        {
            $this->images[$index]['duplicates'] = [];
            $this->imagesSnapshot[$index]['duplicates'] = [];
            $imageUpload = ImageUpload::find($image['uuid']);
            $imageUpload->duplicates = json_encode([]);
            $imageUpload->save();
        }
    }

    #[On('imageDeleted')]
    public function imageDeleted($uuid)
    {
        $index = -1;
        foreach($this->images as $key => $image)
        {
            if($image['uuid'] == $uuid)
            {
                $index = $key;
                break;
            }
        }
        if($index > -1)
        {
            if ($this->count > 0)
                $this->count -= 1;
            else
                $this->count += 1;

            array_splice($this->images, $index, 1);
            array_splice($this->selectedImages, $index, 1);
            array_splice($this->imagesSnapshot, $index, 1);

            dispatch(function () use($uuid) {
                ImageUpload::find($uuid)->delete();
            })->afterResponse();

            if(count($this->images) == 0)
            {
                $this->uploadCancel();
            }
        }
    }

    #[On('categoryMultiSelect')]
    public function categorySelected($selection)
    {
        $category = $selection;
        foreach($this->selectedImages as $key => $selected)
        {
            if(!$selected) continue;

            $this->images[$key]['category'] = $category;
            $this->images[$key]['isDirty'] = true;
        }
    }


    #[On('albumMultiSelect')]
    public function albumSelected($selection)
    {

        $id= $selection['id'];
        $name = $selection['name'];

        foreach($this->selectedImages as $key => $selected)
        {
            if(!$selected) continue;

            $image = $this->images[$key];
            if(isset($image['albums'][$id])) continue;

            $this->images[$key]['albums'][] = ['name' => $name, 'id' => $id];
            $this->images[$key]['isDirty'] = true;
        }
    }

    public function finalizeUpload()
    {
        foreach($this->images as $image)
        {
            if(!empty($image->duplicates))
            {
                $this->js("alert('Cant upload while images need response')");
                return;
            }
        }

        $this->state = UploadStates::Processing->value;
        ProcessMultipleImages::dispatch(Auth::user(), $this->upload);
    }

    public function deleteSelected()
    {
        $imageToDelete = [];
        foreach($this->selectedImages as $index => $selected)
        {
            if(!$selected) continue;

            $imageToDelete[] = ['index' => $index, 'uuid' => $this->images[$index]['uuid']];
        }

        foreach($imageToDelete as $toDelete)
        {
            unset($this->images[$toDelete['index']]);
            unset($this->selectedImages[$toDelete['index']]);
            unset($this->imagesSnapshot[$toDelete['index']]);
        }

        $this->images = array_values($this->images);
        $this->selectedImages = array_values($this->selectedImages);
        $this->imagesSnapshot = array_values($this->imagesSnapshot);

        if ($this->count > 0)
            $this->count -= 1;
        else
            $this->count += 1;


        if(count($this->images) == 0)
        {
            $this->uploadCancel();
        } else {
            dispatch(function () use ($imageToDelete) {
                foreach($imageToDelete as $data)
                {
                    ImageUpload::find($data['uuid'])->delete();
                }
            })->afterResponse();
        }
    }

    public function uploadCancel()
    {
        CleanupUpload::dispatch(Auth::user(), $this->upload);
        return $this->redirectRoute('upload', navigate: true);
    }

    public function render()
    {
        return view('livewire.upload.process-multiple');
    }
}
