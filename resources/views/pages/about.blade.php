<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>About Us - OVPSAS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased flex flex-col min-h-screen">

    <nav x-data="{ open: false }" class="bg-[#800000] border-b border-red-900 sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex">
                    <div class="shrink-0 flex items-center">
                        <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                            <img src="{{ asset('images/PUPLogo.png') }}" alt="Logo" class="block h-12 w-12 rounded-full border-2 border-white bg-white group-hover:scale-105 transition-transform">
                            <div class="hidden lg:flex flex-col text-white leading-tight">
                                <span class="font-bold text-lg tracking-wide group-hover:text-yellow-300 transition-colors">Student Affairs</span>
                                <span class="text-xs opacity-90 font-light">Services and Information System</span>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex items-center">
                    <a href="{{ url('/') }}" class="text-white font-bold hover:text-yellow-300 transition text-sm {{ Request::is('/') ? 'border-b-2 border-yellow-400 pb-1' : '' }}">Home</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-white font-bold text-sm {{ Request::is('dashboard') ? 'border-b-2 border-yellow-400 pb-1' : 'hover:text-yellow-300 transition' }}">Dashboard</a>
                    @endauth
                    <a href="{{ url('/about') }}" class="text-white font-bold hover:text-yellow-300 transition text-sm {{ Request::is('about') ? 'border-b-2 border-yellow-400 pb-1' : '' }}">About Us</a>
                </div>

                <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">

                    <div class="relative hidden lg:block w-64">
                        <input type="text" placeholder="Search..." class="w-full bg-white text-gray-800 rounded-full px-4 py-1.5 pl-4 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        <button class="absolute right-1 top-1/2 transform -translate-y-1/2 bg-[#FCD116] p-1 rounded-full hover:bg-yellow-300 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>

                    @guest
                        <a href="{{ route('login') }}" class="text-white font-bold text-sm border-b-2 border-transparent hover:border-yellow-400 hover:text-yellow-300 transition">Log in</a>
                    @else
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-bold rounded-md text-white hover:text-yellow-300 focus:outline-none transition">
                                    <div>{{ Auth::user()->name }}</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Log Out</x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    @endguest
                </div>

                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-red-900 transition">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-red-900 text-white pb-3">
            <div class="pt-2 space-y-1">
                <x-responsive-nav-link :href="url('/')" :active="request()->is('/')" class="text-white">Home</x-responsive-nav-link>
                @auth <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-white">Dashboard</x-responsive-nav-link> @endauth
                <x-responsive-nav-link :href="url('/about')" :active="request()->is('about')" class="text-white">About Us</x-responsive-nav-link>
            </div>
        </div>
    </nav>

    <main class="flex-grow">
        <div class="py-12">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 space-y-8">

                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg border-l-8 border-[#800000]">
                    <div class="p-8 md:p-10">
                        <h1 class="text-3xl font-bold text-[#800000] mb-6 flex items-center gap-3">
                            <span class="w-2 h-8 bg-[#FCD116]"></span>
                            About the Office
                        </h1>
                        <p class="mb-6 text-gray-700 leading-relaxed text-justify">
                            Welcome to the <strong>Office of the Vice President for Student Affairs and Services (OVPSAS)</strong>.<br>
                            We are dedicated to providing a supportive and enriching environment for all students at the Polytechnic University of the Philippines.
                        </p>
                        <p class="text-gray-700 leading-relaxed text-justify">
                            The Polytechnic University of the Philippines (PUP) is a government educational institution governed by Republic Act Number 8292 known as the Higher Education Modernization Act of 1997, and its Implementing Rules and Regulations contained in the Commission on Higher Education Memorandum Circular No. 4, series 1997. PUP is one of the country's highly competent educational institutions. The PUP Community is composed of the Board of Regents, University Officials, Administrative and Academic Personnel, Students, various Organizations, and the Alumni.<br><br>
                            Governance of PUP is vested upon the Board of Regents, which exercises policy-making functions to carry out the mission and programs of the University by virtue of RA 8292 granted by the Commission on Higher Education. The University is administered by an appointed President by virtue of RA 8292 and is assisted by an Executive Vice President and the Vice Presidents for Academic Affairs, Student Services, Administration, Research, Extension and Development, and Finance.
                        </p>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-8">
                    <div class="bg-white shadow-lg sm:rounded-lg border-l-8 border-[#800000] p-8">
                        <h2 class="text-2xl font-bold text-[#800000] mb-4 flex items-center gap-3">
                            <span class="w-1.5 h-6 bg-[#FCD116]"></span>
                            Our Vision
                        </h2>
                        <p class="text-gray-800 italic text-lg text-justify">
                            PUP: The National Polytechnic University
                        </p>
                    </div>

                    <div class="bg-white shadow-lg sm:rounded-lg border-l-8 border-[#800000] p-8">
                        <h2 class="text-2xl font-bold text-[#800000] mb-4 flex items-center gap-3">
                            <span class="w-1.5 h-6 bg-[#FCD116]"></span>
                            Our Mission
                        </h2>
                        <p class="text-gray-700 italic leading-relaxed whitespace-pre-line text-justify text-sm">
                            Ensuring inclusive and equitable quality education and promoting lifelong learning opportunities through a re-engineered polytechnic university by committing to:
                            • provide democratized access to educational opportunities for the holistic development of individuals with global perspective
                            • offer industry-oriented curricula that produce highly-skilled professionals with managerial and technical capabilities and a strong sense of public service for nation building
                            • embed a culture of research and innovation
                            • continuously develop faculty and employees with the highest level of professionalism
                            • engage public and private institutions and other stakeholders for the attainment of social development goal
                            • establish a strong presence and impact in the international academic community
                        </p>
                    </div>
                </div>

                <div class="bg-white shadow-lg sm:rounded-lg border-l-8 border-[#800000]">
                    <div class="p-8">
                        <h2 class="text-2xl font-bold text-[#800000] mb-4 flex items-center gap-3">
                            <span class="w-2 h-7 bg-[#FCD116]"></span>
                            The PUP Philosophy
                        </h2>
                        <p class="text-gray-700 italic leading-relaxed whitespace-pre-line text-justify">
                            As a state university, the Polytechnic University of the Philippines believes that:
                            Education is an instrument for the development of the citizenry and for the enhancement of nation building; and
                            That meaningful growth and transformation of the country are best achieved in an atmosphere of brotherhood, peace, freedom, justice and nationalist-oriented education imbued with the spirit of humanist internationalism.
                        </p>
                    </div>
                </div>

                <div class="bg-white shadow-lg sm:rounded-lg border-l-8 border-[#800000]">
                    <div class="p-8">
                        <h2 class="text-2xl font-bold text-[#800000] mb-6 flex items-center gap-3">
                            <span class="w-2 h-7 bg-[#FCD116]"></span>
                            Ten Pillars
                        </h2>
                        <div class="grid md:grid-cols-2 gap-x-12 gap-y-4">
                            <div class="space-y-3">
                                <p class="flex gap-3 text-gray-700 text-justify items-start text-sm"><span class="font-bold text-[#800000]">1</span> Pillar 1: Dynamic, Transformational, and Responsible Leadership</p>
                                <p class="flex gap-3 text-gray-700 text-justify items-start text-sm"><span class="font-bold text-[#800000]">2</span> Pillar 2: Responsive and Innovative Curricula and Instruction</p>
                                <p class="flex gap-3 text-gray-700 text-justify items-start text-sm"><span class="font-bold text-[#800000]">3</span> Pillar 3: Enabling and Productive Learning Environment</p>
                                <p class="flex gap-3 text-gray-700 text-justify items-start text-sm"><span class="font-bold text-[#800000]">4</span> Pillar 4: Holistic Student Development and Engagement</p>
                                <p class="flex gap-3 text-gray-700 text-justify items-start text-sm"><span class="font-bold text-[#800000]">5</span> Pillar 5: Empowered Faculty Members and Employees</p>
                            </div>
                            <div class="space-y-3">
                                <p class="flex gap-3 text-gray-700 text-justify items-start text-sm"><span class="font-bold text-[#800000]">6</span> Pillar 6: Vigorous Research Production and Utilization</p>
                                <p class="flex gap-3 text-gray-700 text-justify items-start text-sm"><span class="font-bold text-[#800000]">7</span> Pillar 7: Global Academic Standards and Excellence</p>
                                <p class="flex gap-3 text-gray-700 text-justify items-start text-sm"><span class="font-bold text-[#800000]">8</span> Pillar 8: Synergistic, Productive, Strategic Networks and Partnerships</p>
                                <p class="flex gap-3 text-gray-700 text-justify items-start text-sm"><span class="font-bold text-[#800000]">9</span> Pillar 9: Active and Sustained Stakeholders’ Engagement</p>
                                <p class="flex gap-3 text-gray-700 text-justify items-start text-sm"><span class="font-bold text-[#800000]">10</span> Pillar 10: Sustainable Social Development Programs and Projects</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-6 text-center text-xs">
        &copy; {{ date('Y') }} OVPSAS. Polytechnic University of the Philippines.
    </footer>

</body>
</html>
