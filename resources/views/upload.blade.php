@extends('layouts.app')
@section('title', 'Upload Video to YouTube')
@section('content')

    <div class="glass-form-card p-8 rounded-3xl max-w-lg w-full text-slate-900 shadow-xl mt-8">
        <h1 class="text-2xl font-bold mb-6 text-slate-900 drop-shadow-sm text-center">Upload to YouTube</h1>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
                @if(session('url'))
                    <a href="{{ session('url') }}" target="_blank" class="block font-bold mt-2 underline">View Video on YouTube</a>
                @endif
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <form action="{{ route('youtube.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label for="title" class="block text-sm font-medium text-slate-600">Video Title / Description</label>
                <input type="text" name="title" id="title" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 border" 
                    placeholder="My Awesome Video #Shorts">
            </div>

            <div>
                <label for="video" class="block text-sm font-medium text-slate-600">Select Video (MP4, MOV)</label>
                <input type="file" name="video" id="video" accept="video/*" required
                    class="mt-1 block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-md file:border-0
                    file:text-sm file:font-semibold
                    file:bg-red-50 file:text-red-700
                    hover:file:bg-red-100 border border-gray-300 rounded-md p-1">
            </div>

            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                Upload to YouTube
            </button>
        </form>
    </div>

@endsection
