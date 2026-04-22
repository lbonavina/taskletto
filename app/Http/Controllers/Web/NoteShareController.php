<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\NoteShare;
use Illuminate\Http\Request;

class NoteShareController extends Controller
{
    /** Create or update the share config for a note. */
    public function store(Request $request, Note $note)
    {
        $data = $request->validate([
            'visibility'     => ['required', 'in:public,private'],
            'allowed_emails' => ['nullable', 'string'],
            'expires_at'     => ['nullable', 'date', 'after:now'],
        ]);

        // Parse comma/newline-separated emails
        $emails = null;
        if ($data['visibility'] === 'private' && filled($data['allowed_emails'] ?? null)) {
            $emails = collect(preg_split('/[\s,;]+/', $data['allowed_emails']))
                ->map(fn($e) => strtolower(trim($e)))
                ->filter(fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL))
                ->unique()
                ->values()
                ->all();
        }

        $share = $note->share ?? new NoteShare(['note_id' => $note->id, 'token' => NoteShare::generateToken()]);

        $share->fill([
            'visibility'     => $data['visibility'],
            'allowed_emails' => $emails,
            'expires_at'     => $data['expires_at'] ?? null,
            'active'         => true,
        ])->save();

        return response()->json([
            'url'   => $share->url(),
            'token' => $share->token,
        ]);
    }

    /** Revoke (deactivate) the share link. */
    public function destroy(Note $note)
    {
        $note->share?->update(['active' => false]);
        return response()->json(['ok' => true]);
    }

    /** Regenerate the token (new link, old one dies). */
    public function regenerate(Note $note)
    {
        $share = $note->share;
        if (! $share) {
            return response()->json(['error' => 'Nenhum compartilhamento ativo.'], 404);
        }

        $share->update(['token' => NoteShare::generateToken(), 'active' => true]);

        return response()->json(['url' => $share->fresh()->url()]);
    }
}
