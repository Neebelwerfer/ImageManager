<x-app-layout>
    <div class="flex flex-row min-h-screen">
        <x-sidebar>
            <x-sidebar.link-button route="{{ route('manage.categories') }}">Categories</x-sidebar.link-button>
            <x-sidebar.link-button route="{{ route('manage.tags') }}">Tags</x-sidebar.link-button>
        </x-sidebar>

        <div class="flex justify-center flex-grow">
            <div class="flex flex-col flex-grow-0">
                {{ $slot }}
            </div>
        </div>
    </div>
</x-app-layout>
