<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-100">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <!-- Logo & Judul -->
            <div class="mb-6 text-center">
                <a href="/" class="flex flex-col items-center gap-y-3">
                    <x-application-logo class="w-20 h-20 fill-current text-indigo-600 drop-shadow-sm" />
                    <span class="text-3xl font-extrabold text-gray-800 tracking-tight">
                        CBT Satpel Bantul
                    </span>
                </a>
            </div>

            <!-- Kartu Form -->
            <div class="w-full sm:max-w-md px-8 py-8 bg-white shadow-xl overflow-hidden sm:rounded-2xl border border-gray-100">
                {{ $slot }}
            </div>

            <!-- Footer Copyright -->
            <div class="mt-8 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} Satpel Bantul. All rights reserved.
            </div>
        </div>
    </body>
</html>