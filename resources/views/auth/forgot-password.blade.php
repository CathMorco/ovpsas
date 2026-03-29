<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password - OVPSAS Portal</title>
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
                    <span class="font-black text-2xl tracking-tight">OVPSAS Portal</span>
                    <span class="font-light text-xs opacity-90 tracking-widest uppercase">Office of Student Affairs and Services</span>
                </div>
            </a>
            <div class="hidden md:flex items-center gap-8 text-white font-bold text-sm tracking-wide">
                <a href="{{ url('/') }}" class="hover:text-yellow-300 transition">Home</a>
                <a href="{{ url('/about') }}" class="hover:text-yellow-300 transition">About Us</a>
                <a href="{{ route('login') }}" class="hover:text-yellow-300 transition">Log in</a>
            </div>
        </div>
    </nav>

    <main class="flex-grow flex items-center justify-center relative"
          style="background: linear-gradient(rgba(0,0,0,0.1), rgba(0,0,0,0.1)), url('/images/background.jpg'); background-size: cover; background-position: center;">

        <div class="bg-white p-10 md:p-14 rounded-[3rem] shadow-2xl w-full max-w-lg mx-4 text-center border-t-8 border-[#800000]">
            <h2 class="text-xl font-bold text-[#800000] mb-2 uppercase italic tracking-tight">Forgot Password?</h2>
            <p class="text-xs text-gray-500 mb-8 leading-relaxed font-bold uppercase">
                Submit your email address and we'll send a reset link.
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

                <button type="submit" class="w-full bg-[#800000] hover:bg-black text-white font-bold py-4 rounded-full transition-all duration-300 text-lg shadow-lg uppercase tracking-widest">
                    Email Reset Link
                </button>

                <div class="pt-2">
                    <a href="{{ route('login') }}" class="text-xs font-black text-[#800000] hover:underline uppercase tracking-tighter">
                        &larr; Return to Log in
                    </a>
                </div>
            </form>
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-4 text-center text-xs">
        &copy; {{ date('Y') }} OVPSAS Portal. Polytechnic University of the Philippines.
    </footer>
</body>
</html>