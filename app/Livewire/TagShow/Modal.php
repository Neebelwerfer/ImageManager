<?php

namespace App\Livewire\TagShow;

use App\Models\ImageTag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Modal extends Component
{
    public $tag;

    #[Validate('required|min:2')]
    public $newTag = '';

    public function saveTag()
    {
        $this->validate();

        if(ImageTag::where('name', $this->newTag)->exists()) {
            Session::flash('message', 'Tag already exists');
            return;
        }
        $tag = new ImageTag();
        $tag->name = $this->newTag;
        $tag->owner_id = Auth::user()->id;
        $tag->save();
    }

    public function close()
    {
        $this->dispatch('closeModal');
    }

    public function selectTag($id)
    {
        if($id == -1) {
            $this->tag = null;
        }
        $this->dispatch('tagSelected', $id);
        $this->close();
    }

    #[Computed()]
    public function tags()
    {
        return ImageTag::where('owner_id', Auth::user()->id)->get();
    }

    public function render()
    {
        return <<<'HTML'
            <div class="fixed inset-0 z-40 flex items-center justify-center bg-gray-900 bg-opacity-50">
                <!-- Modal Content -->
                <div class="w-1/2 max-w-3xl p-8 bg-gray-700 rounded-lg shadow-lg"  x-on:click.away="$wire.close">
                    <h2 class="mb-4 text-2xl font-bold border-b">Tags</h2>

                    <div class="grid grid-flow-row grid-cols-5 mb-6">
                        @foreach ($this->tags as $tag)
                            <div class="w-full py-2 border-gray-900">
                                <button class="w-full px-5 py-2 mx-2 text-white rounded-md hover:bg-gray-500 hover:dark:bg-gray-500" wire:click="selectTag({{ $tag->id }})">{{ $tag->name }}</button>
                            </div>
                        @endforeach
                    </div>


                    <form class="border-t border-gray-900" wire:submit="saveTag">
                        @csrf

                        <label for="newTag" class="form-label">Name</label>
                        <div class="mb-3">
                            <div>
                                <input type="text" class="text-black form-control" wire:model="newTag">
                                <button type="submit"
                                    class="p-1 border rounded btn bg-slate-600 dark:bg-gray-700 hover:bg-gray-400 hover:dark:bg-gray-500"
                                    id="submit">Add tag</button>

                                @error('name')
                                    <div class="mt-1 mb-1 text-red-600">{{ $message }}</div>
                                @enderror
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
