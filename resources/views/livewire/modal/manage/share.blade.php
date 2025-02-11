<x-modal>
    <x-slot name="title">
        Share {{ $type }}
    </x-slot>

    <x-slot name="content">
        <form wire:submit.prevent="share">
            <div class="flex flex-col">
                <label for="email" class="">Email</label>
                <input type="text" class="text-black form-control" wire:model="email" placeholder="Email..."/>
            </div>
            <div class="flex flex-col">
                <label for="accessLevel" class="">AccessLevel</label>
                <select class="text-black form-control" wire:model="accessLevel">
                    <option value="view">View</option>
                    <option value="edit">Edit</option>
                </select>
            </div>
        </form>

        <livewire:component.shared-with-list id="{{ $id }}" type="{{ $type }}" x-transition/>
    </x-slot>

    <x-slot name="buttons">
        <div class="flex flex-row justify-between w-full gap-2">
            <button class="p-1 mt-4 bg-gray-700 border rounded btn dark:bg-slate-500 hover:bg-gray-400 hover:dark:bg-gray-500" wire:click="share">Share</button>
            <button class="p-1 mt-4 bg-red-700 border rounded btn dark:bg-red-700 hover:bg-gray-400 hover:dark:bg-gray-500" wire:click="$dispatch('closeModal')">Cancel</button>
        </div>
    </x-slot>
</x-modal>
