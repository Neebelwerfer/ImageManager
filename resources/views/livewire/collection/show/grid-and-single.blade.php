<div class="relative w-full h-full overflow-hidden" x-data="{ gridView: $wire.entangle('gridView')} ">
    @if(isset($collectionName))
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ $collectionName }}
        </h2>
    </x-slot>
    @endif

    <div class="relative flex flex-col h-full">
        <div class="flex flex-row justify-between pb-2">
            <div class="flex flex-row mx-2 mt-2">
                @if($this->showBackButton)
                    <button class="p-1 mr-5 border rounded bg-slate-600 dark:bg-gray-700 hover:bg-gray-400 hover:dark:bg-gray-500" wire:click="goBack()">Back</button>
                @endif
                <button id="Grid"
                    class="p-1 border rounded hover:bg-gray-400 hover:dark:bg-gray-500"
                    x-on:click="gridView = true" :class="gridView ? 'bg-slate-400 dark:bg-gray-500' : ' bg-slate-600 dark:bg-gray-700'" >Grid</button>
                <button id="Single"
                    class="p-1 border rounded hover:bg-gray-400 hover:dark:bg-gray-500"
                    x-on:click="gridView = false" :class="!gridView ? 'bg-slate-400 dark:bg-gray-500' : ' bg-slate-600 dark:bg-gray-700'">Single</button>
            </div>
            @if($singleImage !== null)
            <div class="mt-2 mr-4 @if($gridView) hidden @endif">
                <button wire:click="$dispatch('openModal', {component: 'modal.image.details', arguments: {imageUuid: '{{ $singleImage->uuid }}', source: '{{ $collectionType }}'}})" class="p-1 border rounded bg-slate-600 dark:bg-gray-700 hover:bg-gray-400 hover:dark:bg-gray-500">Details</button>
            </div>
            @endif
            {{ $this->images->links() }}
        </div>

        <div x-show="gridView" class="flex justify-center">
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
                                    <x-button class="px-2 h-fit" type="button" wire:click="$dispatch('openModal', {component: 'modal.manage.edit-collection', arguments: {'collectionId': '{{ $collectionID }}', 'collectionType': '{{ $collectionType }}'} })">Edit</x-button>
                                </div>
                            </form>
                        </div>
                    </x-slot>

                    @foreach ($this->images as $key => $image)
                        <x-grid.image-card-button :image="$image" x-on:click="$wire.show({{ $key }})" owned="{{ $image->owner_id == Auth::user()->id }}" wire:key='grid-{{ $image->uuid }}'>
                        </x-grid.image-card-button>
                    @endforeach
                </x-grid>
            </div>
        </div>


        @if (isset($singleImage))
            <div x-show="!gridView" class="h-full">
                <button wire:click="previousImage" class="absolute z-30 left-0 w-20 h-full @if(!$this->gotPrevious()) bg-gray-600 @else bg-gray-900 hover:bg-gray-700 @endif border-l border-y"><</button>
                <button wire:click="nextImage" class="absolute z-30 right-0 w-20 h-full @if(!$this->gotNext()) bg-gray-600 @else bg-gray-900 hover:bg-gray-700 @endif border-r border-y">></button>

                <div class="flex flex-col justify-center w-full h-full border-y"  x-on:keyup.left.window="$wire.previousImage()" x-on:keyup.right.window="$wire.nextImage()">
                    <div class="flex flex-row justify-center h-full">
                        <div class="flex justify-center flex-grow-0">
                            <livewire:image classes="flex justify-center h-full" vWidth="w-10/12" hWidth="w-3/4" :image="$singleImage"/>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@script
<script>
    const urlParams = new URLSearchParams(window.location.search);
    const imagesCount = typeof($wire.images);

    console.log(imagesCount);
</script>
@endscript
