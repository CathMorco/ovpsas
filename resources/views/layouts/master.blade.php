<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OVPSAS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased flex flex-col min-h-screen">

    <nav class="w-full bg-[#800000] px-6 py-4 shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">

            <a href="{{ url('/') }}" class="flex items-center gap-4 group">
                <img src="/images/PUPLogo.png" alt="PUP Logo" class="h-14 w-14 rounded-full bg-white p-0.5 border-2 border-white group-hover:scale-105 transition-transform">
                <div class="flex flex-col text-white leading-tight">
                    <span class="font-bold text-lg tracking-wide group-hover:text-yellow-300 transition-colors">Student Affairs</span>
                    <span class="font-light text-sm opacity-90">Services and Information System</span>
                </div>
            </a>

            <div class="hidden md:flex items-center gap-8 text-white font-bold text-sm tracking-wide">
                <a href="{{ url('/') }}" class="hover:text-yellow-300 transition {{ Request::is('/') ? 'text-yellow-300 border-b-2 border-yellow-300' : '' }}">
                    Home
                </a>

                @auth
                    <a href="{{ url('/dashboard') }}" class="hover:text-yellow-300 transition {{ Request::is('dashboard') ? 'text-yellow-300 border-b-2 border-yellow-300' : '' }}">
                        Dashboard
                    </a>
                @endauth

                <a href="{{ url('/about') }}" class="hover:text-yellow-300 transition {{ Request::is('about') ? 'text-yellow-300 border-b-2 border-yellow-300' : '' }}">
                    About Us
                </a>

                @guest
                    <a href="{{ route('login') }}" class="border-b-2 border-white pb-1 hover:text-yellow-300 hover:border-yellow-300 transition">
                        Log in
                    </a>
                @else
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="hover:text-yellow-300 transition">Log Out</button>
                    </form>
                @endguest
            </div>

            <div class="relative hidden md:block w-64 ml-4">
                <input type="text" placeholder="Search..." class="w-full bg-white text-gray-800 rounded-full px-4 py-1.5 pl-4 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                <button class="absolute right-1 top-1/2 transform -translate-y-1/2 bg-[#FCD116] p-1 rounded-full hover:bg-yellow-300 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <main class="flex-grow">
        @yield('content')
    </main>

    <footer class="bg-gray-800 text-white py-4 text-center text-xs">
        &copy; {{ date('Y') }} OVPSAS. Polytechnic University of the Philippines.
    </footer>

</body>
</html>
