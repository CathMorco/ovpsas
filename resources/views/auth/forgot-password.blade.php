<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password - Student Affairs Services and Information System</title>

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
                <a href="{{ route('login') }}" class="hover:text-yellow-300 transition">Log in</a>
            </div>

            <div class="relative hidden md:block w-64 ml-4">
                <form action="#" method="GET" class="w-full">
                    <input type="text" placeholder="Search..."
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

        <div class="bg-white p-10 md:p-14 rounded-[3rem] shadow-2xl w-full max-w-lg mx-4 text-center">

            <h2 class="text-xl font-semibold text-gray-800 mb-2">Forgot Password?</h2>
            <p class="text-sm text-gray-600 mb-8 leading-relaxed">
                Just let us know your email address and we will email you a password reset link.
            </p>

            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600 border border-green-200 bg-green-50 p-3 rounded-xl">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 text-sm text-red-600 bg-red-50 p-3 rounded-xl border border-red-200">
                    <ul class="list-none">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-6">
                @csrf

                <div class="mt-2 text-left">
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Email Address" required autofocus
                        class="w-full bg-[#E8EDF2] border-none rounded-xl p-4 text-gray-700 placeholder-gray-500 focus:ring-2 focus:ring-[#800000]">
                </div>

                <button type="submit" class="w-full bg-[#800000] hover:bg-black text-white font-bold py-4 rounded-full transition-all duration-300 text-lg shadow-lg uppercase tracking-wide">
                    Email Password Reset Link
                </button>

                <div class="pt-2">
                    <a href="{{ route('login') }}" class="text-sm font-bold text-[#800000] hover:underline">
                        &larr; Back to Log in
                    </a>
                </div>
            </form>
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-4 text-center text-xs">
        &copy; {{ date('Y') }} OSAS. Polytechnic University of the Philippines.
    </footer>
</body>
</html>
