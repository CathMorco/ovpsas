@extends('layouts.master')

@section('title', 'Search Results')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-2xl font-bold text-[#800000] mb-6">Search Results for: "<span class="italic">{{ $query }}</span>"</h1>

    {{-- SECTION 1: FILES & ANNOUNCEMENTS --}}
    <div class="mb-10">
        <h2 class="text-xl font-bold text-gray-800 border-b border-gray-200 pb-2 mb-4">Files & Announcements</h2>
        @if($announcements->isEmpty())
            <p class="text-gray-500 italic">No files found.</p>
        @else
            <div class="grid gap-4">
                @foreach($announcements as $file)
                    <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-[#FCD116] flex justify-between items-center">
                        <div>
                            <h3 class="font-bold text-[#800000]">{{ $file->title ?? 'Untitled Document' }}</h3>
                            
                            <p class="text-xs text-gray-500 uppercase font-semibold">
                                {{ is_array($file->office) ? implode(', ', $file->office) : $file->office }} 
                                • 
                                {{ is_array($file->category) ? implode(', ', $file->category) : $file->category }}
                            </p>
                            
                            <p class="text-sm text-gray-700 mt-1">{{ Str::limit($file->content, 100) }}</p>

                            {{-- NEW: Show attached filenames below the result! --}}
                            @php
                                $attachedFiles = json_decode($file->file_path, true);
                            @endphp
                            @if(is_array($attachedFiles) && count($attachedFiles) > 0)
                                <div class="mt-2 space-y-1">
                                    @foreach($attachedFiles as $f)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 border border-gray-200 rounded text-[9px] font-bold text-gray-600 uppercase">
                                            📄 {{ $f['original_name'] ?? 'Document' }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                        </div>
                        <div class="flex gap-2">
                            @if($file->link)
                                <a href="{{ $file->link }}" target="_blank" class="bg-blue-100 text-blue-800 px-4 py-2 rounded text-xs font-bold uppercase hover:bg-blue-200 transition flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg> Link
                                </a>
                            @endif
                            @if($file->file_path)
                                <a href="{{ route('file.view', $file->id) }}" class="bg-[#800000] text-white px-4 py-2 rounded text-xs font-bold uppercase hover:bg-red-900 transition">View File</a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- SECTION 2: PEOPLE --}}
    <div>
        <h2 class="text-xl font-bold text-gray-800 border-b border-gray-200 pb-2 mb-4">People</h2>
        @if($users->isEmpty())
            <p class="text-gray-500 italic">No users found.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($users as $user)
                    <div class="flex items-center gap-3 bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-[#800000] font-bold">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-bold text-gray-800 text-sm">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                            <p class="text-[10px] text-[#800000] uppercase font-bold mt-1">{{ $user->designation ?? 'Staff' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection