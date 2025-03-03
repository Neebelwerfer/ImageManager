@props(['duplicates' => []])

<div x-data="{duplicates: JSON.parse('{{ $duplicates }}'), count:0}">
    <template x-if="duplicates.length > 1">
        <div class="flex flex-row justify-between h-fit">
            <button class="p-1 bg-gray-700 border rounded btn dark:bg-slate-700 hover:bg-gray-400 hover:dark:bg-gray-500"
                wire:click="previous">Prev</button>
            <p class="align-middle" x-text="(count + 1) + ' / ' + duplicates.length"></p>
            <button class="p-1 bg-gray-700 border rounded btn dark:bg-slate-700 hover:bg-gray-400 hover:dark:bg-gray-500"
                wire:click="next">Next</button>
        </div>
    </template>
    <div class="flex justify-center">
        <p>There are <p class="mx-1" x-text="duplicates.length"></p> potential duplicate(s)</p>
    </div>
    <div class="flex justify-center">
        <button
            x-on:click="$dispatch('openModal', {component: 'modal.image.details', arguments: {imageUuid: duplicates[count]}})">
            <img :src="'{{ url('thumbnail') }}/' + duplicates[count]" class="object-scale-down">
        </button>
    </div>
</div>
