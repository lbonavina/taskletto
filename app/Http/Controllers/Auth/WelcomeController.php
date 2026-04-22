<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WelcomeController extends Controller
{
    public function show()
    {
        // If already authenticated, go to dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // If onboarding was completed and a user exists, go to login
        if (AppSetting::get('onboarding_complete') && User::exists()) {
            return redirect()->route('login');
        }

        return view('auth.welcome');
    }

}
