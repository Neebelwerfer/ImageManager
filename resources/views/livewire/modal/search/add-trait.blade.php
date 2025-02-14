<x-modal>
    <x-slot name="title">
        Add Trait
    </x-slot>

    <x-slot name="content">
        <div class="grid w-full h-full grid-cols-5 grid-rows-4 gap-2 rounded">
            @foreach ($traits as $trait)
                <button class="flex items-center justify-center w-full h-full p-2 border rounded bg-slate-600 hover:bg-slate-500" wire:click="selectEntry({{ $trait->id }})">
                    {{ $trait->name }}
                </button>
            @endforeach
        </div>
    </x-slot>

    <x-slot name="buttons">
        <div class="flex flex-row justify-between w-full gap-2">
            <x-button wire:click="$dispatch('closeModal')">Close</x-button>
        </div>
    </x-slot>
</x-modal>
