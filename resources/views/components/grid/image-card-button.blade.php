@props(['image'])

<button class="relative transition ease-in-out delay-75 bg-black border border-gray-700 shadow-md shadow-black hover:scale-110"
    style="width: 256px; height: 300px" {{ $attributes->merge(['wire:click']) }}>
    @if ($image)
        <livewire:grid.image :image="$image"/>
    @else
        <x-no-image />
    @endif
    {{ $slot }}
</button>
