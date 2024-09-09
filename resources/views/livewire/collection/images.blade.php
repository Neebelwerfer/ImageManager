<x-grid>
    <x-slot name="header">
        <div class="flex flex-row justify-center gap-2">
            <div class="flex flex-col">
            <label for="name" class="">Name</label>
            <input type="text" class="text-black form-control" wire:model="name" placeholder="Name..."/>
            </div>
            <div class="flex flex-col">
            <label for="rating" class="">Min Rating</label>
            <input type="number" class="text-black form-control" wire:model="rating" placeholder="Rating..." min="0" max="10"/>
            </div>
            <button class="p-1 border rounded btn bg-slate-600 dark:bg-gray-700 hover:bg-gray-400 hover:dark:bg-gray-500" wire:click="search">Search</button>
        </div>

        <div>
            {{ $images->links() }}
        </div>
    </x-slot>
    @foreach ($images as $image)
        <x-grid.image-card :image="$image" route="{{ route('image.show', $image->uuid) }}" />
    @endforeach
</x-grid>
