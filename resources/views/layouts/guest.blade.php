<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Guest')</title>

    <link rel="icon" type="image/png" href="{{ asset('storage/theme/logo.png') }}">

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-gray-100 text-gray-800 antialiased">
    <div class="min-h-screen flex flex-col justify-center items-center">
        <div class="w-full max-w-md px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            @yield('content')
        </div>
    </div>
</body>
</html>
