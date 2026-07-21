<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\GoogleAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(GoogleAuthService $googleAuthService): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->user();
        $user = $googleAuthService->findOrCreateUser($googleUser);

        Auth::login($user);

        request()->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }
}
