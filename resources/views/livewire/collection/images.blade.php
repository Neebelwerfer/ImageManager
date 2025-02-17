<div class="flex justify-center w-full h-full" x-data="{selectedImages: $wire.entangle('selectedImages')}">
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
                        <button class="p-1 mx-3 mt-4 border rounded btn hover:bg-gray-400 hover:dark:bg-gray-500" type="button" wire:click="changeSelectMode" :class="$wire.editMode ? 'bg-gray-500' : 'bg-gray-700'">Edit</button>
                    </div>
                </form>
                <template x-if="$wire.editMode">
                    <div class="ml-3">
                        <x-button class="h-fit">Add To Category</x-button>
                        <x-button class="h-fit">Add To Album</x-button>
                        <x-button class="h-fit" wire:click='deleteSelected'>Delete</x-button>
                    </div>
                </template>
            </div>
            <div>
                {{ $images->links() }}
            </div>
        </x-slot>
        @foreach ($images as $image)
            <x-grid.image-card-button :image="$image" wire:model="selectedImages.{{ $image->uuid }}" x-on:click="
                if($wire.editMode)
                    $wire.selectedImages['{{ $image->uuid }}'] = !$wire.selectedImages['{{ $image->uuid }}'];
                else
                    Livewire.navigate('{{ route('image.show', $image->uuid) }}');"
                owned="{{ $image->owner_id == Auth::user()->id }}" wire:key='grid-{{ $image->uuid }}' />
        @endforeach
    </x-grid>
</div>
