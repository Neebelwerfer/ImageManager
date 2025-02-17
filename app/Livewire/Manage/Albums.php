<?php

namespace App\Livewire\Manage;

use App\Models\Album;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.manage')]
class Albums extends Component
{
    #[Validate('required|min:1')]
    public string $name = '';

    public function delete($id){

        $album = Album::find($id);
        if(isset($album)) {
            $album->delete();
        }
    }

    public function imageCount($id) : int
    {
        return Album::find($id)->images()->count();
    }


    public function create()
    {
        $this->validate();

        if(Album::where('name', $this->name)->where('owner_id', Auth::user()->id)->exists()) {
            return $this->addError('name', 'Album already exists');
        }

        Album::create([
            'name' => $this->name,
            'owner_id' => Auth::user()->id
        ]);
    }

    public function render()
    {
        return view('livewire.manage.albums',
            [
                'albums' => Album::owned(Auth::user()->id)->paginate(50),
                'shared' => Album::shared(Auth::user()->id)->where('name', 'like', '%' . $this->name . '%')->paginate(20)
            ]);
    }
}
