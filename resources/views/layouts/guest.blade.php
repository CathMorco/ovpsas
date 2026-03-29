<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Authentication - OVPSAS Portal</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900 px-4">
            
            {{-- UPDATED OVPSAS Portal BRANDING --}}
            <div class="mb-8">
                <a href="/" class="flex flex-col items-center gap-4 group">
                    <img src="{{ asset('images/PUPLogo.png') }}" alt="Logo" class="w-24 h-24 rounded-full border-4 border-white bg-white shadow-lg group-hover:scale-105 transition duration-300">
                    <div class="flex flex-col text-center leading-none">
                        <span class="font-black text-5xl tracking-tighter text-[#800000]">OVPSAS Portal</span>
                        <span class="text-[10px] font-bold text-gray-500 tracking-[0.2em] uppercase mt-2">Office of Student Affairs and Services</span>
                    </div>
                </a>
            </div>

            {{-- The Login/Register Box --}}
            <div class="w-full sm:max-w-md px-8 py-10 bg-white dark:bg-gray-800 shadow-2xl overflow-hidden sm:rounded-2xl border-t-[10px] border-[#800000]">
                {{ $slot }}
            </div>

            {{-- Footer --}}
            <footer class="mt-10 text-center">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                    &copy; {{ date('Y') }} OVPSAS Portal. Polytechnic University of the Philippines.
                </p>
            </footer>
        </div>
    </body>
</html>