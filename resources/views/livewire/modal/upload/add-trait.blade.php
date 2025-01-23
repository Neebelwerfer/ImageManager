<x-modal>
    <x-slot name="title">
        Add trait
    </x-slot>

    <x-slot name="content">
        <div>
            {{ $traits->links() }}
        @foreach ($traits as $trait)
            <x-button wire:click="selectTrait({{ $trait }})">{{ $trait->name }}</x-button>
        @endforeach
        </div>
    </x-slot>

    <x-slot name="buttons">

    </x-slot>
</x-modal>
