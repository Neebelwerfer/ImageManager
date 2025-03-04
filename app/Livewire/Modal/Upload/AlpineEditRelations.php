<?php

namespace App\Livewire\Modal\Upload;

use App\Models\Album;
use App\Models\ImageCategory;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AlpineEditRelations extends Component
{
    public function getEntries($type)
    {
        if($type == 'category') {
            $res = ImageCategory::ownedOrShared(Auth::user()->id)->paginate(20);
            $data = [];
            foreach ($res as $category) {
                $data[] = ['name' => $category->name, 'id' => $category->id];
            }
        }
        else if ($type == 'album') {
            $res = Album::where('owner_id', Auth::user()->id)->paginate(20);
            $data = [];
            foreach ($res as $album) {
                $data[] = ['name' => $album->name, 'id' => $album->id];
            }
        }
        return json_encode($data);
    }

    public function render()
    {
        return view('livewire.modal.upload.alpine-edit-relations');
    }
}
