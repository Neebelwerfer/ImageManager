<div class="flex justify-center w-full h-full">
    <div class="flex flex-col w-4/6 h-full gap-2 mt-4 align-top">
        <div>
            <h1 class="text-xl font-semibold leading-tight text-center text-gray-800 dark:text-gray-200">Categories</h1>
            <div class="flex">
                <span>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                    <input class="px-2 py-1 text-black border rounded" type="text" placeholder="Name..." wire:model="name">
                    <button class="p-1 bg-blue-600 border border-blue-500 rounded" wire:click="create" wire:target="create">Create</button>
                </span>
            </div>
            @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>

        <div class="flex justify-center w-full min-h-96">
            <x-section class="w-full">
                <div>
                    <h1 class="text-xl font-semibold leading-tight text-center text-gray-800 dark:text-gray-200">Own</h1>
                    <div class="grid w-full grid-flow-row grid-cols-10 gap-2 mx-2">
                        @foreach ($categories as $category)
                            <button class="flex flex-col justify-between p-2 border rounded bg-slate-800 w-fit hover:bg-gray-600"  wire:click="$dispatch('openModal', {component: 'modal.manage.edit-collection', arguments: {'collectionType': 'category', 'collectionId': '{{ $category->id }}'}})">
                                <div class="w-full">
                                    <h1>{{ $category->name }}</h1>
                                    <h1>Images: {{ $this->imageCount($category->id) }}</h1>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>

                @if($shared->isNotEmpty())
                <div>
                    <h1 class="text-xl font-semibold leading-tight text-center text-gray-800 dark:text-gray-200">Shared with me</h1>
                    <div class="grid w-full grid-flow-row grid-cols-10 gap-2 mx-2">
                        @foreach ($shared as $category)
                            <button class="flex flex-col justify-between p-2 bg-teal-700 border rounded w-fit hover:bg-gray-600" wire:click="$dispatch('openModal', {component: 'modal.manage.shared-details', arguments: {type: 'category', id: '{{ $category->id }}'}})">
                                <div class="w-full">
                                    <h1>{{ $category->name }}</h1>
                                    <h1>Images: {{ $this->imageCount($category->id) }}</h1>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>
                @endif
            </x-section>
        </div>
    </div>
</div>
