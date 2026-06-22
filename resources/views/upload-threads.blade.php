@extends('layouts.app')
@section('title', 'Post to Threads')
@section('content')

    <div class="glass-form-card p-8 rounded-3xl max-w-lg w-full text-slate-900 shadow-xl mt-8">
        <h1 class="text-2xl font-bold mb-6 text-slate-900 drop-shadow-sm text-center">Post to Threads</h1>

        @if (session('success'))
            <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
                @if(session('url'))
                    <a href="{{ session('url') }}" target="_blank" class="block font-bold mt-2 hover:text-emerald-900 underline transition-colors">View Post on Threads</a>
                @endif
            </div>
        @endif

        @if (session('error'))
            <div class="bg-rose-100 border border-rose-400 text-rose-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <form action="{{ route('threads.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label for="content" class="block text-sm font-medium text-slate-600">Thread Content</label>
                <textarea name="content" id="content" required rows="4" maxlength="500"
                    class="mt-1 block w-full rounded-xl border border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm p-3 text-slate-900 placeholder-slate-400 transition-all" 
                    placeholder="Start a thread..."></textarea>
            </div>

            <div>
                <label for="image" class="block text-sm font-medium text-slate-600">Attach Media (Optional)</label>
                <input type="file" name="image" id="image" accept="image/*,video/*"
                    class="mt-1 block w-full text-sm text-slate-600
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-xl file:border-0
                    file:text-sm file:font-semibold
                    file:bg-slate-100 file:text-slate-700
                    hover:file:bg-slate-200 border border-gray-300 rounded-xl p-1 transition-all">
            </div>

            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900">
                Post to Threads
            </button>
        </form>
    </div>

@endsection
