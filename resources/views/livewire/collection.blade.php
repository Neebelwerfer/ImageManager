<div class="flex flex-row min-h-screen">
    <div class="justify-center flex-shrink-0 w-64 bg-gray-800">
        <ul class="mt-5 space-y-2">
            <li><button class="w-full p-1 border-t border-b btn hover:bg-gray-400 hover:dark:bg-gray-500 @if($this->type == 'images') bg-gray-400 dark:bg-gray-500 @endif" wire:click="setType('images')">Images</button></li>
            <li><button class="w-full p-1 border-t border-b btn hover:bg-gray-400 hover:dark:bg-gray-500 @if($this->type == 'categories') bg-gray-400 dark:bg-gray-500 @endif" wire:click="setType('categories')">Categories</button></li>
        </ul>

    </div>

    <div class="flex justify-center flex-grow">
        <div class="flex flex-col flex-grow-0 ">

        @if($this->type == 'images')
            <livewire:collection.images placeholder="Loading images..." wire:lazy/>
        @endif
    </div>
</div>
