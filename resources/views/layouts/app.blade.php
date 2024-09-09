<!DOCTYPE html>
<html class="dark" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">


    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="font-sans antialiased">
    <div class="flex flex-col min-h-screen bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">

        <livewire:layout.navbar />

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-gray-300 shadow dark:bg-gray-800">
                <div class="px-4 py-6 mx-auto max-w-fit sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <main class="flex flex-col flex-grow">
            {{ $slot }}
        </main>



        <footer
            class="w-full mt-4 text-center text-black bg-gray-200 border-t border-slate-600 dark:text-white/70 dark:bg-black">
            Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
        </footer>
    </div>
</body>

</html>
