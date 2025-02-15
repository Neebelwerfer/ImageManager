<x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        {{ __('Image Upload') }}
    </h2>
</x-slot>

<div class="relative flex flex-row h-full">
    <x-hidable-side-menu>
        <x-slot name="title">
            Image Uploads
        </x-slot>
        <div class="overflow-scroll">
            @if(count($completedImageUploads) > 0)
                    <p class="underline">Completed</p>
                    <ul class="space-y-1">
                        @foreach ($completedImageUploads as $uuid => $imageUpload)
                            <li>
                                <button x-on:click="Livewire.navigate('/upload/{{ $uuid }}')" class="w-full border rounded shadow-md bg-green-800/80 shadow-black">
                                    <p class="overflow-clip">{{ $uuid }}</p>
                                    <div class="flex flex-row justify-between mx-2">
                                        <p>{{ $imageUpload['startTime'] }}
                                        <p>{{ $imageUpload['state'] }}</p>
                                    </div>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if(count($imageUploads) > 0)
                    <p class="underline">In Progress</p>
                    <ul class="space-y-1">
                        @foreach ($imageUploads as $uuid => $imageUpload)
                            <li>
                                <button x-on:click="Livewire.navigate('/upload/{{ $uuid }}')" class="w-full border rounded shadow-md {{ $this->getStateColour($imageUpload['state']) }} shadow-black">
                                    <p class="overflow-clip">{{ $uuid }}</p>
                                    <div class="flex flex-row justify-between mx-2">
                                        <p>{{ $imageUpload['startTime'] }}
                                        <p>{{ $imageUpload['state'] }}</p>
                                    </div>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                @endif
        </div>
    </x-hidable-side-menu>

    <div class="flex justify-center w-full"
        x-on:livewire-upload-finish="$wire.onUploadFinished">
        <div>
            <div wire:loading wire:target="image">
                Uploading...
                <x-spinning-loader />
            </div>
            <div class="flex flex-col">
                <div class="mb-3">
                    <input type="file" wire:model='image' name="image" placeholder="Choose image" id="imageInput">
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
