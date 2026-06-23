<?php

if (! function_exists('social_poster_trans')) {
    function social_poster_trans(string $message): string
    {
        // If host app has a translate() helper, delegate to it; otherwise return as-is
        if (function_exists('translate')) {
            return translate($message);
        }

        return $message;
    }
}

if (! function_exists('social_poster_response_status')) {
    function social_poster_response_status(string $message, string $type = 'success'): array
    {
        // If host app has response_status(), delegate; otherwise return standard array
        if (function_exists('response_status')) {
            return response_status($message, $type);
        }

        return [
            'status' => $type === 'success' || $type === 'ok',
            'message' => $message,
            'type' => $type,
        ];
    }
}

if (! function_exists('imageURL')) {
    function imageURL($file, $context = 'post', $fullUrl = true)
    {
        // For local uploads with fopen(), we need the absolute system path.
        // This avoids deadlocks when using 'php artisan serve' and allows reading the file.
        return public_path($file->path);
    }
}

if (! function_exists('filePath')) {
    function filePath($file, $context)
    {
        return $file->path; // Returns path relative to public folder
    }
}

if (! function_exists('isValidVideoUrl')) {
    function isValidVideoUrl($url): bool
    {
        $extensions = ['mp4', 'mpeg', 'mpg', 'mov', 'avi', 'mkv'];
        $extension = strtolower(pathinfo($url, PATHINFO_EXTENSION));

        return in_array($extension, $extensions);
    }
}
