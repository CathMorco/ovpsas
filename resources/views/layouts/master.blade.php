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

    {{-- Auth Restriction Toast --}}
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
            // Grabs users active in the last 24 hours (1440 mins)
            $activeUsers = \App\Models\User::whereHas('sessions', function($query) {
                $query->where('last_activity', '>=', now()->subMinutes(1440)->getTimestamp());
            })->with(['sessions' => function($query) {
                $query->orderBy('last_activity', 'desc');
            }])->get();

            // FIX: Use an anonymous function to prevent redeclaration crashes
            $getUserStatusDetails = function($user) {
                $lastActivity = $user->sessions->first()->last_activity ?? 0;
                $minutesAgo = (now()->getTimestamp() - $lastActivity) / 60;
                
                if ($minutesAgo < 5) return ['color' => 'bg-green-500', 'text' => 'Online'];
                if ($minutesAgo < 60) return ['color' => 'bg-yellow-400', 'text' => 'Idle'];
                
                $timeString = \Carbon\Carbon::createFromTimestamp($lastActivity)->diffForHumans();
                return ['color' => 'bg-gray-400', 'text' => 'Last seen ' . $timeString];
            };

            // Calculate pending registrations for the badge
            $pendingCount = \App\Models\User::where('status', 'Pending')->count();
        @endphp
    @endauth

    <nav x-data="{ open: false }" class="bg-[#800000] border-b border-red-900 sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                
                {{-- Logo Section --}}
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

                {{-- Desktop Nav Links --}}
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex items-center">
                    <a href="{{ url('/') }}" class="text-white font-bold hover:text-yellow-300 transition text-sm {{ Request::is('/') ? 'border-b-2 border-yellow-400 pb-1' : '' }}">Home</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-white font-bold hover:text-yellow-300 transition text-sm {{ Request::is('dashboard') ? 'border-b-2 border-yellow-400 pb-1' : '' }}">Dashboard</a>
                        
                        {{-- ADMIN & SUPER ADMIN LINKS --}}
                        @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                            <a href="{{ route('users.list') }}" class="text-white font-bold hover:text-yellow-300 transition text-sm {{ Request::is('userslist*') ? 'border-b-2 border-yellow-400 pb-1' : '' }}">User Management</a>
                            
                            <a href="{{ route('admin.approvals') }}" class="text-white font-bold hover:text-yellow-300 transition text-sm flex items-center gap-2 {{ Request::is('approvals*') ? 'border-b-2 border-yellow-400 pb-1' : '' }}">
                                Approvals 
                                @if($pendingCount > 0)
                                    <span class="bg-yellow-400 text-[#800000] px-2 py-0.5 rounded-full text-[10px] font-black animate-bounce">{{ $pendingCount }}</span>
                                @endif
                            </a>
                        @endif
                    @endauth
                    <a href="{{ url('/about') }}" class="text-white font-bold hover:text-yellow-300 transition text-sm {{ Request::is('about') ? 'border-b-2 border-yellow-400 pb-1' : '' }}">About Us</a>
                </div>

                <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">
                    
                    {{-- SEARCH BAR --}}
                    <form action="{{ route('search') }}" method="GET" class="relative hidden md:block w-64">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search announcements, files..." 
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
                                    <x-dropdown-link :href="route('settings.edit')">{{ __('Settings') }}</x-dropdown-link>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
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

                {{-- Mobile Menu Button --}}
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

        {{-- Mobile Nav Menu --}}
        <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-red-900 text-white">
            <div class="pt-2 pb-3 space-y-1">
                <a href="{{ url('/') }}" class="block px-3 py-2 text-base font-medium text-white hover:bg-red-800">Home</a>
                @auth <a href="{{ route('dashboard') }}" class="block px-3 py-2 text-base font-medium text-white hover:bg-red-800">Dashboard</a> @endauth
                <a href="{{ url('/about') }}" class="block px-3 py-2 text-base font-medium text-white hover:bg-red-800">About Us</a>
                
                <form action="{{ route('search') }}" method="GET" class="px-3 py-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="w-full rounded-md text-gray-900 text-sm">
                </form>
            </div>
        </div>
    </nav>

    <main class="flex-grow py-10 sm:py-12">
        @yield('content')
    </main>

    <footer class="bg-gray-800 text-white py-4 text-center text-xs">
        &copy; {{ date('Y') }} OVPSAS. Polytechnic University of the Philippines.
    </footer>

    {{-- Slide-over Sidebar --}}
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
                                
                                {{-- Active Users Section --}}
                                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                                    <h3 class="font-bold text-gray-800 mb-4 flex items-center justify-between uppercase text-[10px] tracking-widest">
                                        Recent Activity
                                        <span class="text-[9px] bg-gray-200 text-gray-600 px-2 py-1 rounded-full">{{ $activeUsers->count() }} Users</span>
                                    </h3>
                                    @if($activeUsers->isEmpty())
                                        <p class="text-sm text-gray-500 italic">No users active recently.</p>
                                    @else
                                        <ul class="space-y-4 text-sm">
                                            @foreach($activeUsers as $activeUser)
                                                @php $status = $getUserStatusDetails($activeUser); @endphp
                                                <li class="flex justify-between items-center group">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-8 h-8 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600 group-hover:bg-[#800000] group-hover:text-white transition-all shadow-sm">
                                                            @if($activeUser->avatar)
                                                                <img src="{{ asset('storage/' . $activeUser->avatar) }}" alt="{{ $activeUser->name }}" class="w-full h-full object-cover">
                                                            @else
                                                                {{ substr($activeUser->name, 0, 1) }}
                                                            @endif
                                                        </div>
                                                        <div class="flex flex-col">
                                                            <span class="text-gray-700 font-medium text-[11px] uppercase tracking-tighter">{{ $activeUser->name }}</span>
                                                            <span class="text-[9px] text-gray-400 font-bold italic">{{ $status['text'] }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center gap-2" title="{{ $status['text'] }}">
                                                        <span class="h-3 w-3 rounded-full {{ $status['color'] }} ring-2 ring-white shadow-sm {{ $status['color'] === 'bg-green-500' ? 'animate-pulse' : '' }}"></span>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>

                                {{-- Quick Actions Section (Role-Based) --}}
                                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                                    <h3 class="font-bold text-gray-800 mb-4 uppercase text-[10px] tracking-widest">System Controls</h3>
                                    <div class="grid grid-cols-2 gap-3">
                                        
                                        {{-- Staff and Admins: Upload --}}
                                        @if(!auth()->user()->isViewer())
                                            <button class="flex flex-col items-center justify-center p-3 bg-gray-50 rounded-lg border border-gray-100 hover:bg-red-50 hover:border-red-200 hover:text-[#800000] transition-colors group">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 group-hover:text-[#800000] mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                </svg>
                                                <span class="text-[9px] font-black uppercase text-gray-600 group-hover:text-[#800000]">Upload File</span>
                                            </button>
                                        @endif

                                        {{-- Super Admin Only: Manage Roles --}}
                                        @if(auth()->user()->isSuperAdmin())
                                            <a href="{{ route('users.list') }}" class="flex flex-col items-center justify-center p-3 bg-gray-50 rounded-lg border border-gray-100 hover:bg-yellow-50 hover:border-yellow-200 hover:text-yellow-700 transition-colors group">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 group-hover:text-yellow-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                                </svg>
                                                <span class="text-[9px] font-black uppercase text-gray-600 group-hover:text-yellow-700">Manage Roles</span>
                                            </a>
                                        @endif

                                        {{-- Admin & Super Admin: Review Registrations --}}
                                        @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                                            <a href="{{ route('admin.approvals') }}" class="flex flex-col items-center justify-center p-3 bg-gray-50 rounded-lg border border-gray-100 hover:bg-red-50 hover:border-red-200 hover:text-[#800000] transition-colors group {{ auth()->user()->isSuperAdmin() ? 'col-span-2' : '' }}">
                                                <div class="relative">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 group-hover:text-[#800000] mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    @if($pendingCount > 0)
                                                        <span class="absolute -top-1 -right-1 flex h-2 w-2">
                                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-yellow-500"></span>
                                                        </span>
                                                    @endif
                                                </div>
                                                <span class="text-[9px] font-black uppercase text-gray-600 group-hover:text-[#800000]">Approvals</span>
                                            </a>
                                        @endif

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

    <script>
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                window.location.reload();
            }
        });
        window.addEventListener( "pageshow", function ( event ) {
            var historyTraversal = event.persisted || 
                                   ( typeof window.performance != "undefined" && 
                                     window.performance.navigation.type === 2 );
            if ( historyTraversal ) {
                window.location.reload();
            }
        });
    </script>
</body>
</html>