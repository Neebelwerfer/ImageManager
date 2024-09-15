<div class="h-full" x-data="{ showOptions: false }">

    @if ($gridView)
        <x-grid>
            <x-slot name="header">
                <div class="flex flex-row justify-center mx-2">
                    <button
                        class="p-1 border rounded bg-slate-600 dark:bg-gray-700 hover:bg-gray-400 hover:dark:bg-gray-500"
                        wire:click="setGridView(true)">Grid</button>
                    <button
                        class="p-1 border rounded bg-slate-600 dark:bg-gray-700 hover:bg-gray-400 hover:dark:bg-gray-500"
                        wire:click="setGridView(false)">Single</button>
                </div>

            </x-slot>

            @foreach ($images as $key => $image)
                <x-grid.image-card :image="$image" x-on:click="$wire.show({{ $key }})" >
                </x-grid.image-card>
            @endforeach


        </x-grid>
    @else
        <div class="flex justify-center w-full" x-on:keyup.left="$wire.previousImage()" x-on:keyup.right="$wire.nextImage()">
            <div class="relative flex flex-col">
                <div class="flex justify-center">
                    <button
                        class="p-1 border rounded bg-slate-600 dark:bg-gray-700 hover:bg-gray-400 hover:dark:bg-gray-500"
                        wire:click="setGridView(true)">Grid</button>
                    <button
                        class="p-1 border rounded bg-slate-600 dark:bg-gray-700 hover:bg-gray-400 hover:dark:bg-gray-500"
                        wire:click="setGridView(false)">Single</button>
                </div>
                <div class="flex flex-row justify-center flex-shrink-0 w-full">
                    <button wire:click="previousImage"  class="w-20 h-full @if(!$this->gotPrevious()) bg-gray-600 @else bg-gray-900 hover:bg-gray-700 @endif border-l border-y"><</button>
                    <div class="flex w-4/5"  x-on:click="showOptions = !showOptions">
                        <img class="object-scale-down " src="{{ asset($image->path) }}">
                    </div>
                    <button wire:click="nextImage" class="w-20 h-full @if(!$this->gotNext()) bg-gray-600 @else bg-gray-900 hover:bg-gray-700 @endif border-r border-y">></button>
                </div>
            </div>

            <livewire:image-show.options :image="$image"/>
        </div>
        @endif
</div>
