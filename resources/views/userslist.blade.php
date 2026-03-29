@extends('layouts.master')

@section('title', 'User Directory - OVPSAS Portal')

@section('content')
    <div class="max-w-7xl mx-auto px-6 lg:px-8 space-y-8">

        {{-- Page Header --}}
        <div class="border-b-2 border-gray-800 pb-2 flex justify-between items-end">
            <div>
                <span class="block w-20 h-2 bg-[#800000] mb-2"></span>
                <h2 class="text-3xl font-black text-[#800000] tracking-tight uppercase italic">User Directory</h2>
                <p class="text-sm text-gray-500 font-bold uppercase tracking-wider mt-1">Manage All Registered Accounts</p>
            </div>
        </div>

        {{-- Search & Filter Form --}}
        <form action="{{ route('users.list') }}" method="GET" class="bg-white p-6 shadow-lg rounded-xl border-t-4 border-[#800000]">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                {{-- Search Name --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Search Name</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Type name..." 
                           class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2 text-sm font-semibold focus:ring-2 focus:ring-[#800000] outline-none">
                </div>

                {{-- Filter Office --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Office</label>
                    <select name="office" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2 text-sm font-semibold focus:ring-2 focus:ring-[#800000] outline-none">
                        <option value="">All Offices</option>
                        @foreach($offices as $office)
                            <option value="{{ $office->id }}" {{ request('office') == $office->id ? 'selected' : '' }}>
                                {{ $office->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Role --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Role</label>
                    <select name="role" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2 text-sm font-semibold focus:ring-2 focus:ring-[#800000] outline-none">
                        <option value="">All Roles</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="viewer" {{ request('role') == 'viewer' ? 'selected' : '' }}>Viewer</option>
                    </select>
                </div>

                {{-- Filter Status --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Status</label>
                    <select name="status" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2 text-sm font-semibold focus:ring-2 focus:ring-[#800000] outline-none">
                        <option value="">All Status</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>

                {{-- Apply Button --}}
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-[#800000] text-white py-2 rounded-lg text-xs font-black uppercase hover:bg-red-900 transition shadow-md tracking-widest">
                        Apply Filters
                    </button>
                </div>
            </div>
        </form>

        {{-- Users Table --}}
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-800 text-white uppercase text-[11px] tracking-wider font-bold">
                        <tr>
                            <th class="px-6 py-4">Name</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4">Role</th>
                            <th class="px-6 py-4">Office & Designation</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            {{-- Only Admins see Actions --}}
                            @if(auth()->user()->role === 'admin')
                                <th class="px-6 py-4 text-center">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($users as $user)
                        <tr class="hover:bg-yellow-50/50 transition-colors group">
                            
                            {{-- Name --}}
                            <td class="px-6 py-4">
                                <span class="font-black text-gray-800 uppercase text-xs">{{ $user->name }}</span>
                            </td>

                            {{-- Email --}}
                            <td class="px-6 py-4 text-gray-500 text-xs font-mono">
                                {{ $user->email }}
                            </td>

                            {{-- Role Badge --}}
                            <td class="px-6 py-4">
                                @if($user->role === 'admin')
                                    <span class="bg-red-100 text-red-800 text-[10px] font-black px-2 py-1 rounded border border-red-200 uppercase">Admin</span>
                                @elseif($user->role === 'staff')
                                    <span class="bg-blue-100 text-blue-800 text-[10px] font-black px-2 py-1 rounded border border-blue-200 uppercase">Staff</span>
                                @else
                                    <span class="bg-gray-100 text-gray-600 text-[10px] font-black px-2 py-1 rounded border border-gray-200 uppercase">Viewer</span>
                                @endif
                            </td>

                            {{-- Office --}}
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-700 text-xs">{{ $user->office->name ?? 'No Office' }}</span>
                                    <span class="text-[10px] text-gray-400 uppercase">{{ $user->designation ?? 'N/A' }}</span>
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4 text-center">
                                @if($user->status === 'approved')
                                    <span class="inline-flex items-center gap-1 text-green-600 font-bold text-[10px] uppercase bg-green-50 px-2 py-1 rounded-full border border-green-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-yellow-600 font-bold text-[10px] uppercase bg-yellow-50 px-2 py-1 rounded-full border border-yellow-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 animate-pulse"></span> Pending
                                    </span>
                                @endif
                            </td>

                            {{-- Actions (Edit Button) --}}
                            @if(auth()->user()->role === 'admin')
                            <td class="px-6 py-4 text-center">
                                {{-- We use a simple JS toggle or a modal here normally. For now, a simple Edit Button --}}
                                <button type="button" class="text-gray-400 hover:text-[#800000] transition font-bold text-[10px] uppercase flex items-center justify-center gap-1 mx-auto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Edit
                                </button>
                            </td>
                            @endif

                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-10">
                                <p class="text-gray-400 italic text-sm">No users found matching your criteria.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection