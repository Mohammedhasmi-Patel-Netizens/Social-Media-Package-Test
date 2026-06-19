<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post to Instagram</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-lg shadow-md max-w-lg w-full">
        <h1 class="text-2xl font-bold mb-6 text-gray-800 text-center">Post to Instagram</h1>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
                @if(session('url'))
                    <a href="{{ session('url') }}" target="_blank" class="block font-bold mt-2 underline">View Post on Instagram</a>
                @endif
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <form action="{{ route('instagram.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label for="content" class="block text-sm font-medium text-gray-700">Caption / Description</label>
                <textarea name="content" id="content" required rows="4" maxlength="2000"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm p-2 border" 
                    placeholder="Write a caption..."></textarea>
            </div>

            <div>
                <label for="image" class="block text-sm font-medium text-gray-700">Select Image/Video (Required)</label>
                <input type="file" name="image" id="image" accept="image/*,video/*" required
                    class="mt-1 block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-md file:border-0
                    file:text-sm file:font-semibold
                    file:bg-purple-50 file:text-purple-700
                    hover:file:bg-purple-100 border border-gray-300 rounded-md p-1">
            </div>

            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                Share to Instagram
            </button>
        </form>
    </div>

</body>
</html>
