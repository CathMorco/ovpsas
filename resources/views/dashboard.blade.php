<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - OVPSAS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased flex flex-col min-h-screen"
      x-data="{ isLoggedIn: {{ Auth::check() ? 'true' : 'false' }} }">

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
                    <a href="{{ url('/') }}" class="text-white font-bold hover:text-yellow-300 transition text-sm">Home</a>
                    <a href="{{ route('dashboard') }}" class="text-white font-bold transition text-sm border-b-2 border-yellow-400 pb-1">Dashboard</a>
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
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 text-base font-medium text-white hover:bg-red-800">Dashboard</a>
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
        <div class="max-w-7xl mx-auto px-6 lg:px-8 space-y-12">

            <div class="grid md:grid-cols-2 gap-8">
                <div class="bg-white p-6 shadow-xl sm:rounded-lg border-t-4 border-[#800000]">
                    <div class="mb-4">
                        <h3 class="font-bold text-gray-800">Lorem Ipsum</h3>
                        <p class="text-xs text-gray-500 italic">Lorem Ipsum</p>
                    </div>
                    <div class="h-48 bg-gray-50 rounded flex flex-col items-center justify-center border border-gray-100">
                        <p class="text-[10px] font-bold mb-2 uppercase text-gray-400 italic">Sample Line Chart</p>
                        <div class="w-full px-10">
                            <svg viewBox="0 0 100 40" class="w-full h-32">
                                <path d="M0 40 L25 30 L50 35 L75 10 L100 20 L100 40 Z" fill="#4D0000" />
                                <path d="M0 40 L25 30 L50 35 L75 10 L100 20" fill="none" stroke="#800000" stroke-width="1" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 shadow-xl sm:rounded-lg border-t-4 border-[#800000]">
                    <div class="mb-4">
                        <h3 class="font-bold text-gray-800">Lorem Ipsum</h3>
                    </div>
                    <div class="h-48 bg-gray-50 rounded flex flex-col items-center justify-center border border-gray-100">
                        <p class="text-[10px] font-bold mb-2 uppercase text-gray-400 italic">Sample Pie Chart</p>
                        <div class="w-24 h-24 rounded-full border-8 border-[#4D0000]" style="border-left-color: #FCD116; border-bottom-color: black;"></div>
                        <div class="flex gap-4 mt-4">
                            <span class="w-3 h-1.5 bg-[#FCD116]"></span>
                            <span class="w-3 h-1.5 bg-[#4D0000]"></span>
                            <span class="w-3 h-1.5 bg-black"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <h2 class="text-2xl font-black text-[#800000] tracking-tight uppercase italic border-b-2 border-gray-800 pb-1">Folder</h2>
                <div class="flex items-center gap-4 text-sm font-bold text-gray-700">
                    <span>Filter by</span>
                    <select class="bg-[#E5E7EB] border-none rounded-md px-10 py-2 focus:ring-2 focus:ring-yellow-400 italic text-xs">
                        <option>Coverage</option>
                    </select>
                </div>

                <div class="bg-white shadow-lg sm:rounded-lg overflow-hidden border-l-8 border-[#800000]">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b-2 border-gray-100">
                            <tr class="text-gray-800 font-black italic">
                                <th class="px-6 py-4">Title</th>
                                <th class="px-6 py-4">Description</th>
                                <th class="px-6 py-4">Coverage</th>
                                <th class="px-6 py-4 text-center">File</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @php
                                $rows = [
                                    ['Number of Higher Education Institutions...', 'Total number of HEIs from ASEAN...', '2024-2025', 'XLS (1 KB)'],
                                    ['Higher Education Institution by Region...', 'Total number of HEIs in the PH...', '2023-2024', 'PDF (1 KB)'],
                                    ['Higher Education Enrollment...', 'Total Enrolment of all HEIs...', '2021-2023', 'DOCX (1 KB)']
                                ];
                            @endphp
                            @foreach($rows as $row)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-medium text-xs leading-tight w-1/4">{{ $row[0] }}</td>
                                <td class="px-6 py-4 text-[10px] text-gray-500 w-1/3">{{ $row[1] }}</td>
                                <td class="px-6 py-4 text-xs font-bold">{{ $row[2] }}</td>
                                <td class="px-6 py-4 text-center">
                                    <button class="bg-[#4D0000] text-white text-[9px] font-bold px-8 py-2 rounded-full hover:bg-red-900 transition uppercase shadow-sm">
                                        {{ $row[3] }}
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid gap-10">
                <div class="space-y-4">
                    <div class="flex justify-between items-end border-b-2 border-gray-800 pb-1">
                        <h2 class="text-xl font-black text-[#800000] tracking-tight uppercase italic">Create New File</h2>
                        <span class="text-[10px] font-bold text-gray-500 italic">Already have a file? <a href="#" class="text-[#800000] underline">Import File</a></span>
                    </div>
                    <div class="bg-white p-8 shadow-lg sm:rounded-lg border-l-8 border-[#800000] space-y-6">
                        <div class="flex items-center gap-4">
                            <div class="flex-grow">
                                <input type="text" placeholder="File Title" class="w-full bg-[#E5E7EB] border-none rounded-md px-4 py-3 text-sm italic focus:ring-2 focus:ring-yellow-400">
                                <p class="text-[9px] text-red-600 italic mt-1 font-bold">* This field is required</p>
                            </div>
                            <button class="bg-[#4D0000] text-white font-bold px-12 py-3 rounded-lg hover:bg-red-900 shadow-md uppercase text-xs tracking-widest h-11">Upload</button>
                        </div>
                        <div>
                            <textarea rows="4" placeholder="Body Content" class="w-full bg-[#E5E7EB] border-none rounded-md px-4 py-3 text-sm italic focus:ring-2 focus:ring-yellow-400"></textarea>
                            <p class="text-[9px] text-red-600 italic mt-1 font-bold">* This field is required</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <h2 class="text-xl font-black text-[#800000] tracking-tight uppercase italic border-b-2 border-gray-800 pb-1">Import File</h2>
                    <div class="bg-white p-8 shadow-lg sm:rounded-lg border-l-8 border-[#800000]">
                        <div class="flex items-center gap-4">
                            <div class="flex-grow">
                                <div class="bg-[#E5E7EB] rounded-md px-4 py-3 text-sm italic text-gray-500 border-2 border-dashed border-gray-300">
                                    Upload file (Max 1000 MB)
                                </div>
                                <p class="text-[9px] text-red-600 italic mt-1 font-bold">* This field is required</p>
                            </div>
                            <button class="bg-[#4D0000] text-white font-bold px-12 py-3 rounded-lg hover:bg-red-900 shadow-md uppercase text-xs tracking-widest h-11">Import</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <footer class="bg-gray-800 text-white py-6 text-center text-xs">
        &copy; {{ date('Y') }} OVPSAS. Polytechnic University of the Philippines.
    </footer>

</body>
</html>