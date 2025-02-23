@props(['image', 'owned' => true])

<button class="relative transition ease-in-out delay-75 bg-black border border-gray-700 shadow-md shadow-black hover:scale-110"
    style="width: 192px; height: 225px" {{ $attributes->merge(['wire:click', 'x-on:click']) }} >
    @if ($image)
        <livewire:grid.temp-image :image="$image" wire:key='image-{{ $image->uuid }}'/>
    @else
        <x-no-image />
    @endif
    {{ $slot }}
</button>
