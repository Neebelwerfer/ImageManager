<div x-data="{ showOptions: false }" class="relative flex flex-grow h-full">
    <a class="absolute top-0 left-0 z-50 m-5" href="{{ url()->previous() }}">Back</a>

    <div class="flex justify-center w-full">
        <div x-on:click="showOptions = !showOptions" class="flex justify-center w-4/5 m-5">
            <img class="object-scale-down" src="{{ asset($image->path) }}" alt="{{ $image->name }}">
        </div>
    </div>

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


    <livewire:image-show.options :image="$image" />
</div>
