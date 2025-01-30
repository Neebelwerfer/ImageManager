<div class="relative flex flex-col flex-grow h-full">
    <a class="absolute top-0 left-0 z-50 m-5" href="{{ url(route('collection.show', 'images')) }}" wire:navigate>Back</a>

    <div class="flex flex-col justify-center w-full mt-5">
        <div class="flex flex-row justify-center gap-2">
            <button class="border rounded w-fit bg-slate-600/75 hover:bg-slate-500" wire:click="$dispatch('openModal', {component: 'modal.image.details', arguments: {imageUuid: '{{ $image->uuid }}'}})">Details</button>
        </div>
    </div>

    <div class="flex justify-center w-full">
        <div class="flex justify-center w-4/5 m-5">
            <img class="object-scale-down" style="width: 1920px; height: 1080px" src="{{ url('images/'.$image->uuid) }}">
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
</div>
