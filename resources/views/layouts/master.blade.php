<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OVPSAS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased flex flex-col min-h-screen">

    <nav x-data="{ open: false }" class="bg-[#800000] border-b border-red-900 sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                
                <div class="flex">
                    <div class="shrink-0 flex items-center">
                        <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                            <img src="{{ asset('images/PUPLogo.png') }}" alt="Logo" class="block h-12 w-12 rounded-full border-2 border-white bg-white group-hover:scale-105 transition-transform">
                            <div class="hidden lg:flex flex-col text-white leading-tight">
                                <span class="font-bold text-lg tracking-wide group-hover:text-yellow-300 transition-colors">Student Affairs</span>
                                <span class="text-xs opacity-90 font-light text-white">Services and Information System</span>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex items-center">
                    <a href="{{ url('/') }}" class="text-white font-bold hover:text-yellow-300 transition text-sm {{ Request::is('/') ? 'border-b-2 border-yellow-400 pb-1' : '' }}">Home</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-white font-bold hover:text-yellow-300 transition text-sm {{ Request::is('dashboard') ? 'border-b-2 border-yellow-400 pb-1' : '' }}">Dashboard</a>
                    @endauth
                    <a href="{{ url('/about') }}" class="text-white font-bold hover:text-yellow-300 transition text-sm {{ Request::is('about') ? 'border-b-2 border-yellow-400 pb-1' : '' }}">About Us</a>
                </div>

                <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">
                    
                    <form action="{{ route('search') }}" method="GET" class="relative hidden md:block w-64">
                        <input type="text" 
                            name="query" 
                            value="{{ request('query') }}" 
                            placeholder="Search..." 
                            class="w-full bg-white text-gray-800 rounded-full px-4 py-1.5 pl-4 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        
                        <button type="submit" class="absolute right-1 top-1/2 transform -translate-y-1/2 bg-[#FCD116] p-1 rounded-full hover:bg-yellow-300 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </form>

                    @guest
                        <a href="{{ route('login') }}" class="text-white font-bold text-sm border-b-2 border-transparent hover:border-yellow-400 hover:text-yellow-300 transition uppercase tracking-wider">Log in</a>
                    @else
                        <div class="relative">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-bold rounded-md text-white hover:text-yellow-300 focus:outline-none transition ease-in-out duration-150">
                                        <div>{{ Auth::user()->name }}</div>
                                        <div class="ms-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile.edit')">
                                        {{ __('Profile') }}
                                    </x-dropdown-link>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')"
                                                onclick="event.preventDefault(); this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endguest
                </div>

                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-gray-200 hover:bg-red-900 focus:outline-none transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-red-900 text-white">
            <div class="pt-2 pb-3 space-y-1">
                <a href="{{ url('/') }}" class="block px-3 py-2 text-base font-medium text-white hover:bg-red-800">Home</a>
                @auth 
                    <a href="{{ route('dashboard') }}" class="block px-3 py-2 text-base font-medium text-white hover:bg-red-800">Dashboard</a> 
                @endauth
                <a href="{{ url('/about') }}" class="block px-3 py-2 text-base font-medium text-white hover:bg-red-800">About Us</a>
            </div>
            
            <div class="pt-4 pb-1 border-t border-red-800">
                @auth
                    <div class="px-4">
                        <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-300">{{ Auth::user()->email }}</div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-base font-medium text-gray-200 hover:bg-red-800">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}" class="block px-3 py-2 text-base font-medium text-gray-200 hover:bg-red-800"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Log Out
                            </a>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2 text-base font-medium text-white hover:bg-red-800">Log In</a>
                @endauth
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