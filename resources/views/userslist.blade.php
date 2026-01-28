<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User List - OVPSAS</title>
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
                    <a href="{{ route('dashboard') }}" class="text-white font-bold hover:text-yellow-300 transition text-sm">Dashboard</a>
                    <a href="{{ url('/about') }}" class="text-white font-bold hover:text-yellow-300 transition text-sm">About Us</a>
                </div>

                <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">
                    @auth
                        <div class="relative">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-bold rounded-md text-white hover:text-yellow-300 focus:outline-none transition">
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
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow py-12">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="space-y-6">
                <div class="border-b-2 border-gray-800 pb-2">
                    <h2 class="text-2xl font-black text-[#800000] tracking-tight uppercase italic">User Directory</h2>
                </div>

                <form action="{{ url('/userslist') }}" method="GET" class="bg-white p-6 shadow-md rounded-lg border-t-4 border-[#800000]">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Search Name</label>
                            <input type="text" name="search" placeholder="Type name..." 
                                   class="w-full bg-[#E5E7EB] border-none rounded-md px-4 py-2 text-sm italic focus:ring-2 focus:ring-yellow-400">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Office</label>
                            <select name="office" class="w-full bg-[#E5E7EB] border-none rounded-md px-4 py-2 text-sm italic focus:ring-2 focus:ring-yellow-400">
                                <option value="">All Offices</option>
                                <option value="ARCDO">ARCDO</option>
                                <option value="OCPS">OCPS</option>
                                <option value="OSFA">OSFA</option>
                                <option value="OSS">OSS</option>
                                <option value="OUR">OUR</option>
                                <option value="SDPO">SDPO</option>
                                <option value="UCCA">UCCA</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Role</label>
                            <select name="role" class="w-full bg-[#E5E7EB] border-none rounded-md px-4 py-2 text-sm italic focus:ring-2 focus:ring-yellow-400">
                                <option value="">All Roles</option>
                                <option value="admin">Admin</option>
                                <option value="staff">Staff</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Status</label>
                            <select name="status" class="w-full bg-[#E5E7EB] border-none rounded-md px-4 py-2 text-sm italic focus:ring-2 focus:ring-yellow-400">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-[#800000] text-white font-bold py-2 rounded-lg hover:bg-red-900 shadow-md uppercase text-[10px] tracking-widest transition">
                                Apply Filters
                            </button>
                        </div>
                    </div>
                </form>

                <div class="bg-white shadow-xl sm:rounded-lg overflow-hidden border-l-8 border-[#800000]">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b-2 border-gray-100 bg-gray-50">
                            <tr class="text-gray-800 font-black italic uppercase text-xs">
                                <th class="px-6 py-4">Name</th>
                                <th class="px-6 py-4">Email</th>
                                <th class="px-6 py-4">Role</th>
                                <th class="px-6 py-4">Office</th> <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-bold text-[#800000]">Sample Administrator</td>
                                <td class="px-6 py-4 text-gray-500 italic lowercase text-xs">admin@ovpsas.edu.ph</td>
                                <td class="px-6 py-4 text-xs font-bold">Admin</td>
                                <td class="px-6 py-4 text-xs font-black text-gray-700">ARCDO</td> <td class="px-6 py-4 text-center">
                                    <span class="bg-green-100 text-green-800 text-[10px] font-bold px-3 py-1 rounded-full uppercase">Active</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('profile.edit') }}" 
                                       class="inline-block bg-[#FCD116] hover:bg-yellow-400 text-[#4D0000] text-[10px] font-bold px-4 py-1.5 rounded-md transition shadow-sm uppercase tracking-wider">
                                        Profile
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-6 text-center text-xs mt-auto">
        &copy; {{ date('Y') }} OVPSAS. Polytechnic University of the Philippines.
    </footer>
</body>
</html>