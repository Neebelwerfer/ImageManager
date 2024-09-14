<?php

namespace App\Livewire\ImageShow;

use App\Models\Image;
use Livewire\Component;

class Rating extends Component
{

    public $image;
    public $rating;

    public function close()
    {
        $this->dispatch('closeModal');
    }

    public function mount(Image $image)
    {
        if(!isset($image) or empty($image)) {
            abort(404);
        }

        $this->image = $image;
        if(!isset($this->image)) {
            abort(404);
        }

        $this->rating = $this->image->rating;
    }

    public function saveRating()
    {
        if($this->rating != $this->image->rating) {
            $this->image->rating = $this->rating;
            $this->image->save();
        }

        $this->close();
    }

    public function render()
    {
        return <<<'HTML'
            <div class="fixed inset-0 z-40 flex items-center justify-center bg-gray-900 bg-opacity-50">
                <!-- Modal Content -->
                <div class="w-1/2 max-w-3xl p-8 bg-gray-700 rounded-lg shadow-lg"  x-on:click.away="$wire.close">
                    <h2 class="mb-4 text-2xl font-bold border-b">Categories</h2>

                    <form class="border-t border-gray-900" wire:submit="saveRating">
                        @csrf

                        <label for="rating" class="form-label">Name</label>
                        <div class="mb-3">
                            <div>
                                <input type="number" class="text-black" wire:model="rating" min=0 max=10 default={{ $rating }}>
                                <button type="submit"
                                    class="p-1 border rounded btn bg-slate-600 dark:bg-gray-700 hover:bg-gray-400 hover:dark:bg-gray-500"
                                    id="submit">Save Rating</button>

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
