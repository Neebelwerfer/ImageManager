<div class="flex justify-center w-full h-full">
    <x-grid class="w-7/12">
        <x-slot name="header">
            <div class="flex flex-row gap-2 ml-2">
                <div class="flex flex-col">
                    <div>
                        <label for="name" class="">Name</label>
                    </div>
                    <div>
                        <input type="text" class="text-black form-control" wire:model.live="name" placeholder="Name..."/>
                    </div>
                </div>
            </div>

            <div>
                {{ $albums->links() }}
            </div>
        </x-slot>
        @if($albums->isEmpty())
            <div class="flex items-center justify-center w-full h-full">
                <h1 class="text-2xl font-semibold leading-tight text-gray-800 dark:text-gray-200">No albums found</h1>
            </div>
        @else
            @foreach ($albums as $album)
                <x-grid.image-card :image="$this->getImageFromAlbum($album)" owned="{{ $album->owner_id == Auth::user()->id }}" route="{{ route('collection.type.show', [ 'albums', $album->id]) }}">
                    <div class="absolute inset-0 flex items-end">
                        <div class="flex justify-center w-full border-t border-gray-700 bg-slate-800/80">
                        {{ $album->name }}
                        </div>
                    </div>
                </x-grid.image-card>
            @endforeach
        @endif
    </x-grid>
</div>
