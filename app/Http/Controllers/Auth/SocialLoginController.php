<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    public function redirect(string $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider)
    {
        $socialUser = Socialite::driver($provider)->user();
        $email = $socialUser->getEmail();

        $user = User::where('provider_id', $socialUser->getId())
            ->where('provider_name', $provider)
            ->first();

        if ($user) {
            $emailTaken = User::where('email', $email)->where('id', '!=', $user->id)->exists();

            $user->update([
                'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                'email' => $emailTaken ? $user->email : $email,
            ]);
        } else {
            if (User::where('email', $email)->exists()) {
                return redirect()->route('login')->withErrors([
                    'email' => 'An account with this email already exists. Please log in using your password or original provider.',
                ]);
            }

            $user = User::create([
                'provider_id' => $socialUser->getId(),
                'provider_name' => $provider,
                'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                'email' => $email,
            ]);
        }

        Auth::login($user);

        return redirect()->intended('/dashboard');
    }
}
