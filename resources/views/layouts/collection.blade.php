<x-app-layout>

    @if(isset($header))
        <x-slot name="header">
            {{ $header }}
        </x-slot>
    @endif

    <div class="flex flex-row flex-grow">
        <x-sidebar>
            <x-sidebar.link-button route="{{ route('collection.show', 'albums') }}">Albums</x-sidebar.button>
            <x-sidebar.link-button route="{{ route('collection.show', 'categories') }}">Categories</x-sidebar.button>
            <x-sidebar.link-button route="{{ route('collection.show', 'images') }}">Images</x-sidebar.button>
        </x-sidebar>

        <div class="flex justify-center w-full">
            <div class="flex flex-col flex-grow-0 w-full">
                {{ $slot }}
            </div>
        </div>
    </div>
</x-app-layout>
