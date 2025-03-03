@props(['owned' => true, 'foundDuplicate' => false])

@php
$borderColour = ($foundDuplicate == false)
    ? 'border-gray-700'
    : 'border-red-700';

@endphp

<button class="relative transition ease-in-out delay-75 bg-black border {{ $borderColour }} bordershadow-md shadow-black hover:scale-110 "
    style="width: 192px; height: 225px" {{ $attributes->merge(['wire:click', 'x-on:click']) }}  x-data="{ isSelected: false, uuid: null }" x-init="uuid = $el.getAttribute('uuid')" x-modelable="isSelected" :class="isSelected ? 'bg-blue-800' : 'bg-black'" >
        <div>
            <img class="object-scale-down px-1" style="width: 190px; height: 215px;" :src="'{{ url('temp') }}/' + uuid">
        </div>
</button>
