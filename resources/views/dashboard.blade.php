<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - OVPSAS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 font-sans antialiased flex flex-col min-h-screen" x-data="{ openComment: null }">

    <nav class="bg-[#800000] border-b border-red-900 sticky top-0 z-50 shadow-md">
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
                    <div class="relative hidden lg:block w-64">
                        <input type="text" placeholder="Search..." class="w-full bg-white text-gray-800 rounded-full px-4 py-1.5 pl-4 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        <button class="absolute right-1 top-1/2 transform -translate-y-1/2 bg-[#FCD116] p-1 rounded-full hover:bg-yellow-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </button>
                    </div>

                    @auth
                        <div class="ms-3 relative">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-bold rounded-md text-white hover:text-yellow-300 focus:outline-none transition">
                                        <div>{{ Auth::user()->name }}</div>
                                        <div class="ms-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                        </div>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
                                    <form method="POST" action="{{ route('logout') }}">@csrf<x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Log Out</x-dropdown-link></form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow py-8">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 space-y-10">

            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-white p-6 shadow-md rounded-2xl border-l-8 border-[#800000] flex flex-col justify-center min-h-[180px]">
                    <span class="text-gray-400 uppercase text-[10px] font-black tracking-widest text-center md:text-left">Total Uploads</span>
                    <h2 class="text-5xl font-black text-gray-800 leading-none mt-2 text-center md:text-left">{{ $announcements->count() }}</h2>
                    <p class="text-[10px] font-bold text-[#800000] mt-2 uppercase tracking-tight text-center md:text-left">Active System Documents</p>
                </div>
                <div class="bg-white p-5 shadow-md rounded-2xl border-t-4 border-yellow-400 flex flex-col h-[220px]">
                    <h3 class="font-black text-gray-700 mb-4 text-[10px] uppercase tracking-widest text-center">Category Distribution</h3>
                    <div class="flex-grow flex items-center justify-center relative overflow-hidden">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
                <div class="bg-white p-5 shadow-md rounded-2xl border-t-4 border-[#800000] flex flex-col h-[220px]">
                    <h3 class="font-black text-gray-700 mb-4 text-[10px] uppercase tracking-widest text-center">Office Uploads</h3>
                    <div class="flex-grow flex items-center justify-center relative overflow-hidden">
                        <canvas id="officeChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="grid lg:grid-cols-5 gap-8">
                <div class="lg:col-span-3 space-y-6">
                    <div class="flex items-center gap-2 border-b-2 border-gray-800 pb-1">
                        <span class="w-2 h-6 bg-[#800000]"></span>
                        <h2 class="text-xl font-black text-[#800000] tracking-tight uppercase italic">Announcement Feed</h2>
                    </div>

                    @forelse($announcements as $announcement)
                    <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition">
                        <div class="p-4 flex items-center justify-between border-b bg-gray-50/50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-[#800000] flex items-center justify-center text-white font-bold text-xs shadow-inner uppercase">
                                    {{ substr($announcement->user->name ?? $announcement->office, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-xs font-black text-gray-900 uppercase tracking-tighter">{{ $announcement->user->name ?? 'Admin' }}</p>
                                    <p class="text-[9px] text-gray-400 font-bold uppercase">{{ $announcement->office }} • {{ $announcement->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-[8px] font-black uppercase">{{ $announcement->category }}</span>
                        </div>

                        <div class="p-5">
                            <h4 class="font-black text-sm text-[#800000] uppercase mb-2">{{ $announcement->title }}</h4>
                            <p class="text-xs text-gray-600 leading-relaxed italic">{{ $announcement->content }}</p>

                            @if($announcement->file_path)
                            <div class="mt-4 p-3 bg-gray-100 rounded-lg flex items-center justify-between border-l-4 border-[#800000]">
                                <div class="flex items-center gap-2 overflow-hidden">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#800000]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                    <span class="text-[10px] font-bold text-gray-500 truncate">{{ basename($announcement->file_path) }}</span>
                                </div>
                                <a href="{{ asset('storage/' . $announcement->file_path) }}" target="_blank" class="text-[9px] bg-[#800000] text-white px-3 py-1 rounded-full font-black uppercase hover:bg-red-800 transition shadow-sm">View</a>
                            </div>
                            @endif
                        </div>

                        <div class="px-5 py-3 border-t bg-white flex gap-6">
                            <button @click="openComment === {{ $announcement->id }} ? openComment = null : openComment = {{ $announcement->id }}" class="flex items-center gap-1.5 text-[10px] font-black text-gray-400 hover:text-[#800000] transition uppercase">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                Comment ({{ $announcement->comments->count() }})
                            </button>
                        </div>

                        <div x-show="openComment === {{ $announcement->id }}" x-collapse class="bg-gray-50 border-t border-gray-100 p-5 space-y-4">
                            <div class="space-y-3">
                                @foreach($announcement->comments as $comment)
                                <div class="flex gap-3">
                                    <div class="w-7 h-7 rounded-full bg-gray-300 flex items-center justify-center text-[10px] font-bold text-white uppercase">
                                        {{ substr($comment->user->name ?? '?', 0, 1) }}
                                    </div>
                                    <div class="bg-white p-2 rounded-lg shadow-sm border border-gray-100 flex-grow">
                                        <p class="text-[9px] font-black uppercase text-[#800000]">{{ $comment->user->name ?? 'Unknown' }}</p>
                                        <p class="text-[11px] text-gray-700">{{ $comment->comment_text }}</p>
                                        <p class="text-[8px] text-gray-400 mt-1 uppercase">{{ $comment->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <form action="{{ route('comments.store', $announcement->id) }}" method="POST" class="mt-4 flex gap-2">
                                @csrf
                                <input type="text" name="comment_text" placeholder="Write a comment..." required class="flex-grow bg-white border border-gray-200 rounded-full px-4 py-2 text-[11px] focus:ring-1 focus:ring-[#800000] outline-none">
                                <button type="submit" class="bg-[#800000] text-white px-4 py-1.5 rounded-full text-[10px] font-black uppercase hover:bg-red-800 transition">Post</button>
                            </form>
                        </div>
                    </div>
                    @empty
                        <div class="text-center py-10 bg-white rounded-xl shadow-sm border-2 border-dashed">
                            <p class="text-gray-400 italic text-sm">No recent activity to show.</p>
                        </div>
                    @endforelse
                </div>

                <div class="lg:col-span-2 space-y-6">
                    <div class="flex items-center gap-2 border-b-2 border-gray-800 pb-1">
                        <span class="w-2 h-6 bg-yellow-400"></span>
                        <h2 class="text-xl font-black text-gray-800 tracking-tight uppercase italic">File Library</h2>
                    </div>
                    <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200">
                        <div class="p-4 bg-gray-800 flex justify-between items-center">
                            <span class="text-[10px] font-black text-white uppercase tracking-widest">Master List</span>
                        </div>
                        <div class="overflow-y-auto max-h-[600px]">
                            <table class="w-full text-left text-[11px]">
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($announcements as $announcement)
                                    <tr class="hover:bg-yellow-50/50 transition-colors group">
                                        <td class="px-4 py-3">
                                            <div class="flex flex-col">
                                                <span class="font-black text-gray-800 uppercase leading-tight">{{ $announcement->title }}</span>
                                                <span class="text-[9px] text-gray-400 font-bold uppercase italic">{{ $announcement->office }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ asset('storage/' . $announcement->file_path) }}" target="_blank" class="text-[#800000] opacity-30 group-hover:opacity-100 transition-all hover:scale-125 inline-block">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-6 text-center text-[10px] uppercase font-bold tracking-widest">
        &copy; {{ date('Y') }} OVPSAS | Polytechnic University of the Philippines
    </footer>

    <script>
        const categoryLabels = JSON.parse('{!! json_encode($categoryData->pluck("category") ?? []) !!}');
        const categoryCounts = JSON.parse('{!! json_encode($categoryData->pluck("total") ?? []) !!}');
        const officeLabels = JSON.parse('{!! json_encode($officeData->pluck("office") ?? []) !!}');
        const officeCounts = JSON.parse('{!! json_encode($officeData->pluck("total") ?? []) !!}');
        const commonOptions = { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } };

        const ctxPie = document.getElementById('categoryChart');
        if (ctxPie) { new Chart(ctxPie, { type: 'doughnut', data: { labels: categoryLabels, datasets: [{ data: categoryCounts, backgroundColor: ['#800000', '#FCD116', '#1a1a1a', '#A8A8A8', '#4D0000'] }] }, options: { ...commonOptions, cutout: '65%' } }); }

        const ctxBar = document.getElementById('officeChart');
        if (ctxBar) { new Chart(ctxBar, { type: 'bar', data: { labels: officeLabels, datasets: [{ data: officeCounts, backgroundColor: '#800000' }] }, options: { ...commonOptions, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } } }); }
    </script>
</body>
</html>
