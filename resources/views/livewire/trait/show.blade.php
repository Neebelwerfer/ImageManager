<div class="inline-flex w-full gap-4 border bg-slate-800">
    <p class="justify-center ml-2 text-center">{{ $trait->getTrait()->name }}</p>
    @if ($trait->getValue() === null)
        <p class="pl-1 border-l"> n/a </p>
    @else
        <div class="flex items-center justify-center w-full align-middle border-l">
            @if($trait->type() === 'boolean')
                <input class="text-black border rounded" type="checkbox" {{ $trait->getValue() === '1' ? 'checked' : '' }} wire:model.live="value">
            @elseif ($trait->type() === 'text')
                <input class="text-black border rounded" type="text" value="{{ $trait->getValue() }}" wire:model.live="value">
            @elseif ($trait->type() === 'integer' || $trait->type() === 'float')
                <input class="text-black border rounded h-fit" type="number" size="4" min='{{ $trait->getTrait()->min }}' max='{{ $trait->getTrait()->max }}' value="{{ $trait->getValue() }}" wire:model.live="value">
            @endif
        </div>
    @endif
</div>
