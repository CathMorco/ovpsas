@extends('layouts.master')

@section('title', 'My Profile - OVPSAS')

@section('content')
    @vite(['resources/css/user_profile/style.css'])

    <div class="bg-white min-h-screen flex flex-col">
        
        <div class="flex-1 w-full max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-12 gap-8 px-4 sm:px-6 lg:px-8 py-10">
            
            <div class="md:col-span-4 flex flex-col items-center flex-shrink-0">
                
                <div class="w-52 h-52 rounded-full overflow-hidden border border-gray-100 shadow-xl mb-4 bg-[#800000] flex items-center justify-center text-white text-5xl font-black">
                    {{ substr($user->name, 0, 1) }}
                </div>
                
                <div class="text-center mb-6">
                    <h2 class="text-3xl font-extrabold text-gray-900 block mb-1">{{ $user->name }}</h2>
                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide">{{ $user->designation ?? 'User' }}</p>
                </div>

                <div class="w-full max-w-sm flex flex-col pb-2">
                    <a href="{{ route('settings.edit') }}" class="w-full bg-[#800000] text-white py-2.5 rounded-lg font-bold text-center hover:bg-red-900 transition shadow-sm text-xs mb-4">
                        Manage your account
                    </a>

                    <div class="w-full p-6 bg-white border border-gray-100 rounded-xl shadow-md ring-1 ring-black/5">
                        <div class="space-y-3">
                            <h4 class="text-[#800000] text-xs font-extrabold tracking-widest uppercase border-b border-gray-50 pb-2 mb-3">About</h4>
                            <div class="flex items-start gap-4 text-sm text-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 text-[#800000] shrink-0">
                                    <path fill-rule="evenodd" d="M4.5 2.25a.75.75 0 0 0 0 1.5v16.5h-.75a.75.75 0 0 0 0 1.5h16.5a.75.75 0 0 0 0-1.5h-.75V3.75a.75.75 0 0 0 0-1.5h-15ZM9 6a.75.75 0 0 0 0 1.5h1.5a.75.75 0 0 0 0-1.5H9Zm-.75 3.75A.75.75 0 0 1 9 9h1.5a.75.75 0 0 1 0 1.5H9a.75.75 0 0 1-.75-.75ZM9 12a.75.75 0 0 0 0 1.5h1.5a.75.75 0 0 0 0-1.5H9Zm3.75-5.25A.75.75 0 0 1 13.5 6H15a.75.75 0 0 1 0 1.5h-1.5a.75.75 0 0 1-.75-.75ZM13.5 9a.75.75 0 0 0 0 1.5H15A.75.75 0 0 0 15 9h-1.5Zm-.75 3.75a.75.75 0 0 1 .75-.75H15a.75.75 0 0 1 0 1.5h-1.5a.75.75 0 0 1-.75-.75ZM9 19.5v-2.25a.75.75 0 0 1 .75-.75h4.5a.75.75 0 0 1 .75.75v2.25a.75.75 0 0 1-.75.75h-4.5A.75.75 0 0 1 9 19.5Z" clip-rule="evenodd" />
                                </svg>
                                <p>{{ $user->office->name ?? 'Office of Student Services' }}</p>
                            </div>
                            <div class="flex items-center gap-4 text-sm text-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 text-[#800000] shrink-0">
                                    <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                                </svg>
                                <p>{{ $user->role ?? 'Staff' }}</p>
                            </div>
                        </div>

                        <div class="space-y-3 mt-6">
                            <h4 class="text-[#800000] text-xs font-extrabold tracking-widest uppercase border-b border-gray-50 pb-2 mb-3">Contact</h4>
                            <div class="flex items-center gap-4 text-sm text-gray-700 italic">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 text-[#800000] shrink-0">
                                    <path fill-rule="evenodd" d="M1.5 4.5a3 3 0 0 1 3-3h1.372c.86 0 1.61.586 1.819 1.42l1.105 4.423a1.875 1.875 0 0 1-.694 1.955l-1.293.97c-.135.101-.164.249-.126.352a11.285 11.285 0 0 0 6.697 6.697c.103.038.25.009.352-.126l.97-1.293a1.875 1.875 0 0 1 1.955-.694l4.423 1.105c.834.209 1.42.959 1.42 1.82V19.5a3 3 0 0 1-3 3h-2.25C8.552 22.5 1.5 15.448 1.5 6.75V4.5Z" clip-rule="evenodd" />
                                </svg>
                                <p>Office Telephone Number</p>
                            </div>
                            <div class="flex items-center gap-4 text-sm text-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 text-[#800000] shrink-0">
                                    <path d="M1.5 8.67v8.58a3 3 0 0 0 3 3h15a3 3 0 0 0 3-3V8.67l-8.928 5.493a3 3 0 0 1-3.144 0L1.5 8.67Z" />
                                    <path d="M22.5 6.908V6.75a3 3 0 0 0-3-3h-15a3 3 0 0 0-3 3v.158l9.714 5.978a1.5 1.5 0 0 0 1.572 0L22.5 6.908Z" />
                                </svg>
                                <p>{{ $user->email }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="md:col-span-8 flex flex-col space-y-6">
                <div class="bg-white shadow-lg rounded-lg border-l-8 border-[#800000] flex flex-col overflow-hidden">
                    <div class="bg-[#800000] p-4 flex items-center gap-3">
                        <span class="w-1.5 h-6 bg-[#FCD116]"></span>
                        <h3 class="text-white font-bold tracking-widest uppercase text-sm">Recent Files</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        @foreach(range(1,3) as $i)
                        <div class="flex items-center gap-3 text-sm text-gray-700 hover:text-[#800000] cursor-pointer group">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#800000]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                            <div class="flex flex-col">
                                <span class="font-bold">EO No. 2224 - 2295.pdf</span>
                                <span class="text-[10px] text-gray-400 uppercase">Published at 2:30 PM</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white shadow-lg rounded-lg border-l-8 border-[#800000] flex flex-col overflow-hidden">
                    <div class="bg-[#800000] p-4 flex items-center gap-3">
                        <span class="w-1.5 h-6 bg-[#FCD116]"></span>
                        <h3 class="text-white font-bold tracking-widest uppercase text-sm">Published Announcements</h3>
                    </div>
                    <div class="p-16 flex items-center justify-center">
                        <p class="text-gray-300 font-black italic tracking-widest uppercase text-sm">No announcements published yet.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection