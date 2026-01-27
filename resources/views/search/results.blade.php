@extends('layouts.master') @section('content')
<div class="max-w-7xl mx-auto py-12 px-4">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">
        Search Results for: <span class="text-[#800000]">{{ $search }}</span>
    </h2>

    @if($results->isEmpty())
        <div class="bg-white p-6 rounded-lg shadow-md text-center text-gray-500">
            <p>No results found. Try searching for something else.</p>
        </div>
    @else
        <div class="grid gap-6">
            @foreach($results as $result)
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition border-l-4 border-[#800000]">
                    <h3 class="text-xl font-bold">{{ $result->name }}</h3>
                    <p class="text-gray-600">{{ $result->email }}</p>
                    
                    <a href="#" class="text-sm text-blue-600 hover:underline mt-2 inline-block">View Details &rarr;</a>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection