<x-modal formAction="save">
    <x-slot name="title">
        Create Trait
    </x-slot>

    <x-slot name="content">
        <div class="flex flex-row justify-between w-full p-2 mb-2">
            <form wire:submit.prevent="createTrait">
                <div class="flex flex-col">
                    <div class="flex flex-row justify-between">
                        <label class="block text-lg font-medium text-gray-700 dark:text-gray-300">Type</label>
                        <select class="px-2 py-1 ml-1 text-black border rounded" wire:model.live ="type">
                            <option value="integer">Integer</option>
                            <option value="float">Float</option>
                            <option value="boolean">Boolean</option>
                            <option value="text">Text</option>
                        </select>
                    </div>
                    @error('type')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                    <div class="flex flex-row justify-between">
                        <label class="block text-lg font-medium text-gray-700 dark:text-gray-300">Name</label>
                        <input class="px-2 py-1 ml-1 text-black border rounded" type="text" placeholder="Name..."
                            wire:model="name">
                    </div>
                    @error('name')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror

                    @if ($type == 'integer' || $type == 'float' || $type == 'text')
                        <div class="flex flex-row justify-between">
                            <div class="flex flex-col justify-between">
                                <label class="block text-lg font-medium text-gray-700 dark:text-gray-300">Min</label>
                                <input class="px-2 py-1 ml-1 text-black border rounded" type="number" size="4"
                                    placeholder="Min..." wire:model="min">
                            </div>
                            <div class="flex flex-col justify-between">
                                <label class="block text-lg font-medium text-gray-700 dark:text-gray-300">Max</label>
                                <input class="px-2 py-1 ml-1 text-black border rounded" type="number" size="4"
                                    placeholder="Max..." wire:model="max">
                            </div>
                        </div>
                        @error('min')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                        @error('max')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    @endif

                    <div class="flex flex-row justify-between border-t">
                        <label class="block text-lg font-medium text-gray-700 dark:text-gray-300">Default</label>¨
                        <input class="px-2 py-1 ml-1 text-black border rounded"
                            @if($type == 'text')
                            type="text"
                            @elseif($type == 'integer' || $type == 'float')
                            type="number"
                            size="4"
                            @elseif($type == 'boolean')
                            type="checkbox"
                            @endif
                            placeholder="Default..."
                            wire:model="default">
                    </div>
                    @error('default')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror

                    @if (Auth::user()->is_admin)
                        <div class="flex flex-row justify-between border-t">
                            <label class="block text-lg font-medium text-gray-700 dark:text-gray-300">Global</label>
                            <input class="px-2 py-1 ml-1 text-black border rounded" type="checkbox" wire:model="global">
                        </div>
                    @endif

                </div>
            </form>
        </div>
    </x-slot>

    <x-slot name="buttons">
        <button
            class="p-1 mt-4 bg-green-600 border rounded btn dark:bg-green-700 hover:bg-gray-400 hover:dark:bg-gray-500"
            wire:click="CreateTrait">Save</button>
        <button class="p-1 mt-4 bg-red-700 border rounded btn dark:bg-red-700 hover:bg-gray-400 hover:dark:bg-gray-500"
            wire:click="closeModal">Cancel</button>
    </x-slot>
</x-modal>
