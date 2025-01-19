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
            $tag->ownership()->detach(Auth::user()->id);
        }
    }

    public function create()
    {
        $this->validate();

        $tag = ImageTag::withoutGlobalScopes()->where('name', $this->name)->first();

        if(isset($tag)) {
            if($tag->ownership()->where('owner_id', Auth::user()->id)->exists()) {
                return $this->addError('name', 'Tag already exists');
            }

            $tag->ownership()->attach(Auth::user()->id);
        }
        else {
            $tag = ImageTag::create([
                'name' => $this->name,
            ]);

            $tag->ownership()->attach(Auth::user()->id);
        }

    }

    public function render()
    {
        return view('livewire.manage.tags',
            [
                'tags' => ImageTag::all()->paginate(50)
            ]);
    }
}
