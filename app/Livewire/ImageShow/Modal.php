<?php

namespace App\Livewire\ImageShow;

use App\Models\Image;
use App\Models\ImageCategory;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Modal extends Component
{
    public $path;

    public function close()
    {
        $this->dispatch('closeModal');
    }


    public function render()
    {
        return <<<'HTML'
            <div class="fixed inset-0 z-40 flex items-center justify-center bg-gray-900 bg-opacity-50">
                <!-- Modal Content -->
                <div class="w-1/2 max-w-3xl p-8 bg-gray-700 rounded-lg shadow-lg"  x-on:click.away="$wire.close">
                    <h2 class="mb-4 text-2xl font-bold">Duplicate Image</h2>

                    <div class="mb-6 space-y-2 overflow-auto">
                        <img class="w-full" src="{{ asset($path) }}" alt="image">
                    </div>




                    <!-- Close Button -->
                    <button wire:click="close" class="px-4 py-2 text-white bg-red-500 rounded-md">
                        Close
                    </button>
                </div>
            </div>
        HTML;
    }
}
