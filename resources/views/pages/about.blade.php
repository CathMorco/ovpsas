<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>About Us - OVPSAS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased flex flex-col min-h-screen">

    <nav class="w-full bg-[#800000] px-6 py-4 shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">

            <a href="{{ url('/') }}" class="flex items-center gap-4 group">
                <img src="{{ asset('images/PUPLogo.png') }}" alt="PUP Logo" class="h-14 w-14 rounded-full bg-white p-0.5 border-2 border-white group-hover:scale-105 transition-transform">
                <div class="flex flex-col text-white leading-tight">
                    <span class="font-bold text-lg tracking-wide group-hover:text-yellow-300 transition-colors">Student Affairs</span>
                    <span class="font-light text-sm opacity-90">Services and Information System</span>
                </div>
            </a>

            <div class="hidden md:flex items-center gap-8 text-white font-bold text-sm tracking-wide">
                <a href="{{ url('/') }}" class="hover:text-yellow-300 transition">
                    Home
                </a>

                {{-- Only show Dashboard if logged in --}}
                @auth
                    <a href="{{ url('/dashboard') }}" class="hover:text-yellow-300 transition">
                        Dashboard
                    </a>
                @endauth

                <a href="{{ url('/about') }}" class="text-yellow-300 border-b-2 border-yellow-300 transition">
                    About Us
                </a>

                {{-- Logic for Guest vs User --}}
                @guest
                    <a href="{{ route('login') }}" class="border-b-2 border-white pb-1 hover:text-yellow-300 hover:border-yellow-300 transition">
                        Log in
                    </a>
                @else
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="hover:text-yellow-300 transition">Log Out</button>
                    </form>
                @endguest
            </div>

            <div class="relative hidden md:block w-64 ml-4">
                <input type="text" placeholder="Search..." class="w-full bg-white text-gray-800 rounded-full px-4 py-1.5 pl-4 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                <button class="absolute right-1 top-1/2 transform -translate-y-1/2 bg-[#FCD116] p-1 rounded-full hover:bg-yellow-300 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <main class="flex-grow">
        <div class="py-12">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h1 class="text-3xl font-bold text-[#800000] mb-6">About the Office</h1>

                        <p class="mb-4 text-gray-700 leading-relaxed">
                            Welcome to the <strong>Office of the Vice President for Student Affairs and Services (OVPSAS)</strong>.<br>
                            We are dedicated to providing a supportive and enriching environment for all students at the Polytechnic University of the Philippines.
                        </p>

                        <p class="mb-4 text-gray-700 leading-relaxed">
                            The Polytechnic University of the Philippines (PUP) is a government educational institution governed by Republic Act Number 8292 known as the Higher Education Modernization Act of 1997, and its Implementing Rules and Regulations contained in the Commission on Higher Education Memorandum Circular No. 4, series 1997. PUP is one of the country's highly competent educational institutions. The PUP Community is composed of the Board of Regents, University Officials, Administrative and Academic Personnel, Students, various Organizations, and the Alumni.<br>
                            Governance of PUP is vested upon the Board of Regents, which exercises policy-making functions to carry out the mission and programs of the University by virtue of RA 8292 granted by the Commission on Higher Education. The University is administered by an appointed President by virtue of RA 8292 and is assisted by an Executive Vice President and the Vice Presidents for Academic Affairs, Student Services, Administration, Research, Extension and Development, and Finance.
                        </p>

                        <div class="mt-8 border-t pt-6">
                            <h2 class="text-xl font-semibold text-gray-800 mb-2">Our Vision</h2>
                            <p class="text-gray-600 italic">
                                PUP: The National Polytechnic University
                            </p>
                        </div>

                        <div class="mt-8 border-t pt-6">
                            <h2 class="text-xl font-semibold text-gray-800 mb-2">Our Mission</h2>
                            <p class="text-gray-600 italic">
                                Ensuring inclusive and equitable quality education and promoting lifelong learning opportunities through a re-engineered polytechnic university by committing to:<br>
                                    provide democratized access to educational opportunities for the holistic development of individuals with global perspective<br>
                                    offer industry-oriented curricula that produce highly-skilled professionals with managerial and technical capabilities and a strong sense of public service for nation building<br>
                                    embed a culture of research and innovation<br>
                                    continuously develop faculty and employees with the highest level of professionalism<br>
                                    engage public and private institutions and other stakeholders for the attainment of social development goal<br>
                                    establish a strong presence and impact in the international academic community
                            </p>
                        </div>

                        <div class="mt-8 border-t pt-6">
                            <h2 class="text-xl font-semibold text-gray-800 mb-2">The PUP Philosophy</h2>
                            <p class="text-gray-600 italic">
                                As a state university, the Polytechnic University of the Philippines believes that:<br>
                                    Education is an instrument for the development of the citizenry and for the enhancement of nation building; and<br>
                                    That meaningful growth and transformation of the country are best achieved in an atmosphere of brotherhood, peace, freedom, justice and nationalist-oriented education imbued with the spirit of humanist internationalism.
                            </p>
                        </div>

                        <div class="mt-8 border-t pt-6">
                            <h2 class="text-xl font-semibold text-gray-800 mb-2">Ten Pillars</h2>
                            <p class="text-gray-600 italic">
                                Pillar 1:  Dynamic, Transformational, and Responsible Leadership<br>
                                Pillar 2: Responsive and Innovative Curricula and Instruction<br>
                                Pillar 3: Enabling and Productive Learning Environment<br>
                                Pillar 4: Holistic Student Development and Engagement<br>
                                Pillar 5: Empowered Faculty Members and Employees<br>
                                Pillar 6: Vigorous Research Production and Utilization<br>
                                Pillar 7:  Global Academic Standards and Excellence<br>
                                Pillar 8: Synergistic, Productive, Strategic Networks and Partnerships<br>
                                Pillar 9: Active and Sustained Stakeholders’ Engagement<br>
                                Pillar 10: Sustainable Social Development Programs and Projects
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-4 text-center text-xs">
        &copy; {{ date('Y') }} OVPSAS. Polytechnic University of the Philippines.
    </footer>

</body>
</html>
