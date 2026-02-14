@extends('layouts.master')

@section('title', 'Account Settings - OVPSAS')

@section('content')
<div class="pt-2 pb-12 bg-[#F3F4F6] min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
        
        {{-- Header Section --}}
        <div class="mb-0">
            <h2 class="font-black text-xl text-[#800000] leading-tight uppercase italic tracking-wider">
                {{ __('Account Settings') }}
            </h2>
            <hr class="border-gray-300 mt-1">
        </div>

        {{-- 1. Profile Information (Avatar, Name, Email) --}}
        <div class="bg-white p-8 rounded-2xl shadow-sm border-l-8 border-[#800000]">
            @include('profile.partials.update-profile-information-form')
        </div>

        {{-- 2. Update Password --}}
        {{-- The ID here enables the automatic scroll after saving --}}
        <div id="update-password" class="bg-white p-8 rounded-2xl shadow-sm border-l-8 border-[#800000]">
            @include('profile.partials.update-password-form')
        </div>

        {{-- 3. Delete Account --}}
        <div class="bg-white p-8 rounded-2xl shadow-sm border-l-8 border-[#800000]">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection