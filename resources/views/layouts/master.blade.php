<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'OSAS')</title>
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
    <div x-show="showLoginNotice" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="fixed bottom-10 right-10 z-[60] bg-[#800000] text-white px-6 py-4 rounded-lg shadow-2xl flex items-center gap-3 border-l-4 border-yellow-400">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <div>
            <p class="font-bold">Access Restricted</p>
            <p class="text-sm">Please <a href="{{ route('login') }}" class="underline font-bold hover:text-yellow-300">Log In</a> to interact.</p>
        </div>
    </div>

    @auth
        @php
            // 1. Fetch users active in the last 24 hours
            $activeUsers = \App\Models\User::where('last_seen_at', '>=', now()->subDay())
                ->orderBy('last_seen_at', 'desc')
                ->get();

            // 2. Logic to determine status badge and text
            $getUserStatusDetails = function($user) {
                if (!$user->last_seen_at) return ['color' => 'bg-gray-300', 'text' => 'Offline'];
                $diff = $user->last_seen_at->diffInMinutes(now());
                if ($diff < 5) return ['color' => 'bg-green-500', 'text' => 'Online'];
                if ($diff < 60) return ['color' => 'bg-yellow-400', 'text' => 'Idle'];
                return ['color' => 'bg-gray-400', 'text' => 'Active ' . $user->last_seen_at->diffForHumans()];
            };

            // 3. Count pending registrations
            $pendingCount = \App\Models\User::where('status', 'pending')->count();
        @endphp
    @endauth

    <nav class="bg-[#800000] border-b border-red-900 sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex">
                    <div class="shrink-0 flex items-center">
                        <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                            <img src="{{ asset('images/PUPLogo.png') }}" alt="Logo" class="block h-12 w-12 rounded-full border-2 border-white bg-white group-hover:scale-105 transition">
                            <div class="hidden lg:flex flex-col text-white leading-tight">
                                <span class="font-black text-2xl tracking-tight">OSAS</span>
                                <span class="text-xs opacity-90 font-light tracking-widest">Office of Student Affairs and Services</span>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex items-center">
                    <a href="{{ url('/') }}" class="text-white font-bold hover:text-yellow-300 transition text-sm {{ Request::is('/') ? 'border-b-2 border-yellow-400' : '' }}">Home</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-white font-bold hover:text-yellow-300 transition text-sm {{ Request::is('dashboard') ? 'border-b-2 border-yellow-400' : '' }}">Dashboard</a>
                        
                        <a href="{{ route('directory.index') }}" class="text-white font-bold hover:text-yellow-300 transition text-sm {{ Request::is('directory*') ? 'border-b-2 border-yellow-400' : '' }}">Staff Directory</a>

                        @if(auth()->user()->isSuperAdmin())
                            <a href="{{ route('users.list') }}" class="text-white font-bold hover:text-yellow-300 transition text-sm {{ Request::is('userslist*') ? 'border-b-2 border-yellow-400' : '' }}">Management</a>
                        @endif

                        @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                            <a href="{{ route('admin.approvals') }}" class="text-white font-bold hover:text-yellow-300 transition text-sm flex items-center gap-2 {{ Request::is('approvals*') ? 'border-b-2 border-yellow-400' : '' }}">
                                Approvals 
                                @if($pendingCount > 0)
                                    <span class="bg-yellow-400 text-[#800000] px-2 py-0.5 rounded-full text-[10px] font-black animate-bounce">{{ $pendingCount }}</span>
                                @endif
                            </a>
                        @endif
                    @endauth
                    <a href="{{ url('/about') }}" class="text-white font-bold hover:text-yellow-300 transition text-sm {{ Request::is('about') ? 'border-b-2 border-yellow-400' : '' }}">About Us</a>
                </div>

                <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">
                    <form action="{{ route('search') }}" method="GET" class="relative w-64">
                        <input type="text" name="search" placeholder="Search..." class="w-full bg-white text-gray-800 rounded-full px-4 py-1.5 text-sm border-none focus:ring-2 focus:ring-yellow-400 outline-none">
                        <button type="submit" class="absolute right-1 top-1/2 -translate-y-1/2 bg-[#FCD116] p-1 rounded-full hover:bg-yellow-300 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </button>
                    </form>

                    @guest
                        <a href="{{ route('login') }}" class="text-white font-bold text-sm hover:text-yellow-300 uppercase tracking-widest transition">Log in</a>
                    @else
                        <div class="flex items-center gap-4">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="text-white font-bold text-sm flex items-center gap-1 hover:text-yellow-300 transition uppercase">
                                        {{ Auth::user()->name }}
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" /></svg>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
                                    <x-dropdown-link :href="route('settings.edit')">Settings</x-dropdown-link>
                                    <form method="POST" action="{{ route('logout') }}">@csrf <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Log Out</x-dropdown-link></form>
                                </x-slot>
                            </x-dropdown>
                            
                            <button @click="sidebarOpen = !sidebarOpen" class="text-white hover:text-yellow-300 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" :class="{'rotate-180': !sidebarOpen}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" /></svg>
                            </button>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow py-10">@yield('content')</main>

    <footer class="bg-gray-800 text-white py-4 text-center text-xs">&copy; {{ date('Y') }} OSAS. Polytechnic University of the Philippines.</footer>

    {{-- Slide-over Sidebar (Quick Panel) --}}
    @auth
    <div class="relative z-50" x-show="sidebarOpen" x-cloak>
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="sidebarOpen = false"></div>
        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div class="fixed inset-y-0 right-0 flex max-w-full pl-10">
                    <div class="w-screen max-w-md pointer-events-auto"
                         x-show="sidebarOpen" x-transition:enter="transform transition ease-in-out duration-500" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-500" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
                        <div class="flex h-full flex-col bg-white shadow-xl">
                            <div class="px-4 py-6 bg-[#800000] flex justify-between items-center text-white">
                                <h2 class="text-lg font-bold">Quick Panel</h2>
                                <button @click="sidebarOpen = false"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg></button>
                            </div>
                            <div class="p-6 space-y-8 bg-gray-50 flex-1 overflow-y-auto">
                                
                                {{-- System Controls --}}
                                <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                    <h3 class="text-[10px] font-black uppercase text-gray-800 tracking-widest mb-4">System Controls</h3>
                                    <div class="grid grid-cols-2 gap-3">
                                        @if(auth()->user()->isSuperAdmin())
                                            <a href="{{ route('users.list') }}" class="flex flex-col items-center justify-center p-3 bg-yellow-50 rounded-lg border border-yellow-100 hover:bg-yellow-100 transition-colors group">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                                <span class="text-[9px] font-black uppercase text-yellow-700">Manage Roles</span>
                                            </a>
                                        @endif
                                        @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                                            <a href="{{ route('admin.approvals') }}" class="flex flex-col items-center justify-center p-3 bg-red-50 rounded-lg border border-red-100 hover:bg-red-100 transition-colors">
                                                <div class="relative">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#800000] mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                    @if($pendingCount > 0) <span class="absolute -top-1 -right-1 h-2 w-2 bg-yellow-400 rounded-full animate-ping"></span> @endif
                                                </div>
                                                <span class="text-[9px] font-black uppercase text-[#800000]">Approvals</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                {{-- Active Users --}}
                                <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                    <h3 class="text-[10px] font-black uppercase text-gray-800 tracking-widest mb-4 flex justify-between">Active Users <span>{{ $activeUsers->count() }} Users</span></h3>
                                    <ul class="space-y-4">
                                        @foreach($activeUsers as $user)
                                            @php $status = $getUserStatusDetails($user); @endphp
                                            <li class="flex justify-between items-center">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600 overflow-hidden">
                                                        @if($user->avatar) <img src="{{ asset('storage/' . $user->avatar) }}" class="h-full w-full object-cover"> @else {{ substr($user->name, 0, 1) }} @endif
                                                    </div>
                                                    <div class="flex flex-col leading-none">
                                                        <span class="text-gray-700 font-bold text-[11px] uppercase tracking-tighter">{{ $user->name }}</span>
                                                        <span class="text-[9px] text-gray-400 font-bold italic">{{ $status['text'] }}</span>
                                                    </div>
                                                </div>
                                                <span class="h-2 w-2 rounded-full {{ $status['color'] }} shadow-sm {{ $status['color'] === 'bg-green-500' ? 'animate-pulse' : '' }}"></span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                {{-- SYSTEM ACTIVITY LOG BLOCK --}}
                                <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="text-[10px] font-black text-gray-800 uppercase tracking-widest">System Log</h3>
                                        <span class="text-[9px] font-bold text-gray-400 uppercase">Live</span>
                                    </div>
                                    
                                    <div class="space-y-4 max-h-[300px] overflow-y-auto">
                                        {{-- Directly fetch the latest 10 activities so it works on any page --}}
                                        @php
                                            $globalActivities = \App\Models\RecentActivity::with('user')->latest()->take(10)->get();
                                        @endphp
                                        
                                        @forelse($globalActivities as $activity)
                                            <div class="flex items-start gap-3 border-b border-gray-50 pb-3 last:border-0">
                                                <div class="w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center shrink-0 font-black text-[#800000] text-[9px] uppercase border border-gray-200">
                                                    {{ substr($activity->user->name ?? '?', 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="text-[10px] text-gray-800 leading-tight">
                                                        <span class="font-black uppercase">{{ $activity->user->name ?? 'User' }}</span> 
                                                        
                                                        @if($activity->action === 'Uploaded') <span class="text-blue-600 font-bold">uploaded</span>
                                                        @elseif($activity->action === 'Deleted') <span class="text-red-600 font-bold">deleted</span>
                                                        @elseif($activity->action === 'Edited') <span class="text-yellow-600 font-bold">edited</span>
                                                        @elseif($activity->action === 'Commented') <span class="text-green-600 font-bold">commented on</span>
                                                        @else <span class="text-gray-500 font-bold">{{ strtolower($activity->action) }}</span>
                                                        @endif 
                                                        
                                                        <span class="italic text-gray-600">"{{ Str::limit($activity->file_name, 22) }}"</span>
                                                    </p>
                                                    <p class="text-[8px] text-gray-400 font-bold uppercase mt-1 tracking-tighter">
                                                        {{ $activity->created_at->diffForHumans() }} • {{ Str::limit($activity->office_name, 15) }}
                                                    </p>
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-center text-gray-400 text-[10px] uppercase font-bold tracking-widest italic py-4">No recent activity.</p>
                                        @endforelse
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
        document.addEventListener('visibilitychange', function() { if (document.visibilityState === 'visible') window.location.reload(); });
    </script>
</body>
</html>