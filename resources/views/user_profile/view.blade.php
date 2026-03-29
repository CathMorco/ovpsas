@extends('layouts.master')

@section('title', 'My Profile - OVPSAS Portal')

@section('content')
    {{-- REMOVED the missing Vite CSS line that caused the crash. Tailwind will handle the styling. --}}

    <div class="min-h-screen flex flex-col bg-gray-100">
        <div class="flex-1 w-full max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-12 gap-8 px-4 sm:px-6 lg:px-8 py-10">
            
            {{-- ================= LEFT COLUMN: USER IDENTITY ================= --}}
            <div class="md:col-span-4 flex flex-col items-center flex-shrink-0">
                <div class="w-52 h-52 rounded-full overflow-hidden border-4 border-white shadow-xl mb-4 bg-[#800000] flex items-center justify-center text-white text-6xl font-black relative">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        {{ substr($user->name, 0, 1) }}
                    @endif
                </div>
                
                <div class="text-center mb-6">
                    <h2 class="text-3xl font-extrabold text-gray-900 block mb-1">{{ $user->name }}</h2>
                    <p class="text-sm font-bold text-gray-400 tracking-wide">{{ $user->suffix ?? '' }}</p>
                </div>

                <div class="w-full max-w-sm flex flex-col pb-2">
                    <a href="{{ route('settings.edit') }}" class="w-full bg-[#800000] text-white py-3 rounded-lg font-black text-xs text-center uppercase tracking-widest hover:bg-red-900 transition shadow-md mb-6">
                        Manage your account
                    </a>

                    <div class="w-full p-6 bg-white border border-gray-100 rounded-xl shadow-sm mb-4">
                        <div class="space-y-4">
                            <h4 class="text-[#800000] text-xs font-black tracking-widest uppercase border-b border-gray-100 pb-2 mb-3">About</h4>
                            <div class="flex items-start gap-4 text-sm text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 text-[#800000] shrink-0">
                                    <path fill-rule="evenodd" d="M4.5 2.25a.75.75 0 0 0 0 1.5v16.5h-.75a.75.75 0 0 0 0 1.5h16.5a.75.75 0 0 0 0-1.5h-.75V3.75a.75.75 0 0 0 0-1.5h-15ZM9 6a.75.75 0 0 0 0 1.5h1.5a.75.75 0 0 0 0-1.5H9Zm-.75 3.75A.75.75 0 0 1 9 9h1.5a.75.75 0 0 1 0 1.5H9a.75.75 0 0 1-.75-.75ZM9 12a.75.75 0 0 0 0 1.5h1.5a.75.75 0 0 0 0-1.5H9Zm3.75-5.25A.75.75 0 0 1 13.5 6H15a.75.75 0 0 1 0 1.5h-1.5a.75.75 0 0 1-.75-.75ZM13.5 9a.75.75 0 0 0 0 1.5H15A.75.75 0 0 0 15 9h-1.5Zm-.75 3.75a.75.75 0 0 1 .75-.75H15a.75.75 0 0 1 0 1.5h-1.5a.75.75 0 0 1-.75-.75ZM9 19.5v-2.25a.75.75 0 0 1 .75-.75h4.5a.75.75 0 0 1 .75.75v2.25a.75.75 0 0 1-.75.75h-4.5A.75.75 0 0 1 9 19.5Z" clip-rule="evenodd" />
                                </svg>
                                <span class="font-medium">{{ $user->office->name ?? 'No Office Assigned' }}</span>
                            </div>
                            <div class="flex items-center gap-4 text-sm text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 text-[#800000] shrink-0">
                                    <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                                </svg>
                                <span class="font-medium">{{ $user->designation ?? 'Staff' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="w-full p-6 bg-white border border-gray-100 rounded-xl shadow-sm">
                        <div class="space-y-4">
                            <h4 class="text-[#800000] text-xs font-black tracking-widest uppercase border-b border-gray-100 pb-2 mb-3">Contact</h4>
                            <div class="flex items-center gap-4 text-sm text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 text-[#800000] shrink-0">
                                    <path fill-rule="evenodd" d="M1.5 4.5a3 3 0 0 1 3-3h1.372c.86 0 1.61.586 1.819 1.42l1.105 4.423a1.875 1.875 0 0 1-.694 1.955l-1.293.97c-.135.101-.164.249-.126.352a11.285 11.285 0 0 0 6.697 6.697c.103.038.25.009.352-.126l.97-1.293a1.875 1.875 0 0 1 1.955-.694l4.423 1.105c.834.209 1.42.959 1.42 1.82V19.5a3 3 0 0 1-3 3h-2.25C8.552 22.5 1.5 15.448 1.5 6.75V4.5Z" clip-rule="evenodd" />
                                </svg>
                                <span class="font-medium">{{ $user->phone ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center gap-4 text-sm text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 text-[#800000] shrink-0">
                                    <path d="M1.5 8.67v8.58a3 3 0 0 0 3 3h15a3 3 0 0 0 3-3V8.67l-8.928 5.493a3 3 0 0 1-3.144 0L1.5 8.67Z" />
                                    <path d="M22.5 6.908V6.75a3 3 0 0 0-3-3h-15a3 3 0 0 0-3 3v.158l9.714 5.978a1.5 1.5 0 0 0 1.572 0L22.5 6.908Z" />
                                </svg>
                                <span class="font-medium truncate">{{ $user->email }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= RIGHT COLUMN: PUBLICATIONS ================= --}}
            <div class="md:col-span-8 flex flex-col h-full min-h-0">
                <div class="bg-white rounded-xl border border-gray-100 flex flex-col h-full shadow-lg overflow-hidden">
                    <div class="bg-[#800000] p-4 flex items-center gap-3 shrink-0">
                        <span class="w-1.5 h-6 bg-[#FCD116]"></span>
                        <h3 class="text-white font-black tracking-widest uppercase text-sm">My Publications</h3>
                    </div>

                    <form action="{{ route('profile.edit') }}" method="GET" class="shrink-0">
                        <div class="bg-gray-50 p-4 border-b border-gray-200 grid grid-cols-1 md:grid-cols-12 gap-x-4 gap-y-3">
                            <div class="md:col-span-4">
                                <label class="text-[10px] font-bold text-gray-400 uppercase mb-1 block tracking-wider">Search Title</label>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Keyword..." class="w-full border border-gray-300 rounded px-3 py-2 text-xs text-gray-800 focus:ring-1 focus:ring-[#800000] focus:border-[#800000]">
                            </div>
                            <div class="md:col-span-4">
                                <label class="text-[10px] font-bold text-gray-400 uppercase mb-1 block tracking-wider">Category</label>
                                <select name="category" class="w-full border border-gray-300 rounded px-3 py-2 text-xs text-gray-800 bg-white focus:ring-1 focus:ring-[#800000] focus:border-[#800000]">
                                    <option value="">All Categories</option>
                                    @foreach(['Memorandums', 'Executive Orders', 'Reports', 'Minutes of Meeting', 'Activity Proposals', 'Letters', 'Financials', 'Forms', 'Policies', 'MOAs', 'Masterlists', 'Event Material', 'Others'] as $cat)
                                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-4">
                                <label class="text-[10px] font-bold text-gray-400 uppercase mb-1 block tracking-wider">Date</label>
                                <input type="date" name="date" value="{{ request('date') }}" class="w-full border border-gray-300 rounded px-3 py-2 text-xs text-gray-800 focus:ring-1 focus:ring-[#800000] focus:border-[#800000]">
                            </div>
                            <div class="md:col-span-12 flex gap-4 pt-1">
                                <button type="submit" class="text-[#800000] text-[10px] font-bold uppercase underline">Apply Filters</button>
                                @if(request()->anyFilled(['search', 'category', 'date']))
                                    <a href="{{ route('profile.edit') }}" class="text-gray-400 text-[10px] font-bold uppercase underline hover:text-red-500">Clear Filters</a>
                                @endif
                            </div>
                        </div>
                    </form>
                    
                    <div class="overflow-y-auto max-h-[600px] custom-scroll p-6 bg-white space-y-4">
                        @forelse($publications as $announcement)
                            <div class="bg-white border-l-4 border-[#800000] rounded shadow-sm p-4 hover:shadow-md transition group border border-gray-100">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-[9px] font-black text-[#800000] uppercase">
                                                {{ is_array($announcement->office) ? implode(', ', $announcement->office) : $announcement->office }}
                                            </span>
                                            <span class="text-[9px] text-gray-300">•</span>
                                            <span class="text-[9px] text-gray-400 font-bold uppercase">{{ $announcement->created_at->diffForHumans() }}</span>
                                        </div>
                                        <h4 class="text-sm font-bold text-gray-800 uppercase leading-tight">{{ $announcement->title }}</h4>
                                    </div>
                                    <span class="px-2 py-1 bg-yellow-50 text-yellow-700 rounded text-[8px] font-black uppercase tracking-wide border border-yellow-200">
                                        {{ is_array($announcement->category) ? implode(', ', $announcement->category) : $announcement->category }}
                                    </span>
                                </div>
                                
                                <div class="mt-3 flex items-center justify-between">
                                    <span class="text-[9px] text-gray-400 italic">Published: {{ $announcement->created_at->format('M d, Y') }}</span>
                                    <div class="flex gap-2">
                                        @if($announcement->link)
                                            <a href="{{ $announcement->link }}" target="_blank" class="text-[9px] font-black text-blue-800 bg-blue-100 px-3 py-1 rounded-full hover:bg-blue-200 transition uppercase shadow-sm flex items-center gap-1">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg> Link
                                            </a>
                                        @endif
                                        @if($announcement->file_path)
                                            <a href="{{ asset('storage/' . json_decode($announcement->file_path, true)[0]['path'] ?? $announcement->file_path) }}" target="_blank" 
                                               class="text-[9px] font-black text-white bg-[#800000] px-3 py-1 rounded-full hover:bg-red-900 transition uppercase shadow-sm flex items-center gap-1">
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg> File
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center flex flex-col items-center justify-center">
                                <div class="bg-gray-100 p-4 rounded-full mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                </div>
                                <p class="text-gray-500 text-xs font-bold uppercase tracking-widest">No publications found.</p>
                                @if(request()->anyFilled(['search', 'category', 'date']))
                                    <a href="{{ route('profile.edit') }}" class="text-[#800000] text-[10px] font-bold uppercase underline mt-2">Clear all filters</a>
                                @endif
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection