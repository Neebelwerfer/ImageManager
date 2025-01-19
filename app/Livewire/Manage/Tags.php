<?php

namespace App\Livewire\Manage;

use App\Models\ImageTag;
use App\Services\TagService;
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
        app(TagService::class)->delete($id);
    }

    public function create()
    {
        $this->validate();

        $tag = ImageTag::owned()->where('name', $this->name)->first();

        if(isset($tag)) {
           return $this->addError('name', 'Category already exists');
        }
        else {
            $tag = app(TagService::class)->create($this->name);
        };

    }

    public function render()
    {
        return view('livewire.manage.tags',
            [
                'tags' => ImageTag::all()->paginate(50)
            ]);
    }
}
