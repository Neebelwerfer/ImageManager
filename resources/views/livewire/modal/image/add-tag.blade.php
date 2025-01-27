<x-modal formAction="save">
    <x-slot name="title">
        Add Tag
    </x-slot>

    <x-slot name="content">
        <div class="flex flex-col">
            <label for="name" class="">Name</label>
            <input type="text" class="text-black form-control" wire:model="name" placeholder="Name..."/>
        </div>
        <div>
            <label for="personal" class="">Personal?</label>
            <input type="checkbox" class="text-black form-control" wire:model="personal"/>
        </div>
    </x-slot>

    <x-slot name="buttons">
        <button class="p-1 mt-4 bg-green-600 border rounded btn dark:bg-green-700 hover:bg-gray-400 hover:dark:bg-gray-500" type="submit">Add</button>
        <button class="p-1 mt-4 bg-red-700 border rounded btn dark:bg-red-700 hover:bg-gray-400 hover:dark:bg-gray-500" type="button" wire:click="closeModal">Cancel</button>
    </x-slot>
</x-modal>
