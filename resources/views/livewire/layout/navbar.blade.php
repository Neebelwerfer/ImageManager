<?php

use App\Livewire\Actions\Logout;

$logout = function (Logout $logout) {
    $logout();

    $this->redirect('/', navigate: true);
};

?>

<nav x-data="{ open: false }" class="bg-gray-300 border-b border-gray-400 dark:bg-gray-800 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="flex items-center shrink-0">
                    <a href="{{ route('home') }}" wire:navigate>
                        <x-application-logo class="block w-auto text-gray-800 fill-current h-9 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @auth
                        @if (Route::has('admin') && Auth::user()->can('admin'))
                            <x-nav-link :href="route('admin')" :active="request()->routeIs('admin')" wire:navigate>
                                {{ __('Admin') }}
                            </x-nav-link>
                        @endif

                        @if (Route::has('image.upload'))
                        <x-nav-link :href="route('image.upload')" :active="request()->routeIs('image.upload')" wire:navigate>
                            {{ __('Image Upload') }}
                        </x-nav-link>
                        @endif

                        @if (Route::has('collection'))
                        <x-nav-link :href="route('collection')" :active="request()->routeIs('collection')" wire:navigate>
                            {{ __('Collection') }}
                        </x-nav-link>
                        @endif

                        @if (Route::has('manage'))
                            <x-nav-link :href="route('manage')" :active="request()->routeIs('manage')" wire:navigate>
                                {{ __('Manage') }}
                            </x-nav-link>
                        @endif
                    @endauth

                </div>
            </div>



            <div class="hidden sm:flex sm:items-center sm:ms-6">

                <!-- Theme Dropdown -->
                <div class="sm:flex sm:items-center sm:ms-6">
                    <x-dropdown align="right" width="14">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-600 transition duration-150 ease-in-out border border-transparent rounded-md dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none">
                                <p id="theme">Theme</p>
                                <div class="ms-1">
                                    <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <div class="flex flex-col items-center">
                                <button id="light"
                                    class="flex justify-center w-full h-8 border-b border-gray-900 dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-600">
                                    <svg class="w-5" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                                            fill-rule="evenodd" clip-rule="evenodd"></path>
                                    </svg>
                                </button>

                                <button id="dark"
                                    class="inline-flex justify-center w-full h-8 border-b border-gray-900 dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-600">
                                    <svg class="w-5" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z">
                                        </path>
                                    </svg>
                                </button>
                                <button id="system"
                                    class="flex justify-center w-full h-8 hover:bg-gray-200 dark:hover:bg-gray-600">
                                    <svg class="w-5" fill="currentColor" version="1.1"
                                        xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        viewBox="0 0 34.418 34.418">
                                        <path
                                            d="M32.335,11.606h-6.533v-1.009h6.533V11.606z M32.335,12.264h-6.533v1.011h6.533V12.264z    M32.331,7.687h-6.523V9.91h6.523V7.687z M34.418,5.297v23.827c-0.001,0.357-0.292,0.648-0.65,0.648h-9.397   c-0.359,0-0.649-0.291-0.649-0.649V5.297c0-0.358,0.29-0.65,0.649-0.65h9.397C34.127,4.647,34.418,4.939,34.418,5.297z    M33.119,5.944h-8.1v22.53h8.1V5.944z M32.335,13.932h-6.533v1.008h6.533V13.932z M31.272,21.45c0,1.162-0.939,2.099-2.097,2.099   c-1.16,0-2.099-0.938-2.099-2.099c0-1.158,0.94-2.098,2.099-2.098C30.333,19.352,31.272,20.293,31.272,21.45z M30.515,21.45   c0-0.739-0.6-1.341-1.341-1.341c-0.742,0-1.342,0.601-1.342,1.341s0.6,1.34,1.342,1.34C29.915,22.79,30.515,22.19,30.515,21.45z    M21.937,9.218v13.505c0,0.814-0.655,1.473-1.461,1.473H13.18c0,0-0.414,2.948,2.212,2.948v1.475H13.18H8.758H6.546v-1.473   c2.529,0,2.212-2.948,2.212-2.948H1.465C0.656,24.198,0,23.539,0,22.725V9.218c0-0.814,0.656-1.47,1.465-1.47h19.01   C21.282,7.748,21.937,8.404,21.937,9.218z M12.332,22.394c0-0.698-0.566-1.263-1.264-1.263c-0.699,0-1.266,0.565-1.266,1.263   s0.566,1.265,1.266,1.265C11.767,23.659,12.332,23.092,12.332,22.394z M20.371,9.311H1.568v11.387H20.37h0.001   C20.371,20.698,20.371,9.311,20.371,9.311z M11.081,21.603c-0.434,0-0.785,0.352-0.785,0.785s0.352,0.785,0.785,0.785   s0.785-0.352,0.785-0.785S11.515,21.603,11.081,21.603z" />
                                    </svg> </button>
                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>

                @auth
                    <!-- Settings Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md dark:text-gray-400 dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none">
                                    <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name"
                                        x-on:profile-updated.window="name = $event.detail.name"></div>

                                    <div class="ms-1">
                                        <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile')" wire:navigate>
                                    {{ __('Profile') }}
                                </x-dropdown-link>

                                <!-- Authentication -->
                                <button wire:click="logout" class="w-full text-start">
                                    <x-dropdown-link>
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </button>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @else
                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <x-nav-link :href="route('login')" wire:navigate>{{ __('Login') }}</x-nav-link>
                    </div>

                    @if (Route::has('register'))
                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <x-nav-link :href="route('register')" wire:navigate>{{ __('Register') }}</x-nav-link>
                        </div>
                    @endif
                @endauth
            </div>

        </div>
    </div>

</nav>
