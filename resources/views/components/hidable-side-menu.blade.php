<div class="fixed flex flex-col h-full" x-data="{showMenu: true}" :class="showMenu ? 'border-r' : ''">
    <div class="flex flex-row justify-between">
        <h1 class="mx-2 text-xl font-bold underline" x-show="showMenu">@isset($title) {{ $title }} @endif</h1>
        <div class="flex" :class="showMenu ? 'justify-end' : ''">
            <button x-on:click="showMenu = !showMenu">
                <x-svg.burger-menu/>
            </button>
        </div>
    </div>
    <div x-show="showMenu" class="mx-2 w-80" x-transition>
        {{ $slot }}
    </div>
</div>
