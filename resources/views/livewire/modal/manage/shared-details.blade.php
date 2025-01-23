<x-modal>
    <x-slot name="title">
        Shared {{ $type }}
    </x-slot>

    <x-slot name="content">
        <div class="flex">
            <ul>
                <li>Shared by: {{ $this->data['sharedBy'] }}</li>
                <li>Access Level: {{ $this->data['accessLevel'] }}</li>
            </ul>
        </div>
    </x-slot>

    <x-slot name="buttons">
        <div class="flex flex-row justify-between w-full gap-2">
            <button class="p-1 mt-4 bg-red-700 border rounded btn dark:bg-red-700 hover:bg-gray-400 hover:dark:bg-gray-500" wire:click="stopSharing">Stop Sharing</button>
            <button class="p-1 mt-4 bg-gray-700 border rounded btn dark:bg-slate-500 hover:bg-gray-400 hover:dark:bg-gray-500" wire:click="$dispatch('closeModal')">Close</button>
        </div>
    </x-slot>
</x-modal>
