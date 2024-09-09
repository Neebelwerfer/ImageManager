@props(['image', 'route' => '#'])

<a class="relative transition ease-in-out delay-75 border border-gray-700 shadow-md shadow-black hover:scale-110" style="width: 256px; height: 300px" href={{ $route }}>
    <img style="width: 256px; height: 300px;" src="@if(isset($image)) {{ asset($image->thumbnail_path) }} @else {{ asset('svgs/image-placeholder.svg') }} @endif">
    {{ $slot }}
</a>
