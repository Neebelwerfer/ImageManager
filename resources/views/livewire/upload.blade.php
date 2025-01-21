<x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        {{ __('Image Upload') }}
    </h2>
</x-slot>

<div class="flex flex-col mx-5 mt-5" wire:poll.visible.60s>
    <div class="flex justify-center w-full"
        x-on:livewire-upload-start="$wire.onUploadStarted"
        x-on:livewire-upload-finish="$wire.onUploadFinished">
        <div>
            <div wire:loading wire:target="image">
                Uploading...
                <x-spinning-loader />
            </div>
            @empty($uuid)
            <div class="flex flex-col">
                <div class="mb-3">
                    <input type="file" wire:model='image' name="image" placeholder="Choose image" id="imageInput">
                    @error('image')
                        <div class="mt-1 mb-1 text-red-600 alert">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            @endempty
        </div>
    </div>

    @if ($imageUpload)
    <form class="" wire:submit="save">
        @csrf
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

            <div class="flex justify-center mt-3 mb-3 border-slate-800">
                <img id="image-preview" src="{{ url('temp/'.$uuid) }}" style="max-height: 500px;">
            </div>
        </div>

        <div class="flex flex-row justify-center gap-2">
            <div class="mb-3">
            <button type="submit"
                class="p-1 border rounded btn bg-slate-600 dark:bg-gray-700 hover:bg-gray-400 hover:dark:bg-gray-500"
                id="submit">Submit</button>
            </div>

            <div class="mb-3">
                <button
                    class="p-1 border rounded btn bg-slate-600 dark:bg-gray-700 hover:bg-gray-400 hover:dark:bg-gray-500"
                    wire:click='cancel' type="button">Cancel</button>
            </div>
        </div>



        <div class="flex flex-row justify-evenly">
            <div class="flex flex-col">
                <div class="inline-flex gap-4">
                    <label for="tags">Tags:</label>
                    <div class="mb-3">
                        <input type="hidden" class="text-black form-control" wire:model="tags"></textarea>

                        @error('tags')
                            <div class="mt-1 mb-1 text-red-600">{{ $message }}</div>
                        @enderror
                        <button class="px-2 border rounded" type="button"
                            wire:click="$dispatch('openModal', {component: 'modal.upload.edit-relations', arguments: ['tag']})">+</button>
                    </div>
                </div>
                @if (count($tags) > 0)
                <div class="flex flex-col mx-5 mb-4">
                    @foreach ($tags as $tag)
                        <div class="inline-flex justify-between w-20 gap-2 border rounded">
                            <h1>{{ $tag->name }}</h1>
                            <button class="w-5 border border-red-600 rounded hover:bg-red-400 bg-red-600/80 h-fit" type="button"
                                    wire:click='removeTag({{ $tag->id }})'>X</button>
                        </div>
                    @endforeach
                </div>
                @endif
            </div>

            <div class="flex flex-col">
                <div class="inline-flex gap-4">
                    <label for="tags">Traits:</label>
                    <div class="mb-3">
                        <input type="hidden" class="text-black form-control" wire:model="traits"></textarea>

                        @error('tags')
                            <div class="mt-1 mb-1 text-red-600">{{ $message }}</div>
                        @enderror
                        <button class="px-2 border rounded" type="button"
                            wire:click="$dispatch('openModal', {component: 'modal.upload.edit-relations', arguments: ['tag']})">+</button>
                    </div>
                </div>
                @if (count($tags) > 0)
                <div class="flex flex-col mx-5 mb-4">
                    @foreach ($tags as $tag)
                        <div class="inline-flex justify-between w-20 gap-2 border rounded">
                            <h1>{{ $tag->name }}</h1>
                            <button class="w-5 border border-red-600 rounded hover:bg-red-400 bg-red-600/80 h-fit" type="button"
                                    wire:click='removeTag({{ $tag->id }})'>X</button>
                        </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </form>
    @endif


    @if (session('status'))
        <x-status-modal>
            <x-slot name="header">
                Status
            </x-slot>
            <div class="@if (session('error')) text-red-500 @endif">
                {{ session('status') }}
                @if (session('error'))
                    {{ session('error_message') }}
                @endif
            </div>
        </x-status-modal>
    @endif
</div>
