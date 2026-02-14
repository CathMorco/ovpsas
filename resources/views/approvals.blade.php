@extends('layouts.master')

@section('title', 'Admin Controls - OVPSAS')

@section('content')
    <div class="max-w-7xl mx-auto px-6 lg:px-8 space-y-10">

        {{-- Success/Error Notifications --}}
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm">
                <p class="font-bold">Success</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
                <p class="font-bold">Error</p>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        {{-- SECTION 1: ROLE MANAGEMENT (Search & Edit) --}}
        <div class="space-y-6">
            <div class="flex items-center gap-2 border-b-2 border-gray-800 pb-1">
                <span class="w-2 h-6 bg-yellow-500"></span>
                <h2 class="text-xl font-black text-gray-800 tracking-tight uppercase italic">Role Management</h2>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                
                {{-- Search Box --}}
                <div class="md:col-span-1 bg-white shadow-md rounded-xl p-6 border-t-4 border-yellow-500 h-fit">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Find User</h3>
                    <form action="{{ route('admin.approvals') }}" method="GET" class="space-y-4">
                        <div>
                            <label class="text-[10px] font-bold uppercase text-gray-700">Email Address</label>
                            <input type="email" name="search_email" value="{{ request('search_email') }}" placeholder="Enter user email..." class="w-full mt-1 px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-yellow-500 outline-none" required>
                        </div>
                        <button type="submit" class="w-full bg-gray-800 text-white py-2 rounded-lg text-xs font-black uppercase hover:bg-black transition">
                            Search User
                        </button>
                    </form>
                </div>

                {{-- Edit Card (Shows only if search finds someone) --}}
                <div class="md:col-span-2">
                    @if(request('search_email') && !$searchedUser)
                        <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded-xl shadow-sm h-full flex items-center justify-center">
                            <div class="text-center text-red-700">
                                <p class="font-bold text-lg">User Not Found</p>
                                <p class="text-sm">No user found with email: <strong>{{ request('search_email') }}</strong></p>
                            </div>
                        </div>
                    @elseif($searchedUser)
                        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
                            <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center">
                                <div>
                                    <h3 class="text-lg font-black text-[#800000] uppercase">{{ $searchedUser->name }}</h3>
                                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">{{ $searchedUser->designation }} • {{ $searchedUser->office->name ?? 'No Office' }}</p>
                                </div>
                                <div class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded text-[10px] font-black uppercase border border-yellow-200">
                                    Current: {{ $searchedUser->role }}
                                </div>
                            </div>
                            
                            <div class="p-6">
                                <form action="{{ route('admin.users.updateRole') }}" method="POST" class="flex items-end gap-4">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="user_id" value="{{ $searchedUser->id }}">
                                    
                                    <div class="flex-grow">
                                        <label class="text-[10px] font-bold uppercase text-gray-400">Assign New Role</label>
                                        <select name="role" class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 focus:ring-2 focus:ring-[#800000] outline-none">
                                            <option value="viewer" {{ $searchedUser->role == 'viewer' ? 'selected' : '' }}>Viewer (Read Only)</option>
                                            <option value="staff" {{ $searchedUser->role == 'staff' ? 'selected' : '' }}>Staff (Upload & Comment)</option>
                                            <option value="admin" {{ $searchedUser->role == 'admin' ? 'selected' : '' }}>Admin (Full Access)</option>
                                        </select>
                                    </div>

                                    <button type="submit" class="bg-[#800000] text-white px-6 py-2.5 rounded-lg text-xs font-black uppercase hover:bg-red-900 transition shadow-md">
                                        Apply Changes
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        {{-- Empty State --}}
                        <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-xl h-full flex items-center justify-center p-10 opacity-50">
                            <p class="text-gray-400 text-xs uppercase font-bold">Search for a user to manage their role</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>


        {{-- SECTION 2: PENDING APPROVALS --}}
        <div class="space-y-6">
            <div class="flex items-center gap-2 border-b-2 border-gray-800 pb-1">
                <span class="w-2 h-6 bg-[#800000]"></span>
                <h2 class="text-xl font-black text-[#800000] tracking-tight uppercase italic">Pending Registrations</h2>
            </div>

            <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200">
                <div class="p-4 bg-gray-800 flex justify-between items-center">
                    <span class="text-[10px] font-black text-white uppercase tracking-widest">Accounts Awaiting Approval</span>
                </div>

                <div class="overflow-x-auto">
                    @if($pendingUsers->isEmpty())
                        <div class="text-center py-10 bg-white">
                            <p class="text-gray-400 italic text-sm">No pending registrations at the moment.</p>
                        </div>
                    @else
                        <table class="w-full text-left text-[11px]">
                            <thead class="bg-gray-50 text-gray-500 font-black uppercase tracking-wider border-b border-gray-100">
                                <tr>
                                    <th class="px-4 py-3">Name</th>
                                    <th class="px-4 py-3">Email</th>
                                    <th class="px-4 py-3">Date Registered</th>
                                    <th class="px-4 py-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($pendingUsers as $user)
                                <tr class="hover:bg-yellow-50/50 transition-colors group">
                                    <td class="px-4 py-3">
                                        <span class="font-black text-gray-800 uppercase leading-tight">{{ $user->name }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 italic">
                                        {{ $user->created_at->format('M d, Y h:i A') }}
                                    </td>
                                    <td class="px-4 py-3 text-right flex justify-end gap-2">
                                        {{-- Approve --}}
                                        <form action="{{ route('admin.users.approve', $user->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="bg-[#800000] text-white px-4 py-1.5 rounded-full text-[10px] font-black uppercase hover:bg-red-800 transition shadow-sm">
                                                Approve
                                            </button>
                                        </form>
                                        {{-- Decline --}}
                                        <form action="{{ route('admin.users.decline', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to decline and delete this user?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-gray-200 text-gray-600 px-4 py-1.5 rounded-full text-[10px] font-black uppercase hover:bg-red-500 hover:text-white transition shadow-sm">
                                                Decline
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

    </div>
@endsection