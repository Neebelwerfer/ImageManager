@props(['image', 'owned' => true])

<button class="relative transition ease-in-out delay-75 bg-black border @if($owned) border-gray-700 @else border-teal-600 @endif shadow-md shadow-black hover:scale-110"
    style="width: 256px; height: 300px" {{ $attributes->merge(['wire:click']) }}>
    @if ($image)
        <livewire:grid.image :image="$image" wire:key='image-{{ $image->uuid }}'/>
    @else
        <x-no-image />
    @endif
    {{ $slot }}
</button>
