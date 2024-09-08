<?php

namespace App\Livewire\CategoryShow;

use App\Models\ImageCategory;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Modal extends Component
{
    public $category;

    #[Validate('required|min:2')]
    public $newCategory = '';

    public function saveCategory()
    {
        $this->validate();

        $cat = new ImageCategory();
        $cat->name = $this->newCategory;
        $cat->owner_id = Auth::user()->id;
        $cat->save();
    }

    public function close()
    {
        $this->dispatch('closeModal');
    }

    public function selectCategory($id)
    {
        $this->dispatch('categorySelected', $id);
        $this->close();
    }

    public function deleteCategory($id)
    {
        $cat = ImageCategory::find($id);
        $cat->delete();
    }

    #[Computed()]
    public function categories()
    {
        return ImageCategory::where('owner_id', Auth::user()->id)->get();
    }

    public function render()
    {
        return <<<'HTML'
            <div class="fixed inset-0 z-40 flex items-center justify-center bg-gray-900 bg-opacity-50">
                <!-- Modal Content -->
                <div class="w-1/2 max-w-3xl p-8 bg-gray-700 rounded-lg shadow-lg"  x-on:click.away="$wire.close">
                    <h2 class="mb-4 text-2xl font-bold">Categories</h2>

                    <div class="mb-6 space-y-2 overflow-auto">
                        <h3 class="mb-2 text-xl font-bold">Categories</h3>
                        <button class="w-auto px-5 py-2 text-white bg-gray-500 rounded-md" wire:click="selectCategory(-1)">None</button>
                        @foreach ($this->categories as $category)
                            <div class="flex justify-between py-2 border-t border-gray-900">
                                <button class="w-auto px-5 py-2 text-white bg-gray-500 rounded-md" wire:click="selectCategory({{ $category->id }})">{{ $category->name }}</button>
                                <button class="px-4 py-2 text-white bg-red-500 rounded-md" wire:confirm="Are you sure?" wire:click="deleteCategory({{ $category->id }})">Delete</button>
                            </div>
                        @endforeach
                    </div>


                    <form class="border-t border-gray-900" wire:submit="saveCategory">
                        @csrf

                        <label for="newCategory" class="form-label">Name</label>
                        <div class="mb-3">
                            <div>
                                <input type="text" class="text-black form-control" wire:model="newCategory">
                                <button type="submit"
                                    class="p-1 border rounded btn bg-slate-600 dark:bg-gray-700 hover:bg-gray-400 hover:dark:bg-gray-500"
                                    id="submit">Add category</button>

                                @error('name')
                                    <div class="mt-1 mb-1 text-red-600">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">

                            </div>
                        </div>
                    </form>

                    <!-- Close Button -->
                    <button wire:click="close" class="px-4 py-2 text-white bg-red-500 rounded-md">
                        Close
                    </button>
                </div>
            </div>
        HTML;
    }
}
