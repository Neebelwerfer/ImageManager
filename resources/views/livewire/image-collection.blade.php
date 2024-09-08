<x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        {{ __('Images') }}
    </h2>
</x-slot>

<div class="flex flex-row flex-grow flex-shrink-0 mx-4 my-4 space-x-2 auto-rows-min">
    @foreach ($this->images as $image)
        <a class="transition ease-in-out delay-75 border border-gray-700 hover:scale-110" style="width: 256px; height: 300px" href="#">
            <img style="width: 256px; height: 300px;" src="{{ asset($image->thumbnail_path) }}" alt="{{ $image->name }}">
        </a>
    @endforeach
</div>
