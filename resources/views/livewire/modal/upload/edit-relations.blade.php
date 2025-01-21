<x-modal>
    <x-slot name="title">
        {{ $type }}
    </x-slot>

    <x-slot name="content">
        @if ($this->entries->count() > 0)
        <div class="grid w-full h-full grid-cols-5 grid-rows-4 gap-2 rounded">
                @foreach ($this->entries as $entry)
                    <button class="flex items-center justify-center w-full h-full p-2 border rounded @if($entry->user_id == Auth::user()->id) bg-slate-600 @else bg-cyan-700/80 @endif hover:bg-slate-500" wire:click="selectEntry({{ $entry->id }})">
                        {{ $entry->name }}
                    </button>
                @endforeach
            </div>
        @endif
        @if($this->noneOption)
            <button class="p-1 mt-4 border rounded btn bg-slate-600 dark:bg-gray-700 hover:bg-gray-400 hover:dark:bg-gray-500" wire:click="selectEntry(-1)">None</button>
        @endif
    </x-slot>

    <x-slot name="buttons">
        <button class="p-1 mt-4 border rounded btn bg-slate-600 dark:bg-gray-700 hover:bg-gray-400 hover:dark:bg-gray-500" wire:click="closeModal">Close</button>
        <button class="p-1 mt-4 border rounded btn bg-slate-600 dark:bg-gray-700 hover:bg-gray-400 hover:dark:bg-gray-500" wire:click="$dispatch('openModal', {component: 'modal.image.create-relation-classification', arguments: {type: '{{ $type }}'}})">Create {{ $type }}</button>
    </x-slot>
</x-modal>
