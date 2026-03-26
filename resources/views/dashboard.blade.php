@extends('layouts.master')

@section('title', 'Dashboard - OVPSAS')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @php
        /** * 1. LOGICAL DATA PARTITIONING */
        
        // UPCOMING EVENTS: Anything with a scheduled date today or in the future
        $upcomingEvents = $announcements->filter(function($item) {
            return !empty($item->scheduled_date) && 
                   $item->scheduled_date->isAfter(now()->startOfDay());
        })->sortBy('scheduled_date');

        // FEED ITEMS: General announcements that aren't future events
        $feedItems = $announcements->filter(function($item) use ($upcomingEvents) {
            return !$upcomingEvents->contains('id', $item->id);
        });

        /** * 2. ANALYTICS & STATS */
        $repositoryFiles = $announcements->filter(fn($item) => !empty($item->file_path));
        $totalActualFiles = $repositoryFiles->count();
        $filesThisMonthCount = $announcements->filter(fn($item) => $item->created_at->isCurrentMonth())->count();

        // Filters for Charts and Stats (Excluding general categories)
        $filteredCategoryData = $categoryData->filter(fn($c) => !in_array(strtolower($c->category), ['events', 'event']));
        $filteredOfficeData = $officeData->filter(fn($o) => !in_array(strtolower($o->office), ['general', 'all offices', 'events']));

        $mostActiveOffice = $filteredOfficeData->sortByDesc('total')->first()->office ?? 'N/A';
    @endphp

    <div class="max-w-7xl mx-auto px-6 lg:px-8 space-y-8 py-8">

        {{-- TOP STAT CARDS --}}
        <div class="bg-white p-8 rounded-xl shadow-md border-t-4 border-[#800000]">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 uppercase tracking-tight">System Administrative Report</h2>
                    <p class="text-gray-500 italic text-sm">Real-time analytics for monitored university offices.</p>
                </div>
                <a href="{{ route('reports.download') }}" class="bg-[#800000] text-white px-6 py-2 rounded-lg font-bold hover:bg-red-900 transition shadow-lg flex items-center gap-2 text-sm uppercase">
                    GENERATE PDF REPORT
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-red-50 p-6 rounded-xl border border-red-100 text-center">
                    <p class="text-[10px] font-bold text-red-700 uppercase tracking-widest">Total Files</p>
                    <h3 class="text-3xl font-black text-gray-800">{{ $totalActualFiles }}</h3>
                </div>
                <div class="bg-gray-50 p-6 rounded-xl border border-gray-100 text-center">
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Monitored Offices</p>
                    <h3 class="text-3xl font-black text-gray-800">{{ $filteredOfficeData->count() }}</h3>
                </div>
                <div class="bg-green-50 p-6 rounded-xl border border-green-100 text-center">
                    <p class="text-[10px] font-bold text-green-700 uppercase tracking-widest">Uploaded This Month</p>
                    <h3 class="text-3xl font-black text-green-600">{{ $filesThisMonthCount }}</h3>
                </div>
                <div class="bg-blue-50 p-6 rounded-xl border border-blue-100 text-center">
                    <p class="text-[10px] font-bold text-blue-700 uppercase tracking-widest">Most Active Office</p>
                    <h3 class="text-xl font-black text-blue-600 truncate px-2">{{ $mostActiveOffice }}</h3>
                </div>
            </div>
        </div>

        {{-- CHARTS --}}
        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-white p-5 shadow-md rounded-2xl border-t-4 border-yellow-400 h-[300px]">
                <h3 class="font-black text-gray-700 mb-4 text-[10px] uppercase tracking-widest text-center">Category Distribution</h3>
                <div class="relative h-48"><canvas id="categoryChart"></canvas></div>
            </div>
            <div class="bg-white p-5 shadow-md rounded-2xl border-t-4 border-[#800000] h-[300px]">
                <h3 class="font-black text-gray-700 mb-4 text-[10px] uppercase tracking-widest text-center">Office Uploads</h3>
                <div class="relative h-48"><canvas id="officeChart"></canvas></div>
            </div>
        </div>

        <div class="grid lg:grid-cols-5 gap-8">
            {{-- FEED AREA --}}
            <div class="lg:col-span-3 space-y-6">
                <div class="flex items-center gap-2 border-b-2 border-gray-800 pb-1">
                    <span class="w-2 h-6 bg-[#800000]"></span>
                    <h2 class="text-xl font-black text-[#800000] tracking-tight uppercase italic">Announcement Feed</h2>
                </div>

                @forelse($feedItems as $announcement)
                    <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden" x-data="{ openComment: false }">
                        <div class="p-4 flex items-center justify-between border-b bg-gray-50/50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-[#800000] flex items-center justify-center text-white font-bold text-xs uppercase">{{ substr($announcement->user->name ?? '?', 0, 1) }}</div>
                                <div>
                                    <p class="text-xs font-black text-gray-900 uppercase">{{ $announcement->user->name ?? 'Admin' }}</p>
                                    <p class="text-[9px] text-gray-400 font-bold uppercase tracking-tighter">
                                        {{ is_array($announcement->office) ? implode(', ', $announcement->office) : $announcement->office }} • {{ $announcement->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>

                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-[8px] font-black uppercase">
                                @php
                                    $cats = is_array($announcement->category) ? $announcement->category : [$announcement->category];
                                    $displayCats = array_map(function($cat) use ($announcement) {
                                        return (trim($cat) === 'Others' && !empty($announcement->custom_category))
                                            ? $announcement->custom_category : $cat;
                                    }, $cats);
                                @endphp
                                {{ implode(', ', $displayCats) }}
                            </span>
                        </div>
                        
                        <div class="p-5">
                            {{-- NEW: DATE BADGE IN FEED --}}
                            @if($announcement->scheduled_date)
                                <div class="inline-flex items-center gap-2 px-3 py-1 bg-blue-50 text-blue-700 rounded-md text-[9px] font-black uppercase mb-3 border border-blue-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <span>Event Date: {{ $announcement->scheduled_date->format('M d, Y') }}</span>
                                </div>
                            @endif

                            <h4 class="font-black text-sm text-[#800000] uppercase mb-2">{{ $announcement->title }}</h4>
                            <p class="text-xs text-gray-600 italic leading-relaxed">{{ $announcement->content }}</p>
                            
                            @if($announcement->file_path)
                                <div class="mt-4 p-3 bg-gray-100 rounded-lg flex items-center justify-between border-l-4 border-[#800000]">
                                    <span class="text-[10px] font-bold text-gray-500 truncate">{{ basename($announcement->file_path) }}</span>
                                    <a href="{{ route('file.view', $announcement->id) }}" target="_blank" class="text-[9px] bg-[#800000] text-white px-3 py-1 rounded-full font-black uppercase">View File</a>
                                </div>
                            @endif
                        </div>

                        <div class="px-5 py-3 border-t bg-gray-50/30">
                            <button @click="openComment = !openComment" class="flex items-center gap-1.5 text-gray-500 hover:text-[#800000] transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                <span class="text-[10px] font-black uppercase tracking-widest">Comments ({{ $announcement->comments->count() }})</span>
                            </button>

                            <div x-show="openComment" x-cloak x-transition class="mt-4 space-y-4">
                                @foreach($announcement->comments as $comment)
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-full bg-[#800000]/10 flex items-center justify-center text-[10px] font-black text-[#800000] border border-[#800000]/20 uppercase">{{ substr($comment->user->name ?? '?', 0, 1) }}</div>
                                        <div class="flex-1 bg-white p-2.5 rounded-xl border border-gray-100 shadow-sm">
                                            <div class="flex justify-between items-center mb-1">
                                                <p class="text-[9px] font-black text-[#800000] uppercase">{{ $comment->user->name ?? 'User' }}</p>
                                                <p class="text-[8px] text-gray-400 font-bold uppercase">{{ $comment->created_at->diffForHumans() }}</p>
                                            </div>
                                            <p class="text-[11px] text-gray-700 leading-snug">{{ $comment->comment_text }}</p>
                                        </div>
                                    </div>
                                @endforeach
                                <form action="{{ route('comments.store', $announcement->id) }}" method="POST" class="flex items-start gap-3 mt-4 border-t pt-4">
                                    @csrf
                                    <textarea name="comment_text" placeholder="Write a comment..." required class="flex-1 p-3 text-xs border rounded-xl bg-white outline-none focus:ring-1 focus:ring-[#800000] min-h-[60px] italic"></textarea>
                                    <button type="submit" class="bg-[#800000] text-white px-4 py-2 rounded-lg text-[9px] font-black uppercase shadow-sm">Post</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center py-20 italic text-gray-400 text-sm uppercase font-bold tracking-widest">No active announcements.</p>
                @endforelse
            </div>

            {{-- SIDEBAR AREA --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- NEW: UPCOMING EVENTS CALENDAR SIDEBAR --}}
                <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 border-t-4 border-blue-600">
                    <div class="p-4 bg-gray-50 flex items-center justify-between border-b">
                        <span class="text-[#1a202c] uppercase text-[10px] font-black tracking-widest">Upcoming Events Calendar</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                    <div class="p-4 space-y-4 max-h-[400px] overflow-y-auto">
                        @forelse($upcomingEvents as $event)
                            <div class="flex items-center gap-4 bg-blue-50/50 p-3 rounded-lg border border-blue-100 hover:bg-blue-100 transition cursor-default">
                                <div class="flex flex-col items-center justify-center bg-white border-2 border-blue-600 rounded-lg w-14 h-14 shrink-0 shadow-sm">
                                    <span class="text-[9px] font-black text-blue-600 uppercase">{{ $event->scheduled_date->format('M') }}</span>
                                    <span class="text-lg font-black text-gray-800 leading-none">{{ $event->scheduled_date->format('d') }}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] font-black text-gray-800 uppercase truncate" title="{{ $event->title }}">{{ $event->title }}</p>
                                    <p class="text-[8px] font-bold text-blue-700 uppercase italic">{{ is_array($event->office) ? implode(', ', $event->office) : $event->office }}</p>
                                    <p class="text-[8px] font-medium text-gray-500 mt-1">
                                        @php $days = now()->startOfDay()->diffInDays($event->scheduled_date); @endphp
                                        @if($days == 0) <span class="text-red-600 font-black animate-pulse">HAPPENING TODAY</span> 
                                        @else In {{ $days }} {{ Str::plural('day', $days) }} @endif
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="py-10 text-center">
                                <p class="text-[10px] text-gray-400 italic font-bold uppercase tracking-widest">No upcoming events scheduled.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- REPOSITORY --}}
                <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200">
                    <div class="p-4 bg-gray-800 text-white uppercase text-[10px] font-black tracking-widest">File Repository</div>
                    <div class="overflow-y-auto max-h-[300px]">
                        <table class="w-full text-left text-[11px]">
                            <tbody class="divide-y divide-gray-100">
                                @forelse($repositoryFiles->take(15) as $file)
                                    <tr class="hover:bg-red-50 group transition">
                                        <td class="px-4 py-3">
                                            <span class="font-black text-gray-800 uppercase leading-tight">{{ $file->title }}</span>
                                            <br>
                                            <span class="text-[9px] text-[#800000] font-bold uppercase italic">
                                                @php
                                                    $fCats = is_array($file->category) ? $file->category : [$file->category];
                                                    $fDisplay = array_map(function($c) use ($file) {
                                                        return (trim($c) === 'Others' && !empty($file->custom_category))
                                                            ? $file->custom_category : $c;
                                                    }, $fCats);
                                                @endphp
                                                {{ implode(', ', $fDisplay) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('file.view', $file->id) }}" target="_blank" class="text-[#800000] opacity-30 group-hover:opacity-100 transition hover:scale-110 inline-block">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="px-4 py-10 text-center text-gray-400 italic uppercase">No files available.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const opts = { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } };
        new Chart(document.getElementById('categoryChart'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($filteredCategoryData->pluck("category")) !!},
                datasets: [{ data: {!! json_encode($filteredCategoryData->pluck("total")) !!}, backgroundColor: ['#800000', '#FCD116', '#1a1a1a', '#A8A8A8'], borderWidth: 0 }]
            },
            options: { ...opts, cutout: '75%' }
        });
        new Chart(document.getElementById('officeChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($filteredOfficeData->pluck("office")) !!},
                datasets: [{ data: {!! json_encode($filteredOfficeData->pluck("total")) !!}, backgroundColor: '#800000', borderRadius: 5 }]
            },
            options: { ...opts, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
        });
    </script>
    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
@endsection