<div class="flex flex-col flex-grow ">
    @if(isset($header))
        <div class="flex flex-col justify-center p-2">
            {{ $header }}
        </div>
    @endif

    <div class="grid grid-cols-5 grid-rows-4 gap-2 p-1.5 mx-4 my-2 bg-gray-800 border rounded h-full" style="width: 1330px;">
       {{ $slot }}
    </div>
</div>
