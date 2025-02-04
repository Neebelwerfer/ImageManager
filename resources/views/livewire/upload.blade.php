<x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        {{ __('Image Upload') }}
    </h2>
</x-slot>

<div class="flex flex-col h-full mx-5 mt-5">
    <div class="flex justify-center w-full"
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

            @if(!session('error'))
                <x-slot name="buttons">
                    <x-button wire:click='goToImage()'>
                        Go To image
                    </x-button>

                    <button x-on:click="showModal = false" class="px-4 py-2 text-white bg-red-500 rounded-md">
                        Close
                    </button>
                </x-slot>
            @endif
        </x-status-modal>
    @endif
</div>
