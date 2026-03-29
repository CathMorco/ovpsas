<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password - OVPSAS Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased flex flex-col min-h-screen">
    <main class="flex-grow flex items-center justify-center relative"
          style="background: linear-gradient(rgba(0,0,0,0.1), rgba(0,0,0,0.1)), url('/images/background.jpg'); background-size: cover; background-position: center;">

        <div class="bg-white p-10 md:p-14 rounded-[3rem] shadow-2xl w-full max-w-lg mx-4 border-t-8 border-[#800000]">
            <h2 class="text-xl font-bold text-[#800000] mb-6 text-center uppercase italic tracking-tight">Set New Password</h2>

            <form method="POST" action="{{ route('password.store') }}" class="flex flex-col gap-6">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="space-y-4">
                    <input type="email" name="email" value="{{ old('email', $request->email) }}" required readonly
                        class="w-full bg-[#E8EDF2] border-none rounded-xl p-4 text-gray-400 cursor-not-allowed italic font-bold">

                    <input type="password" name="password" placeholder="New Password" required autofocus
                        class="w-full bg-[#E8EDF2] border-none rounded-xl p-4 text-gray-700 focus:ring-2 focus:ring-[#800000]">

                    <input type="password" name="password_confirmation" placeholder="Confirm New Password" required
                        class="w-full bg-[#E8EDF2] border-none rounded-xl p-4 text-gray-700 focus:ring-2 focus:ring-[#800000]">
                </div>

                <button type="submit" class="w-full bg-[#800000] hover:bg-black text-white font-bold py-4 rounded-full transition-all duration-300 text-lg shadow-lg uppercase tracking-widest">
                    Update Password
                </button>
            </form>
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-4 text-center text-xs">
        &copy; {{ date('Y') }} OVPSAS Portal. Polytechnic University of the Philippines.
    </footer>
</body>
</html>