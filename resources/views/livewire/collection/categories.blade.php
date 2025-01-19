<x-grid>
    <x-slot name="header">
        <div class="flex flex-row justify-center gap-2">
            <div class="flex flex-col">
                <div>
                    <label for="name" class="">Name</label>
                </div>
                <div>
                    <input type="text" class="text-black form-control" wire:model="name" placeholder="Name..."/>
                    <button class="p-1 border rounded btn bg-slate-600 dark:bg-gray-700 hover:bg-gray-400 hover:dark:bg-gray-500" wire:click="search">Search</button>
                </div>
            </div>
        </div>

        <div>
            {{ $categories->links() }}
        </div>
    </x-slot>
    @if($categories->isEmpty())
        <div class="flex items-center justify-center w-full h-full">
            <h1 class="text-2xl font-semibold leading-tight text-gray-800 dark:text-gray-200">No categories found</h1>
        </div>
    @else
        @foreach ($categories as $category)
            <x-grid.image-card :image="$this->getImageFromCategory($category)" route="{{ route('collection.type.show', [ 'categories', $category->id]) }}">
                <div class="absolute inset-0 flex items-end">
                    <div class="flex justify-center w-full border-t border-gray-700 bg-slate-800/80">
                    {{ $category->name }}
                    </div>
                </div>
            </x-grid.image-card>
        @endforeach
    @endif
</x-grid>
