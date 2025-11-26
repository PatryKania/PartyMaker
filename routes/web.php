<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleLoginController;

Route::get('/', function () {
    return view('welcome');
});


// Google Login
Route::get('/auth/google/redirect', [GoogleLoginController::class, 'redirectToGoogle'])
    ->name('auth.google');

Route::get('/auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback']);
