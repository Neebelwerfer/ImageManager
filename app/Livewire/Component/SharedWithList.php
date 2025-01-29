<?php

namespace App\Livewire\Component;

use App\Models\SharedResources;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class SharedWithList extends Component
{
    #[Locked()]
    public $id;

    #[Locked()]
    public $type = "category";

    #[On('updateShared')]
    public function updateShared()
    {
        unset($this->sharedWith);
    }

    public function removeShared($id) {
        SharedResources::find($id)->delete();
    }

    #[Computed()]
    public function sharedWith() {
        return SharedResources::where('resource_id', $this->id)->where('type', $this->type)->get();
    }

    public function render()
    {
        return <<<'HTML'
        <div class="flex flex-col" x-data="{ open: false } ">
            <div class="flex flex-row border-b border-slate-800" x-on:click="open = !open">
                <p>Shared With</p>
                <x-arrow class="self-center"/>
            </div>
            <div x-show="open">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th scope="col" class="px-6 py-1 text-xs font-bold text-left text-gray-500 uppercase dark:text-gray-400">
                            Email
                        </th>
                        <th scope="col" class="px-6 py-1 text-xs font-bold text-left text-gray-500 uppercase dark:text-gray-400">
                            AccessLevel
                        </th>
                        <th scope="col" class="px-6 py-1 text-xs font-bold text-left text-gray-500 uppercase dark:text-gray-400">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    @foreach ($this->sharedWith as $sharedResource)
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                            <td class="px-6 py-2 text-sm font-medium text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                {{ $sharedResource->shared_with->email }}
                            </td>
                            <td class="px-6 py-2 text-sm text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                {{ $sharedResource->level }}
                            </td>
                            <td class="px-6 py-2 text-sm text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                <button class="p-1 bg-red-700 border rounded btn dark:bg-red-700 hover:bg-gray-400 hover:dark:bg-gray-500" wire:click="removeShared({{ $sharedResource->id }})">Remove</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>
        HTML;
    }
}
