<div class="flex flex-col mx-5 mt-5" x-data="{
        imageData: $wire.entangle('imageData'),

        removeID(type, id)
        {
            if(id in this.imageData[type])
            {
                delete this.imageData[type][id];
                this.imageData['isDirty'] = true;
            }
        },
    }">
    <div class="flex justify-center flex-shrink-0 w-full h-full">
        <x-section class="w-3/5" style="height: 37rem">
            @if(!$error)
                @if($state == "waiting")
                    <div class="flex flex-row justify-between h-full">
                        <div class="w-3/6 border-r border-slate-500">
                            <div class="flex flex-col justify-center w-full">
                                <div class="inline-flex justify-center gap-4">
                                    <label for="category" class="flex flex-col form-label">
                                            <p>Category:</p>
                                            <template x-if="imageData.category.name != null">
                                                <h2 class="text-xl font-semibold leading-tight text-gray-800 underline dark:text-gray-500" x-text="imageData.category.name">
                                                </h2>
                                            </template>
                                    </label>
                                    <div class="mb-3">
                                        <input type="hidden" class="text-black form-control" wire:model="imageData.category"></textarea>

                                        @error('category')
                                            <div class="mt-1 mb-1 text-red-600">{{ $message }}</div>
                                        @enderror
                                        <button class="px-2 border rounded" type="button"
                                            wire:click="$dispatch('openModal', {component: 'modal.upload.edit-relations', arguments: {type:'category', noneOption: true}})">+</button>
                                    </div>
                                </div>
                                <img id="image-preview" class="object-scale-down mr-2" src="{{ url('temp/'.$imageUpload->uuid) }}" style="max-height: 500px;">
                            </div>
                        </div>

                        <form class="relative content-between w-full h-full ml-2 space-y-4" wire:submit="save">
                            <div class="absolute flex justify-end w-full gap-2">
                                <div class="mb-3">
                                    <button
                                        class="p-1 bg-red-600 border rounded btn dark:bg-red-700 hover:bg-gray-400 hover:dark:bg-gray-500"
                                        wire:click='removeImage' type="button">Remove image</button>
                                </div>
                            </div>
                            @csrf
                            <div class="w-full border-b border-slate-500">
                                <ul>
                                    <li class="inline-flex">Image Dimensions: <p class="ml-2" x-text="imageData.dimensions.height + ' / ' + imageData.dimensions.width"></p></li>
                                    <li class="flex">Image Size: <p class="ml-2" x-text="imageData.size"></p> mb</li>
                                    <li class="flex">Image Type: <p class="ml-2" x-text="imageData.extension"></p></li>
                                </ul>
                            </div>

                            <div class="flex flex-col">
                                <div class="inline-flex gap-4">
                                    <label for="tags">Tags:</label>
                                    <div class="mb-3">
                                        <input type="hidden" class="text-black form-control" wire:model="imageData.tags"></textarea>

                                        @error('tags')
                                            <div class="mt-1 mb-1 text-red-600">{{ $message }}</div>
                                        @enderror
                                        <button class="px-2 border rounded" type="button"
                                            wire:click="$dispatch('openModal', {component: 'modal.image.add-tag'})">+</button>
                                    </div>
                                </div>
                                <div class="flex flex-col mx-5 mb-4">
                                    <template x-for="tag in imageData['tags']">
                                        <div class="inline-flex justify-between w-20 gap-2 border rounded">
                                            <h1 x-text="tag.name"></h1>
                                            <button class="w-5 border border-red-600 rounded hover:bg-red-400 bg-red-600/80 h-fit" type="button" x-on:click="removeID('tags', tag.id)">X</button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <div class="inline-flex gap-4">
                                    <label for="tags">Albums:</label>
                                    <div class="mb-3">
                                        <input type="hidden" class="text-black" wire:model="imageData.albums"></textarea>

                                        @error('albums')
                                            <div class="mt-1 mb-1 text-red-600">{{ $message }}</div>
                                        @enderror
                                        <button class="px-2 border rounded" type="button"
                                            wire:click="$dispatch('openModal', {component: 'modal.upload.edit-relations', arguments: {type: 'album'}})">+</button>
                                    </div>
                                </div>
                                <div class="flex flex-col mx-5 mb-4">
                                    <template x-for="album in imageData['albums']">
                                        <div class="inline-flex justify-between w-20 gap-2 border rounded">
                                            <h1 x-text="album.name"></h1>
                                            <button class="w-5 border border-red-600 rounded hover:bg-red-400 bg-red-600/80 h-fit" type="button"
                                                    x-on:click="removeID('albums', album.id)">X</button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- @if(count($traits) > 0)
                            <div class="flex flex-col w-fit">
                                <div class="inline-flex gap-4">
                                    <label for="tags">Traits:</label>
                                    <div class="mb-3">
                                        <input type="hidden" class="text-black" wire:model="traits"></input>

                                        @error('tags')
                                            <div class="mt-1 mb-1 text-red-600">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="flex flex-col mx-5 mb-4 overflow-scroll">
                                    @foreach ($traits as $id => $trait)
                                        <livewire:trait.show :trait="$trait" wire:key='trait-{{ $id }}' />
                                    @endforeach
                                </div>
                            </div>
                            @endif --}}

                        </form>
                    </div>
                @elseif ($state == "foundDuplicates")
                    <div class="relative flex flex-row w-full h-full">
                        <div class="w-2/6 border-r border-slate-500">
                            <div class="flex flex-col justify-center w-full">
                                <livewire:upload.duplicate-images :duplicates="$this->duplicates"/>
                            </div>
                        </div>
                        <img id="image-preview" class="object-scale-down" src="{{ url('temp/'.$imageUpload->uuid) }}" style="max-height: 500px;">

                        <div class="absolute bottom-0 flex flex-row justify-between w-full gap-2">
                            <div class="mb-3">
                            <button type="button"
                                class="p-1 border rounded bg-slate-600 dark:bg-gray-700 hover:bg-gray-400 hover:dark:bg-gray-500"
                                wire:click='accept'>Accept and proceed</button>
                            </div>

                            <div class="mb-3">
                                <button
                                    class="p-1 bg-red-600 border rounded dark:bg-red-700 hover:bg-gray-400 hover:dark:bg-gray-500"
                                    wire:click='removeImage' type="button">Remove image</button>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </x-section>
    </div>
</div>
