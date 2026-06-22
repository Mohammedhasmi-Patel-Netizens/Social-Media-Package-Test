<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Social Poster Integration')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,100%,90%,1) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(225,100%,90%,1) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(339,100%,90%,1) 0, transparent 50%);
            background-attachment: fixed;
            color: #0f172a;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card:hover {
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(255, 255, 255, 1);
            transform: translateY(-5px);
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.1);
        }
        .glass-form-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 1);
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
        }
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        .animation-delay-4000 {
            animation-delay: 4s;
        }
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center py-16 px-4 sm:px-6 lg:px-8 relative overflow-x-hidden text-slate-900">
    <!-- Animated background blobs -->
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-purple-300 rounded-full mix-blend-multiply filter blur-[128px] opacity-60 animate-blob pointer-events-none"></div>
    <div class="absolute top-0 right-1/4 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-[128px] opacity-60 animate-blob animation-delay-2000 pointer-events-none"></div>
    <div class="absolute -bottom-32 left-1/2 w-96 h-96 bg-pink-300 rounded-full mix-blend-multiply filter blur-[128px] opacity-60 animate-blob animation-delay-4000 pointer-events-none"></div>

    <div class="max-w-7xl w-full space-y-12 relative z-10 flex flex-col items-center">
        @yield('content')
    </div>
</body>
</html>
