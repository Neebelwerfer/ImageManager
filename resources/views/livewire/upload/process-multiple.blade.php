<x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        {{ __('Upload'). ' ' .$upload->ulid }}
    </h2>
</x-slot>

<div class="w-full h-full" x-data="{count: $wire.entangle('count'), maxValue: '{{ count($this->images) }}'}">
    <div class="flex flex-row justify-center w-full gap-5 mt-2">
        <button class="p-1 bg-gray-700 border rounded dark:bg-slate-700 hover:bg-gray-400 hover:dark:bg-gray-500" wire:click='finalizeUpload'>Finalize Upload</button>
        <button class="p-1 bg-red-700 border rounded dark:bg-red-700 hover:bg-gray-400 hover:dark:bg-gray-500" wire:click='uploadCancel'>Cancel Upload</button>
    </div>

    @if($state == "uploading")
        <div class="flex justify-center w-full">
            <div class="flex flex-col justify-center">
                <p class="text-4xl font-bold">Preparing upload</p>
                <div class="w-60 h-60">
                    <x-spinning-loader/>
                </div>
            </div>
        </div>
    @elseif($state == "waiting")
        @if($count != -1)
            <div class="flex flex-col w-full" id="process">
                <div class="flex justify-center">
                    <div class="flex flex-row justify-between w-2/4">
                        <button :disabled="count === 0" id="previous" :class="count === 0 ? 'bg-gray-400 dark:bg-gray-500' : 'bg-gray-700 border rounded dark:bg-slate-700 hover:bg-gray-400 hover:dark:bg-gray-500'" class="p-1 border rounded" wire:click='previous'>Previous</button>
                        <p class="self-center">{{ $count+1 }} of {{ count($this->images) }}</p>
                        <button :disabled="count === maxValue - 1" :class="count === maxValue - 1 ? 'bg-gray-400 dark:bg-gray-500' : 'bg-gray-700 border rounded dark:bg-slate-700 hover:bg-gray-400 hover:dark:bg-gray-500'" id="next" class="p-1 border rounded " wire:click='next'>Next</button>
                    </div>
                </div>
                <div wire:replace>
                    <livewire:upload.process-image uuid="{{ $this->images[$count]->uuid }}" wire:key='preview-{{ $count }}'/>
                </div>
            </div>
        @endif

        <div class="grid-flow-col mx-2 mt-2">
            @foreach ($this->images as $key => $image)
                <x:grid.upload-image-card :image="$image" wire:key='grid-{{ $key }}' x-bind:disabled="count === '{{ $key }}'" x-on:click="$wire.select('{{ $key }}')"/>
            @endforeach
        </div>
    @elseif($state == "scanning")
        <div class="flex justify-center w-full mt-2">
            <div class="flex flex-row">
                <h1 class="text-xl font-bold underline w-fit">Scanning For Duplicates</h1>
                <div class="flex justify-center" style="width: 40%">
                    <x-spinning-loader />
                </div>
            </div>
        </div>
    @elseif ($state == "foundDuplicates")
        @if(!empty($selectedUUID))
            <div wire:replace>
                <livewire:upload.process-image uuid="{{ $selectedUUID }}" wire:key='preview-{{ $selectedUUID }}'/>
            </div>
        @endif

        <div class="grid-flow-col mx-2 mt-2">
            @foreach ($upload->images as $image)
                <x:grid.upload-image-card :image="$image" wire:key='grid-{{ $image->uuid }}' wire:click="select('{{ $image->uuid }}')"  />
            @endforeach
        </div>
    @endif
</div>
