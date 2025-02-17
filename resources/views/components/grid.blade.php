<div {{ $attributes->merge(['class' => 'flex flex-col']) }}>
    @if(isset($header))
        <div class="flex flex-col justify-center p-2">
            {{ $header }}
        </div>
    @endif

    <div class="grid grid-cols-5 grid-rows-4 gap-2 p-1.5 mx-4 my-2 bg-slate-300 dark:bg-gray-800 border border-black dark:border-white rounded h-full" style="width: 97.5%;">
       {{ $slot }}
    </div>
</div>
