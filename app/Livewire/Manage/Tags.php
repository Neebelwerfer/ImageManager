<?php

namespace App\Livewire\Manage;

use App\Models\ImageTag;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.manage')]
class Tags extends Component
{

    #[Validate('required|min:1')]
    public string $name = '';

    public function delete($id){
        $tag = ImageTag::find($id);
        if(isset($tag)) {
            $tag->delete();
        }
    }

    public function create()
    {
        $this->validate();

        if(ImageTag::where('name', $this->name)->where('owner_id', Auth::user()->id)->exists()) {
            return $this->addError('name', 'Tag already exists');
        }

        ImageTag::create([
            'name' => $this->name,
            'owner_id' => Auth::user()->id
        ]);
    }

    public function render()
    {
        return view('livewire.manage.tags',
            [
                'tags' => ImageTag::where('owner_id', Auth::user()->id)->paginate(50)
            ]);
    }
}
