<?php

namespace App\Livewire\Manage;

use App\Models\Image;
use App\Models\ImageCategory;
use App\Services\CategoryService;
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

    public function isOwned($id) : bool
    {
        $category = ImageCategory::owned(Auth::user()->id)->find($id);
        return isset($category);
    }

    public function imageCount($id) : int
    {
        return Image::owned(Auth::user()->id)->where('category_id', $id)->count();
    }

    public function create()
    {
        $user = Auth::user();
        $this->validate();

        $cat = ImageCategory::owned($user->id)->where('name', $this->name)->first();

        if(isset($cat)) {
           return $this->addError('name', 'Category already exists');
        }
        else {
            $cat = app(CategoryService::class)->create($user, $this->name);
        }
    }

    public function render()
    {
        return view('livewire.manage.categories',
            [
                'categories' => ImageCategory::owned(Auth::user()->id)->where('name', 'like', '%' . $this->name . '%')->paginate(50),
                'shared' => ImageCategory::shared(Auth::user()->id)->where('name', 'like', '%' . $this->name . '%')->paginate(20)
            ]);
    }
}
