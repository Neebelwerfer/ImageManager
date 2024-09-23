<!DOCTYPE html>
<html class="dark" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />


    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="flex flex-col justify-between min-h-screen bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">

        <livewire:layout.navbar />

        <main class="mt-2">

            <div class="container mx-auto text-center">
                @auth
                    <h1 class="text-3xl font-bold">Welcome, {{ Auth::user()->name }}!</h1>

                    <div class="mt-4 columns-3">
                        <a href="{{ route('collection') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Collection</h5>
                        </a>

                        <a href="{{ route('manage') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Manage Images</h5>
                        </a>

                        <a href="{{ route('image.upload') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Upload Image</h5>
                        </a>
                    </div>
                @else
                    <h1 class="text-3xl font-bold"><a class="text-blue-500" href="{{ route('login') }}">Login</a> to manage images</h1>
                @endauth
            </div>

        </main>
        <footer
            class="w-full mt-4 text-center text-black bg-gray-200 border-t border-slate-600 dark:text-white/70 dark:bg-black">
            Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
        </footer>
    </div>
</body>

</html>
