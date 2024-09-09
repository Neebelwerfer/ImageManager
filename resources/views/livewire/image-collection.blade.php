<x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        {{ __('Images') }}
    </h2>
</x-slot>

<div class="flex justify-center flex-grow">
    <div class="flex flex-row flex-wrap flex-shrink-0 flex-grow-0 gap-2 p-1.5 mx-4 my-2 bg-gray-800 border rounded" style="width: 1350px;">
        @foreach ($this->images as $image)
            <a class="transition ease-in-out delay-75 border border-gray-700 shadow-md shadow-black hover:scale-110" style="width: 256px; height: 300px" href="{{ route('image.show', $image->uuid) }}">
                <img style="width: 256px; height: 300px;" src="{{ asset($image->thumbnail_path) }}" alt="{{ $image->name }}">
            </a>
        @endforeach
    </div>
</div>
