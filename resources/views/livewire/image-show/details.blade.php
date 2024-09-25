<div x-cloak x-show="showOptions" x-on:click.away="showOptions = false"
    class="static left-0 z-40 flex flex-col w-full bg-black border-t border-b border-gray-800 h-1/5">
    <div class="flex flex-col justify-center w-full h-full">
        <div class="flex flex-row h-full mx-5">
            <div class="flex flex-row w-full h-full space-x-6 justify-evenly">
                <div class="h-full columns-2">
                    <p>Rating: {{ $image->rating }}</p>
                    <p>Size: {{ $image->width }}x{{ $image->height }}</p>
                    <p>Uploaded By: {{ $image->user->name }}</p>
                    <p>Uploaded: {{ $image->created_at->diffForHumans() }}</p>
                    <button class="w-full p-1 bg-blue-600 border border-blue-500 rounded h-fit"
                        wire:click='toggleRatingModal'>Change Rating</button>

                    <div class= "flex flex-col space-y-1">
                        @isset($image->category)
                            <h1 class="text-xl font-semibold text-gray-800 underline dark:text-gray-200">Category:
                                {{ $image->category->name }}</h1>
                            <button class="bg-gray-600 border border-gray-500 rounded w-fit"
                                wire:click='toggleCategoryModal'>Change Category</button>
                            <button class="text-white bg-red-600 border border-red-500 rounded w-fit"
                                wire:click='removeCategory'>Remove Category</button>
                        @else
                            <h1 class="text-xl font-semibold text-gray-800 underline dark:text-gray-200">No Category
                            </h1>
                            <button class="bg-blue-600 border border-blue-500 rounded w-fit"
                                wire:click='toggleCategoryModal'>Add Category</button>
                        @endisset
                    </div>
                </div>


                <div class="flex flex-col my-2">
                    <div class="flex flex-row justify-center h-10 space-x-2">
                        <button class="bg-blue-600 border border-blue-500 rounded h-fit" wire:click='toggleTag'>Add
                            Tag</button>
                        <h1>Tags</h1>
                    </div>
                    <ul>
                        @foreach ($image->tags as $tag)
                            <li class="flex flex-row justify-between pl-2 space-x-4 bg-gray-800 border rounded ">
                                <p>{{ $tag->name }}</p>
                                <button class="h-full bg-red-600 border border-red-500 rounded"
                                    wire:click='removeTag({{ $tag->id }})'>Remove</button>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="my-2 overflow-scroll columns-2">
                    <button class="bg-blue-600 border border-blue-500 rounded h-fit" wire:click='addAlbum'>Add To
                        album</button>
                    <div class="flex flex-col">
                        ALBUMS
                        <ul>
                            @foreach ($image->albums as $album)
                                <li class="flex flex-row justify-between p-2 bg-gray-800 border rounded ">
                                    <p>{{ $album->name }}</p>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <button class="w-20 p-1 bg-red-600 border border-red-500 rounded h-fit" wire:click='delete'
                wire:confirm="Are you sure you want to delete this image?">Delete</button>
        </div>
    </div>

    @if ($showCategory)
        <livewire:category-show.modal />
    @endif

    @if ($showTags)
        <livewire:tag-show.modal />
    @endif

    @if ($showRating)
        <livewire:image-show.rating :image="$image" />
    @endif
</div>
