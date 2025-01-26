<x-grid>
    <x-slot name="header">
        <div>
            <form class="flex flex-row justify-between mx-3" wire:submit.prevent="filter">
                <div class="flex flex-col w-1/2">
                    <label>Tags</label>
                    <input class="text-black" type="text" wire:model='tags'>
                </div>
                <div class="flex self-end">
                    <x-button class="h-fit" type="submit">Search</x-button>
                </div>
            </form>
        </div>
        <div>
            {{ $images->links() }}
        </div>
    </x-slot>
    @foreach ($images as $image)
        <x-grid.image-card :image="$image" route="{{ route('image.show', $image->uuid) }}" owned="{{ $image->owner_id == Auth::user()->id }}" wire:key='grid-{{ $image->uuid }}' />
    @endforeach
</x-grid>
