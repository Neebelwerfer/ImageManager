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
                        </li>
                        <li>Width x Height: {{ $image->width }}x{{ $image->height }}</li>
                        <li>Format: .{{ $image->format }}</li>
                        <li>Uploaded By: {{ $image->user->name }}</li>
                    </ol>

                    <div class="flex flex-col justify-center w-full">
                        <h1 class="border-b border-black">Traits</h1>
                        @foreach ($this->traits as $trait)
                        <li>
                            {{ $trait->display() }}
                            <button class="border border-gray-500 rounded bg-slate-500/75 w-fit hover:bg-slate-600" wire:click="$dispatch('openModal', {component: 'modal.manage.edit-trait-value', arguments: {imageTrait: '{{ $trait->imageTrait()->id }}'}})">Edit</button>
                        </li>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex flex-row justify-between w-full gap-2 columns-2">
                <div class="w-1/2 overflow-scroll ">
                    <div class="flex flex-row border-b border-black">
                        <h1>Tags</h1>
                        <button class="ml-2 border border-gray-500 rounded bg-slate-500/75 w-fit hover:bg-slate-600" wire:click="$dispatch('openModal', {component: 'modal.image.add-tag'})">Edit</button>
                    </div>
                    <div>
                        @foreach ($this->tags as $tag)
                            <div class="flex flex-row justify-between">
                                <p>{{ $tag->name }}</p>
                                <button class="border border-gray-500 rounded bg-slate-500/75 w-fit hover:bg-slate-600" wire:click="removeTag({{ $tag->id }})">X</button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="w-1/2 overflow-scroll">
                    <div class="flex flex-row border-b border-black">
                        <h1>Albums</h1>
                        <button class="ml-2 border border-gray-500 rounded bg-slate-500/75 w-fit hover:bg-slate-600" wire:click="$dispatch('openModal', {component: 'modal.upload.edit-relations', arguments: ['album']})">Edit</button>
                    </div>
                    <div>
                        @foreach ($this->getAlbums() as $album)
                            <div class="flex flex-row justify-between">
                                <p>{{ $album->name }}</p>
                                <button class="border border-gray-500 rounded bg-slate-500/75 w-fit hover:bg-slate-600" wire:click="removeAlbum({{ $album->id }})">X</button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <x-slot name="buttons">
        <div class="flex flex-row justify-between w-full gap-2">
            <div>
            <x-button wire:click="closeModal">Close</x-button>
            <x-button wire:confirm='This will redirect you to the image' wire:click="show">Show Image</x-button>
            </div>
        </div>
    </x-slot>

    <x-slot name="footer">
        <div class="flex flex-row justify-between w-full gap-2">
            <p>Added: {{ $image->created_at->diffForHumans() }}</p>
            <p>Updated: {{ $image->updated_at->diffForHumans() }}</p>
        </div>
    </x-slot>
</x-modal>
