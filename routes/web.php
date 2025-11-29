<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleLoginController;
use App\Http\Controllers\Auth\FacebookLoginController;

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
