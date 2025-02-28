<x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        {{ __('Image Upload') }}
    </h2>
</x-slot>


<div class="relative flex flex-row h-full" x-data="upload('{{ route('media.upload') }}')">
    <div class="flex justify-center w-full">
        <div>
            <div wire:loading wire:target="image">
                Uploading...
                <x-spinning-loader />
            </div>
            <div class="flex flex-col">
                <div class="mb-3" x-show="!uploading">
                    <input type="file" accept="image/*" name="images" x-on:change="handleUpload" placeholder="Choose images" id="imageInput" multiple>
                    @error('image')
                        <div class="mt-1 mb-1 text-red-600 alert">{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex flex-col" x-show="uploading" x-cloak>
                    <h1 class="mb-2 text-6xl font-bold underline">Upload In progress: <span id="percentage">{{ number_format($progress, 0) }}%</span></h1>
                    <progress max="100" value="{{ $progress }}" id="progress"></progress>
                </div>
            </div>
        </div>
    </div>
</div>
