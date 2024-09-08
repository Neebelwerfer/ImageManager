<x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        {{ __('Image Upload') }}
    </h2>
</x-slot>

<div class="mt-5 panel panel-primary card">
    <div class="items-center w-1/2 p-1" style="margin-left: 33%;">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
        <div wire:loading wire:target="photo">Uploading...</div>
        <form wire:submit="save">
            @csrf

            <div class="columns-2">
                <div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <div>
                            <input type="text" class="text-black form-control" wire:model="name">

                            @error('name')
                                <div class="mt-1 mb-1 text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <label for="rating" class="form-label">Rating</label>
                    <div class="mb-3">
                        <input type="number" class="text-black form-control" wire:model="rating" value="5">

                        @error('rating')
                            <div class="mt-1 mb-1 text-red-600">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <button type="submit"
                            class="p-1 border rounded btn bg-slate-600 dark:bg-gray-700 hover:bg-gray-400 hover:dark:bg-gray-500"
                            id="submit">Submit</button>
                    </div>
                </div>

                <div class="flex flex-col min-h-screen">
                    <div class="mb-3">
                        <input type="file" oninput="readURL(this)" wire:model='image' name="image"
                            placeholder="Choose image" id="imageInput">
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
    </div>
</div>
