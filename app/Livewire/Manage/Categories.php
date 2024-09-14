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
            $category->delete();
        }
    }

    public function create()
    {
        $this->validate();

        if(ImageCategory::where('name', $this->name)->where('owner_id', Auth::user()->id)->exists()) {
            return $this->addError('name', 'Category already exists');
        }

        ImageCategory::create([
            'name' => $this->name,
            'owner_id' => Auth::user()->id
        ]);
    }

    public function render()
    {
        return view('livewire.manage.categories',
            [
                'categories' => ImageCategory::where('owner_id', Auth::user()->id)->paginate(50)
            ]);
    }
}
