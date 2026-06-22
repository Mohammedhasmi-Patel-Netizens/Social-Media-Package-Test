@extends('layouts.app')
@section('title', 'Post to LinkedIn')
@section('content')

    <div class="glass-form-card p-8 rounded-3xl max-w-lg w-full text-slate-900 shadow-xl mt-8">
        <h1 class="text-2xl font-bold mb-6 text-slate-900 drop-shadow-sm text-center">Post to LinkedIn</h1>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
                @if(session('url'))
                    <a href="{{ session('url') }}" target="_blank" class="block font-bold mt-2 underline">View Post on LinkedIn</a>
                @endif
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <form action="{{ route('linkedin.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label for="content" class="block text-sm font-medium text-slate-600">Post Content</label>
                <textarea name="content" id="content" required rows="4" maxlength="2000"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600 sm:text-sm p-2 border" 
                    placeholder="Share an update or article..."></textarea>
            </div>

            <div>
                <label for="image" class="block text-sm font-medium text-slate-600">Select Image (Optional)</label>
                <input type="file" name="image" id="image" accept="image/*"
                    class="mt-1 block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-md file:border-0
                    file:text-sm file:font-semibold
                    file:bg-blue-50 file:text-blue-700
                    hover:file:bg-blue-100 border border-gray-300 rounded-md p-1">
            </div>

            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-700">
                Share on LinkedIn
            </button>
        </form>
    </div>

@endsection
