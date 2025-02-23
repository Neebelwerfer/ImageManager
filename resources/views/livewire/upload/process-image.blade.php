<div class="flex flex-col mx-5 mt-5">
    <div class="flex justify-center flex-shrink-0 w-full h-full">
        <x-section class="w-3/5" style="height: 40rem">
            @if(!$error)
                @if($state == "waiting")
                    <div class="flex flex-row justify-between h-full">
                        <div class="w-3/6 border-r border-slate-500">
                            <div class="flex flex-col justify-center w-full">
                                <div class="inline-flex justify-center gap-4">
                                    <label for="category" class="flex flex-col form-label">
                                            <p>Category:</p>
                                            <h2 class="text-xl font-semibold leading-tight text-gray-800 underline dark:text-gray-500">
                                                @if (isset($category))
                                                {{ $category->name }}
                                                @endif
                                            </h2>
                                    </label>
                                    <div class="mb-3">
                                        <input type="hidden" class="text-black form-control" wire:model="category"></textarea>

                                        @error('category')
                                            <div class="mt-1 mb-1 text-red-600">{{ $message }}</div>
                                        @enderror
                                        <button class="px-2 border rounded" type="button"
                                            wire:click="$dispatch('openModal', {component: 'modal.upload.edit-relations', arguments: {type:'category', noneOption: true}})">+</button>
                                    </div>
                                </div>
                                <img id="image-preview" class="object-scale-down" src="{{ url('temp/'.$imageUpload->uuid) }}" style="max-height: 500px;">
                            </div>

                        </div>

                        <form class="relative content-between w-full h-full ml-2 space-y-4" wire:submit="save">
                            @csrf
                            <div class="w-full border-b border-slate-500">
                                <ul>
                                    <li>Image Dimensions: {{ $this->ImageMetadata['dimensions']['height'] }} / {{ $this->ImageMetadata['dimensions']['width'] }}</li>
                                    <li>Image Size: {{ $this->ImageMetadata['size'] }} mb</li>
                                    <li>Image Type: {{ $this->ImageMetadata['extension'] }}</li>
                                </ul>
                            </div>

                            <div class="flex flex-col">
                                <div class="inline-flex gap-4">
                                    <label for="tags">Tags:</label>
                                    <div class="mb-3">
                                        <input type="hidden" class="text-black form-control" wire:model="tags"></textarea>

                                        @error('tags')
                                            <div class="mt-1 mb-1 text-red-600">{{ $message }}</div>
                                        @enderror
                                        <button class="px-2 border rounded" type="button"
                                            wire:click="$dispatch('openModal', {component: 'modal.image.add-tag'})">+</button>
                                    </div>
                                </div>
                                @if (count($tags) > 0)
                                <div class="flex flex-col mx-5 mb-4">
                                    @foreach ($tags as $tagData)
                                        <div class="inline-flex justify-between w-20 gap-2 border rounded">
                                            <h1>{{ $tagData['tag']->name }}</h1>
                                            <button class="w-5 border border-red-600 rounded hover:bg-red-400 bg-red-600/80 h-fit" type="button"
                                                    wire:click='removeTag({{ $tagData['tag']->id }})'>X</button>
                                        </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>

                            <div class="flex flex-col">
                                <div class="inline-flex gap-4">
                                    <label for="tags">Albums:</label>
                                    <div class="mb-3">
                                        <input type="hidden" class="text-black" wire:model="albums"></textarea>

                                        @error('albums')
                                            <div class="mt-1 mb-1 text-red-600">{{ $message }}</div>
                                        @enderror
                                        <button class="px-2 border rounded" type="button"
                                            wire:click="$dispatch('openModal', {component: 'modal.upload.edit-relations', arguments: {type: 'album'}})">+</button>
                                    </div>
                                </div>
                                @if (count($albums) > 0)
                                <div class="flex flex-col mx-5 mb-4">
                                    @foreach ($albums as $album)
                                        <div class="inline-flex justify-between w-20 gap-2 border rounded">
                                            <h1>{{ $album->name }}</h1>
                                            <button class="w-5 border border-red-600 rounded hover:bg-red-400 bg-red-600/80 h-fit" type="button"
                                                    wire:click='removeAlbum({{ $album->id }})'>X</button>
                                        </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>

                            @if(count($traits) > 0)
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
                            @endif

                            <div class="absolute bottom-0 flex flex-row justify-between w-full gap-2">
                                <div class="mb-3">
                                <button type="submit"
                                    class="p-1 border rounded btn bg-slate-600 dark:bg-gray-700 hover:bg-gray-400 hover:dark:bg-gray-500"
                                    id="submit">Save</button>
                                </div>

                                <div class="mb-3">
                                    <button
                                        class="p-1 bg-red-600 border rounded btn dark:bg-red-700 hover:bg-gray-400 hover:dark:bg-gray-500"
                                        wire:click='removeImage' type="button">Remove image</button>
                                </div>
                            </div>
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
                    </div>
                @endif
            @else

            @endif
        </x-section>
    </div>
</div>
