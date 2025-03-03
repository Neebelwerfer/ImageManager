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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ProcessMultiple extends Component
{
    use WithPagination;

    public Upload $upload;

    public $state = "waiting";
    public $selectedUUID = "";
    public $count = 0;
    public $selectedImages = [];
    public $editMode = false;

    public $images = [];

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

            $this->images[$count] = [
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

            dispatch(function () use($uuid) {
                ImageUpload::find($uuid)->delete();
            })->afterResponse();

            if(count($this->images) == 0)
            {
                $this->uploadCancel();
            }
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
        $countNotDeleted = 0;
        foreach($this->selectedImages as $count => $selected)
        {
            if(!$selected) continue;

            unset($this->images[$count]);
            // $image = ImageUpload::find($uuid);
            // if($image->user_id === Auth::user()->id)
            // {
            //     $image->delete();
            //     unset($this->selectedImages[$uuid]);
            // }
            // else
            // {
            //     $countNotDeleted++;
            // }
        }
        if($countNotDeleted > 0)
        {
            $this->js("alert(" . $countNotDeleted . ' images were not deleted');
        }
        if(count($this->images) == 0)
        {
            $this->uploadCancel();
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
