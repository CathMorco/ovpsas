@extends('layouts.master')

@section('title', 'Home - OVPSAS')

@section('content')
    <div class="max-w-7xl mx-auto px-6 lg:px-8 space-y-10">

        {{-- Success Message --}}
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm mb-6" role="alert">
                <p class="font-bold">Success</p>
                <p class="text-sm">{{ session('success') }}</p>
            </div>
        @endif

        {{-- ANNOUNCEMENT BOARD --}}
        <div class="bg-white shadow-xl sm:rounded-lg border-l-8 border-[#800000]">
            <div class="bg-[#800000] py-3 px-6 flex items-center gap-3">
                <span class="w-1.5 h-5 bg-[#FCD116]"></span>
                <h2 class="text-white font-bold tracking-widest uppercase text-sm">Announcements Board</h2>
            </div>
            <div class="p-8">
                <form action="{{ route('announcements.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="max-w-5xl space-y-5">

                        {{-- Row 1: Target Office, Category, & Date Picker --}}
                        <div class="flex flex-col md:flex-row gap-6 items-start relative z-20">

                            {{-- 1. Target Office Dropdown --}}
                            <div class="flex flex-col flex-1 w-full" x-data="{ open: false, selected: [] }">
                                <label class="font-bold text-[#800000] text-sm mb-1">Target Offices:</label>

                                <div class="relative" @click.away="open = false">
                                    <div @click="open = !open" class="w-full bg-gray-100 border border-gray-200 rounded-md px-4 py-2.5 text-sm cursor-pointer flex justify-between items-center hover:bg-gray-200 transition">
                                        <span x-text="selected.length > 0 ? selected.join(', ') : 'Select Offices...'" :class="selected.length > 0 ? 'text-gray-800 font-bold' : 'text-gray-500'"></span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" :class="{'rotate-180': open}" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>

                                    <div x-show="open" x-cloak class="absolute top-full left-0 w-full bg-white border border-gray-200 rounded-md shadow-xl mt-1 z-50 flex flex-col overflow-hidden">
                                        <div class="max-h-60 overflow-y-auto p-2 space-y-1">
                                            <label class="flex items-center space-x-3 p-2 hover:bg-yellow-50 rounded cursor-pointer transition">
                                                <input type="checkbox" name="office[]" value="All Offices" x-model="selected" class="form-checkbox h-4 w-4 text-[#800000] rounded focus:ring-yellow-400">
                                                <span class="text-sm font-bold text-[#800000]">All Offices</span>
                                            </label>
                                            <div class="border-t border-gray-100 my-1"></div>
                                            @foreach(\App\Models\Office::all() as $office)
                                                <label class="flex items-center space-x-3 p-2 hover:bg-gray-50 rounded cursor-pointer transition">
                                                    <input type="checkbox" name="office[]" value="{{ $office->code }}" x-model="selected" class="form-checkbox h-4 w-4 text-[#800000] rounded focus:ring-yellow-400">
                                                    <span class="text-sm text-gray-700">{{ $office->name }} ({{ $office->code }})</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        <div class="bg-gray-50 border-t border-gray-100 p-2 flex justify-end">
                                            <button type="button" @click="open = false" class="bg-[#800000] text-white text-xs font-bold px-4 py-1.5 rounded hover:bg-red-900 transition uppercase tracking-wider">OK</button>
                                        </div>
                                    </div>
                                </div>
                                @error('office')
                                    <span class="text-xs font-bold text-red-600 mt-1 italic">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- 2. Category Dropdown --}}
                            <div class="flex flex-col flex-1 w-full" x-data="{ open: false, selected: [], customCategory: '' }">
                                <label class="font-bold text-[#800000] text-sm mb-1">Categories:</label>

                                <div class="relative" @click.away="open = false">
                                    <div @click="open = !open" class="w-full bg-gray-100 border border-gray-200 rounded-md px-4 py-2.5 text-sm cursor-pointer flex justify-between items-center hover:bg-gray-200 transition">
                                        <span x-text="selected.length > 0
                                            ? selected.map(cat => (cat === 'Others' && customCategory !== '') ? customCategory : cat).join(', ')
                                            : 'Select Categories...'"
                                            :class="selected.length > 0 ? 'text-gray-800 font-bold' : 'text-gray-500'">
                                        </span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" :class="{'rotate-180': open}" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>

                                    <div x-show="open" x-cloak class="absolute top-full left-0 w-full bg-white border border-gray-200 rounded-md shadow-xl mt-1 z-50 flex flex-col overflow-hidden">
                                        <div class="max-h-60 overflow-y-auto p-2 space-y-1">
                                            @foreach(['Memorandums', 'Executive Orders', 'Reports', 'Minutes of Meeting', 'Activity Proposals', 'Letters', 'Financials', 'Forms', 'Policies', 'MOAs', 'Masterlists', 'Event Material', 'Others'] as $cat)
                                                <label class="flex items-center space-x-3 p-2 hover:bg-gray-50 rounded cursor-pointer transition">
                                                    <input type="checkbox" name="category[]" value="{{ $cat }}" x-model="selected" class="form-checkbox h-4 w-4 text-[#800000] rounded focus:ring-yellow-400">
                                                    <span class="text-sm text-gray-700">{{ $cat }}</span>
                                                </label>
                                            @endforeach
                                        </div>

                                        <div x-show="selected.includes('Others')" class="p-3 bg-yellow-50 border-t border-gray-100" x-transition>
                                            <label class="text-[10px] font-bold text-[#800000] uppercase mb-1 block">Specify Other Category:</label>
                                            <input type="text" name="custom_category" x-model="customCategory" placeholder="Type here..." class="w-full text-sm border-gray-300 rounded-md focus:ring-[#800000] focus:border-[#800000] py-1.5">
                                        </div>

                                        <div class="bg-gray-50 border-t border-gray-100 p-2 flex justify-end">
                                            <button type="button" @click="open = false" class="bg-[#800000] text-white text-xs font-bold px-4 py-1.5 rounded hover:bg-red-900 transition uppercase tracking-wider">OK</button>
                                        </div>
                                    </div>
                                </div>
                                @error('category')
                                    <span class="text-xs font-bold text-red-600 mt-1 italic">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- 3. NEW: Target Date Picker --}}
                            <div class="flex flex-col w-full md:w-48">
                                <label class="font-bold text-[#800000] text-sm mb-1">Target Date (Optional):</label>
                                <input type="date" name="scheduled_date" value="{{ old('scheduled_date') }}"
                                       class="w-full bg-gray-100 border border-gray-200 rounded-md px-4 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 hover:bg-gray-200 transition cursor-pointer">
                                @error('scheduled_date')
                                    <span class="text-xs font-bold text-red-600 mt-1 italic">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Row 2: Title --}}
                        <div class="flex flex-col relative z-0">
                            <div class="flex items-center gap-4">
                                <label class="font-bold text-[#800000] w-20 text-sm">Title:</label>
                                <input type="text" name="title" value="{{ old('title') }}" placeholder="Enter Details..." class="flex-grow bg-gray-100 border-none rounded-md px-4 py-2.5 italic text-sm focus:ring-2 focus:ring-yellow-400" required>
                            </div>
                            @error('title')
                                <span class="text-xs font-bold text-red-600 mt-1 ml-24 italic">⚠️ {{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Row 3: Content --}}
                        <div class="flex flex-col relative z-0">
                            <div class="flex items-start gap-4">
                                <label class="font-bold text-[#800000] w-20 pt-2 text-sm">Content:</label>
                                <textarea name="content" rows="3" placeholder="Enter Details..." class="flex-grow bg-gray-100 border-none rounded-md px-4 py-2.5 italic text-sm focus:ring-2 focus:ring-yellow-400 w-full">{{ old('content') }}</textarea>
                            </div>
                            @error('content')
                                <span class="text-xs font-bold text-red-600 mt-1 ml-24 italic">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Row 4: File Upload & Submit --}}
                        <div class="flex flex-col md:flex-row items-center justify-between gap-4 pl-0 md:pl-24 relative z-0" x-data="{ fileName: '' }">
                            <div class="flex items-center gap-3 w-full">
                                <label class="cursor-pointer bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-xs font-bold transition flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    <span x-text="fileName ? fileName : 'Upload File'">Upload File</span>
                                    <input type="file" name="attachment" class="hidden" @change="if($event.target.files[0]) fileName = $event.target.files[0].name;">
                                </label>
                                <template x-if="fileName"><span class="text-[10px] text-green-600 font-bold italic">Selected: <span x-text="fileName"></span></span></template>
                                @error('attachment')
                                    <span class="text-xs font-bold text-red-600 italic ml-2">{{ $message }}</span>
                                @enderror
                            </div>
                            <button type="submit" class="bg-[#4D0000] text-white font-bold px-12 py-3 rounded-lg hover:bg-[#800000] transition shadow-md uppercase text-xs tracking-widest whitespace-nowrap">Publish</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- QUICK ACCESS BUTTONS --}}
        <div class="bg-white shadow-lg sm:rounded-lg border-l-8 border-[#800000]">
            <div class="bg-[#800000] py-3 px-6 flex items-center gap-3">
                <span class="w-1.5 h-5 bg-[#FCD116]"></span>
                <h2 class="text-white font-bold tracking-widest uppercase text-sm">Quick Access</h2>
            </div>
            <div class="p-8">
                <div class="grid grid-cols-4 md:grid-cols-7 gap-6">
                    @foreach(\App\Models\Office::all() as $office)
                    <a href="{{ route('offices.show', $office->code) }}" class="flex flex-col items-center group cursor-pointer">
                        <div class="w-12 h-10 border-2 border-[#800000] rounded-md flex items-center justify-center bg-white group-hover:bg-[#800000] transition-all shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#800000] group-hover:text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>
                            </svg>
                        </div>
                        <span class="text-[9px] font-black mt-2 text-gray-800 text-center uppercase tracking-tighter">{{ $office->code }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- THREE COLUMNS --}}
        <div class="grid lg:grid-cols-3 gap-10">
            {{-- 1. Memorandums --}}
            <div class="bg-white shadow-lg sm:rounded-lg border-l-8 border-[#800000] relative overflow-hidden">
                <div class="bg-[#800000] py-3 px-6 flex items-center gap-3">
                    <span class="w-1.5 h-5 bg-[#FCD116]"></span>
                    <h2 class="text-white font-bold tracking-widest uppercase text-[10px]">Memorandums</h2>
                </div>
                <div class="p-6 overflow-y-auto max-h-[300px]">
                    <div class="space-y-3">
                        @forelse($announcements->filter(fn($a) => is_array($a->category) ? in_array('Memorandums', $a->category) : $a->category == 'Memorandums') as $announcement)
                            <a href="{{ route('file.view', $announcement->id) }}" target="_blank" class="flex items-center gap-3 text-sm text-gray-700 hover:text-[#800000] transition-colors group">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#800000] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                                <span class="truncate block text-[11px] font-bold uppercase" title="{{ $announcement->title }}">{{ $announcement->title }}</span>
                            </a>
                        @empty
                            <p class="text-[10px] text-gray-400 italic text-center">Empty.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- 2. Executive Orders --}}
            <div class="bg-white shadow-lg sm:rounded-lg border-l-8 border-[#800000] relative overflow-hidden">
                <div class="bg-[#800000] py-3 px-6 flex items-center gap-3">
                    <span class="w-1.5 h-5 bg-[#FCD116]"></span>
                    <h2 class="text-white font-bold tracking-widest uppercase text-[10px]">Executive Orders</h2>
                </div>
                <div class="p-6 overflow-y-auto max-h-[300px]">
                    <div class="space-y-3">
                        @forelse($announcements->filter(fn($a) => is_array($a->category) ? in_array('Executive Orders', $a->category) : $a->category == 'Executive Orders') as $announcement)
                            <a href="{{ route('file.view', $announcement->id) }}" target="_blank" class="flex items-center gap-3 text-sm text-gray-700 hover:text-[#800000] transition-colors group">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#800000] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                                <span class="truncate block text-[11px] font-bold uppercase" title="{{ $announcement->title }}">{{ $announcement->title }}</span>
                            </a>
                        @empty
                            <p class="text-[10px] text-gray-400 italic text-center">Empty.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- 3. Recent Activity --}}
            <div class="bg-white shadow-lg sm:rounded-lg border-l-8 border-[#800000] relative overflow-hidden">
                <div class="bg-[#800000] py-3 px-6 flex items-center gap-3">
                    <span class="w-1.5 h-5 bg-[#FCD116]"></span>
                    <h2 class="text-white font-bold tracking-widest uppercase text-[10px]">Recent Activity</h2>
                </div>
                <div class="p-6 overflow-y-auto max-h-[300px]">
                    <div class="space-y-3">
                        @forelse($announcements->take(10) as $announcement)
                        <div class="flex justify-between items-center text-[10px] text-gray-600 gap-2 border-b border-gray-50 pb-2 last:border-0">
                            <div class="truncate flex flex-col min-w-0">
                                <span class="font-bold text-[#800000] uppercase text-[8px]">
                                    {{ is_array($announcement->office) ? implode(', ', $announcement->office) : $announcement->office }}
                                </span>
                                <span class="truncate italic uppercase font-bold text-gray-800">{{ $announcement->title }}</span>

                                {{-- DISPLAY THE DATE BADGE IF IT EXISTS --}}
                                @if($announcement->scheduled_date)
                                    <span class="text-[7px] text-blue-600 font-black italic uppercase flex items-center gap-1 mt-0.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-2 w-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Event Date: {{ $announcement->scheduled_date->format('M d, Y') }}
                                    </span>
                                @endif

                                {{-- Display custom category if available --}}
                                @if($announcement->custom_category)
                                    <span class="text-[7px] text-[#800000] font-black italic uppercase">Type: {{ $announcement->custom_category }}</span>
                                @endif

                                <span class="text-[7px] text-gray-400 font-medium">{{ $announcement->created_at->diffForHumans() }}</span>
                            </div>
                            @if($announcement->file_path)
                                <a href="{{ route('file.view', $announcement->id) }}" target="_blank" class="bg-[#4D0000] text-white px-3 py-1 rounded text-[8px] hover:bg-red-800 transition uppercase shrink-0 font-bold">VIEW</a>
                            @endif
                        </div>
                        @empty
                            <p class="text-[10px] text-gray-400 italic">No activity yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection