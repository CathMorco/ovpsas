@extends('layouts.master')

@section('content')
    <div class="flex flex-col justify-center items-center py-12 bg-gray-100">

        <div class="mb-6 text-center">
             <h2 class="text-3xl font-extrabold text-[#800000]">Create Account</h2>
        </div>

        <div class="w-full sm:max-w-md px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg">
            
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-4 mt-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Office</label>
                    <select name="office_id" class="w-full bg-gray-200 border-none rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#800000]">
                        <option value="">Select Office...</option>
                        <option value="1">OSFA</option>
                        <option value="2">Guidance</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                    <input type="text" name="name" placeholder="Full Name" class="w-full bg-gray-200 border-none rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#800000]">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Designation</label>
                    <input type="text" name="designation" placeholder="e.g. Head of Office" class="w-full bg-gray-200 border-none rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#800000]">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input type="email" name="email" placeholder="email@pup.edu.ph" class="w-full bg-gray-200 border-none rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#800000]">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input type="password" name="password" class="w-full bg-gray-200 border-none rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#800000]">
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="w-full bg-gray-200 border-none rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#800000]">
                </div>

                <button type="submit" class="w-full bg-[#800000] text-white font-bold py-3 rounded-full hover:bg-red-900 transition duration-300">
                    Create Account
                </button>

                <div class="mt-4 text-center">
                    <a class="text-sm text-gray-600 hover:text-[#800000]" href="{{ route('login') }}">
                        Already have an account? Log in
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection