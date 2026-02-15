@extends('layouts.master')

@section('title', 'Staff Directory')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    
    {{-- Header & Search Section --}}
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4 border-b border-gray-200 pb-6">
        <div>
            <h1 class="text-3xl font-black text-[#800000] uppercase tracking-tight italic">Staff Directory</h1>
            <p class="text-sm text-gray-500 font-bold uppercase tracking-widest mt-1">Contact Information</p>
        </div>

        {{-- Filter Form --}}
        <form method="GET" action="{{ route('directory.index') }}" class="flex flex-col sm:flex-row w-full md:w-auto gap-2">
            
            {{-- Office Dropdown --}}
            <select name="office_id" class="border-gray-300 rounded-lg text-sm focus:ring-[#800000] focus:border-[#800000] font-semibold" onchange="this.form.submit()">
                <option value="">All Offices</option>
                @foreach($offices as $office)
                    <option value="{{ $office->id }}" {{ request('office_id') == $office->id ? 'selected' : '' }}>
                        {{ $office->code ?? $office->name }}
                    </option>
                @endforeach
            </select>

            {{-- Search Bar --}}
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email..." 
                       class="border-gray-300 rounded-lg text-sm w-full sm:w-64 pl-4 pr-10 focus:ring-[#800000] focus:border-[#800000] font-semibold">
                <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-[#800000]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
            </div>
            
            @if(request()->anyFilled(['search', 'office_id']))
                <a href="{{ route('directory.index') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm font-bold hover:bg-gray-200 transition text-center">Clear</a>
            @endif
        </form>
    </div>

    {{-- User Grid (Cards) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($users as $user)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg transition-shadow duration-300 overflow-hidden group flex flex-col">
                {{-- Decorative Top --}}
                <div class="h-2 bg-[#800000] w-full"></div> 
                
                <div class="p-6 flex flex-col items-center text-center flex-grow">
                    
                    {{-- Avatar --}}
                    <div class="w-24 h-24 rounded-full bg-gray-50 mb-4 flex items-center justify-center overflow-hidden border-4 border-white shadow-md ring-1 ring-gray-100">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-3xl font-black text-[#800000]">{{ substr($user->name, 0, 1) }}</span>
                        @endif
                    </div>

                    {{-- Name & Role --}}
                    <h3 class="font-bold text-gray-900 text-lg leading-tight mb-1">{{ $user->name }}</h3>
                    <p class="text-xs text-[#800000] font-bold uppercase tracking-wider mb-4">{{ $user->designation ?? 'Staff' }}</p>
                    
                    {{-- Divider --}}
                    <div class="w-full border-t border-dashed border-gray-200 my-2"></div>

                    {{-- Details --}}
                    <div class="text-sm text-gray-600 space-y-2 w-full text-left mt-2">
                        {{-- Office --}}
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center flex-shrink-0 text-[#800000]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                            <span class="font-semibold text-gray-700 truncate" title="{{ $user->office->name ?? 'N/A' }}">
                                {{ $user->office->code ?? 'No Office' }}
                            </span>
                        </div>

                        {{-- Email --}}
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center flex-shrink-0 text-[#800000]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <a href="mailto:{{ $user->email }}" class="truncate hover:text-[#800000] hover:underline text-xs font-mono">
                                {{ $user->email }}
                            </a>
                        </div>
                        
                        {{-- Phone (Optional) --}}
                        @if($user->phone)
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center flex-shrink-0 text-[#800000]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </div>
                            <span class="text-xs font-mono">{{ $user->phone }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-20 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <p class="text-gray-500 font-bold">No staff found matching your criteria.</p>
                <a href="{{ route('directory.index') }}" class="text-[#800000] text-sm hover:underline mt-2 inline-block">Reset Filters</a>
            </div>
        @endforelse
    </div>

    {{-- Pagination Links --}}
    <div class="mt-10">
        {{ $users->withQueryString()->links() }}
    </div>
</div>
@endsection