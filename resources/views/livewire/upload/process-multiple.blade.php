<x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        {{ __('Upload'). ' ' .$upload->ulid }}
    </h2>
</x-slot>

<div x-data="multiSelect($wire.entangle('selectedImages'))">
    <div class="w-full h-full">
        @if($state == "waiting")
        <div x-data="{
            images: $wire.entangle('images'),
            count: $wire.entangle('count'),
            maxValue: '{{ count($this->images) }}'
            }">
            <div class="flex flex-row justify-center w-full gap-5 mt-2">
                <button class="p-1 bg-gray-700 border rounded dark:bg-slate-700 hover:bg-gray-400 hover:dark:bg-gray-500" wire:click='finalizeUpload'>Finalize Upload</button>
                <button class="p-1 bg-gray-700 border rounded dark:bg-slate-700 hover:bg-gray-400 hover:dark:bg-gray-500" wire:click='saveImageData'>Save Data</button>
                <button class="p-1 bg-red-700 border rounded dark:bg-red-700 hover:bg-gray-400 hover:dark:bg-gray-500" wire:click='uploadCancel'>Cancel Upload</button>
            </div>
            @if($count != -1)
                <div class="flex flex-col w-full" id="process">
                    <div class="flex justify-center">
                        <div class="flex flex-row justify-between w-2/4">
                            <button :disabled="count === 0" id="previous" :class="count === 0 ? 'bg-gray-400 dark:bg-gray-500' : 'bg-gray-700 border rounded dark:bg-slate-700 hover:bg-gray-400 hover:dark:bg-gray-500'" class="p-1 border rounded" wire:click='previous'>Previous</button>
                            <p class="self-center">{{ $count+1 }} of {{ count($images) }}</p>
                            <button :disabled="count === maxValue - 1" :class="count === maxValue - 1 ? 'bg-gray-400 dark:bg-gray-500' : 'bg-gray-700 border rounded dark:bg-slate-700 hover:bg-gray-400 hover:dark:bg-gray-500'" id="next" class="p-1 border rounded " wire:click='next'>Next</button>
                        </div>
                    </div>
                    @if(count($images) > 0)
                        <livewire:upload.process-image uuid="{{ $images[$count]['uuid'] }}" wire:key='preview-{{ $count }}' wire:model="images.{{ $count }}"/>
                    @endif
                </div>

                <button class="p-1 mx-8 mt-4 border rounded btn hover:bg-gray-400 hover:dark:bg-gray-500" type="button" x-on:click="changeSelectMode" :class="$wire.editMode ? 'bg-gray-500' : 'bg-gray-700'">Edit</button>
                <template x-if="$wire.editMode">
                    <div class="ml-5">
                    <x-edit>
                        <div class="flex flex-col mx-0.5">
                            <button class="p-1 bg-gray-700 border rounded border-slate-600 dark:bg-slate-700 hover:bg-gray-400 hover:dark:bg-gray-500">Set Category</button>
                            <button class="p-1 bg-gray-700 border rounded border-slate-600 dark:bg-slate-700 hover:bg-gray-400 hover:dark:bg-gray-500">Add Tag</button>
                            <button class="p-1 bg-gray-700 border rounded border-slate-600 dark:bg-slate-700 hover:bg-gray-400 hover:dark:bg-gray-500">Add Album</button>
                            <button class="p-1 bg-red-700 border rounded border-slate-600 hover:bg-red-400" wire:confirm='Are you sure you want to delete selected images?' wire:click='deleteSelected'>Delete</button>
                        </div>
                    </x-edit>
                    </div>
                </template>
            @endif

            <template x-if="maxValue > 0">
            <div class="flex justify-center">
                <div class="grid-flow-col mx-2 mt-2">
                    <template x-for="(image, index) in images" :key="image.uuid">
                        <button class="relative transition ease-in-out delay-75 bg-black border shadow-md shadow-black hover:scale-110 "
                                style="width: 192px; height: 225px"
                                x-on:click="onClick(index, () => $wire.select(index))"
                                x-init="$watch('selectedImages[index]', (value) => isSelected = value)"
                                x-data="{ isSelected: false }"
                                :class="[(isSelected ? 'bg-blue-800' : 'bg-black'), (image.state == 'foundDuplicate' ? 'border-red-700' : image.isDirty ? 'border-orange-500' : 'border-gray-700')]" >
                            <div>
                                <img class="object-scale-down px-1" style="width: 190px; height: 215px;" :src="'{{ url('temp') }}/' + image.uuid">
                            </div>
                        </button>
                 </template>
                </div>
            </div>
            </template>
        </div>
        @elseif($state == 'done')
            <div class="flex justify-center w-full">
                <p class="text-5xl font-bold">DONE!</p>
            </div>
            <div class="flex flex-row justify-center w-full gap-5 mt-2">
                <button class="p-1 bg-gray-700 border rounded dark:bg-slate-700 hover:bg-gray-400 hover:dark:bg-gray-500" x-on:click="Livewire.navigate('{{ route('collection') }}')">Go to images</button>
                <button class="p-1 bg-gray-700 border rounded dark:bg-slate-700 hover:bg-gray-400 hover:dark:bg-gray-500" x-on:click="Livewire.navigate('{{ route('upload') }}')">Upload More</button>
            </div>
        @elseif($state == "uploading")
            <div class="flex justify-center w-full">
                <div class="flex flex-col justify-center">
                    <p class="text-4xl font-bold">Preparing upload</p>
                    <div class="w-60 h-60">
                        <x-spinning-loader/>
                    </div>
                </div>
            </div>
        @elseif($state == "processing")
            <div class="flex flex-row justify-center w-full gap-5 mt-2">
                <button class="p-1 bg-red-700 border rounded dark:bg-red-700 hover:bg-gray-400 hover:dark:bg-gray-500" wire:click='uploadCancel'>Cancel Upload</button>
            </div>
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
