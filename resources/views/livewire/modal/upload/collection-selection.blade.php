<x-simple-modal class="z-10" name="collection-selection">
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
                <div class="w-full h-full gap-2 overflow-scroll rounded">
                    <template x-for="entry in data" :key="entry.id">
                        <button class="flex items-center justify-center w-1/3 h-full p-2 border rounded bg-slate-600 hover:bg-slate-500" x-on:click="$dispatch(context.type + eventName(), {selection: entry}); closeModal()">
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
        <button class="p-1 mt-4 border rounded btn bg-slate-600 dark:bg-gray-700 hover:bg-gray-400 hover:dark:bg-gray-500" x-on:click="closeModal()">Close</button>
        <button class="p-1 mt-4 border rounded btn bg-slate-600 dark:bg-gray-700 hover:bg-gray-400 hover:dark:bg-gray-500" x-on:click="$dispatch('modalOpen', {name: 'add-tag'})">Create <p x-text="context.type"></p></button>
    </x-slot>
    <x-slot name="modals">
    </x-slot>
</x-simple-modal>
