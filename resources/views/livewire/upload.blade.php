<x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        {{ __('Image Upload') }}
    </h2>
</x-slot>

<div class="relative flex flex-row h-full">

    <div class="flex justify-center w-full"
        x-on:livewire-upload-finish="$wire.onUploadFinished">
        <div>
            <div wire:loading wire:target="image">
                Uploading...
                <x-spinning-loader />
            </div>
            <div class="flex flex-col">
                <div class="mb-3">
                    <input type="file" wire:model='images' name="image" placeholder="Choose images" id="imageInput" multiple>
                    @error('image')
                        <div class="mt-1 mb-1 text-red-600 alert">{{ $message }}</div>
                    @enderror
                </div>
            </div>
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
        </x-status-modal>
    @endif
</div>
