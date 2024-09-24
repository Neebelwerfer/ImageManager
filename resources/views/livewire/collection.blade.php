<div class="flex flex-row flex-grow min-h-full">
    <x-sidebar>
        <x-sidebar.link-button route="{{ route('collection.show', 'albums') }}" active="{{ $this->type == 'albums' }}">Albums</x-sidebar.button>
        <x-sidebar.link-button route="{{ route('collection.show', 'categories') }}" active="{{ $this->type == 'categories' }}">Categories</x-sidebar.button>
        <x-sidebar.link-button route="{{ route('collection.show', 'images') }}" active="{{ $this->type == 'images' }}">Images</x-sidebar.button>
    </x-sidebar>

    <div class="flex justify-center flex-grow">
        <div class="flex flex-col flex-grow-0 flex-shrink">

            @if(!empty($this->type))
            <h2 class="flex justify-center text-xl font-semibold leading-tight text-gray-800 underline uppercase dark:text-gray-200">
                {{ $this->type }}
            </h2>
            @endif

            @if (empty($this->type))
                <div class="flex flex-row gap-2 mt-16 w-fit">
                    <x-button-link route="{{ route('collection.show', 'albums') }}">Albums</x-button-link>
                    <x-button-link route="{{ route('collection.show', 'categories') }}">Categories</x-button-link>
                    <x-button-link route="{{ route('collection.show', 'images') }}">Images</x-button-link>
                </div>
            @endif

            @if ($this->type == 'albums')
                <livewire:collection.albums placeholder="Loading albums..." wire:lazy />
            @endif

            @if ($this->type == 'images')
                <livewire:collection.images placeholder="Loading images..." wire:lazy />
            @endif

            @if ($this->type == 'categories')
                <livewire:collection.categories placeholder="Loading categories..." wire:lazy />
            @endif
        </div>
    </div>
</div>
