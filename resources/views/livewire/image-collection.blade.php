<div class="flex flex-row mx-4 my-4 space-x-3 auto-rows-min">
    @foreach ($this->images as $image)
        <img width="200" height="200" src="{{ asset($image->thumbnail_path) }}" alt="{{ $image->name }}">
    @endforeach
</div>
