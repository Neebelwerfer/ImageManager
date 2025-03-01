<div class="flex justify-center w-full h-full" x-data="multiSelect($wire.entangle('selectedImages'))">
    <x-grid class="w-7/12">
        <x-slot name="header">
            <div>
                <form class="flex flex-row justify-between mx-3" wire:submit.prevent="filter">
                    <div class="flex flex-col w-1/2">
                        <label>Tags</label>
                        <input class="text-black" type="text" wire:model='tags'>
                    </div>
                    <div class="flex self-end">
                        <x-button class="h-fit" type="submit">Search</x-button>
                        <button class="p-1 mx-3 mt-4 border rounded btn hover:bg-gray-400 hover:dark:bg-gray-500" type="button" x-on:click="changeSelectMode" :class="$wire.editMode ? 'bg-gray-500' : 'bg-gray-700'">Edit</button>
                    </div>
                </form>
                <div class="my-2">
                    {{ $this->images->links() }}
                </div>
                <template x-if="$wire.editMode">
                    <div class="flex flex-row ml-3">
                        <x-button class="h-fit" x-on:click='selectAll(true)'  x-show="!allSelected">Select All</x-button>
                        <x-button class="h-fit" x-on:click='selectAll(false)' x-show="allSelected">Deselect All</x-button>
                        <x-dropdown class="ml-2" align="left">
                            <x-slot name="trigger">
                                <p class="p-1 mt-4 bg-gray-700 border rounded dark:bg-slate-700 hover:bg-gray-400 hover:dark:bg-gray-500">Options</p>
                            </x-slot>
                            <x-slot name="content">
                                <div class="flex flex-col mx-0.5">
                                    <button class="p-1 bg-gray-700 border rounded border-slate-600 dark:bg-slate-700 hover:bg-gray-400 hover:dark:bg-gray-500">Change Category</button>
                                    <button class="p-1 bg-gray-700 border rounded border-slate-600 dark:bg-slate-700 hover:bg-gray-400 hover:dark:bg-gray-500">Add To Album</button>
                                    <button class="p-1 bg-teal-700 border rounded border-slate-600 hover:bg-teal-400" >Share</button>
                                    <button class="p-1 bg-red-700 border rounded border-slate-600 hover:bg-red-400" wire:confirm='Are you sure you want to delete selected images?' wire:click='deleteSelected'>Delete</button>
                                </div>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </template>
            </div>

        </x-slot>
        @foreach ($this->images as $image)
            <x-grid.image-card-button :image="$image" wire:model="selectedImages.{{ $image->uuid }}" x-on:click="onClick('{{ $image->uuid }}', () => Livewire.navigate('{{ route('image.show', $image->uuid) }}'))"
                owned="{{ $image->owner_id == Auth::user()->id }}" wire:key='grid-{{ $image->uuid }}' />
        @endforeach
    </x-grid>
</div>
