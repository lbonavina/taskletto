@extends('layouts.app')

@section('page-title', 'Perfil')

@push('styles')
<style>
.profile-wrap { max-width: 560px; display: flex; flex-direction: column; gap: 14px; }

/* ── Avatar section ── */
.avatar-row {
    display: flex; align-items: center; gap: 16px;
    padding-bottom: 20px; margin-bottom: 4px;
    border-bottom: 1px solid var(--border);
}
.avatar-large {
    width: 64px; height: 64px; border-radius: 50%;
    background: rgba(255,145,77,.12);
    border: 2px solid rgba(255,145,77,.2);
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; font-weight: 700; color: var(--accent);
    flex-shrink: 0; overflow: hidden;
}
.avatar-large img { width: 100%; height: 100%; object-fit: cover; }

.avatar-actions { display: flex; flex-direction: column; gap: 6px; }
.avatar-name    { font-size: 15px; font-weight: 700; color: var(--text); }
.avatar-email   { font-size: 12px; color: var(--muted); margin-bottom: 4px; }

.btn-avatar-upload {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 12px; border-radius: var(--radius-sm);
    background: var(--surface2); border: 1px solid var(--border);
    color: var(--text); font-size: 12px; font-weight: 500;
    font-family: inherit; cursor: pointer;
    transition: border-color .15s, background .15s;
}
.btn-avatar-upload:hover { border-color: var(--accent); background: rgba(255,145,77,.06); }

.btn-avatar-remove {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 12px; border-radius: var(--radius-sm);
    background: none; border: 1px solid transparent;
    color: var(--muted); font-size: 12px; font-weight: 500;
    font-family: inherit; cursor: pointer;
    transition: color .15s, border-color .15s;
}
.btn-avatar-remove:hover { color: var(--danger); border-color: rgba(224,84,84,.25); }

/* ── OAuth badge ── */
.oauth-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 10px; border-radius: 20px;
    background: rgba(96,165,250,.08); border: 1px solid rgba(96,165,250,.2);
    color: var(--info); font-size: 11px; font-weight: 600;
    width: fit-content; margin-bottom: 4px;
}
</style>
@endpush

@section('content')
<div class="profile-wrap">

    {{-- Avatar + identity --}}
    <div class="card">
        <div class="avatar-row">
            <div class="avatar-large" id="avatar-preview">
                @if($user->avatar)
                    <img src="{{ $user->avatar }}" alt="{{ $user->name }}" id="avatar-img">
                @else
                    <span id="avatar-initials">{{ $user->initials() }}</span>
                @endif
            </div>
            <div class="avatar-actions">
                <div class="avatar-name">{{ $user->name }}</div>
                <div class="avatar-email">{{ $user->email }}</div>

                @if($user->oauth_provider)
                    <div class="oauth-badge">
                        @if($user->oauth_provider === 'google')
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                        @elseif($user->oauth_provider === 'github')
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/></svg>
                        @endif
                        Conectado via {{ ucfirst($user->oauth_provider) }}
                    </div>
                @endif

                <div style="display:flex;gap:6px">
                    <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data" id="avatar-form">
                        @csrf
                        <input type="file" name="avatar" id="avatar-input" accept="image/*" style="display:none"
                               onchange="document.getElementById('avatar-form').submit()">
                        <label for="avatar-input" class="btn-avatar-upload">
                            <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 11V3M5 6l3-3 3 3M2 12v1a1 1 0 001 1h10a1 1 0 001-1v-1"/></svg>
                            Alterar foto
                        </label>
                    </form>
                    @if($user->avatar)
                        <form method="POST" action="{{ route('profile.avatar.remove') }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-avatar-remove">Remover</button>
                        </form>
                    @endif
                </div>
                @error('avatar')
                    <div class="form-error" style="margin-top:4px">{{ $message }}</div>
                @enderror
                @if(session('avatar_saved'))
                    <div class="alert-inline success" style="margin-top:6px;padding:6px 10px">
                        <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 8l4 4 8-8"/></svg>
                        Foto atualizada.
                    </div>
                @endif
            </div>
        </div>

        {{-- Personal info --}}
        <div class="section-title" style="margin-bottom:14px">Informações pessoais</div>

        @if(session('info_saved'))
            <div class="alert-inline success" style="margin-bottom:14px">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 8l4 4 8-8"/></svg>
                Dados atualizados com sucesso.
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label" for="name">Nome</label>
                <input type="text" name="name" id="name"
                       class="form-input {{ $errors->has('name') ? 'error' : '' }}"
                       value="{{ old('name', $user->name) }}" required autocomplete="name">
                @error('name') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="email">E-mail</label>
                <input type="email" name="email" id="email"
                       class="form-input {{ $errors->has('email') ? 'error' : '' }}"
                       value="{{ old('email', $user->email) }}" required autocomplete="email">
                @error('email') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <button type="submit" class="btn btn-primary">Salvar alterações</button>
        </form>
    </div>

    {{-- Password --}}
    <div class="card" id="password">
        <div class="section-title" style="margin-bottom:14px">
            {{ $user->hasPassword() ? 'Alterar senha' : 'Definir senha' }}
        </div>

        @if(!$user->hasPassword())
            <p class="action-row-desc" style="margin-bottom:14px;line-height:1.6">
                Sua conta foi criada via OAuth e não possui senha. Defina uma para poder fazer login com e-mail e senha.
            </p>
        @endif

        @if(session('password_saved'))
            <div class="alert-inline success" style="margin-bottom:14px">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 8l4 4 8-8"/></svg>
                Senha atualizada com sucesso.
            </div>
        @endif

        <form method="POST" action="{{ route('profile.password') }}">
            @csrf @method('PUT')
            @if($user->hasPassword())
                <div class="form-group">
                    <label class="form-label" for="current_password">Senha atual</label>
                    <input type="password" name="current_password" id="current_password"
                           class="form-input {{ $errors->has('current_password') ? 'error' : '' }}"
                           autocomplete="current-password">
                    @error('current_password') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            @endif
            <div class="form-group">
                <label class="form-label" for="new_password">{{ $user->hasPassword() ? 'Nova senha' : 'Senha' }}</label>
                <input type="password" name="new_password" id="new_password"
                       class="form-input {{ $errors->has('new_password') ? 'error' : '' }}"
                       autocomplete="new-password">
                @error('new_password') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="new_password_confirmation">Confirmar {{ $user->hasPassword() ? 'nova ' : '' }}senha</label>
                <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                       class="form-input" autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn-primary">
                {{ $user->hasPassword() ? 'Atualizar senha' : 'Definir senha' }}
            </button>
        </form>
    </div>

</div>
@endsection
