@props(['formAction' => false])

<div>
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
            <div class="px-4 bg-slate-400 sm:p-6">
                <div class="space-y-6">
                    {{ $content }}
                </div>
            </div>

            <div class="px-4 pb-5 bg-slate-500 sm:px-4 sm:flex">
                {{ $buttons }}
            </div>
    @if($formAction)
        </form>
    @endif
</div>
