<x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        {{ $image->name }}
    </h2>
</x-slot>

<div class="flex flex-grow columns-2">
    <div class="flex flex-col w-1/3 border-r border-gray-800">
        <div class="flex flex-col justify-center w-full">
            <div class="flex flex-row h-full mx-5">
                <div class="flex flex-row w-1/2 space-x-6 columns-2">
                    <div>
                        <p>Rating: {{ $image->rating }}</p>
                        <p>Uploaded By: {{ $image->user->name }}</p>
                        <p>Uploaded: {{ $image->created_at->diffForHumans() }}</p>

                        <button class="w-20 p-1 bg-red-600 border border-red-500 rounded" wire:click='delete'
                            wire:confirm="Are you sure you want to delete this image?">Delete</button>
                    </div>

                    <div class= "flex flex-col space-y-1">
                        @isset($image->category)
                            <h1 class="text-xl font-semibold text-gray-800 underline dark:text-gray-200">Category: {{ $image->category->name }}</h1>
                            <button class="bg-gray-600 border border-gray-500 rounded w-fit"
                                wire:click='toggleCategoryModal'>Change Category</button>
                            <button class="text-white bg-red-600 border border-red-500 rounded w-fit"
                                wire:click='removeCategory'>Remove Category</button>
                        @else
                            <h1 class="text-xl font-semibold text-gray-800 underline dark:text-gray-200">No Category</h1>
                            <button class="bg-blue-600 border border-blue-500 rounded w-fit"
                                    wire:click='toggleCategoryModal'>Add Category</button>
                        @endisset
                    </div>
                </div>

                <div class="flex flex-col my-2">
                    <div class="flex flex-row justify-center h-10 space-x-2">
                        <button class="bg-blue-600 border border-blue-500 rounded h-fit" wire:click='addTag'>Add Tag</button>
                        <h1>Tags</h1>
                    </div>
                    <ul>
                    @foreach ($image->tags as $tag)
                        <li class="flex flex-row justify-between p-2 bg-gray-800 border rounded ">
                            <p>{{ $tag->name }}</p>
                        </li>
                    @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-center w-full m-5">
        <img class="object-scale-down" src="{{ asset($image->path) }}" alt="{{ $image->name }}">
    </div>

    @if ($showCategory)
        <livewire:category-show.modal />
    @endif

    @if (session('status'))
        <x-status-modal>
            <x-slot name="header">
                Status
            </x-slot>
            <div class="@if (session('error')) text-red-500 @endif">
                {{ session('status') }}
                @if (session('error'))
                    {{ session('error_message') }}
                @endif
            </div>
        </x-status-modal>
    @endif
</div>
