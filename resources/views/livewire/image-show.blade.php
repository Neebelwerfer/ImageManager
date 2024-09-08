<x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        {{ $image->name }}
    </h2>
</x-slot>

<div>
    <div class="flex justify-center my-2">
        <div>
            <img src="{{ asset($image->path) }}" alt="{{ $image->name }}" width="500" height="600">
        </div>


    </div>
    <div class="flex flex-col border-t border-gray-800 justify-items-start">
        <div class="w-full">
            <h1>Name: {{ $image->name }}</h1>
            <p>Rating: {{ $image->rating }}</p>

            <button class="p-1 bg-red-600 border border-red-500 rounded" wire:click='delete'
                wire:confirm="Are you sure you want to delete this image?">Delete</button>
        </div>
    </div>
</div>
