<x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        {{ __('Upload'). ' ' .$upload->ulid }}
    </h2>
</x-slot>

<div x-data="multiSelect($wire.entangle('selectedImages'))">
    <div class="w-full h-full" x-data="{count: $wire.entangle('count'), maxValue: '{{ count($this->images) }}'}">
        @if($state == "waiting" || $state == "foundDuplicates")
            <div class="flex flex-row justify-center w-full gap-5 mt-2">
                <button class="p-1 bg-gray-700 border rounded dark:bg-slate-700 hover:bg-gray-400 hover:dark:bg-gray-500" wire:click='finalizeUpload'>Finalize Upload</button>
                <button class="p-1 bg-red-700 border rounded dark:bg-red-700 hover:bg-gray-400 hover:dark:bg-gray-500" wire:click='uploadCancel'>Cancel Upload</button>
            </div>

        @elseif($state == 'done')
            <div class="flex justify-center w-full">
                <p class="text-5xl font-bold">DONE!</p>
            </div>
            <div class="flex flex-row justify-center w-full gap-5 mt-2">
                <button class="p-1 bg-gray-700 border rounded dark:bg-slate-700 hover:bg-gray-400 hover:dark:bg-gray-500" x-on:click="Livewire.navigate('{{ route('collection') }}')">Go to images</button>
                <button class="p-1 bg-gray-700 border rounded dark:bg-slate-700 hover:bg-gray-400 hover:dark:bg-gray-500" x-on:click="Livewire.navigate('{{ route('upload') }}')">Upload More</button>
            </div>

        @else
            <div class="flex flex-row justify-center w-full gap-5 mt-2">
                <button class="p-1 bg-red-700 border rounded dark:bg-red-700 hover:bg-gray-400 hover:dark:bg-gray-500" wire:click='uploadCancel'>Cancel Upload</button>
            </div>
        @endif

        @if($state == "uploading")
            <div class="flex justify-center w-full">
                <div class="flex flex-col justify-center">
                    <p class="text-4xl font-bold">Preparing upload</p>
                    <div class="w-60 h-60">
                        <x-spinning-loader/>
                    </div>
                </div>
            </div>
        @elseif($state == "waiting" || $state == "foundDuplicates")
            @if($count != -1)
                <div class="flex flex-col w-full" id="process">
                    <div class="flex justify-center">
                        <div class="flex flex-row justify-between w-2/4">
                            <button :disabled="count === 0" id="previous" :class="count === 0 ? 'bg-gray-400 dark:bg-gray-500' : 'bg-gray-700 border rounded dark:bg-slate-700 hover:bg-gray-400 hover:dark:bg-gray-500'" class="p-1 border rounded" wire:click='previous'>Previous</button>
                            <p class="self-center">{{ $count+1 }} of {{ count($this->images) }}</p>
                            <button :disabled="count === maxValue - 1" :class="count === maxValue - 1 ? 'bg-gray-400 dark:bg-gray-500' : 'bg-gray-700 border rounded dark:bg-slate-700 hover:bg-gray-400 hover:dark:bg-gray-500'" id="next" class="p-1 border rounded " wire:click='next'>Next</button>
                        </div>
                    </div>
                    @if(count($this->images) > 0)
                        <div>
                            <livewire:upload.process-image uuid="{{ $this->images[$count]->uuid }}" wire:key='preview-{{ $this->images[$count]->uuid }}'/>
                        </div>
                    @endif
                </div>

                <button class="p-1 mx-3 mt-4 border rounded btn hover:bg-gray-400 hover:dark:bg-gray-500" type="button" x-on:click="changeSelectMode" :class="$wire.editMode ? 'bg-gray-500' : 'bg-gray-700'">Edit</button>
                <template x-if="$wire.editMode">
                    <div class="flex flex-row ml-3">
                        <x-button class="h-fit" x-on:click='selectAll(true)' x-show="!allSelected">Select All</x-button>
                        <x-button class="h-fit" x-on:click='selectAll(false)' x-show="allSelected">Deselect All</x-button>
                        <x-dropdown class="ml-2" align="left">
                            <x-slot name="trigger">
                                <p class="p-1 mt-4 bg-gray-700 border rounded dark:bg-slate-700 hover:bg-gray-400 hover:dark:bg-gray-500">Options</p>
                            </x-slot>
                            <x-slot name="content">
                                <div class="flex flex-col mx-0.5">
                                    <button class="p-1 bg-gray-700 border rounded border-slate-600 dark:bg-slate-700 hover:bg-gray-400 hover:dark:bg-gray-500">Set Category</button>
                                    <button class="p-1 bg-gray-700 border rounded border-slate-600 dark:bg-slate-700 hover:bg-gray-400 hover:dark:bg-gray-500">Add Tag</button>
                                    <button class="p-1 bg-gray-700 border rounded border-slate-600 dark:bg-slate-700 hover:bg-gray-400 hover:dark:bg-gray-500">Add Album</button>
                                    <button class="p-1 bg-red-700 border rounded border-slate-600 hover:bg-red-400" wire:confirm='Are you sure you want to delete selected images?' wire:click='deleteSelected'>Delete</button>
                                </div>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </template>
            @endif

            @if(count($this->images) > 0)
                <div class="grid-flow-col mx-2 mt-2">
                    @foreach ($this->images as $key => $image)
                        <x:grid.upload-image-card :image="$image" wire:key='grid-{{ $key }}' wire:model="selectedImages.{{ $image->uuid }}" x-on:click="onClick('{{ $image->uuid }}', () => $wire.select('{{ $key }}'))" foundDuplicate='{{ $image->state === "foundDuplicates" }}'/>
                    @endforeach
                </div>
            @endif

        @elseif($state == "processing")
            <div class="flex justify-center w-full mt-2">
                <div class="flex flex-col">
                    <h1 class="text-4xl font-bold underline">Processing images</h1>
                    <div class="flex justify-center w-60 h-60">
                        <x-spinning-loader />
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
