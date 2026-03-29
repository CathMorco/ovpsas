@extends('layouts.master')

@section('title', 'User Management - OVPSAS Portal')

@section('content')
<div class="max-w-7xl mx-auto px-6 lg:px-8 py-8 space-y-8">
    <div class="border-b-2 border-gray-800 pb-2 flex justify-between items-end">
        <div>
            <span class="block w-20 h-2 bg-[#800000] mb-2"></span>
            <h2 class="text-3xl font-black text-[#800000] uppercase italic">User Management</h2>
            <p class="text-sm text-gray-500 font-bold uppercase tracking-wider mt-1">Super Admin Control Panel</p>
        </div>
    </div>

    <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-800 text-white uppercase text-[11px] tracking-wider font-bold">
                    <tr>
                        <th class="px-6 py-4">Name & Request Status</th>
                        <th class="px-6 py-4">Current Role</th>
                        <th class="px-6 py-4">Office / Designation</th>
                        <th class="px-6 py-4 text-center">Manage Role</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($users as $user)
                    <tr class="hover:bg-yellow-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-black text-gray-800 uppercase text-xs">{{ $user->name }}</span>
                                @if($user->requesting_admin)
                                    <span class="mt-1 inline-flex items-center gap-1.5 text-[8px] font-black text-blue-600 uppercase animate-pulse">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span> 
                                        Promotion Requested
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-[10px] font-black uppercase px-2 py-1 rounded {{ $user->isSuperAdmin() ? 'bg-black text-white' : 'bg-gray-100 text-gray-600' }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-bold text-gray-700 text-xs">{{ $user->office->code ?? 'N/A' }}</p>
                            <p class="text-[9px] text-gray-400 font-bold uppercase">{{ $user->designation ?? 'Faculty' }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('admin.users.updateRole') }}" method="POST">
                                @csrf @method('PUT')
                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                <select name="role" onchange="this.form.submit()" class="text-[10px] font-black uppercase border-gray-200 rounded py-1 focus:ring-[#800000]">
                                    <option value="Super Admin" {{ $user->role == 'Super Admin' ? 'selected' : '' }}>Super Admin</option>
                                    <option value="Admin" {{ $user->role == 'Admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="Office Staff" {{ $user->role == 'Office Staff' ? 'selected' : '' }}>Staff</option>
                                    <option value="Viewer" {{ $user->role == 'Viewer' ? 'selected' : '' }}>Viewer</option>
                                </select>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button class="text-gray-400 hover:text-[#800000] font-black text-[10px] uppercase">Edit</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection