<div class="flex flex-row ml-3">
    <x-button class="h-fit" x-on:click='selectAll(true)' x-show="!isAllSelected()">Select All</x-button>
    <x-button class="h-fit" x-on:click='selectAll(false)' x-show="isAllSelected()">Deselect All</x-button>
    <x-dropdown class="ml-2" align="left">
        <x-slot name="trigger">
            <p class="p-1 mt-4 bg-gray-700 border rounded dark:bg-slate-700 hover:bg-gray-400 hover:dark:bg-gray-500">Options</p>
        </x-slot>
        <x-slot name="content">
            {{ $slot }}
        </x-slot>
    </x-dropdown>
</div>
