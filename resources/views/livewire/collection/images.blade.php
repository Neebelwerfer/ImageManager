<div class="flex flex-col flex-grow-0 ">
    <div class="flex flex-col justify-center p-2">
        <div class="flex flex-row justify-center gap-2">
            <div class="flex flex-col">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="text-black form-control" wire:model="name" placeholder="Name..."/>
            </div>
            <input type="number" class="text-black form-control" wire:model="rating" placeholder="Rating..." min="0" max="10"/>
            <button class="p-1 border rounded btn bg-slate-600 dark:bg-gray-700 hover:bg-gray-400 hover:dark:bg-gray-500" wire:click="search">Search</button>
        </div>

        <div>
            {{ $images->links() }}
        </div>
    </div>

    <div class="grid grid-cols-5 grid-rows-4 gap-2 p-1.5 mx-4 my-2 bg-gray-800 border rounded" style="width: 1330px;">
        @foreach ($images as $image)
            <a class="transition ease-in-out delay-75 border border-gray-700 shadow-md shadow-black hover:scale-110" style="width: 256px; height: 300px" href="{{ route('image.show', $image->uuid) }}">
                <img style="width: 256px; height: 300px;" src="{{ asset($image->thumbnail_path) }}" alt="{{ $image->name }}">
            </a>
        @endforeach
    </div>
</div>
