<x-modal>
    <x-slot name="content">
        <div class="flex flex-col gap-2">
            <div class="flex flex-row gap-2">
                <div class="flex flex-shrink">
                    <img src="{{ url('thumbnail/'.$image->uuid) }}" alt="{{ $image->name }}" class="object-scale-down">
                </div>

                <div class="flex-none">
                    <ol>
                        <li class="font-semibold border-b">
                            @isset($image->category)
                                Category: {{ $image->category->name }}
                            @else
                                No Category
                            @endisset
                            <button class="border border-gray-500 rounded bg-slate-500/75 w-fit hover:bg-slate-600" wire:click="$dispatch('openModal', {component: 'modal.upload.edit-relations', arguments: {type: 'category', noneOption: true }})">Edit</button>
                        </li>
                        <li>Width x Height: {{ $image->width }}x{{ $image->height }}</li>
                        <li>Format: .{{ $image->format }}</li>
                        <li>Uploaded By: {{ $image->user->name }}</li>
                        <li>Added: {{ $image->created_at->diffForHumans() }}</li>
                        <li>Updated: {{ $image->updated_at->diffForHumans() }}</li>
                    </ol>
                </div>
            </div>

            <div class="flex flex-row justify-between w-full gap-2 columns-2">
                <div class="w-1/2 overflow-scroll ">
                    <div class="flex flex-row border-b border-black">
                        <h1>Tags</h1>
                        <button class="ml-2 border border-gray-500 rounded bg-slate-500/75 w-fit hover:bg-slate-600" wire:click="$dispatch('openModal', {component: 'modal.upload.edit-relations', arguments: ['tag']})">Edit</button>
                    </div>
                    <div>
                        @foreach ($image->tags as $tag)
                            <p>{{ $tag->name }}</p>
                        @endforeach
                    </div>
                </div>

                <div class="w-1/2 overflow-scroll">
                    <div class="flex flex-row border-b border-black">
                        <h1>Albums</h1>
                        <button class="ml-2 border border-gray-500 rounded bg-slate-500/75 w-fit hover:bg-slate-600">Edit</button>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <x-slot name="buttons">
        <div class="flex flex-row gap-2">
            <button class="p-1 mt-4 bg-gray-700 border rounded btn dark:bg-slate-700 hover:bg-gray-400 hover:dark:bg-gray-500" wire:click="closeModal">Close</button>
            <button class="p-1 mt-4 bg-red-700 border rounded btn dark:bg-red-700 hover:bg-gray-400 hover:dark:bg-gray-500" wire:confirm="Are your sure you want to delete this image?" wire:click="deleteImage">Delete</button>
        </div>
    </x-slot>
</x-modal>
