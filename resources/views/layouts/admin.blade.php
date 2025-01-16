<x-app-layout>

    @if(isset($header))
        <x-slot name="header">
            {{ $header }}
        </x-slot>
    @endif

    <div class="flex flex-row flex-grow min-h-full">
        <x-sidebar>
            <x-sidebar.link-button active="{{ request()->routeIs('admin') }}" route="{{ route('admin') }} ">Dashboard</x-sidebar.link-button>
            <x-sidebar.link-button active="{{ request()->routeIs('admin.users') }}" route="{{ route('admin.users') }}">User Management</x-sidebar.link-button>

        </x-sidebar>

        <div class="flex justify-center flex-grow">
            <div class="flex flex-col flex-grow-0 w-full">
                {{ $slot }}
            </div>
        </div>
    </div>
</x-app-layout>
