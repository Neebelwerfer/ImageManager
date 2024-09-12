<x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        {{ __('Image Upload') }}
    </h2>
</x-slot>

<div class="mt-5">
    <div class="items-center w-1/2 p-1" style="margin-left: 33%;">

        <div wire:loading wire:target="image">Uploading...</div>
        <form wire:submit="save">
            @csrf

            <div class="columns-2">
                <div>
                    <label for="rating" class="form-label">Rating</label>
                    <div class="mb-3">
                        <input type="number" class="text-black form-control" wire:model="rating" value="5">

                        @error('rating')
                            <div class="mt-1 mb-1 text-red-600">{{ $message }}</div>
                        @enderror
                    </div>

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
                                wire:click="toggleCategoryModal">+</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <button type="submit"
                            class="p-1 border rounded btn bg-slate-600 dark:bg-gray-700 hover:bg-gray-400 hover:dark:bg-gray-500"
                            id="submit">Submit</button>
                    </div>
                </div>

                <div class="flex flex-col min-h-screen">
                    <div class="mb-3">
                        <input type="file" wire:model='image' name="image" placeholder="Choose image"
                            id="imageInput">
                        @error('image')
                            <div class="mt-1 mb-1 text-red-600 alert">{{ $message }}</div>
                        @enderror
                        @if ($image)
                            <div class="mb-3">
                                <img id="image-preview" src="{{ $image->temporaryUrl() }}" style="max-height: 250px;">
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </form>
        @if ($showCategory)
            <livewire:category-show.modal />
        @endif

        @if (session('status'))
            <x-status-modal>
                <x-slot name="header">
                        Status
                </x-slot>
                <div class="@if(session('error')) text-red-500 @endif">
                    {{ session('status') }}
                    @if(session('error'))
                        {{ session('error_message') }}
                    @endif
                </div>
                @if (session('duplicate'))
                    <img class="w-full" src="{{ asset(session('duplicate')) }}" alt="image">
                @endif
            </x-status-modal>
        @endif

    </div>
</div>
