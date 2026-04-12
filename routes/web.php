<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleLoginController;
use App\Http\Controllers\Auth\FacebookLoginController;
use App\Http\Controllers\QrPdfController;
use App\Http\Controllers\EventPageController;

use App\Events\WebRtcSignal;
use Illuminate\Http\Request;


Route::get('/', function () {
    return redirect('/dashboard');
});

// Google Login
Route::get('/auth/google/redirect', [GoogleLoginController::class, 'redirectToGoogle'])
    ->name('auth.google');

Route::get('/auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback']);

// Facebook Login
Route::get('/auth/facebook/redirect', [FacebookLoginController::class, 'redirectToFacebook'])
    ->name('auth.facebook');

Route::get('/auth/facebook/callback', [FacebookLoginController::class, 'handleFacebookCallback']);

// Download QR code
Route::get('/qr-pdf', [QrPdfController::class, 'generate'])->name('qr.pdf');

// Conference
Route::post('/video-chat/signal', function (Request $request) {
    broadcast(new WebRtcSignal(
        $request->input('data'),
        $request->input('channel_name')
    ))->toOthers();

    return response()->json(['status' => 'ok']);
})->middleware(['auth']);


//Event page
Route::get('/site/{slug}', [EventPageController::class, 'show'])->name('public.event.show');