<x-modal>
    <x-slot name="title">
        <h1>Error</h1>
    </x-slot>

    <x-slot name="content">
        <div class="flex justify-center w-full">
            <p class="text-red-500">{{ $message }}</p>
        </div>
    </x-slot>

    <x-slot name="buttons">
        <x-button wire:click='closeModal'>Close</x-button>
    </x-slot>
</x-modal>
