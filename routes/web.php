<?php

use App\Http\Controllers\FacebookController;
use App\Http\Controllers\InstagramController;
use App\Http\Controllers\LinkedInController;
use App\Http\Controllers\ThreadsController;
use App\Http\Controllers\TikTokController;
use App\Http\Controllers\TwitterController;
use App\Http\Controllers\YoutubeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth/youtube/redirect', [YoutubeController::class, 'redirectToYoutube'])->name('youtube.redirect');
Route::get('/account/youtube/callback', [YoutubeController::class, 'handleYoutubeCallback'])->name('youtube.callback');

Route::get('/auth/facebook/redirect', [FacebookController::class, 'redirectToFacebook'])->name('facebook.redirect');
Route::get('/account/facebook/callback', [FacebookController::class, 'handleFacebookCallback'])->name('facebook.callback');


Route::get('/auth/tiktok/redirect', [TikTokController::class, 'redirectToTikTok'])->name('tiktok.redirect');
Route::get('/account/tiktok/callback', [TikTokController::class, 'handleTikTokCallback'])->name('tiktok.callback');

Route::get('/auth/twitter/redirect', [TwitterController::class, 'redirectToTwitter'])->name('twitter.redirect');
Route::get('/account/twitter/callback', [TwitterController::class, 'handleTwitterCallback'])->name('twitter.callback');

// Video upload routes (YouTube)
Route::get('/upload-video', [YoutubeController::class, 'showUploadForm'])->name('youtube.upload.form');
Route::post('/upload-video', [YoutubeController::class, 'uploadVideo'])->name('youtube.upload');

// Facebook upload routes
Route::get('/upload-facebook', [FacebookController::class, 'showUploadForm'])->name('facebook.upload.form');
Route::post('/upload-facebook', [FacebookController::class, 'uploadPost'])->name('facebook.upload');

// TikTok upload routes
Route::get('/upload-tiktok', [TikTokController::class, 'showUploadForm'])->name('tiktok.upload.form');
Route::post('/upload-tiktok', [TikTokController::class, 'uploadVideo'])->name('tiktok.upload');

// Twitter upload routes
Route::get('/upload-twitter', [TwitterController::class, 'showUploadForm'])->name('twitter.upload.form');
Route::post('/upload-twitter', [TwitterController::class, 'uploadPost'])->name('twitter.upload');

Route::get('/auth/linkedin/redirect', [LinkedInController::class, 'redirectToLinkedIn'])->name('linkedin.redirect');
Route::get('/account/linkedin/callback', [LinkedInController::class, 'handleLinkedInCallback'])->name('linkedin.callback');

// LinkedIn upload routes
Route::get('/upload-linkedin', [LinkedInController::class, 'showUploadForm'])->name('linkedin.upload.form');
Route::post('/upload-linkedin', [LinkedInController::class, 'uploadPost'])->name('linkedin.upload');

Route::get('/auth/instagram/redirect', [InstagramController::class, 'redirectToInstagram'])->name('instagram.redirect');
Route::get('/account/instagram/callback', [InstagramController::class, 'handleInstagramCallback'])->name('instagram.callback');

// Instagram upload routes
Route::get('/upload-instagram', [InstagramController::class, 'showUploadForm'])->name('instagram.upload.form');
Route::post('/upload-instagram', [InstagramController::class, 'uploadPost'])->name('instagram.upload');

Route::get('/auth/threads/redirect', [ThreadsController::class, 'redirectToThreads'])->name('threads.redirect');
Route::get('/account/threads/callback', [ThreadsController::class, 'handleThreadsCallback'])->name('threads.callback');

// Threads upload routes
Route::get('/upload-threads', [ThreadsController::class, 'showUploadForm'])->name('threads.upload.form');
Route::post('/upload-threads', [ThreadsController::class, 'uploadPost'])->name('threads.upload');
