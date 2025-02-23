<x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        {{ __('Upload'). ' ' .$upload->ulid }}
    </h2>
</x-slot>

<div class="w-full h-full">
    <div class="flex flex-row justify-center w-full gap-5 mt-2">
        <button class="p-1 bg-gray-700 border rounded dark:bg-slate-700 hover:bg-gray-400 hover:dark:bg-gray-500" wire:click='finalizeUpload'>Finalize Upload</button>
        {{ $this->ImageUploads->links() }}
        <button class="p-1 bg-red-700 border rounded dark:bg-red-700 hover:bg-gray-400 hover:dark:bg-gray-500" wire:click='uploadCancel'>Cancel Upload</button>
    </div>

    @if($state == "waiting")
        @if(isset($this->imageUploads) && $this->imageUploads->first() !== null)
            <div wire:replace>
                <livewire:upload.process-image uuid="{{ $this->imageUploads->first()->uuid }}" wire:key='{{ $this->imageUploads->first()->uuid }}'/>
            </div>
        @endif

    @elseif($state == "scanning")
        <div class="flex flex-col mx-5 mt-5">
            <div class="flex justify-center flex-shrink-0 w-full h-full">
                <x-section class="w-3/5" style="height: 40rem">
                    <div class="relative flex flex-row w-full h-full">
                        <div class="w-2/6 border-r border-slate-500">
                            <div class="flex flex-col justify-center w-full">
                                <x-spinning-loader/>
                            </div>
                        </div>
                        <div class="ml-2">
                            <h1 class="@if($state == "scanning") font-extrabold underline @endif text-black-400 text-xl">Scanning for potential duplicates</h1>
                        </div>
                </x-section>
            </div>
        </div>

    @elseif($state == "foundDuplicates")
        @if(isset($this->imageUploads) && $this->imageUploads->first() !== null)
        <div wire:replace>
            <livewire:upload.process-image uuid="{{ $this->imageUploads->first()->uuid }}" wire:key='{{ $this->imageUploads->first()->uuid }}'/>
        </div>
        @endif
    @endif
</div>
