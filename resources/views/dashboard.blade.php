@extends('layouts.master')

@section('title', 'Dashboard - OSAS')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <div class="max-w-7xl mx-auto px-6 lg:px-8 space-y-8 py-8">

        {{-- 1. ADMIN-ONLY ANALYTICS BLOCK --}}
        @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
            
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

        @endif

        <div class="grid lg:grid-cols-5 gap-8">
            {{-- FEED AREA (Visible to All) --}}
            <div class="lg:col-span-3 space-y-6">
                <h2 class="text-xl font-black text-[#800000] uppercase italic border-b-2 border-gray-800 pb-1">Announcement Feed</h2>

                @forelse($feedItems as $announcement)
                    <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden" x-data="{ openComment: false, openEdit: false }">
                        <div class="p-4 flex items-center justify-between border-b bg-gray-50/50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-[#800000] flex items-center justify-center text-white font-bold text-xs uppercase">{{ substr($announcement->user->name ?? '?', 0, 1) }}</div>
                                <div>
                                    <p class="text-xs font-black text-gray-900 uppercase">{{ $announcement->user->name ?? 'Admin' }}</p>
                                    <p class="text-[9px] text-gray-400 font-bold uppercase tracking-tighter">
                                        {{ is_array($announcement->office) ? implode(', ', $announcement->office) : $announcement->office }} • 
                                        {{ $announcement->created_at->diffForHumans() }}
                                        @if($announcement->updated_at->diffInSeconds($announcement->created_at) > 60)
                                            <span class="text-gray-400 italic"> (Edited {{ $announcement->updated_at->diffForHumans() }})</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-[8px] font-black uppercase">
                                @php
                                    $cats = is_array($announcement->category) ? $announcement->category : [$announcement->category];
                                    $displayCats = array_map(function($cat) use ($announcement) {
                                        return (trim($cat) === 'Others' && !empty($announcement->custom_category)) ? $announcement->custom_category : $cat;
                                    }, $cats);
                                @endphp
                                {{ implode(', ', $displayCats) }}
                            </span>
                        </div>
                        
                        <div class="p-5 relative">
                            {{-- 2. ACTION BUTTONS LOCKDOWN (Only visible to the original poster) --}}
                            @if(auth()->id() === $announcement->user_id)
                                <div class="absolute top-5 right-5 flex gap-2">
                                    <button @click="openEdit = true" class="text-[9px] bg-gray-200 text-gray-700 px-3 py-1 rounded hover:bg-gray-300 font-black uppercase transition shadow-sm">
                                        Edit
                                    </button>

                                    <form action="{{ route('announcements.destroy', $announcement->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this post and its files from all offices?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-[9px] bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 font-black uppercase transition shadow-sm">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            @endif

                            @if($announcement->scheduled_date)
                                <div class="inline-flex items-center gap-2 px-3 py-1 bg-blue-50 text-blue-700 rounded-md text-[9px] font-black uppercase mb-3 border border-blue-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <span>Event Date: {{ \Carbon\Carbon::parse($announcement->scheduled_date)->format('M d, Y') }}</span>
                                </div>
                            @endif

                            <h4 class="font-black text-sm text-[#800000] uppercase mb-2 w-5/6">{{ $announcement->title }}</h4>
                            <p class="text-xs text-gray-600 italic leading-relaxed whitespace-pre-line">{{ $announcement->content }}</p>
                            
                            @php 
                                $files = [];
                                if($announcement->file_path) {
                                    $decoded = json_decode($announcement->file_path, true);
                                    $files = is_array($decoded) ? $decoded : [['path' => $announcement->file_path, 'original_name' => basename($announcement->file_path)]];
                                }
                            @endphp
                            @if(!empty($files))
                                <div class="mt-4 space-y-2">
                                    @foreach($files as $file)
                                        <div class="p-3 bg-gray-100 rounded-lg flex items-center justify-between border-l-4 border-[#800000]">
                                            <span class="text-[10px] font-bold text-gray-500 truncate max-w-[200px]">{{ $file['original_name'] }}</span>
                                            <a href="{{ route('file.view', ['announcement' => $announcement->id, 'path' => $file['path']]) }}" target="_blank" class="text-[9px] bg-[#800000] text-white px-3 py-1 rounded-full font-black uppercase shadow-sm hover:bg-red-800 transition">View File</a>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="mt-4 p-3 bg-gray-100 rounded-lg flex items-center justify-between border-l-4 border-[#800000]">
                                    <span class="text-[10px] font-bold text-gray-400 italic">No attachment. Reading from post content...</span>
                                    <a href="{{ route('file.view', $announcement->id) }}" target="_blank" class="text-[9px] bg-gray-600 text-white px-3 py-1 rounded-full font-black uppercase shadow-sm hover:bg-gray-700 transition">View Post.txt</a>
                                </div>
                            @endif
                        </div>

                        {{-- EDIT MODAL (POSTER ONLY) --}}
                        @if(auth()->id() === $announcement->user_id)
                        <div x-show="openEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm px-4">
                            <div @click.away="openEdit = false" class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto border-t-4 border-[#800000]">
                                <div class="flex justify-between items-center border-b pb-3 mb-4">
                                    <h3 class="text-lg font-black text-[#800000] uppercase tracking-tight">Edit Announcement</h3>
                                    <button @click="openEdit = false" class="text-gray-400 hover:text-red-600 font-bold text-2xl transition">&times;</button>
                                </div>

                                <form action="{{ route('announcements.update', $announcement->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                                    @csrf
                                    @method('PUT')
                                    
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-1">Title</label>
                                        <input type="text" name="title" value="{{ $announcement->title }}" class="w-full p-3 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#800000]" required>
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-1">Content</label>
                                        <textarea name="content" rows="4" class="w-full p-3 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#800000]">{{ $announcement->content }}</textarea>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="bg-gray-50 p-3 rounded-lg border">
                                            <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-2 border-b pb-1">Target Offices</label>
                                            <div class="space-y-1">
                                                @php $offices = ['All Offices', 'ARCDO', 'OCPS', 'OSFA', 'OSS', 'OUR', 'SDPO', 'UCCA']; @endphp
                                                @foreach($offices as $office)
                                                    <label class="flex items-center gap-2 text-xs text-gray-600">
                                                        <input type="checkbox" name="office[]" value="{{ $office }}" 
                                                        {{ in_array($office, is_array($announcement->office) ? $announcement->office : [$announcement->office]) ? 'checked' : '' }}
                                                        class="rounded text-[#800000] focus:ring-[#800000]">
                                                        {{ $office }}
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="bg-gray-50 p-3 rounded-lg border flex flex-col justify-between" x-data="{ showCustom: {{ $announcement->custom_category ? 'true' : 'false' }} }">
                                            <div>
                                                <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-2 border-b pb-1">Categories</label>
                                                <div class="space-y-1 max-h-[150px] overflow-y-auto pr-2">
                                                    @foreach($allAvailableCategories as $cat)
                                                        <label class="flex items-center gap-2 text-xs text-gray-600">
                                                            <input type="checkbox" name="category[]" value="{{ $cat }}" 
                                                            {{ in_array($cat, is_array($announcement->category) ? $announcement->category : [$announcement->category]) ? 'checked' : '' }}
                                                            @if($cat === 'Others') x-model="showCustom" @endif
                                                            class="rounded text-[#800000] focus:ring-[#800000]">
                                                            {{ $cat }}
                                                        </label>
                                                    @endforeach
                                                </div>
                                                <div class="mt-3" x-show="showCustom" x-cloak x-transition>
                                                    <input type="text" name="custom_category" value="{{ $announcement->custom_category }}" placeholder="Type new category name..." class="w-full p-2 border border-[#800000] rounded text-xs outline-none focus:ring-2 focus:ring-[#800000] bg-red-50">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-1">Scheduled Date (Optional)</label>
                                            <input type="date" name="scheduled_date" value="{{ $announcement->scheduled_date ? \Carbon\Carbon::parse($announcement->scheduled_date)->format('Y-m-d') : '' }}" class="w-full p-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#800000]">
                                        </div>
                                        <div x-data="{ addFileNames: '' }">
                                            <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-1">Add More Files</label>
                                            <label class="cursor-pointer bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 w-full p-1.5 rounded text-xs font-bold transition flex items-center justify-center gap-2">
                                                <span>Choose Files</span>
                                                <input type="file" name="attachments[]" multiple class="hidden" @change="addFileNames = Array.from($event.target.files).map(f => f.name).join(', ');">
                                            </label>
                                            <template x-if="addFileNames"><p class="text-[9px] text-green-600 mt-1 italic truncate" x-text="addFileNames"></p></template>
                                        </div>
                                    </div>

                                    @if(!empty($files))
                                        <div class="bg-red-50 border border-red-100 p-3 rounded-lg">
                                            <label class="block text-[10px] font-black text-red-800 uppercase tracking-widest mb-2">Remove Existing Attachments</label>
                                            <div class="space-y-2">
                                                @foreach($files as $file)
                                                    <label class="flex items-center gap-2 text-xs text-gray-700 bg-white p-2 rounded shadow-sm cursor-pointer border border-red-50 hover:bg-red-100 transition">
                                                        <input type="checkbox" name="remove_files[]" value="{{ $file['path'] }}" class="text-red-600 rounded">
                                                        <span class="truncate">🗑️ Delete: {{ $file['original_name'] }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                                        <button type="button" @click="openEdit = false" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg text-[10px] font-black uppercase hover:bg-gray-200 transition">Cancel</button>
                                        <button type="submit" class="px-5 py-2.5 bg-[#800000] text-white rounded-lg text-[10px] font-black uppercase shadow-md hover:bg-red-900 transition">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endif

                        {{-- COMMENTS AREA --}}
                        <div class="px-5 py-3 border-t bg-gray-50/30">
                            <button @click="openComment = !openComment" class="flex items-center gap-1.5 text-gray-500 hover:text-[#800000] transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
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
                    <p class="text-center py-20 italic text-gray-400 text-sm uppercase">No active announcements.</p>
                @endforelse
            </div>

            {{-- SIDEBAR AREA --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- CALENDAR --}}
                <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 border-t-4 border-blue-600">
                    <div class="p-4 bg-gray-50 flex items-center justify-between border-b">
                        <span class="text-[#1a202c] uppercase text-[10px] font-black tracking-widest">Upcoming Events Calendar</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
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
                    <div class="p-4 bg-gray-800 text-white uppercase text-[10px] font-black tracking-widest border-b">File Repository</div>
                    <div class="overflow-y-auto max-h-[300px]">
                        <table class="w-full text-left text-[11px]">
                            <tbody class="divide-y divide-gray-100">
                                @forelse($repositoryFiles->take(15) as $file)
                                    <tr class="hover:bg-red-50 group transition">
                                        <td class="px-4 py-3 font-black text-gray-800 uppercase leading-tight">{{ $file->title }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('file.view', $file->id) }}" target="_blank" class="text-[#800000] opacity-30 group-hover:opacity-100 transition inline-block">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
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

    {{-- SCRIPTS (Wrapped in Admin check to avoid JS errors for Staff) --}}
    @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
    <script>
        const opts = { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } };
        
        // Category Chart
        const catCtx = document.getElementById('categoryChart');
        if(catCtx) {
            new Chart(catCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($categoryData->pluck("category")) !!},
                    datasets: [{ data: {!! json_encode($categoryData->pluck("total")) !!}, backgroundColor: ['#800000', '#FCD116', '#1a1a1a', '#A8A8A8'], borderWidth: 0 }]
                },
                options: { ...opts, cutout: '75%' }
            });
        }

        // Office Chart
        const offCtx = document.getElementById('officeChart');
        if(offCtx) {
            new Chart(offCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($filteredOfficeData->pluck("office")) !!},
                    datasets: [{ data: {!! json_encode($filteredOfficeData->pluck("total")) !!}, backgroundColor: '#800000', borderRadius: 5 }]
                },
                options: { ...opts, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
            });
        }
    </script>
    @endif

    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
@endsection