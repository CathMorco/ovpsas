<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="bg-white shadow-sm sm:rounded-lg p-8 border-t-4 border-red-800">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 uppercase">System Administrative Report</h2>
                        <p class="text-sm text-gray-500 italic">Real-time analytics of system utilization.</p>
                    </div>
                    <a href="{{ route('reports.download') }}" class="bg-red-800 hover:bg-red-900 text-white font-bold py-2 px-6 rounded-lg transition duration-200 shadow-md">
                        GENERATE PDF REPORT
                    </a>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="p-5 bg-red-50 border border-red-100 rounded-xl">
                        <h4 class="font-bold text-red-800 uppercase text-[10px] mb-1">Total Files</h4>
                        <p class="text-3xl font-black text-gray-800">{{ $totalFiles }}</p>
                    </div>

                    <div class="p-5 bg-gray-50 border border-gray-200 rounded-xl">
                        <h4 class="font-bold text-gray-400 uppercase text-[10px] mb-1">Monitored Offices</h4>
                        <p class="text-3xl font-black text-gray-800">{{ $activeOffices }}</p>
                    </div>

                    <div class="p-5 bg-green-50 border border-green-100 rounded-xl">
                        <h4 class="font-bold text-green-800 uppercase text-[10px] mb-1">Uploaded This Month</h4>
                        <p class="text-3xl font-black text-gray-800">{{ $filesThisMonth }}</p>
                    </div>

                    <div class="p-5 bg-blue-50 border border-blue-100 rounded-xl">
                        <h4 class="font-bold text-blue-800 uppercase text-[10px] mb-1">Most Active Office</h4>
                        <p class="text-xl font-black text-gray-800 truncate">{{ $mostActiveOffice }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-8 border-l-8 border-red-800">
                <h3 class="text-lg font-bold text-gray-800 uppercase mb-4 tracking-tight">Recent System Activities</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 font-bold text-gray-700">Date</th>
                                <th class="px-4 py-3 font-bold text-gray-700">Office</th>
                                <th class="px-4 py-3 font-bold text-gray-700">Latest Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($activities as $activity)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-gray-600">{{ $activity['date'] }}</td>
                                <td class="px-4 py-3 font-bold text-red-800 uppercase">{{ $activity['office'] }}</td>
                                <td class="px-4 py-3 text-gray-500 italic text-xs">{{ $activity['action'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>