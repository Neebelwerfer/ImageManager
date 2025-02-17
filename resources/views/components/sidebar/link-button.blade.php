@props(['route' => '#'])

<li><a {{ $attributes->merge(['class' => 'w-full flex justify-center p-1 block border-t border-b hover:bg-gray-400 hover:dark:bg-gray-500', 'href' => $route]) }} wire:navigate wire:current='bg-gray-400 dark:bg-gray-500'>{{ $slot }}</a></li>
