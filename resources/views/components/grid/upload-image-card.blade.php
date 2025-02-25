@props(['image', 'owned' => true, 'foundDuplicate' => false])

@php
$borderColour = ($foundDuplicate == false)
    ? 'border-gray-700'
    : 'border-red-700';

@endphp

<button class="relative transition ease-in-out delay-75 bg-black border {{ $borderColour }} bordershadow-md shadow-black hover:scale-110 "
    style="width: 192px; height: 225px" {{ $attributes->merge(['wire:click', 'x-on:click']) }} >
    @if ($image)
        <livewire:grid.temp-image :image="$image" wire:key='image-{{ $image->uuid }}'/>
    @else
        <x-no-image />
    @endif
    {{ $slot }}
</button>
