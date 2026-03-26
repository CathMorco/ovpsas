@extends('layouts.master')

@section('title', 'Pending Approvals - OVPSAS')

@section('content')
<div class="max-w-7xl mx-auto px-6 lg:px-8 py-8 space-y-8">

    {{-- Alert Section --}}
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md mb-6 animate-pulse">
            <p class="font-bold uppercase text-xs tracking-widest">System Update</p>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif

    {{-- HEADER SECTION --}}
    <div class="flex items-center gap-3 border-b-2 border-gray-800 pb-2">
        <span class="w-3 h-7 bg-yellow-400"></span>
        <h2 class="text-2xl font-black text-[#800000] tracking-tight uppercase italic">Admission Queue</h2>
    </div>

    {{-- PENDING TABLE --}}
    <div class="bg-white shadow-2xl rounded-xl overflow-hidden border border-gray-200">
        <div class="p-4 bg-gray-800 flex justify-between items-center">
            <span class="text-[10px] font-black text-white uppercase tracking-widest">Registrations Awaiting Verification</span>
            <span class="text-[10px] font-black bg-yellow-400 text-[#800000] px-2 py-1 rounded-full uppercase">
                {{ $pendingUsers->count() }} Total Pending
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-[11px]">
                <thead class="bg-gray-50 text-gray-500 font-black uppercase tracking-wider border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Faculty Member</th>
                        <th class="px-6 py-4">Contact Information</th>
                        <th class="px-6 py-4">Registration Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pendingUsers as $user)
                    <tr class="hover:bg-yellow-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-[#800000]/10 flex items-center justify-center text-[#800000] font-black border border-[#800000]/20 uppercase">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-black text-gray-900 uppercase text-sm leading-tight">{{ $user->name }} {{ $user->suffix }}</p>
                                    <p class="text-[9px] text-gray-400 font-bold uppercase tracking-tighter">{{ $user->designation ?? 'Faculty/Staff' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-gray-700 font-bold">{{ $user->email }}</p>
                            <p class="text-gray-400">{{ $user->phone ?? 'No Phone Provided' }}</p>
                        </td>
                        <td class="px-6 py-4 text-gray-500 italic">
                            {{ $user->created_at->format('M d, Y') }}<br>
                            <span class="text-[9px] font-bold uppercase tracking-tighter">{{ $user->created_at->diffForHumans() }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-3">
                                {{-- APPROVE BUTTON --}}
                                <form action="{{ route('admin.users.approve', $user->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-[#800000] text-white px-5 py-2 rounded-lg text-[10px] font-black uppercase hover:bg-red-800 transition shadow-sm transform active:scale-95">
                                        Admit User
                                    </button>
                                </form>

                                {{-- DECLINE BUTTON --}}
                                <form action="{{ route('admin.users.decline', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this registration request?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="bg-gray-100 text-gray-400 px-5 py-2 rounded-lg text-[10px] font-black uppercase hover:bg-red-600 hover:text-white transition shadow-sm">
                                        Decline
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center opacity-30">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="italic text-sm uppercase font-black tracking-widest text-gray-400">Queue is Clear</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection