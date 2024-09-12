@props(['active' => false])

@php

    $classes =
        $active ?? false
            ? 'w-full p-1 border-t border-b hover:bg-gray-400 hover:dark:bg-gray-500 bg-gray-400 dark:bg-gray-500'
            : 'w-full p-1 border-t border-b hover:bg-gray-400 hover:dark:bg-gray-500';

@endphp

<li><button {{ $attributes->merge(['class' => $classes]) }}
        {{ $attributes->merge(['wire:click' => '']) }}>{{ $slot }}</button></li>
