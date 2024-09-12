@props(['image', 'route' => '#'])

<a class="relative transition ease-in-out delay-75 border border-gray-700 shadow-md shadow-black hover:scale-110"
    style="width: 256px; height: 300px" href={{ $route }}>
    @if ($image)
        <img style="width: 256px; height: 300px;" src="{{ asset($image->thumbnail_path()) }}">
    @else
        <x-no-image />
    @endif
    {{ $slot }}
</a>
