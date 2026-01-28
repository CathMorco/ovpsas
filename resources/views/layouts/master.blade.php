<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'OVPSAS')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style> [x-cloak] { display: none !important; } </style>
</head>

<body class="bg-gray-100 font-sans antialiased flex flex-col min-h-screen" 
      x-data="{ 
          sidebarOpen: false, 
          isLoggedIn: {{ Auth::check() ? 'true' : 'false' }},
          showLoginNotice: false,
          triggerAuthNotice() {
              this.showLoginNotice = true;
              setTimeout(() => this.showLoginNotice = false, 3000);
          }
      }">

    <div x-show="showLoginNotice"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-2"
         class="fixed bottom-10 right-10 z-[60] bg-[#800000] text-white px-6 py-4 rounded-lg shadow-2xl flex items-center gap-3 border-l-4 border-yellow-400"
         style="display: none;" x-cloak>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <div>
            <p class="font-bold">Access Restricted</p>
            <p class="text-sm">Please <a href="{{ route('login') }}" class="underline font-bold hover:text-yellow-300">Log In</a> to interact with this content.</p>
        </div>
    </div>

    @auth
        @php
            $activeUsers = \App\Models\User::whereHas('sessions', function($query) {
                $query->where('last_activity', '>=', now()->subMinutes(60)->getTimestamp());
            })->with(['sessions' => function($query) {
                $query->orderBy('last_activity', 'desc');
            }])->get();

            function getUserStatus($user) {
                $lastActivity = $user->sessions->first()->last_activity ?? 0;
                $minutesAgo = (now()->getTimestamp() - $lastActivity) / 60;
                if ($minutesAgo < 5) return 'online';
                if ($minutesAgo < 60) return 'idle';
                return 'offline';
            }
        @endphp
    @endauth

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
                        <input type="text" name="query" value="{{ request('query') }}" placeholder="Search..." 
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
                                    <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endguest

                    @auth
                    <button @click="sidebarOpen = !sidebarOpen" 
                            class="text-white hover:text-yellow-300 focus:outline-none transition-colors z-50 relative" 
                            title="Toggle Side Panel">
                        <svg xmlns="http://www.w3.org/2000/svg" 
                             class="h-8 w-8 transition-transform duration-500 ease-in-out" 
                             :class="{'rotate-180': !sidebarOpen}" 
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                        </svg>
                    </button>
                    @endauth
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
                @auth <a href="{{ route('dashboard') }}" class="block px-3 py-2 text-base font-medium text-white hover:bg-red-800">Dashboard</a> @endauth
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
                            <a href="{{ route('logout') }}" class="block px-3 py-2 text-base font-medium text-gray-200 hover:bg-red-800" onclick="event.preventDefault(); this.closest('form').submit();">Log Out</a>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2 text-base font-medium text-white hover:bg-red-800">Log In</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="flex-grow py-10 sm:py-12">
        @yield('content')
    </main>

    <footer class="bg-gray-800 text-white py-4 text-center text-xs">
        &copy; {{ date('Y') }} OVPSAS. Polytechnic University of the Philippines.
    </footer>

    @auth
    <div class="relative z-50" aria-labelledby="slide-over-title" role="dialog" aria-modal="true" x-show="sidebarOpen" x-cloak>
        <div x-show="sidebarOpen" x-transition:enter="ease-in-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="sidebarOpen = false"></div>
        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                    <div x-show="sidebarOpen" x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="pointer-events-auto w-screen max-w-md">
                        <div class="flex h-full flex-col overflow-y-scroll bg-white shadow-xl">
                            <div class="px-4 py-6 sm:px-6 bg-[#800000]">
                                <div class="flex items-start justify-between">
                                    <h2 class="text-lg font-medium text-white" id="slide-over-title">Quick Panel</h2>
                                    <div class="ml-3 flex h-7 items-center">
                                        <button @click="sidebarOpen = false" type="button" class="rounded-md bg-[#800000] text-white hover:text-yellow-300 focus:outline-none">
                                            <span class="sr-only">Close panel</span>
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="relative flex-1 px-4 py-6 sm:px-6 space-y-8 bg-gray-50">
                                
                                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                                    <h3 class="font-bold text-gray-800 mb-4 flex items-center justify-between">
                                        Active Users
                                        <span class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded-full">{{ $activeUsers->count() }} Online</span>
                                    </h3>
                                    @if($activeUsers->isEmpty())
                                        <p class="text-sm text-gray-500 italic">No other users are currently online.</p>
                                    @else
                                        <ul class="space-y-3 text-sm">
                                            @foreach($activeUsers as $activeUser)
                                                @php $status = getUserStatus($activeUser); @endphp
                                                <li class="flex justify-between items-center group">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600 group-hover:bg-[#800000] group-hover:text-white transition-colors">
                                                            {{ substr($activeUser->name, 0, 1) }}
                                                        </div>
                                                        <span class="text-gray-700 font-medium">{{ $activeUser->name }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-2" title="{{ ucfirst($status) }}">
                                                        @if($status === 'online')
                                                            <span class="text-[10px] text-green-600 font-bold hidden group-hover:block">Online</span>
                                                            <span class="h-3 w-3 rounded-full bg-green-500 ring-2 ring-white shadow-sm animate-pulse"></span>
                                                        @else
                                                            <span class="text-[10px] text-yellow-600 font-bold hidden group-hover:block">Idle</span>
                                                            <span class="h-3 w-3 rounded-full bg-yellow-400 ring-2 ring-white shadow-sm"></span>
                                                        @endif
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>

                                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                                    <h3 class="font-bold text-gray-800 mb-4">Office</h3>
                                    <ul class="space-y-2 text-xs text-gray-600">
                                        <li class="hover:text-[#800000] cursor-pointer">Alumni Relations and Career Development Office (ARCDO)</li>
                                        <li class="hover:text-[#800000] cursor-pointer">Office of the Counseling and Psychological Services (OCPS)</li>
                                        <li class="hover:text-[#800000] cursor-pointer">Office of Scholarship and Financial Assistance (OSFA)</li>
                                        <li class="hover:text-[#800000] cursor-pointer">Office of the Student Services (OSS)</li>
                                        <li class="hover:text-[#800000] cursor-pointer">Office of the University Registrar (OUR)</li>
                                    </ul>
                                </div>

                                <div>
                                    <h3 class="font-bold text-gray-800 mb-4">Recently Accessed Items</h3>
                                    <div class="space-y-3">
                                        <div class="flex items-center bg-[#800000] rounded-xl p-3 shadow-md text-white">
                                            <div class="bg-white/20 p-2 rounded-lg mr-3"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg></div>
                                            <div><p class="font-bold text-sm">Filename Gamboa</p><p class="text-xs text-yellow-300 opacity-90">Office of the Student Services (OSS)</p></div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endauth

</body>
</html>