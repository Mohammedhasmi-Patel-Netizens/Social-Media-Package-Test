@extends('layouts.app')

@section('title', 'Social Hub')

@section('content')
        <!-- Header -->
        <div class="text-center space-y-4 w-full">
            <h1 class="text-5xl md:text-6xl font-extrabold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-indigo-400 to-purple-400 mb-2 drop-shadow-lg">
                Social Hub
            </h1>
            <p class="text-lg md:text-xl text-slate-500 max-w-2xl mx-auto font-light">
                Connect and manage your social platforms seamlessly from one unified dashboard.
            </p>
        </div>

        <!-- Alerts -->
        @if (session('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-2xl p-4 shadow-lg backdrop-blur-md max-w-2xl w-full mx-auto flex items-center">
                <div class="flex-shrink-0 bg-emerald-500/20 p-2 rounded-full">
                    <svg class="h-6 w-6 text-emerald-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-emerald-300">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-rose-500/10 border border-rose-500/20 rounded-2xl p-4 shadow-lg backdrop-blur-md max-w-2xl w-full mx-auto flex items-center">
                <div class="flex-shrink-0 bg-rose-500/20 p-2 rounded-full">
                    <svg class="h-6 w-6 text-rose-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-rose-300">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mt-12 w-full">
            
            <!-- YouTube -->
            <div class="glass-card rounded-3xl overflow-hidden flex flex-col group">
                <div class="p-8 flex-grow flex flex-col items-center text-center">
                    <div class="w-20 h-20 bg-red-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 group-hover:shadow-[0_0_30px_rgba(239,68,68,0.3)]">
                        <svg class="w-10 h-10 text-red-500 drop-shadow-[0_0_8px_rgba(239,68,68,0.5)]" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M19.812 5.418c.861.23 1.538.907 1.768 1.768C21.998 8.746 22 12 22 12s0 3.255-.418 4.814a2.504 2.504 0 0 1-1.768 1.768c-1.56.418-7.814.418-7.814.418s-6.255 0-7.814-.418a2.505 2.505 0 0 1-1.768-1.768C2 15.255 2 12 2 12s0-3.255.418-4.814a2.504 2.504 0 0 1 1.768-1.768C5.746 5 12 5 12 5s6.255 0 7.814.418zM10 15l5-3-5-3v6z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-semibold text-slate-900 mb-2">YouTube</h3>
                    <p class="text-slate-500 text-sm mb-6 leading-relaxed">Connect your channel to upload shorts and manage videos.</p>
                </div>
                <div class="px-6 pb-6 space-y-3 mt-auto">
                    <a href="{{ route('youtube.redirect') }}" class="w-full flex items-center justify-center px-4 py-3 rounded-xl font-medium text-white bg-red-600 hover:bg-red-500 shadow-[0_0_15px_rgba(220,38,38,0.4)] hover:shadow-[0_0_25px_rgba(220,38,38,0.6)] transition-all duration-300 hover:-translate-y-0.5">
                        Connect Account
                    </a>
                    <a href="{{ route('youtube.upload.form') }}" class="w-full flex items-center justify-center px-4 py-3 rounded-xl font-medium text-slate-700 bg-white/50 hover:bg-white hover:text-slate-900 border border-slate-200 shadow-sm transition-all duration-300">
                        Upload Video
                    </a>
                </div>
            </div>

            <!-- Facebook -->
            <div class="glass-card rounded-3xl overflow-hidden flex flex-col group">
                <div class="p-8 flex-grow flex flex-col items-center text-center">
                    <div class="w-20 h-20 bg-blue-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 group-hover:shadow-[0_0_30px_rgba(59,130,246,0.3)]">
                        <svg class="w-10 h-10 text-blue-500 drop-shadow-[0_0_8px_rgba(59,130,246,0.5)]" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-semibold text-slate-900 mb-2">Facebook</h3>
                    <p class="text-slate-500 text-sm mb-6 leading-relaxed">Manage posts, reels, and your community effectively.</p>
                </div>
                <div class="px-6 pb-6 space-y-3 mt-auto">
                    <a href="{{ route('facebook.redirect') }}" class="w-full flex items-center justify-center px-4 py-3 rounded-xl font-medium text-white bg-blue-600 hover:bg-blue-500 shadow-[0_0_15px_rgba(37,99,235,0.4)] hover:shadow-[0_0_25px_rgba(37,99,235,0.6)] transition-all duration-300 hover:-translate-y-0.5">
                        Connect Account
                    </a>
                    <a href="{{ route('facebook.upload.form') }}" class="w-full flex items-center justify-center px-4 py-3 rounded-xl font-medium text-slate-700 bg-white/50 hover:bg-white hover:text-slate-900 border border-slate-200 shadow-sm transition-all duration-300">
                        Create Post
                    </a>
                </div>
            </div>

            <!-- Instagram -->
            <div class="glass-card rounded-3xl overflow-hidden flex flex-col group">
                <div class="p-8 flex-grow flex flex-col items-center text-center">
                    <div class="w-20 h-20 bg-pink-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 group-hover:shadow-[0_0_30px_rgba(236,72,153,0.3)]">
                        <svg class="w-10 h-10 text-pink-500 drop-shadow-[0_0_8px_rgba(236,72,153,0.5)]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-semibold text-slate-900 mb-2">Instagram</h3>
                    <p class="text-slate-500 text-sm mb-6 leading-relaxed">Share moments and post directly to your Instagram feed.</p>
                </div>
                <div class="px-6 pb-6 space-y-3 mt-auto">
                    <a href="{{ route('instagram.redirect') }}" class="w-full flex items-center justify-center px-4 py-3 rounded-xl font-medium text-white bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-400 hover:to-pink-400 shadow-[0_0_15px_rgba(236,72,153,0.4)] hover:shadow-[0_0_25px_rgba(236,72,153,0.6)] transition-all duration-300 hover:-translate-y-0.5">
                        Connect Account
                    </a>
                    <a href="{{ route('instagram.upload.form') }}" class="w-full flex items-center justify-center px-4 py-3 rounded-xl font-medium text-slate-700 bg-white/50 hover:bg-white hover:text-slate-900 border border-slate-200 shadow-sm transition-all duration-300">
                        Create Post
                    </a>
                </div>
            </div>

            <!-- Twitter (X) -->
            <div class="glass-card rounded-3xl overflow-hidden flex flex-col group">
                <div class="p-8 flex-grow flex flex-col items-center text-center">
                    <div class="w-20 h-20 bg-slate-900/5 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 group-hover:shadow-[0_0_30px_rgba(0,0,0,0.1)]">
                        <svg class="w-9 h-9 text-slate-800 drop-shadow-sm" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-semibold text-slate-900 mb-2">Twitter (X)</h3>
                    <p class="text-slate-500 text-sm mb-6 leading-relaxed">Connect to post updates, threads, and engage followers.</p>
                </div>
                <div class="px-6 pb-6 space-y-3 mt-auto">
                    <a href="{{ route('twitter.redirect') }}" class="w-full flex items-center justify-center px-4 py-3 rounded-xl font-medium text-black bg-slate-100 hover:bg-slate-200 shadow-[0_0_15px_rgba(0,0,0,0.05)] hover:shadow-[0_0_25px_rgba(0,0,0,0.1)] transition-all duration-300 hover:-translate-y-0.5">
                        Connect Account
                    </a>
                    <a href="{{ route('twitter.upload.form') }}" class="w-full flex items-center justify-center px-4 py-3 rounded-xl font-medium text-slate-700 bg-white/50 hover:bg-white hover:text-slate-900 border border-slate-200 shadow-sm transition-all duration-300">
                        Create Post
                    </a>
                </div>
            </div>

            <!-- LinkedIn -->
            <div class="glass-card rounded-3xl overflow-hidden flex flex-col group">
                <div class="p-8 flex-grow flex flex-col items-center text-center">
                    <div class="w-20 h-20 bg-blue-400/10 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 group-hover:shadow-[0_0_30px_rgba(96,165,250,0.3)]">
                        <svg class="w-10 h-10 text-blue-400 drop-shadow-[0_0_8px_rgba(96,165,250,0.5)]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-semibold text-slate-900 mb-2">LinkedIn</h3>
                    <p class="text-slate-500 text-sm mb-6 leading-relaxed">Share professional updates and articles with your network.</p>
                </div>
                <div class="px-6 pb-6 space-y-3 mt-auto">
                    <a href="{{ route('linkedin.redirect') }}" class="w-full flex items-center justify-center px-4 py-3 rounded-xl font-medium text-white bg-blue-600 hover:bg-blue-500 shadow-[0_0_15px_rgba(37,99,235,0.4)] hover:shadow-[0_0_25px_rgba(37,99,235,0.6)] transition-all duration-300 hover:-translate-y-0.5">
                        Connect Account
                    </a>
                    <a href="{{ route('linkedin.upload.form') }}" class="w-full flex items-center justify-center px-4 py-3 rounded-xl font-medium text-slate-700 bg-white/50 hover:bg-white hover:text-slate-900 border border-slate-200 shadow-sm transition-all duration-300">
                        Create Post
                    </a>
                </div>
            </div>

            <!-- TikTok -->
            <div class="glass-card rounded-3xl overflow-hidden flex flex-col group">
                <div class="p-8 flex-grow flex flex-col items-center text-center">
                    <div class="w-20 h-20 bg-cyan-400/10 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 group-hover:shadow-[0_0_30px_rgba(34,211,238,0.3)]">
                        <svg class="w-10 h-10 text-cyan-400 drop-shadow-[0_0_8px_rgba(34,211,238,0.5)]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19.589 6.686a4.793 4.793 0 0 1-3.77-4.245V2h-3.445v13.672a2.896 2.896 0 0 1-5.201 1.743l-.002-.001.002.001a2.895 2.895 0 0 1 3.183-4.51v-3.5a6.329 6.329 0 0 0-5.394 10.692 6.33 6.33 0 0 0 10.857-4.424V8.687a8.182 8.182 0 0 0 4.77 1.526V6.79a4.831 4.831 0 0 1-1.003-.104z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-semibold text-slate-900 mb-2">TikTok</h3>
                    <p class="text-slate-500 text-sm mb-6 leading-relaxed">Engage your audience with viral short-form video content.</p>
                </div>
                <div class="px-6 pb-6 space-y-3 mt-auto">
                    <a href="{{ route('tiktok.redirect') }}" class="w-full flex items-center justify-center px-4 py-3 rounded-xl font-medium text-black bg-cyan-400 hover:bg-cyan-300 shadow-[0_0_15px_rgba(34,211,238,0.4)] hover:shadow-[0_0_25px_rgba(34,211,238,0.6)] transition-all duration-300 hover:-translate-y-0.5">
                        Connect Account
                    </a>
                    <a href="{{ route('tiktok.upload.form') }}" class="w-full flex items-center justify-center px-4 py-3 rounded-xl font-medium text-slate-700 bg-white/50 hover:bg-white hover:text-slate-900 border border-slate-200 shadow-sm transition-all duration-300">
                        Upload Video
                    </a>
                </div>
            </div>

            <!-- Threads -->
            <div class="glass-card rounded-3xl overflow-hidden flex flex-col group">
                <div class="p-8 flex-grow flex flex-col items-center text-center">
                    <div class="w-20 h-20 bg-slate-900/5 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 group-hover:shadow-[0_0_30px_rgba(0,0,0,0.1)]">
                        <svg class="w-10 h-10 text-slate-800 drop-shadow-sm" viewBox="0 0 192 192" xmlns="http://www.w3.org/2000/svg" fill="none">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="14" d="M141.53 103.54c-1.38 18.06-14.7 32.1-32.55 32.1-15.02 0-26.69-10.74-27.18-25.04M141.53 103.54C142.6 90.07 131.79 78 118.23 78c-12.22 0-22.38 9.38-23.77 21.67m47.07 3.87c.3 4.22.45 8.52.45 12.91 0 25.13-16.71 42.55-40.35 42.55-22.61 0-39.63-17.65-39.63-42.55 0-25.77 17.62-44.45 40.54-44.45 15.61 0 28.51 7.28 35.12 19.34m-33.84 5.86c0-7.39 5.82-13.2 13.04-13.2 7.15 0 12.83 5.6 13.03 12.72"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-semibold text-slate-900 mb-2">Threads</h3>
                    <p class="text-slate-500 text-sm mb-6 leading-relaxed">Join the conversation and post quick updates seamlessly.</p>
                </div>
                <div class="px-6 pb-6 space-y-3 mt-auto">
                    <a href="{{ route('threads.redirect') }}" class="w-full flex items-center justify-center px-4 py-3 rounded-xl font-medium text-black bg-slate-100 hover:bg-slate-200 shadow-[0_0_15px_rgba(0,0,0,0.05)] hover:shadow-[0_0_25px_rgba(0,0,0,0.1)] transition-all duration-300 hover:-translate-y-0.5">
                        Connect Account
                    </a>
                    <a href="{{ route('threads.upload.form') }}" class="w-full flex items-center justify-center px-4 py-3 rounded-xl font-medium text-slate-700 bg-white/50 hover:bg-white hover:text-slate-900 border border-slate-200 shadow-sm transition-all duration-300">
                        Create Post
                    </a>
                </div>
            </div>

@endsection
