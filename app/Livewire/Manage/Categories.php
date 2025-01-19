<?php

namespace App\Livewire\Manage;

use App\Models\ImageCategory;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

use Livewire\Component;

#[Layout('layouts.manage')]
class Categories extends Component
{

    #[Validate('required|min:1')]
    public string $name = '';

    public function delete($id){
        $category = ImageCategory::find($id);
        if(isset($category)) {
            $category->ownership()->detach(Auth::user()->id);
        }
    }

    public function create()
    {
        $this->validate();

        $cat = ImageCategory::withoutGlobalScopes()->where('name', $this->name)->first();

        if(isset($cat)) {
            if($cat->ownership()->where('owner_id', Auth::user()->id)->exists()) {
                return $this->addError('name', 'Tag already exists');
            }

            $cat->ownership()->attach(Auth::user()->id);
        }
        else {
            $cat = ImageCategory::create([
                'name' => $this->name,
            ]);

            $cat->ownership()->attach(Auth::user()->id);
        }
    }

    public function render()
    {
        return view('livewire.manage.categories',
            [
                'categories' => ImageCategory::all()->paginate(50)
            ]);
    }
}
