<x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        {{ __('Image Upload') }}
    </h2>
</x-slot>

<div class="flex justify-center mt-5" wire:poll.visible.60s>
    <div class="flex items-center justify-center w-1/2 p-1"
        x-on:livewire-upload-start="$wire.onUploadStarted"
        x-on:livewire-upload-finish="$wire.onUploadFinished">

        <div wire:loading wire:target="image">
            Uploading...
            <x-spinning-loader />
        </div>
        <form wire:submit="save">
            @csrf
            @empty($uuid)
            <div class="flex flex-col">
                <div class="mb-3">
                    <input type="file" wire:model='image' name="image" placeholder="Choose image" id="imageInput">
                    @error('image')
                        <div class="mt-1 mb-1 text-red-600 alert">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            @else
            <div class="mb-3 border-slate-800">
                <img id="image-preview" src="{{ url('temp/'.$uuid) }}" style="max-height: 500px;">
            </div>
            @endempty

            @if ($imageUpload)
                <div class="flex flex-row justify-between">
                    <div class="inline-flex gap-4">
                        <label for="category" class="form-label">Category: @if (isset($category))
                                {{ $category->name }}
                            @endif
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
                                <div class="inline-flex justify-around w-20 gap-2 border-y">
                                    <h1>{{ $tag->name }}</h1>
                                    <button class="border rounded h-fit" type="button"
                                            wire:click='removeTag({{ $tag->id }})'>X</button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="flex flex-row gap-2">
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
            @endif
        </form>

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
</div>
