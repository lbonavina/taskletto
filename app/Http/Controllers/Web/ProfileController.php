<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use App\Services\PlanService;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index', ['user' => Auth::user()]);
    }

    public function updateInfo(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('info_saved', true);
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $rules = ['new_password' => ['required', 'confirmed', Password::min(8)]];

        if ($user->hasPassword()) {
            $rules['current_password'] = ['required'];
            $request->validate($rules);

            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Senha atual incorreta.'])->withFragment('password');
            }
        } else {
            $request->validate($rules);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return back()->with('password_saved', true)->withFragment('password');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:2048', 'mimes:jpg,jpeg,png,webp,gif'],
        ]);

        $user = Auth::user();

        $limitMb = $user->plan()->limit('storage_mb');
        if ($limitMb !== null) {
            $newAvatarBytes = $request->file('avatar')->getSize();
            $currentMb = app(PlanService::class)->getStorageMbInUse($user);
            $deltaMb = $newAvatarBytes / (1024 * 1024);
            $oldMb = 0;
            
            if ($user->avatar && str_starts_with($user->avatar, '/storage/')) {
                $path = str_replace('/storage/', 'public/', $user->avatar);
                if (Storage::exists($path)) {
                    $oldMb = Storage::size($path) / (1024 * 1024);
                }
            }
            
            if (($currentMb - $oldMb + $deltaMb) > $limitMb) {
                return back()->withErrors(['avatar' => app(PlanService::class)->limitMessage('storage_mb')]);
            }
        }

        // Delete old avatar if stored locally
        if ($user->avatar && str_starts_with($user->avatar, '/storage/')) {
            $path = str_replace('/storage/', 'public/', $user->avatar);
            Storage::delete($path);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => '/storage/' . $path]);

        return back()->with('avatar_saved', true);
    }

    public function removeAvatar()
    {
        $user = Auth::user();

        if ($user->avatar && str_starts_with($user->avatar, '/storage/')) {
            $path = str_replace('/storage/', 'public/', $user->avatar);
            Storage::delete($path);
        }

        $user->update(['avatar' => null]);

        return back()->with('avatar_saved', true);
    }
}
