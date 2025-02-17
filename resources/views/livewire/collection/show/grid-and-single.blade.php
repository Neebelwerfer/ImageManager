<div class="relative w-full h-full overflow-hidden" x-data="collectionShow($wire.entangle('count'), $wire.entangle('gridView'))">
    @if(isset($collectionName))
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ $collectionName }}
        </h2>
    </x-slot>
    @endif

    <div class="relative flex flex-col h-full">
        <div class="flex flex-row justify-between pb-2 mr-2">
            <div class="flex flex-row mx-2 mt-2">
                <button class="p-1 mr-5 border rounded bg-slate-600 dark:bg-gray-700 hover:bg-gray-400 hover:dark:bg-gray-500" wire:click="goBack()">Back</button>
                <button id="Grid"
                    class="p-1 border rounded hover:bg-gray-400 hover:dark:bg-gray-500"
                    x-on:click="gridView = true" :class="gridView ? 'bg-slate-400 dark:bg-gray-500' : ' bg-slate-600 dark:bg-gray-700'" >Grid</button>
                <button id="Single"
                    class="p-1 border rounded hover:bg-gray-400 hover:dark:bg-gray-500"
                    x-on:click="gridView = false" :class="!gridView ? 'bg-slate-400 dark:bg-gray-500' : ' bg-slate-600 dark:bg-gray-700'">Single</button>
            </div>
            {{ $this->images->links() }}
            <x-button class="px-2 h-fit" type="button" wire:click="$dispatch('openModal', {component: 'modal.manage.edit-collection', arguments: {'collectionId': '{{ $collectionID }}', 'collectionType': '{{ $collectionType }}'} })">Edit Collection</x-button>

            @if(count($this->images) > 0)
            <div class="mt-2 mr-4" :class="gridView ? 'hidden' : ''">
                <button wire:click="$dispatch('openModal', {component: 'modal.image.details', arguments: {imageUuid: '{{ $this->images[$count]->uuid }}', source: '{{ $collectionType }}'}})" class="p-1 border rounded bg-slate-600 dark:bg-gray-700 hover:bg-gray-400 hover:dark:bg-gray-500">Details</button>
            </div>
            @endif
        </div>

        <div x-show="gridView" class="flex justify-center">
            {{-- <div class="w-2/12 mt-8 mb-2 border-t border-r shadow-sm bg-slate-900/20">
                <h1 class="ml-1 text-3xl font-bold underline">Search Parameters</h1>
                <div class="flex justify-center w-full">
                    <x-button class="w-full" wire:click="$dispatch('openModal', {component: 'modal.search.add-trait'})">Add Trait</x-button>
                </div>
                @foreach ($searchTraits as $key => $entry)
                    <div class="w-full border">
                        <div class="flex justify-center">
                        <p>{{ $entry['name'] }}</p>
                        </div>
                        <input class="text-black" wire:model='searchTraits.{{ $key }}.value' value="{{ $entry['value'] }}" type="number" size="4">
                    </div>
                @endforeach
            </div> --}}

            <div class="flex flex-col justify-center w-7/12 justify-items-center">
                <x-grid>
                    <x-slot name="header">
                        <div>
                            <form class="flex flex-row justify-between mx-3" wire:submit.prevent="filter">
                                <div class="flex flex-col w-1/2">
                                    <label>Tags</label>
                                    <input class="text-black" type="text" wire:model='tags'>
                                </div>
                                <div class="flex self-end gap-2">
                                    <x-button class="h-fit" type="submit">Search</x-button>
                                    <button class="p-1 px-2 mt-4 border rounded btn hover:bg-gray-400 hover:dark:bg-gray-500" type="button" wire:click="changeSelectMode" :class="$wire.editMode ? 'bg-gray-500' : 'bg-gray-700'">Edit</button>
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
                    </x-slot>

                    @foreach ($this->images as $key => $image)
                        <x-grid.image-card-button :image="$image" wire:model="selectedImages.{{ $image->uuid }}" owned="{{ $image->owner_id == Auth::user()->id }}" wire:key='grid-{{ $image->uuid }}' x-on:click="
                            if($wire.editMode)
                                $wire.selectedImages['{{ $image->uuid }}'] = !$wire.selectedImages['{{ $image->uuid }}'];
                            else
                                wire.show('{{ $key }}');">
                        </x-grid.image-card-button>
                    @endforeach
                </x-grid>
            </div>
        </div>

        <div :class="!gridView ? '' : 'collapse'" class="h-full">
            <button wire:click="previousImage" class="absolute z-30 left-0 w-20 h-full @if(!$this->gotPrevious()) bg-gray-600 @else bg-gray-900 hover:bg-gray-700 @endif border-l border-y"><</button>
            <button wire:click="nextImage" class="absolute z-30 right-0 w-20 h-full @if(!$this->gotNext()) bg-gray-600 @else bg-gray-900 hover:bg-gray-700 @endif border-r border-y">></button>

            <div class="flex flex-col justify-center w-full h-full border-y"  x-on:keyup.left.window="$wire.previousImage()" x-on:keyup.right.window="$wire.nextImage()">
                <div class="flex flex-row justify-center h-full">
                    <div class="flex justify-center flex-grow-0">
                        <livewire:image classes="flex justify-center h-full" vWidth="w-10/12" hWidth="w-3/4" :image="$this->images[$count]"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
