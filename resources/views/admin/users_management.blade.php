@extends('layouts.master')

@section('title', 'User Management - OVPSAS')

@section('content')
<div class="max-w-7xl mx-auto px-6 lg:px-8 py-8">
    <div class="bg-white shadow-xl rounded-xl overflow-hidden border-t-4 border-[#800000]">
        <div class="p-6 bg-gray-50 border-b flex justify-between items-center">
            <h2 class="text-xl font-black text-[#800000] uppercase tracking-tight">Faculty & Staff Management</h2>
            <span class="text-xs font-bold bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full uppercase">Official Directory</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-800 text-white text-[10px] uppercase tracking-widest">
                        <th class="px-6 py-4">Name / Designation</th>
                        <th class="px-6 py-4">Office</th>
                        <th class="px-6 py-4">System Role</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($users as $user)
                    <tr class="hover:bg-red-50 transition group">
                        <td class="px-6 py-4">
                            <p class="font-black text-gray-900 uppercase text-sm">{{ $user->name }}</p>
                            <p class="text-[10px] text-gray-500 font-bold uppercase">{{ $user->designation ?? 'No Designation Set' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold text-[#800000]">{{ $user->office->code ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if(auth()->user()->isSuperAdmin())
                                {{-- Super Admin Role Switcher --}}
                                <form action="{{ route('admin.users.updateRole') }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    <select name="role" onchange="this.form.submit()" class="text-[10px] font-bold uppercase border-gray-200 rounded focus:ring-[#800000] focus:border-[#800000] py-1">
                                        <option value="Viewer" {{ $user->role == 'Viewer' ? 'selected' : '' }}>Viewer</option>
                                        <option value="Office Staff" {{ $user->role == 'Office Staff' ? 'selected' : '' }}>Office Staff</option>
                                        <option value="Admin" {{ $user->role == 'Admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="Super Admin" {{ $user->role == 'Super Admin' ? 'selected' : '' }}>Super Admin</option>
                                    </select>
                                </form>
                            @else
                                <span class="text-[10px] font-black uppercase text-gray-600 px-2 py-1 bg-gray-100 rounded">{{ $user->role }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            {{-- Button to open a separate Modal for updating Designation/Office --}}
                            <button @click="/* Open Edit Modal Logic */" class="text-[10px] bg-[#800000] text-white px-4 py-1.5 rounded font-black uppercase hover:bg-red-900 transition">Edit Details</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection