<x-modal formAction="save">
    <x-slot name="title">
        Edit {{ $trait->name }}
    </x-slot>

    <x-slot name="content">
        @if($trait->type == 'float' || $trait->type == 'integer' || $trait->type == 'text')
        <div class="flex flex-col">
            <div class="flex flex-row">
                <label>Min: </label><p>{{ $trait->min }}</p>
            </div>
            <div class="flex flex-row">
                <label>Max: </label><p>{{ $trait->max }}</p>
            </div>
        </div>
        @endif

        <div class="flex flex-col">
            <input class="px-2 py-1 ml-1 text-black border rounded w-fit"
                                @if($trait->type == 'text')
                                type="text"
                                @elseif($trait->type == 'integer' || $trait->type == 'float')
                                type="number"
                                size="4"
                                @elseif($trait->type == 'boolean')
                                type="checkbox"
                                @endif
                                wire:model="value">
            @error('value')
            <span class="text-red-500">{{ $message }}</span>
            @enderror
        </div>
    </x-slot>

    <x-slot name="buttons">
        <div class="flex flex-row justify-between w-full gap-2">
            <button class="p-1 mt-4 bg-green-700 border rounded btn dark:bg-green-500 hover:bg-gray-400 hover:dark:bg-gray-500" type="submit">Save</button>
            <button class="p-1 mt-4 bg-red-700 border rounded btn dark:bg-red-700 hover:bg-gray-400 hover:dark:bg-gray-500" type="button" wire:click="closeModal">Cancel</button>
        </div>
    </x-slot>
</x-modal>
