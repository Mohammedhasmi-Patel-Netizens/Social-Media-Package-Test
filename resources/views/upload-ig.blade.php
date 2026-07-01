@extends('layouts.app')
@section('title', 'Post to Instagram')
@section('content')

    <div class="glass-form-card p-8 rounded-3xl max-w-lg w-full text-slate-900 shadow-xl mt-8">
        <h1 class="text-2xl font-bold mb-6 text-slate-900 drop-shadow-sm text-center">Post to Instagram</h1>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
                @if(session('url'))
                    <a href="{{ session('url') }}" target="_blank" class="block font-bold mt-2 underline">View Post on Instagram</a>
                @endif
            </div>
        @endif

        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 shadow-sm rounded-r-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700 font-medium">
                        <strong>Local Development Warning:</strong> Instagram Graph API requires images to be publicly accessible. Because you are using Ngrok's free tier, Instagram will be blocked by Ngrok's "Visit Site" warning screen, resulting in an upload failure. This will work perfectly once deployed to a staging or production server!
                    </p>
                </div>
            </div>
        </div>

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <form action="{{ route('instagram.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label for="content" class="block text-sm font-medium text-slate-600">Caption / Description</label>
                <textarea name="content" id="content" required rows="4" maxlength="2000"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm p-2 border" 
                    placeholder="Write a caption..."></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-600 mb-2">Media Source (Choose One)</label>
                
                <div class="space-y-4 p-4 border border-gray-200 rounded-lg bg-gray-50">
                    <!-- Option 1: File Upload -->
                    <div>
                        <label for="image" class="block text-sm font-medium text-slate-600">Option 1: Upload File</label>
                        <input type="file" name="image" id="image" accept="image/*,video/*"
                            class="mt-1 block w-full text-sm text-gray-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-md file:border-0
                            file:text-sm file:font-semibold
                            file:bg-purple-50 file:text-purple-700
                            hover:file:bg-purple-100 border border-gray-300 rounded-md p-1 bg-white">
                        <p class="text-xs text-gray-500 mt-1">Will fail on local Ngrok connections due to Meta API restrictions.</p>
                    </div>

                    <div class="relative flex items-center py-2">
                        <div class="flex-grow border-t border-gray-300"></div>
                        <span class="flex-shrink-0 mx-4 text-gray-400 text-sm font-medium">OR</span>
                        <div class="flex-grow border-t border-gray-300"></div>
                    </div>

                    <!-- Option 2: Public URL -->
                    <div>
                        <label for="public_url" class="block text-sm font-medium text-slate-600">Option 2: Provide Public Image URL</label>
                        <input type="url" name="public_url" id="public_url" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm p-2 border" 
                            placeholder="https://example.com/image.jpg">
                        <p class="text-xs text-green-600 mt-1">Use this to successfully test locally! (e.g. Unsplash URL)</p>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                Share to Instagram
            </button>
        </form>
    </div>

@endsection
