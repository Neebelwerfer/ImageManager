@props(['name', 'formAction' => false])

<div
    x-data="modal('{{ $name }}')"
>
@teleport('body')
    <div class="fixed top-0 left-0 w-full h-full origin-center bg-slate-400/50 {{ $attributes->get('class') }}"
        x-show="isOpen"
        x-cloak
        x-transition
    >
        <div class="flex justify-center w-full h-full">
            <div class="flex flex-col self-center bg-slate-400" x-on:click.away="closeModal()">
                @if($formAction)
                    <form wire:submit.prevent="{{ $formAction }}">
                @endif
                @if(isset($title))
                    <div class="p-4 border-b bg-slate-400 sm:px-6 sm:py-4 border-slate-900">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">
                            {{ $title }}
                        </h3>
                    </div>
                @endif
                <div class="px-4 bg-slate-400 min-h-40 min-w-96">
                        {{ $content }}
                </div>
                @isset($buttons)
                    <div class="px-4 pb-5 bg-slate-600 sm:px-4 sm:flex">
                        {{ $buttons }}
                    </div>
                @endisset
                @if($formAction)
                    </form>
                @endif

                @if(isset($footer))
                    <div class="w-full px-4 border-t border-black bg-slate-600 sm:px-4 sm:flex">
                        {{ $footer }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endteleport
</div>
