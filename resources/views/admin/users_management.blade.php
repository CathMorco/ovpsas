@extends('layouts.master')

@section('title', 'User Management - OVPSAS')

@section('content')
    <div class="max-w-7xl mx-auto px-6 lg:px-8 space-y-8">

        {{-- Page Header --}}
        <div class="border-b-2 border-gray-800 pb-2 flex justify-between items-end">
            <div>
                <span class="block w-20 h-2 bg-[#800000] mb-2"></span>
                <h2 class="text-3xl font-black text-[#800000] tracking-tight uppercase italic">User Management</h2>
                <p class="text-sm text-gray-500 font-bold uppercase tracking-wider mt-1">Admin Control Panel</p>
            </div>
        </div>

        {{-- Filters (Keeping your Search form) --}}
        <form action="{{ route('users.list') }}" method="GET" class="bg-white p-6 shadow-lg rounded-xl border-t-4 border-[#800000]">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Search Name</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Type name..." class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2 text-sm font-semibold focus:ring-2 focus:ring-[#800000] outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Office</label>
                    <select name="office" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2 text-sm font-semibold focus:ring-2 focus:ring-[#800000] outline-none">
                        <option value="">All Offices</option>
                        @foreach($offices as $office)
                            <option value="{{ $office->id }}" {{ request('office') == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end md:col-span-3">
                    <button type="submit" class="bg-[#800000] text-white px-8 py-2 rounded-lg text-xs font-black uppercase hover:bg-red-900 transition shadow-md tracking-widest">Apply Filters</button>
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
                            <th class="px-6 py-4">Current Role</th>
                            <th class="px-6 py-4">Office / Designation</th>
                            <th class="px-6 py-4 text-center">Manage Role</th>
                            <th class="px-6 py-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($users as $user)
                        
                        {{-- HIERARCHY LOGIC: Admins cannot see other Admins/SuperAdmins --}}
                        @if(auth()->user()->isSuperAdmin() || (!auth()->user()->isSuperAdmin() && !$user->isAdmin() && !$user->isSuperAdmin()))
                        
                        <tr class="hover:bg-yellow-50/50 transition-colors group">
                            <td class="px-6 py-4"><span class="font-black text-gray-800 uppercase text-xs">{{ $user->name }}</span></td>
                            <td class="px-6 py-4 text-gray-500 text-xs font-mono">{{ $user->email }}</td>
                            
                            {{-- Role Badge --}}
                            <td class="px-6 py-4">
                                @if($user->isSuperAdmin())
                                    <span class="bg-black text-white text-[10px] font-black px-2 py-1 rounded border border-gray-800 uppercase">Super Admin</span>
                                @elseif($user->isAdmin())
                                    <span class="bg-red-100 text-red-800 text-[10px] font-black px-2 py-1 rounded border border-red-200 uppercase">Admin</span>
                                @elseif($user->isStaff())
                                    <span class="bg-blue-100 text-blue-800 text-[10px] font-black px-2 py-1 rounded border border-blue-200 uppercase">Staff</span>
                                @else
                                    <span class="bg-gray-100 text-gray-600 text-[10px] font-black px-2 py-1 rounded border border-gray-200 uppercase">Viewer</span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex flex-col leading-tight">
                                    <span class="font-bold text-gray-700 text-xs uppercase">{{ $user->office->code ?? 'N/A' }}</span>
                                    <span class="text-[9px] text-gray-400 font-bold uppercase">{{ $user->designation ?? 'Faculty' }}</span>
                                </div>
                            </td>

                            {{-- ROLE MANAGEMENT DROPDOWN --}}
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('admin.users.updateRole') }}" method="POST" class="flex items-center gap-2 justify-center">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    <select name="role" onchange="this.form.submit()" class="text-[10px] font-black uppercase border-gray-200 rounded py-1 focus:ring-[#800000]">
                                        {{-- Only Super Admin can assign the "Admin" role --}}
                                        @if(auth()->user()->isSuperAdmin())
                                            <option value="Super Admin" {{ $user->role == 'Super Admin' ? 'selected' : '' }}>Super Admin</option>
                                            <option value="Admin" {{ $user->role == 'Admin' ? 'selected' : '' }}>Admin</option>
                                        @endif
                                        <option value="Office Staff" {{ $user->role == 'Office Staff' ? 'selected' : '' }}>Staff</option>
                                        <option value="Viewer" {{ $user->role == 'Viewer' ? 'selected' : '' }}>Viewer</option>
                                    </select>
                                </form>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <button type="button" class="text-gray-400 hover:text-[#800000] font-black text-[10px] uppercase flex items-center justify-center gap-1 mx-auto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    Edit Details
                                </button>
                            </td>
                        </tr>
                        @endif
                        @empty
                        <tr><td colspan="6" class="text-center py-20 italic text-gray-400 uppercase text-xs">No users found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection