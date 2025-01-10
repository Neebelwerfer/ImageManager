<div class="relative h-full overflow-hidden" x-data="{ showOptions: $wire.entangle('showOptions') }">

    @if($this->images->count() == 0)
        <h1>No images available</h1>
    @else
        @if(isset($collection->name))
        <x-slot name="header">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ $collection->name }}
            </h2>
        </x-slot>
        @endif

        <div class="relative flex flex-col h-full">
            <div class="flex flex-row justify-between pb-2">
                <div class="flex flex-row mx-2 mt-2">
                    <button
                        class="p-1 border rounded  @if($gridView) bg-slate-400 dark:bg-gray-500 @else bg-slate-600 dark:bg-gray-700 @endif hover:bg-gray-400 hover:dark:bg-gray-500"
                        wire:click="setGridView(true)" @if($gridView) disabled @endif>Grid</button>
                    <button
                        class="p-1 border rounded @if(!$gridView) bg-slate-400 dark:bg-gray-500 @else bg-slate-600 dark:bg-gray-700 @endif hover:bg-gray-400 hover:dark:bg-gray-500"
                        wire:click="setGridView(false)" @if(!$gridView) disabled @endif>Single</button>
                </div>
                <div class="mt-2 mr-4 @if($gridView) hidden @endif">
                    <button x-on:click="$wire.showOptions = !$wire.showOptions" class="p-1 border rounded bg-slate-600 dark:bg-gray-700 hover:bg-gray-400 hover:dark:bg-gray-500">Details</button>
                </div>
                {{ $this->images->links() }}
            </div>

            @if(isset($singleImage))
                <livewire:image-show.options :image="$singleImage"/>
            @endif

            <div class="@if($gridView) flex  @else collapse @endif justify-center">
                <div class="flex flex-col justify-center justify-items-center">
                    <x-grid>
                        <x-slot name="header">
                            <div>
                                <form wire:submit.prevent="filter">
                                    <div>
                                        <label for="rating" class="">Minimum Rating</label>
                                        <input type="number" wire:model='minRating' name="rating" class="text-black" min=0 max=10>
                                    </div>
                                    <button type="submit">Filter</button>
                                </form>
                            </div>
                        </x-slot>

                        @foreach ($this->images as $key => $image)
                            <x-grid.image-card-button :image="$image" x-on:click="$wire.show({{ $key }})" wire:key='grid-{{ $image->uuid }}'>
                            </x-grid.image-card-button>
                        @endforeach
                    </x-grid>
                </div>
            </div>


            @if (!$gridView && isset($singleImage))
                <div class="h-full">
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
    @endif
</div>
