<div>
    @if ($this->count() > 1)
        <div class="flex flex-row justify-between h-fit">
            <button class="p-1 bg-gray-700 border rounded btn dark:bg-slate-700 hover:bg-gray-400 hover:dark:bg-gray-500"
                wire:click="previous">Prev</button>
            <p class="align-middle">{{ $this->count + 1 }}/{{ count($duplicates) }}</p>
            <button class="p-1 bg-gray-700 border rounded btn dark:bg-slate-700 hover:bg-gray-400 hover:dark:bg-gray-500"
                wire:click="next">Next</button>
        </div>
    @endif
    <div class="flex justify-center">
        <p>There are {{ count($duplicates) }} potential duplicate(s)</p>
    </div>
    <div class="flex justify-center">
        <button
            wire:click="$dispatch('openModal', {component: 'modal.image.details', arguments: {imageUuid: '{{ $duplicates[$count] }}'}})">
            <img src="{{ url('thumbnail/' . $duplicates[$count]) }}" class="object-scale-down">
        </button>
    </div>
</div>
