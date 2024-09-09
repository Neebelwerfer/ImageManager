<div class="flex justify-center flex-grow">
    <div class="flex flex-col flex-grow-0 ">

        <div class="flex justify-center p-2">
            {{ $images->links() }}
        </div>

        <div class="grid grid-cols-5 grid-rows-4 gap-2 p-1.5 mx-4 my-2 bg-gray-800 border rounded" style="width: 1330px;">
            @foreach ($images as $image)
                <a class="transition ease-in-out delay-75 border border-gray-700 shadow-md shadow-black hover:scale-110" style="width: 256px; height: 300px" href="{{ route('image.show', $image->uuid) }}">
                    <img style="width: 256px; height: 300px;" src="{{ asset($image->thumbnail_path) }}" alt="{{ $image->name }}">
                </a>
            @endforeach
        </div>
    </div>
</div>
