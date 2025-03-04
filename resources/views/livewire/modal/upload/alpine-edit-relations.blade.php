<x-simple-modal class="z-10" name="edit-relations">
    <x-slot name="title">
        <p x-text="context.type"></p>
    </x-slot>

    <x-slot name="content">
        <template x-if="context.type != null">
            <div
                x-data="{
                    data: [],

                    eventName()
                    {
                        if(this.context.event != null)
                        {
                            return this.context.event;
                        }
                        return 'Selected'
                    }
                }"
                x-init="data = JSON.parse(await $wire.getEntries(context.type));"
            >
                <div class="grid w-full h-full grid-cols-5 grid-rows-4 gap-2 rounded">
                    <template x-for="entry in data" :key="entry.id">
                        <button class="flex items-center justify-center w-full h-full p-2 border rounded bg-slate-600 hover:bg-slate-500" x-on:click="$dispatch(context.type + eventName(), {selection: entry}); closeModal()">
                            <p x-text="entry.name">
                        </button>
                    </template>
                </div>
                <template x-if="context.noOption != null && context.noOption">
                    <button class="p-1 mt-4 border rounded btn bg-slate-600 dark:bg-gray-700 hover:bg-gray-400 hover:dark:bg-gray-500" wire:click="$dispatch(context.type + eventName(), {selection: []}); closeModal()">None</button>
                </template>
            </div>
        </template>
    </x-slot>

    <x-slot name="buttons">
        <button class="p-1 mt-4 border rounded btn bg-slate-600 dark:bg-gray-700 hover:bg-gray-400 hover:dark:bg-gray-500" x-on:click="$dispatch('modalClose', {name: 'edit-relations'})">Close</button>
        {{-- <button class="p-1 mt-4 border rounded btn bg-slate-600 dark:bg-gray-700 hover:bg-gray-400 hover:dark:bg-gray-500" wire:click="$dispatch('openModal', {component: 'modal.image.create-relation-classification', arguments: {type: context.type}})">Create <p x-text="context.type"></p></button> --}}
    </x-slot>
</x-simple-modal>
