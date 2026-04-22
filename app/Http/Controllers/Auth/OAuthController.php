<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    private array $allowedProviders = ['github'];

    public function redirect(string $provider)
    {
        abort_unless(in_array($provider, $this->allowedProviders), 404);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider)
    {
        abort_unless(in_array($provider, $this->allowedProviders), 404);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Não foi possível autenticar com ' . ucfirst($provider) . '. Tente novamente.',
            ]);
        }

        $user = User::where('email', $socialUser->getEmail())->first();

        if ($user) {
            $user->update([
                'oauth_provider' => $provider,
                'oauth_id'       => $socialUser->getId(),
                'avatar'         => $user->avatar ?? $socialUser->getAvatar(),
            ]);
        } else {
            $user = User::create([
                'name'           => $socialUser->getName() ?? $socialUser->getNickname() ?? 'Usuário',
                'email'          => $socialUser->getEmail(),
                'oauth_provider' => $provider,
                'oauth_id'       => $socialUser->getId(),
                'avatar'         => $socialUser->getAvatar(),
            ]);
        }

        AppSetting::set('onboarding_complete', '1');

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
