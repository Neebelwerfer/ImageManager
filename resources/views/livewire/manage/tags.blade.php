<div class="flex justify-center">
    <div class="flex flex-col justify-center gap-2 mt-16">
        <h1 class="text-xl font-semibold leading-tight text-center text-gray-800 dark:text-gray-200">Categories</h1>
        <div class="flex justify-center">
            <span>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                <input class="px-2 py-1 text-black border rounded" type="text" placeholder="Name..." wire:model="name">
                <button class="p-1 bg-blue-600 border border-blue-500 rounded" wire:click="create" wire:target="create">Create</button>
            </span>
        </div>
        @error('name') <span class="text-red-500">{{ $message }}</span> @enderror

        <div class="grid w-full grid-flow-row grid-cols-5 gap-2 mx-2">
            @foreach ($tags as $tag)
                <div class="flex flex-row justify-between p-2 bg-gray-800 border rounded w-fit">
                    <div class="w-full">
                        <h1>{{ $tag->name }}</h1>
                        <h1>Images: {{ $tag->images->count() }}</h1>
                    </div>
                    <button class="px-2 ml-2 bg-red-700 border" wire:click='delete({{ $tag->id }})' wire:confirm="Are you sure you want to delete this tag?">Delete</button>
                </div>
            @endforeach
        </div>
    </div>
</div>
