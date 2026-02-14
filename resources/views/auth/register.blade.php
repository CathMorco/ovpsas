@extends('layouts.master')

@section('content')
    <div class="flex flex-col justify-center items-center py-12 bg-gray-100 min-h-screen">

        <div class="mb-6 text-center">
             <h2 class="text-3xl font-extrabold text-[#800000]">Create Account</h2>
        </div>

        <div class="w-full sm:max-w-md px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg">

            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Whoops!</strong>
                    <span class="block sm:inline">There were some problems with your input.</span>
                    <ul class="mt-2 text-sm list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-4 mt-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Office</label>
                    <select name="office_id" class="w-full bg-gray-200 border-none rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#800000] @error('office_id') border-2 border-red-500 @enderror">
                        <option value="">Select Office...</option>
                        
                        {{-- Checks if $offices exists to prevent crashing if the controller isn't updated yet --}}
                        @if(isset($offices) && count($offices) > 0)
                            @foreach($offices as $office)
                                <option value="{{ $office->id }}" {{ old('office_id') == $office->id ? 'selected' : '' }}>
                                    {{ $office->name }} {{ $office->code ? '('.$office->code.')' : '' }}
                                </option>
                            @endforeach
                        @else
                            <option value="" disabled>No offices found in database</option>
                        @endif
                    </select>
                    @error('office_id')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Full Name" 
                           class="w-full bg-gray-200 border-none rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#800000] @error('name') border-2 border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Designation</label>
                    <input type="text" name="designation" value="{{ old('designation') }}" placeholder="e.g. Head of Office" 
                           class="w-full bg-gray-200 border-none rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#800000] @error('designation') border-2 border-red-500 @enderror">
                    @error('designation')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="email@pup.edu.ph" 
                           class="w-full bg-gray-200 border-none rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#800000] @error('email') border-2 border-red-500 @enderror">
                    @error('email')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input type="password" name="password" 
                           class="w-full bg-gray-200 border-none rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#800000] @error('password') border-2 border-red-500 @enderror">
                    @error('password')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Confirm Password</label>
                    <input type="password" name="password_confirmation" 
                           class="w-full bg-gray-200 border-none rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#800000]">
                </div>

                <button type="submit" 
                        onclick="this.disabled=true; this.form.submit(); this.innerText='Creating...';"
                        class="w-full bg-[#800000] text-white font-bold py-3 rounded-full hover:bg-red-900 transition duration-300 shadow-md">
                    Create Account
                </button>

                <div class="mt-4 text-center">
                    <a class="text-sm text-gray-600 hover:text-[#800000] underline" href="{{ url('/') }}">
                        Already have an account? Return to Home
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection