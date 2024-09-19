<div class="relative flex-grow-0 w-full h-full overflow-hidden" x-on:keyup.left="$wire.previousImage()" x-on:keyup.right="$wire.nextImage()" x-data="{ showOptions: $wire.entangle('showOptions') }">

    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ $collection->name }}
        </h2>
    </x-slot>

    @if(isset($image))
        <livewire:image-show.options :image="$image"/>
    @endif

    <div class="flex flex-row mx-2 mt-2">
        <button
            class="p-1 border rounded  @if($gridView) bg-slate-400 dark:bg-gray-500 @else bg-slate-600 dark:bg-gray-700 @endif hover:bg-gray-400 hover:dark:bg-gray-500"
            wire:click="setGridView(true)" @if($gridView) disabled @endif>Grid</button>
        <button
            class="p-1 border rounded @if(!$gridView) bg-slate-400 dark:bg-gray-500 @else bg-slate-600 dark:bg-gray-700 @endif hover:bg-gray-400 hover:dark:bg-gray-500"
            wire:click="setGridView(false)" @if(!$gridView) disabled @endif>Single</button>
    </div>

    <div class="flex justify-center ">
        <div class="@if($gridView)flex flex-col @else collapse @endif justify-center justify-items-center">
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


    @if (!$gridView && isset($singeleImage))
        <button wire:click="previousImage" class="absolute z-30 mt-2 left-0 w-20 h-5/6 @if(!$this->gotPrevious()) bg-gray-600 @else bg-gray-900 hover:bg-gray-700 @endif border-l border-y"><</button>
        <button wire:click="nextImage" class="absolute z-30 mt-2 right-0 w-20 h-5/6 @if(!$this->gotNext()) bg-gray-600 @else bg-gray-900 hover:bg-gray-700 @endif border-r border-y">></button>

        <div class="flex flex-col justify-center flex-shrink-0 w-full mt-2 border h-5/6">
            <div class="flex flex-row justify-center h-full">
                <div class="flex justify-center"  x-on:click="$wire.showOptions = !$wire.showOptions">
                    <livewire:image classes="flex justify-center w-5/6 h-full" :image="$singeleImage"/>
                </div>
            </div>
        </div>
    @endif
</div>
