<x-app-layout>
    <div class="flex flex-row min-h-screen">
        <x-sidebar>
            <x-sidebar.link-button route="{{ route('collection.show', 'albums') }}">Albums</x-sidebar.button>
            <x-sidebar.link-button route="{{ route('collection.show', 'categories') }}">Categories</x-sidebar.button>
            <x-sidebar.link-button route="{{ route('collection.show', 'images') }}">Images</x-sidebar.button>
        </x-sidebar>

        <div class="flex justify-center flex-grow">
            <div class="flex flex-col flex-grow-0 ">
                {{ $slot }}
            </div>
        </div>
    </div>
</x-app-layout>
