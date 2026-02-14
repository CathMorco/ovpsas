@extends('layouts.master')

@section('title', 'Dashboard - OVPSAS')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="max-w-7xl mx-auto px-6 lg:px-8 space-y-8 py-8" x-data="{ openComment: null }">

        <div class="bg-white p-8 rounded-xl shadow-md border-t-4 border-[#800000]">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 uppercase tracking-tight">System Administrative Report</h2>
                    <p class="text-gray-500 italic text-sm">Real-time analytics of system utilization.</p>
                </div>
                <a href="{{ route('reports.download') }}" class="bg-[#800000] text-white px-6 py-2 rounded-lg font-bold hover:bg-red-900 transition shadow-lg flex items-center gap-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    GENERATE PDF REPORT
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-8">
                <div class="bg-red-50 p-6 rounded-xl border border-red-100 text-center">
                    <p class="text-[10px] font-bold text-red-700 uppercase tracking-widest">Total Files</p>
                    <h3 class="text-3xl font-black text-gray-800">{{ $totalFiles }}</h3>
                </div>
                <div class="bg-gray-50 p-6 rounded-xl border border-gray-100 text-center">
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Monitored Offices</p>
                    <h3 class="text-3xl font-black text-gray-800">{{ $activeOffices }}</h3>
                </div>
                <div class="bg-green-50 p-6 rounded-xl border border-green-100 text-center">
                    <p class="text-[10px] font-bold text-green-700 uppercase tracking-widest">Uploaded This Month</p>
                    <h3 class="text-3xl font-black text-green-600">{{ $filesThisMonth }}</h3>
                </div>
                <div class="bg-blue-50 p-6 rounded-xl border border-blue-100 text-center">
                    <p class="text-[10px] font-bold text-blue-700 uppercase tracking-widest">Most Active Office</p>
                    <h3 class="text-xl font-black text-blue-600 truncate" title="{{ $mostActiveOffice }}">{{ \Illuminate\Support\Str::limit($mostActiveOffice, 15) }}</h3>
                </div>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-white p-5 shadow-md rounded-2xl border-t-4 border-yellow-400 flex flex-col h-[300px]">
                <h3 class="font-black text-gray-700 mb-4 text-[10px] uppercase tracking-widest text-center">Category Distribution</h3>
                <div class="flex-grow flex items-center justify-center relative overflow-hidden">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>

            <div class="bg-white p-5 shadow-md rounded-2xl border-t-4 border-[#800000] flex flex-col h-[300px]">
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
                            <a href="{{ route('file.view', $announcement->id) }}" target="_blank" class="text-[9px] bg-[#800000] text-white px-3 py-1 rounded-full font-black uppercase hover:bg-red-800 transition shadow-sm">View</a>
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
                                        <a href="{{ route('file.view', $announcement->id) }}" target="_blank" class="text-[#800000] opacity-30 group-hover:opacity-100 transition-all hover:scale-125 inline-block">
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

        // --- SIDEBAR AUTO REFRESH LOGIC ---
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                window.location.reload();
            }
        });
        window.addEventListener( "pageshow", function ( event ) {
            var historyTraversal = event.persisted || ( typeof window.performance != "undefined" && window.performance.navigation.type === 2 );
            if ( historyTraversal ) { window.location.reload(); }
        });
    </script>
@endsection