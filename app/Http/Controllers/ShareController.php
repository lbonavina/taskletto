<?php

namespace App\Http\Controllers;

use App\Models\NoteShare;
use Illuminate\Http\Request;

class ShareController extends Controller
{
    public function show(Request $request, string $token)
    {
        $share = NoteShare::where('token', $token)->with('note')->firstOrFail();

        if (! $share->isAccessible()) {
            abort(410, 'Este link foi desativado ou expirou.');
        }

        if ($share->visibility === 'public') {
            $share->incrementViews();
            return view('share.show', compact('share'));
        }

        // Private: check session for already-verified email
        $verified = session("share_access_{$token}");
        if ($verified && $share->allowsEmail($verified)) {
            $share->incrementViews();
            return view('share.show', compact('share'));
        }

        return view('share.gate', compact('share'));
    }

    public function gate(Request $request, string $token)
    {
        $share = NoteShare::where('token', $token)->firstOrFail();

        if (! $share->isAccessible()) {
            abort(410, 'Este link foi desativado ou expirou.');
        }

        $request->validate(['email' => ['required', 'email']]);

        if (! $share->allowsEmail($request->email)) {
            return back()->withErrors(['email' => 'Este e-mail não tem permissão para acessar esta nota.']);
        }

        session(["share_access_{$token}" => strtolower($request->email)]);

        return redirect()->route('share.show', $token);
    }
}
