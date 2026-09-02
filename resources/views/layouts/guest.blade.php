<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#047857">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23047857'%3E%3Cpath d='M3 3a1 1 0 0 0-.98.8L1.02 8.6A2.5 2.5 0 0 0 3.5 11.5c.78 0 1.48-.33 1.98-.85.46.52 1.14.85 1.9.85.78 0 1.48-.33 1.98-.85.46.52 1.14.85 1.9.85.78 0 1.48-.33 1.98-.85.46.52 1.14.85 1.9.85.78 0 1.48-.33 1.98-.85a2.65 2.65 0 0 0 1.98.85 2.5 2.5 0 0 0 2.48-2.9L21.98 3.8A1 1 0 0 0 21 3H3z'/%3E%3Cpath d='M4 12.9V19a2 2 0 0 0 2 2h3v-5h6v5h3a2 2 0 0 0 2-2v-6.1a4.48 4.48 0 0 1-1.98.35 4.5 4.5 0 0 1-1.9-.42 4.5 4.5 0 0 1-3.88 0 4.5 4.5 0 0 1-3.88 0 4.5 4.5 0 0 1-1.9.42 4.48 4.48 0 0 1-1.98-.35z'/%3E%3C/svg%3E">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <!-- TOP BAR: navigasi balik ke situs publik -->
        <nav class="bg-brand text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between">
                <a href="{{ route('katalog.publik') }}" class="inline-flex items-center gap-1.5 text-sm font-medium hover:text-white/80 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 111.414 1.414L7.414 9H17a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" /></svg>
                    Kembali ke Beranda
                </a>
                <span class="font-bold text-sm tracking-wide hidden sm:inline">PORTAL UMKM MUHAMMADIYAH DIY</span>
            </div>
        </nav>

        <div class="min-h-[calc(100vh-3rem)] flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <a href="{{ route('katalog.publik') }}">
                    <x-application-logo class="w-20 h-20 fill-current text-brand" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
