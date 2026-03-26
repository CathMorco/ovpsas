@extends('layouts.master')

@section('title', 'Dashboard - OVPSAS')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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
                    <p class="text-[10px] font-bold text-red-700 uppercase mb-1">Total Files</p>
                    <h3 class="text-3xl font-black text-gray-800">{{ $totalActualFiles }}</h3>
                </div>
                <div class="bg-gray-50 p-6 rounded-xl border border-gray-100 text-center">
                    <p class="text-[10px] font-bold text-gray-500 uppercase mb-1">Monitored Offices</p>
                    <h3 class="text-3xl font-black text-gray-800">{{ $monitoredOfficesCount }}</h3>
                </div>
                <div class="bg-green-50 p-6 rounded-xl border border-green-100 text-center">
                    <p class="text-[10px] font-bold text-green-700 uppercase mb-1">Uploaded This Month</p>
                    <h3 class="text-3xl font-black text-green-600">{{ $filesThisMonthCount }}</h3>
                </div>
                <div class="bg-blue-50 p-6 rounded-xl border border-blue-100 text-center">
                    <p class="text-[10px] font-bold text-blue-700 uppercase mb-1">Most Active Office</p>
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
                <h2 class="text-xl font-black text-[#800000] uppercase italic border-b-2 border-gray-800 pb-1">Announcement Feed</h2>

                @forelse($feedItems as $announcement)
                    <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden" x-data="{ openComment: false }">
                        <div class="p-4 flex items-center justify-between border-b bg-gray-50/50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-[#800000] flex items-center justify-center text-white font-bold text-xs uppercase">{{ substr($announcement->user->name ?? '?', 0, 1) }}</div>
                                <div>
                                    <p class="text-xs font-black text-gray-900 uppercase">{{ $announcement->user->name ?? 'Admin' }}</p>
                                    <p class="text-[9px] text-gray-400 font-bold uppercase">
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
                            {{-- DATE BADGE: Fixes your TEST 3/4 visibility --}}
                            @if($announcement->scheduled_date)
                                <div class="inline-flex items-center gap-2 px-3 py-1 bg-blue-50 text-blue-700 rounded-md text-[9px] font-black uppercase mb-3 border border-blue-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <span>Event Date: {{ \Carbon\Carbon::parse($announcement->scheduled_date)->format('M d, Y') }}</span>
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
                    </div>
                @empty
                    <p class="text-center py-20 italic text-gray-400 text-sm uppercase">No active announcements.</p>
                @endforelse
            </div>

            {{-- SIDEBAR AREA --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- UPCOMING EVENTS CALENDAR --}}
                <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 border-t-4 border-blue-600">
                    <div class="p-4 bg-gray-50 font-black text-[10px] uppercase tracking-widest border-b">Upcoming Events Calendar</div>
                    <div class="p-4 space-y-4 max-h-[400px] overflow-y-auto">
                        @forelse($upcomingEvents as $event)
                            <div class="flex items-center gap-4 bg-blue-50/50 p-3 rounded-lg border border-blue-100 hover:bg-blue-100 transition cursor-default">
                                <div class="flex flex-col items-center justify-center bg-white border-2 border-blue-600 rounded-lg w-14 h-14 shrink-0 shadow-sm">
                                    <span class="text-[9px] font-black text-blue-600 uppercase">{{ \Carbon\Carbon::parse($event->scheduled_date)->format('M') }}</span>
                                    <span class="text-lg font-black text-gray-800 leading-none">{{ \Carbon\Carbon::parse($event->scheduled_date)->format('d') }}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] font-black text-gray-800 uppercase truncate" title="{{ $event->title }}">{{ $event->title }}</p>
                                    @php $days = now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($event->scheduled_date), false); @endphp
                                    <p class="text-[8px] font-medium text-gray-500 mt-1">
                                        @if($days == 0) <span class="text-red-600 font-black">HAPPENING TODAY</span> 
                                        @else In {{ $days }} days @endif
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="text-[10px] text-gray-400 italic text-center py-10 uppercase tracking-widest">No upcoming events scheduled.</p>
                        @endforelse
                    </div>
                </div>

                {{-- REPOSITORY --}}
                <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200">
                    <div class="p-4 bg-gray-800 text-white uppercase text-[10px] font-black tracking-widest border-b">File Repository</div>
                    <div class="overflow-y-auto max-h-[300px]">
                        <table class="w-full text-left text-[11px]">
                            <tbody class="divide-y divide-gray-100">
                                @forelse($repositoryFiles->take(15) as $file)
                                    <tr class="hover:bg-red-50 group">
                                        <td class="px-4 py-3"><span class="font-black text-gray-800 uppercase leading-tight">{{ $file->title }}</span></td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('file.view', $file->id) }}" target="_blank" class="text-[#800000] opacity-30 group-hover:opacity-100 transition inline-block">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="px-4 py-10 text-center text-gray-400 italic">No files available.</td></tr>
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
        
        // Category Chart
        new Chart(document.getElementById('categoryChart'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($categoryData->pluck("category")) !!},
                datasets: [{ data: {!! json_encode($categoryData->pluck("total")) !!}, backgroundColor: ['#800000', '#FCD116', '#1a1a1a', '#A8A8A8'], borderWidth: 0 }]
            },
            options: { ...opts, cutout: '75%' }
        });

        // Office Chart - Syncing with $filteredOfficeData
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