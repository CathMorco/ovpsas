<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $office }} Files - OSAS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased flex flex-col min-h-screen">

    <nav class="bg-[#800000] shadow-md py-4">
        <div class="max-w-7xl mx-auto px-6 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/PUPLogo.png') }}" alt="Logo" class="h-10 w-10 bg-white rounded-full p-0.5">
                <span class="text-white font-bold tracking-wider uppercase text-sm italic">{{ $office }} Document Repository</span>
            </div>
            <a href="{{ url('/') }}" class="text-white text-xs font-bold hover:text-yellow-400 transition uppercase tracking-widest">
                ← Back to Home
            </a>
        </div>
    </nav>

    <main class="flex-grow py-12">
        <div class="max-w-4xl mx-auto px-6">

            @if(session('success'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm" role="alert">
                    <p class="text-xs font-bold uppercase tracking-widest">{{ session('success') }}</p>
                </div>
            @endif

            <div class="mb-8 border-l-4 border-[#FCD116] pl-4">
                <h1 class="text-3xl font-black text-[#800000] uppercase tracking-tighter">
                    Files for <span class="text-gray-700">{{ $office }}</span>
                </h1>
                <p class="text-gray-500 text-xs italic mt-1 font-medium">Documents are organized by their respective categories.</p>
            </div>

            <div class="space-y-10">
                @if($groupedFiles->isEmpty())
                    <div class="bg-white shadow-2xl rounded-xl p-20 text-center border border-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-200 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                        </svg>
                        <p class="text-gray-400 font-bold italic tracking-wide">NO FILES FOUND IN THIS OFFICE</p>
                        <p class="text-gray-400 text-[10px] uppercase">Upload a file in the Announcement Board to see it here.</p>
                    </div>
                @else
                    {{-- LOOP THROUGH CATEGORIES (Folders) --}}
                    @foreach($groupedFiles as $category => $files)

                        {{-- Bulletproof Folder Name Logic --}}
                        @php
                            $folderName = $category;
                            if ($category === 'Others' && $files->isNotEmpty()) {
                                $first = $files->first();
                                $folderName = is_array($first) ? ($first['category'] ?? 'Others') : ($first->custom_category ?? 'Others');
                            }
                        @endphp

                        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
                            <div class="bg-gray-50 px-6 py-3 border-b border-gray-200 flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>
                                </svg>
                                <h2 class="font-black text-[#800000] uppercase tracking-widest text-xs">{{ $folderName }}</h2>
                                <span class="text-[10px] bg-gray-200 text-gray-500 px-2 py-0.5 rounded-full">{{ count($files) }} item(s)</span>
                            </div>

                            {{-- LOOP THROUGH FILES --}}
                            <ul class="divide-y divide-gray-100">
                                @foreach($files as $file)
                                    @php
                                        $isArr = is_array($file);
                                        $f_id = $isArr ? $file['id'] : $file->id;
                                        $f_name = $isArr ? $file['name'] : $file->title;
                                        $f_path = $isArr ? $file['path'] : $file->file_path;
                                        $f_url = $isArr ? $file['url'] : route('file.view', $f_id);
                                        $f_size = $isArr ? $file['size'] : ($f_path ? 'File Attachment' : 'Text Content');
                                        $f_date = $isArr ? $file['date'] : $file->created_at->format('M d, Y');
                                        $f_uploader = $isArr ? $file['uploader'] : ($file->user->name ?? 'Unknown');
                                    @endphp

                                    <li class="p-5 hover:bg-gray-50 transition flex items-center justify-between group">
                                        <div class="flex items-center gap-5">
                                            <div class="p-3 rounded-lg group-hover:text-white transition-all shadow-sm {{ $f_size === 'Text Content' ? 'bg-gray-100 text-gray-500 group-hover:bg-gray-500' : 'bg-gray-100 text-[#800000] group-hover:bg-[#800000]' }}">
                                                @if($f_size === 'Text Content')
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                                @endif
                                            </div>
                                            
                                            <div>
                                                <p class="font-bold text-gray-800 text-sm tracking-wide uppercase">{{ $f_name }}</p>
                                                <div class="flex items-center gap-3 mt-1">
                                                    <span class="text-[9px] font-bold uppercase tracking-tighter {{ $f_size === 'Text Content' ? 'text-gray-400' : 'text-[#800000]' }}">{{ $f_size }}</span>
                                                    <span class="text-gray-300">•</span>
                                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">{{ $f_date }}</span>
                                                    <span class="text-gray-300">•</span>
                                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">By: {{ $f_uploader }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <a href="{{ $f_url }}" target="_blank" class="bg-gray-800 text-white px-3 py-1.5 rounded font-black text-[9px] hover:bg-black transition uppercase tracking-widest">
                                                View
                                            </a>

                                            @auth
                                                <form action="{{ route('files.destroy') }}" method="POST" onsubmit="return confirm('Permanently delete this file?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    {{-- THIS IS THE FIX: Passing the ID so the controller knows what to delete in the Database --}}
                                                    <input type="hidden" name="id" value="{{ $f_id }}">
                                                    <input type="hidden" name="file_path" value="{{ $f_path }}">
                                                    <button type="submit" class="bg-red-600 text-white px-3 py-1.5 rounded font-black text-[9px] hover:bg-red-800 transition uppercase tracking-widest">
                                                        Delete
                                                    </button>
                                                </form>
                                            @endauth
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </main>

    <footer class="py-8 text-center border-t border-gray-200">
        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
            &copy; {{ date('Y') }} Office of the Vice President for Student Affairs and Services
        </p>
    </footer>
</body>
</html>