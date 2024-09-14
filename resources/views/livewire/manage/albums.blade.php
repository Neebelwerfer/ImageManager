<div class="flex flex-col gap-2 mt-16 w-96">
    <h1 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Albums</h1>
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
        <input class="px-2 py-1 text-black border rounded" type="text" placeholder="Name..." wire:model="name">
        <button class="p-1 bg-blue-600 border border-blue-500 rounded" wire:click="create" wire:target="create">Create</button>
    </div>
    @error('name') <span class="text-red-500">{{ $message }}</span> @enderror


    <ul class="space-y-2">
    @foreach ($albums as $album)
        <li class="flex flex-row justify-between p-2 bg-gray-800 border rounded ">
            <div>
                <h1>{{ $album->name }}</h1>
                <h1>Images: {{ $album->images->count() }}</h1>
            </div>
            <button class="px-2 bg-red-700 border" wire:click='delete({{ $album->id }})' wire:confirm="Are you sure you want to delete this album?">Delete</button>
        </li>
    @endforeach
    </ul>
</div>
