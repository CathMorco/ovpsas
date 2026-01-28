<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home - OVPSAS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased flex flex-col min-h-screen"
      x-data="{
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
         style="display: none;">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <div>
            <p class="font-bold">Access Restricted</p>
            <p class="text-sm">Please <a href="{{ route('login') }}" class="underline font-bold hover:text-yellow-300">Log In</a> to interact with this content.</p>
        </div>
    </div>

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
                    <a href="{{ url('/') }}" class="text-white font-bold hover:text-yellow-300 transition text-sm border-b-2 border-yellow-400 pb-1">Home</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-white font-bold hover:text-yellow-300 transition text-sm">Dashboard</a>
                    @endauth
                    <a href="{{ url('/about') }}" class="text-white font-bold hover:text-yellow-300 transition text-sm">About Us</a>
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
                                    <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-dropdown-link>
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

    <main class="flex-grow py-12">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 space-y-10">

            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm mb-6" role="alert">
                    <p class="font-bold">Success</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white shadow-xl sm:rounded-lg border-l-8 border-[#800000]">
                <div class="bg-[#800000] py-3 px-6 flex items-center gap-3">
                    <span class="w-1.5 h-5 bg-[#FCD116]"></span>
                    <h2 class="text-white font-bold tracking-widest uppercase text-sm">Announcements Board</h2>
                </div>
                <div class="p-8">
                    <form action="{{ route('announcements.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="max-w-5xl space-y-5">
                            <div class="flex flex-col md:flex-row gap-6">
                                <div class="flex items-center gap-4 flex-1">
                                    <label class="font-bold text-[#800000] w-20 text-sm">Target:</label>
                                    <select name="office"
                                        @mousedown="!isLoggedIn && ($event.preventDefault(), triggerAuthNotice())"
                                        class="flex-grow bg-gray-100 border-none rounded-md px-4 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400" required>
                                        <option value="" disabled selected>Select Office...</option>
                                        <option value="All Offices">All Offices</option>
                                        <option value="ARCDO">ARCDO</option>
                                        <option value="OCPS">OCPS</option>
                                        <option value="OSFA">OSFA</option>
                                        <option value="OSS">OSS</option>
                                        <option value="OUR">OUR</option>
                                        <option value="SDPO">SDPO</option>
                                        <option value="UCCA">UCCA</option>
                                    </select>
                                </div>

                                <div class="flex items-center gap-4 flex-1">
                                    <label class="font-bold text-[#800000] w-20 text-sm">Category:</label>
                                    <select name="category"
                                        @mousedown="!isLoggedIn && ($event.preventDefault(), triggerAuthNotice())"
                                        class="flex-grow bg-gray-100 border-none rounded-md px-4 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400" required>
                                        <option value="" disabled selected>Select Category...</option>
                                        <option value="Memorandums">Memorandums (Memos)</option>
                                        <option value="Executive Orders">Executive Orders (EOs)</option>
                                        <option value="Reports">Reports</option>
                                        <option value="Minutes of Meeting">Minutes of Meeting</option>
                                        <option value="Activity Proposals">Activity Proposals</option>
                                        <option value="Letters">Letters / Correspondence</option>
                                        <option value="Financials">Financials & Budget</option>
                                        <option value="Forms">Forms & Templates</option>
                                        <option value="Policies">Policies & Guidelines</option>
                                        <option value="MOAs">MOAs / MOUs</option>
                                        <option value="Masterlists">Masterlists</option>
                                        <option value="Event Material">Event Material</option>
                                        <option value="Others">Others</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <label class="font-bold text-[#800000] w-20 text-sm">Title:</label>
                                <input type="text" name="title" placeholder="Enter Details..."
                                    @mousedown="!isLoggedIn && ($event.preventDefault(), triggerAuthNotice())"
                                    class="flex-grow bg-gray-100 border-none rounded-md px-4 py-2.5 italic text-sm focus:ring-2 focus:ring-yellow-400" required>
                            </div>

                            <div class="flex items-start gap-4">
                                <label class="font-bold text-[#800000] w-20 pt-2 text-sm">Content:</label>
                                <textarea name="content" rows="3" placeholder="Enter Details..."
                                    @mousedown="!isLoggedIn && ($event.preventDefault(), triggerAuthNotice())"
                                    class="flex-grow bg-gray-100 border-none rounded-md px-4 py-2.5 italic text-sm focus:ring-2 focus:ring-yellow-400 w-full"></textarea>
                            </div>

                            <div class="flex flex-col md:flex-row items-center justify-between gap-4 pl-0 md:pl-24" x-data="{ fileName: '' }">
                                <div class="flex items-center gap-3 w-full">
                                    <label @click="!isLoggedIn && ($event.preventDefault(), triggerAuthNotice())"
                                           class="cursor-pointer bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-xs font-bold transition flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                        </svg>
                                        <span x-text="fileName ? fileName : 'Upload File'">Upload File</span>
                                        <input type="file" name="attachment" class="hidden" @change="if($event.target.files[0]) fileName = $event.target.files[0].name;">
                                    </label>
                                    <template x-if="fileName"><span class="text-[10px] text-green-600 font-bold italic">Selected: <span x-text="fileName"></span></span></template>
                                </div>
                                <button type="submit" @click="!isLoggedIn && ($event.preventDefault(), triggerAuthNotice())" class="bg-[#4D0000] text-white font-bold px-12 py-3 rounded-lg hover:bg-[#800000] transition shadow-md uppercase text-xs tracking-widest whitespace-nowrap">Publish</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white shadow-lg sm:rounded-lg border-l-8 border-[#800000]">
                <div class="bg-[#800000] py-3 px-6 flex items-center gap-3">
                    <span class="w-1.5 h-5 bg-[#FCD116]"></span>
                    <h2 class="text-white font-bold tracking-widest uppercase text-sm">Quick Access</h2>
                </div>
                <div class="p-8">
                    <div class="grid grid-cols-4 md:grid-cols-7 gap-6">
                        @foreach(['ARCDO', 'OCPS', 'OSFA', 'OSS', 'OUR', 'SDPO', 'UCCA'] as $office)
                        <a href="{{ route('offices.show', $office) }}"
                           @click="!isLoggedIn && ($event.preventDefault(), triggerAuthNotice())"
                           class="flex flex-col items-center group cursor-pointer">
                            <div class="w-12 h-10 border-2 border-[#800000] rounded-md flex items-center justify-center bg-white group-hover:bg-[#800000] transition-all shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#800000] group-hover:text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>
                                </svg>
                            </div>
                            <span class="text-[9px] font-black mt-2 text-gray-800 text-center uppercase tracking-tighter">{{ $office }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-10">

                <div class="bg-white shadow-lg sm:rounded-lg border-l-8 border-[#800000] relative overflow-hidden">
                    <div class="bg-[#800000] py-3 px-6 flex items-center gap-3">
                        <span class="w-1.5 h-5 bg-[#FCD116]"></span>
                        <h2 class="text-white font-bold tracking-widest uppercase text-[10px]">Memorandums</h2>
                    </div>
                    <div class="p-6 overflow-y-auto max-h-[300px] transition-all duration-300" :class="!isLoggedIn ? 'blur-[4px] select-none pointer-events-none opacity-40' : ''">
                        <div class="space-y-3">
                            @php $memoCount = 0; @endphp
                            @foreach($announcements as $announcement)
                                @if($announcement->category == 'Memorandums' && $announcement->file_path)
                                    @php $memoCount++; @endphp
                                    <a href="{{ asset('storage/' . $announcement->file_path) }}" target="_blank" class="flex items-center gap-3 text-sm text-gray-700 hover:text-[#800000] transition-colors group">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#800000] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        <span class="truncate block text-[11px] font-bold uppercase" title="{{ $announcement->title }}">
                                            {{ $announcement->title }}
                                        </span>
                                    </a>
                                @endif
                            @endforeach
                            @if($memoCount == 0)
                                <p class="text-[10px] text-gray-400 italic">No memos found.</p>
                            @endif
                        </div>
                    </div>
                    <div x-show="!isLoggedIn" @click="triggerAuthNotice()" class="absolute inset-0 top-11 bg-white/20 cursor-pointer flex flex-col items-center justify-center z-20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#800000] opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span class="text-[8px] font-black text-[#800000] mt-2 uppercase tracking-widest">Login to Access</span>
                    </div>
                </div>

                <div class="bg-white shadow-lg sm:rounded-lg border-l-8 border-[#800000] relative overflow-hidden">
                    <div class="bg-[#800000] py-3 px-6 flex items-center gap-3">
                        <span class="w-1.5 h-5 bg-[#FCD116]"></span>
                        <h2 class="text-white font-bold tracking-widest uppercase text-[10px]">Executive Orders</h2>
                    </div>
                    <div class="p-6 overflow-y-auto max-h-[300px] transition-all duration-300" :class="!isLoggedIn ? 'blur-[4px] select-none pointer-events-none opacity-40' : ''">
                        <div class="space-y-3">
                            @php $eoCount = 0; @endphp
                            @foreach($announcements as $announcement)
                                @if($announcement->category == 'Executive Orders' && $announcement->file_path)
                                    @php $eoCount++; @endphp
                                    <a href="{{ asset('storage/' . $announcement->file_path) }}" target="_blank" class="flex items-center gap-3 text-sm text-gray-700 hover:text-[#800000] transition-colors group">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#800000] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        <span class="truncate block text-[11px] font-bold uppercase" title="{{ $announcement->title }}">
                                            {{ $announcement->title }}
                                        </span>
                                    </a>
                                @endif
                            @endforeach
                            @if($eoCount == 0)
                                <p class="text-[10px] text-gray-400 italic">No EOs found.</p>
                            @endif
                        </div>
                    </div>
                    <div x-show="!isLoggedIn" @click="triggerAuthNotice()" class="absolute inset-0 top-11 bg-white/20 cursor-pointer flex flex-col items-center justify-center z-20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#800000] opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span class="text-[8px] font-black text-[#800000] mt-2 uppercase tracking-widest">Login to Access</span>
                    </div>
                </div>

                <div class="bg-white shadow-lg sm:rounded-lg border-l-8 border-[#800000] relative overflow-hidden">
                    <div class="bg-[#800000] py-3 px-6 flex items-center gap-3">
                        <span class="w-1.5 h-5 bg-[#FCD116]"></span>
                        <h2 class="text-white font-bold tracking-widest uppercase text-[10px]">Recent Activity</h2>
                    </div>
                    <div class="p-6 overflow-y-auto max-h-[300px] transition-all duration-300" :class="!isLoggedIn ? 'blur-[4px] select-none pointer-events-none opacity-40' : ''">
                        <div class="space-y-3">
                            @forelse($announcements->take(10) as $announcement)
                            <div class="flex justify-between items-center text-[10px] text-gray-600 gap-2 border-b border-gray-50 pb-2 last:border-0">
                                <div class="truncate flex flex-col min-w-0">
                                    <span class="font-bold text-[#800000] uppercase text-[8px]">{{ $announcement->office }}</span>
                                    <span class="truncate italic uppercase font-bold text-gray-800">{{ $announcement->title }}</span>
                                    <span class="text-[7px] text-gray-400 font-medium">{{ $announcement->created_at->diffForHumans() }}</span>
                                </div>
                                @if($announcement->file_path)
                                    <a href="{{ asset('storage/' . $announcement->file_path) }}" target="_blank" class="bg-[#4D0000] text-white px-3 py-1 rounded text-[8px] hover:bg-red-800 transition uppercase shrink-0 font-bold">VIEW</a>
                                @endif
                            </div>
                            @empty
                                <p class="text-[10px] text-gray-400 italic">No activity yet.</p>
                            @endforelse

                            @if($announcements->count() > 10)
                                <a href="{{ route('activity.log') }}" @click="!isLoggedIn && ($event.preventDefault(), triggerAuthNotice())" class="w-full block text-center mt-4 py-1.5 bg-gray-100 text-[8px] font-black uppercase text-gray-500 rounded hover:bg-gray-200 transition tracking-widest">
                                    View All Activity
                                </a>
                            @endif
                        </div>
                    </div>
                    <div x-show="!isLoggedIn" @click="triggerAuthNotice()" class="absolute inset-0 top-11 bg-white/20 cursor-pointer flex flex-col items-center justify-center z-20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#800000] opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span class="text-[8px] font-black text-[#800000] mt-2 uppercase tracking-widest">Login to Access</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-6 text-center text-xs mt-auto">
        &copy; {{ date('Y') }} OVPSAS. Polytechnic University of the Philippines.
    </footer>
</body>
</html>