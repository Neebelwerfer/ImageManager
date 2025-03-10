<x-modal>
    <x-slot name="title">
        Edit {{ $collectionType }}
    </x-slot>

    <x-slot name="content">
        <form wire:submit.prevent="save">
            <div class="flex flex-col">
                <label for="name" class="">Name</label>
                <input type="text" class="text-black form-control" wire:model="name" placeholder="Name..."/>
            </div>
        </form>

        <livewire:component.shared-with-list id="{{ $collectionID }}" type="{{ $collectionType }}" x-transition/>
    </x-slot>

    <x-slot name="buttons">
        <div class="flex flex-row justify-between w-full gap-2">
            <span>
            <x-button wire:click="save">Save</x-button>
            <x-button wire:click="$dispatch('closeModal')">Cancel</x-button>
            <button class="p-1 mt-4 bg-red-700 border rounded btn dark:bg-red-700 hover:bg-gray-400 hover:dark:bg-gray-500" wire:click="delete">Delete</button>
            </span>
            <button class="p-1 mt-4 bg-green-700 border rounded btn dark:bg-green-500 hover:bg-green-400 hover:dark:bg-gray-500" wire:click="$dispatch('openModal', {component: 'modal.manage.share', arguments: {type: '{{ $collectionType }}', id: '{{ $collectionID }}'} })">Share</button>
        </div>
    </x-slot>
</x-modal>
