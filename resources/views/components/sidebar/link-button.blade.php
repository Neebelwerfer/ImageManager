@props(['active' => false, 'route' => '#'])

@php

    $classes =
        $active ?? false
            ? 'w-full flex justify-center p-1 block border-t border-b hover:bg-gray-400 hover:dark:bg-gray-500 bg-gray-400 dark:bg-gray-500'
            : 'w-full flex justify-center p-1 block border-t border-b hover:bg-gray-400 hover:dark:bg-gray-500';

@endphp

<li><a {{ $attributes->merge(['class' => $classes, 'href' => $route]) }} wire:navigate>{{ $slot }}</a></li>
