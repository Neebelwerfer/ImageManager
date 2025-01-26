<x-app-layout>
    <div class="flex flex-row flex-grow min-h-full">
        <x-sidebar>
            <x-sidebar.link-button route="{{ route('manage.albums') }}">Albums</x-sidebar.link-button>
            <x-sidebar.link-button route="{{ route('manage.categories') }}">Categories</x-sidebar.link-button>
            <x-sidebar.link-button route="{{ route('manage.traits') }}">Traits</x-sidebar.link-button>
        </x-sidebar>

        <div class="flex justify-center flex-grow">
            <div class="flex flex-col flex-grow-0 w-full">
                {{ $slot }}
            </div>
        </div>
    </div>
</x-app-layout>
