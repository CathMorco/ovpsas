@extends('layouts.master')

@section('title', 'About Us - OVPSAS')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 space-y-8">

            {{-- About the Office / Intro --}}
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

            {{-- Tagline & Mandate --}}
            <div class="bg-white shadow-lg sm:rounded-lg border-l-8 border-[#800000] p-8">
                <div class="grid md:grid-cols-3 gap-8 items-center">
                    <div class="md:col-span-1">
                        <h2 class="text-xl font-bold text-[#800000] mb-2">Tagline</h2>
                        <p class="text-2xl text-gray-600 font-medium italic">The Country's 1st Polytechnic U</p>
                    </div>
                    <div class="md:col-span-2 md:border-l md:pl-8 border-gray-200">
                        <h2 class="text-xl font-bold text-[#800000] mb-2">Mandate</h2>
                        <p class="text-gray-700 leading-relaxed text-justify text-sm">
                            <span class="text-[#800000] font-medium">Presidential Decree No.1341</span> mandated the Polytechnic University of the Philippines to expand the program offerings of the University to include courses in polytechnic areas and has also given the University the authority to expand diametrically through the establishment of branches, consortia and linkages.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Vision & Mission --}}
            <div class="grid md:grid-cols-2 gap-8">
                <div class="bg-white shadow-lg sm:rounded-lg border-l-8 border-[#800000] p-8">
                    <h2 class="text-2xl font-bold text-[#800000] mb-4 flex items-center gap-3">
                        <span class="w-1.5 h-6 bg-[#FCD116]"></span>
                        Vision
                    </h2>
                    <p class="text-gray-700 text-lg text-justify font-medium">
                        A Leading Comprehensive Polytechnic University in Asia
                    </p>
                </div>

                <div class="bg-white shadow-lg sm:rounded-lg border-l-8 border-[#800000] p-8">
                    <h2 class="text-2xl font-bold text-[#800000] mb-4 flex items-center gap-3">
                        <span class="w-1.5 h-6 bg-[#FCD116]"></span>
                        Mission Statement
                    </h2>
                    <p class="text-gray-700 leading-relaxed text-justify font-medium">
                        Advance an inclusive, equitable, and globally relevant polytechnic education towards national development.
                    </p>
                </div>
            </div>

            {{-- Strategic Goals --}}
            <div class="bg-white shadow-lg sm:rounded-lg border-l-8 border-[#800000]">
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-[#800000] mb-6 flex items-center gap-3">
                        <span class="w-2 h-7 bg-[#FCD116]"></span>
                        Strategic Goals
                    </h2>
                    <div class="grid md:grid-cols-3 gap-8">
                        {{-- Pillar 1 --}}
                        <div class="space-y-4">
                            <h3 class="font-bold text-[#800000] border-b-2 border-gray-100 pb-2">Pillar 1: Teaching and Learning</h3>
                            <ul class="text-sm text-gray-700 space-y-3">
                                <li><span class="font-bold text-gray-900">SG 1:</span> Innovative Curricula and Instruction</li>
                                <li><span class="font-bold text-gray-900">SG 2:</span> Empowered, Expert, and Productive Faculty Members</li>
                                <li><span class="font-bold text-gray-900">SG 3:</span> Holistic Student Development</li>
                            </ul>
                        </div>
                        {{-- Pillar 2 --}}
                        <div class="space-y-4">
                            <h3 class="font-bold text-[#800000] border-b-2 border-gray-100 pb-2">Pillar 2: Research and Extension</h3>
                            <ul class="text-sm text-gray-700 space-y-3">
                                <li><span class="font-bold text-gray-900">SG 4:</span> Intensified Research Innovation, Dissemination and Utilization</li>
                                <li><span class="font-bold text-gray-900">SG 5:</span> Strengthened Sustainable and Impactful Extension Program</li>
                                <li><span class="font-bold text-gray-900">SG 6:</span> Expanded Research and Extension Networks with Local, National, and International Partners</li>
                            </ul>
                        </div>
                        {{-- Pillar 3 --}}
                        <div class="space-y-4">
                            <h3 class="font-bold text-[#800000] border-b-2 border-gray-100 pb-2">Pillar 3: Internal Governance</h3>
                            <ul class="text-sm text-gray-700 space-y-3">
                                <li><span class="font-bold text-gray-900">SG 7:</span> Transformational University Leadership</li>
                                <li><span class="font-bold text-gray-900">SG 8:</span> Judicious and Ethical Stewardship of Physical and Financial Resources</li>
                                <li><span class="font-bold text-gray-900">SG 9:</span> Effective and Efficient Human Resource Management</li>
                                <li><span class="font-bold text-gray-900">SG 10:</span> Excellent Citizen/Client Satisfaction</li>
                                <li><span class="font-bold text-gray-900">SG 11:</span> Smart Campuses</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Core Values --}}
            <div class="bg-white shadow-lg sm:rounded-lg border-l-8 border-[#800000]">
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-[#800000] mb-6 flex items-center gap-3">
                        <span class="w-2 h-7 bg-[#FCD116]"></span>
                        Core Values
                    </h2>
                    
                    <div class="space-y-6 divide-y divide-gray-100">
                        {{-- Nationalism --}}
                        <div class="grid md:grid-cols-3 gap-6 pt-6 first:pt-0">
                            <div class="font-bold text-[#800000]">Nationalism</div>
                            <div class="text-sm text-gray-700 text-justify">We instill a sense of national consciousness to develop citizenry dedicated to serve the Republic.</div>
                            <div class="text-sm text-gray-700">
                                <span class="font-medium text-gray-900">We act in ways that:</span>
                                <ul class="list-disc pl-4 mt-2 space-y-1">
                                    <li>demonstrate loyalty to the Republic by upholding the Philippine Constitution;</li>
                                    <li>inculcate love of country among our stakeholders;</li>
                                    <li>bring honor and pride to our country;</li>
                                    <li>contribute to the development of citizenry; and</li>
                                    <li>contribute to nation-building.</li>
                                </ul>
                            </div>
                        </div>

                        {{-- Sense of Service --}}
                        <div class="grid md:grid-cols-3 gap-6 pt-6">
                            <div class="font-bold text-[#800000]">Sense of Service</div>
                            <div class="text-sm text-gray-700 text-justify">We are committed to perform our duties as public servants with an inherent desire to be of service to others.</div>
                            <div class="text-sm text-gray-700">
                                <span class="font-medium text-gray-900">We act in ways that:</span>
                                <ul class="list-disc pl-4 mt-2 space-y-1">
                                    <li>go beyond and above what is expected from a public servant;</li>
                                    <li>exhibit strong sense of community through volunteerism, outreach, and community engagements;</li>
                                    <li>emphasize virtues such as compassion and empathy;</li>
                                    <li>anticipate, recognize, and meet people's needs; and</li>
                                    <li>manifest selflessness by upholding public interest over and above personal interest.</li>
                                </ul>
                            </div>
                        </div>

                        {{-- Passion for Learning and Innovation --}}
                        <div class="grid md:grid-cols-3 gap-6 pt-6">
                            <div class="font-bold text-[#800000]">Passion for Learning and Innovation</div>
                            <div class="text-sm text-gray-700 text-justify">We commit to steadfastly create new knowledges, methods, and mindsets to develop innovative solutions to societal problems.</div>
                            <div class="text-sm text-gray-700">
                                <span class="font-medium text-gray-900">We act in ways that:</span>
                                <ul class="list-disc pl-4 mt-2 space-y-1">
                                    <li>promote lifelong learning opportunities, including but not limited to, continuing professional and personal development; and</li>
                                    <li>exhibit deep-seated enthusiasm for discovery, invention, and innovation;</li>
                                </ul>
                            </div>
                        </div>

                        {{-- Inclusivity --}}
                        <div class="grid md:grid-cols-3 gap-6 pt-6">
                            <div class="font-bold text-[#800000]">Inclusivity</div>
                            <div class="text-sm text-gray-700 text-justify">We create an academic community that openly embraces individuals regardless of their background where they feel valued, respected, and have equal opportunities.</div>
                            <div class="text-sm text-gray-700">
                                <span class="font-medium text-gray-900">We act in ways that:</span>
                                <ul class="list-disc pl-4 mt-2 space-y-1">
                                    <li>promote equity, diversity, social inclusion, and equal opportunity for all regardless of race, gender, nationality, ethnicity, ideology, language, religion, ability or any other status in the provision of educational programs and services;</li>
                                    <li>accept and embrace change;</li>
                                    <li>is consistent in its interaction with everyone; and</li>
                                    <li>foster a safe space where individuality is respected and valued.</li>
                                </ul>
                            </div>
                        </div>

                        {{-- Respect for Human Rights and the Environment --}}
                        <div class="grid md:grid-cols-3 gap-6 pt-6">
                            <div class="font-bold text-[#800000]">Respect for Human Rights and the Environment</div>
                            <div class="text-sm text-gray-700 text-justify">We acknowledge that human rights and the environment are intertwined; human rights cannot be enjoyed without a safe, clean, and healthy environment.</div>
                            <div class="text-sm text-gray-700">
                                <span class="font-medium text-gray-900">We act in ways that:</span>
                                <ul class="list-disc pl-4 mt-2 space-y-1">
                                    <li>observe and respect fundamental human rights, including but not limited to, academic freedom, freedom of speech and expression, and freedom from discrimination and harassment;</li>
                                    <li>engage the PUP community and its partners in programs, projects, and activities that help protect the environment and adhere to rules and regulations that promote environmental sustainability; and</li>
                                    <li>establish a sustainable environmental governance that respects human rights.</li>
                                </ul>
                            </div>
                        </div>

                        {{-- Excellence --}}
                        <div class="grid md:grid-cols-3 gap-6 pt-6">
                            <div class="font-bold text-[#800000]">Excellence</div>
                            <div class="text-sm text-gray-700 text-justify">We aim for outstanding performance in teaching and learning, research, extension services and community engagements, and internal governance.</div>
                            <div class="text-sm text-gray-700">
                                <span class="font-medium text-gray-900">We act in ways that:</span>
                                <ul class="list-disc pl-4 mt-2 space-y-1">
                                    <li>reflect continuing improvement and innovation;</li>
                                    <li>emphasize attention to details;</li>
                                    <li>observe coherence;</li>
                                    <li>adhere to high standards; and</li>
                                    <li>demonstrate resilience and perseverance.</li>
                                </ul>
                            </div>
                        </div>

                        {{-- Democracy --}}
                        <div class="grid md:grid-cols-3 gap-6 pt-6">
                            <div class="font-bold text-[#800000]">Democracy</div>
                            <div class="text-sm text-gray-700 text-justify">We operate under a system where participatory and inclusive decision making, open dialogue, and respect for diverse perspectives prevail.</div>
                            <div class="text-sm text-gray-700">
                                <span class="font-medium text-gray-900">We act in ways that:</span>
                                <ul class="list-disc pl-4 mt-2 space-y-1">
                                    <li>encourage participation of all members of the PUP community;</li>
                                    <li>consult and involve stakeholders in decision-making;</li>
                                    <li>encourage openness and provide platforms for diverse voices to be heard and considered toward improving the services of the University; and</li>
                                    <li>demonstrate advocacy on socio-civic responsibility.</li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection