<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Filament\Facades\Filament;

class FacebookLoginController extends Controller
{
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $fbUser = Socialite::driver('facebook')->user();
        } catch (\Exception $e) {
            return redirect('/admin/login')->with('error', 'Błąd logowania Facebook.');
        }

        $user = User::where('email', $fbUser->email)
            ->first();

        if (!$user) {
            $user = User::create([
                'name' => $fbUser->name,
                'email' => $fbUser->email,
                'facebook_id' => $fbUser->id,
                'password' => null,
            ]);
        }

        Filament::auth()->login($user);

        return redirect()->intended(Filament::getUrl());
    }
}
