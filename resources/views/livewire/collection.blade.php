<div class="flex flex-row min-h-screen">
    <x-sidebar>
        <x-sidebar.button active="{{ $this->type == 'albums' }}" wire:click="setType('albums')">Albums</x-sidebar.button>
        <x-sidebar.button active="{{ $this->type == 'categories' }}" wire:click="setType('categories')">Categories</x-sidebar.button>
        <x-sidebar.button active="{{ $this->type == 'images' }}" wire:click="setType('images')">Images</x-sidebar.button>
    </x-sidebar>

    <div class="flex justify-center flex-grow">
        <div class="flex flex-col flex-grow-0 ">

        @if($this->type == 'images')
            <livewire:collection.images placeholder="Loading images..." wire:lazy/>
        @endif

        @if($this->type == 'categories')
            <livewire:collection.categories placeholder="Loading categories..." wire:lazy/>
        @endif
    </div>
</div>
