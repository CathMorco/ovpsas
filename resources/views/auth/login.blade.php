<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log In - Student Affairs Services and Information System</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased flex flex-col min-h-screen">

    <nav class="w-full bg-[#800000] px-6 py-4 shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <a href="{{ url('/') }}" class="flex items-center gap-4 group">
                <img src="/images/PUPLogo.png" alt="PUP Logo" class="h-14 w-14 rounded-full bg-white p-0.5 border-2 border-white group-hover:scale-105 transition-transform">
                <div class="flex flex-col text-white leading-tight">
                    <span class="font-bold text-lg tracking-wide">Student Affairs</span>
                    <span class="font-light text-sm opacity-90">Services and Information System</span>
                </div>
            </a>

            <div class="hidden md:flex items-center gap-8 text-white font-bold text-sm tracking-wide">
                <a href="{{ url('/') }}" class="hover:text-yellow-300 transition">Home</a>
                <a href="{{ url('/about') }}" class="hover:text-yellow-300 transition">About Us</a>
                <a href="#" class="border-b-2 border-white pb-1 cursor-default">Log in</a>
            </div>

            <div class="relative hidden md:block w-64 ml-4">
    
                <form action="{{ route('search') }}" method="GET" class="w-full">
        
                    <input type="text" 
                        name="query" 
                        value="{{ request('query') }}" 
                        placeholder="Search..." 
                        class="w-full bg-white text-gray-800 rounded-full px-4 py-1.5 pl-4 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
        
                    <button type="submit" class="absolute right-1 top-1/2 transform -translate-y-1/2 bg-[#FCD116] p-1 rounded-full hover:bg-yellow-300 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>

                </form>
            </div>
        </div>
    </nav>

    <main class="flex-grow flex items-center justify-center relative"
          style="background: linear-gradient(rgba(0,0,0,0.1), rgba(0,0,0,0.1)), url('/images/background.jpg'); background-size: cover; background-position: center;">

        <div class="bg-white p-10 md:p-14 rounded-[3rem] shadow-2xl w-full max-w-lg mx-4">

            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600 text-center">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="mb-4 text-sm text-red-600">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-6">
                @csrf

                <div class="space-y-4 mt-2">
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Email Address" required autofocus
                        class="w-full bg-[#E8EDF2] border-none rounded-xl p-4 text-gray-700 placeholder-gray-500 focus:ring-2 focus:ring-[#800000]">

                    <input type="password" name="password" placeholder="Password" required
                        class="w-full bg-[#E8EDF2] border-none rounded-xl p-4 text-gray-700 placeholder-gray-500 focus:ring-2 focus:ring-[#800000]">
                </div>

                <div class="flex items-center ml-2">
                    <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300 text-[#800000] focus:ring-[#800000]">
                    <label for="remember_me" class="ml-2 text-sm text-gray-600">Remember me</label>
                </div>

                <button type="submit" class="w-full bg-[#800000] hover:bg-black text-white font-bold py-4 rounded-full transition-all duration-300 text-lg shadow-lg">
                    Log in
                </button>

                <div class="text-center space-y-3 pt-2">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-gray-800 hover:underline block">Lost password?</a>
                    @endif
                    <p class="text-sm text-gray-800">
                        Don't have an account?
                        <a href="{{ route('register') }}" class="font-bold text-[#800000] hover:underline">Create new account</a>
                    </p>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
