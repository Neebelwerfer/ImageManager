<x-grid>
    <x-slot name="header">
        <div class="flex flex-row justify-center gap-2">

        </div>

    </x-slot>

    @foreach ($this->category->images as $image)
        <x-grid.image-card :image="$image" route="{{ route('collection.show', 'images/' . $image->uuid) }}">
        </x-grid.image-card>
    @endforeach
</x-grid>
