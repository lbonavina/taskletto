@extends('layouts.auth')

@section('title', 'Criar conta')

@section('content')
    <h1 class="auth-card-title">Criar conta</h1>
    <p class="auth-card-sub">Comece a organizar tudo hoje mesmo</p>

    @if ($errors->any() && !$errors->hasAny(['name','email','password']))
        <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Cloudflare Turnstile --}}
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

        <div class="form-group">
            <label class="form-label" for="name">Nome</label>
            <input
                id="name"
                type="text"
                name="name"
                class="form-input {{ $errors->has('name') ? 'error' : '' }}"
                value="{{ old('name') }}"
                autocomplete="name"
                autofocus
                required
            >
            @error('name')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="email">E-mail</label>
            <input
                id="email"
                type="email"
                name="email"
                class="form-input {{ $errors->has('email') ? 'error' : '' }}"
                value="{{ old('email') }}"
                autocomplete="email"
                required
            >
            @error('email')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Senha</label>
            <div class="password-wrapper">
                <input
                    id="password"
                    type="password"
                    name="password"
                    class="form-input password-input {{ $errors->has('password') ? 'error' : '' }}"
                    autocomplete="new-password"
                    required
                >
                <button type="button" class="password-toggle" onclick="togglePassword('password')" tabindex="-1">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                </button>
            </div>
            @error('password')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password_confirmation">Confirmar senha</label>
            <div class="password-wrapper">
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    class="form-input password-input"
                    autocomplete="new-password"
                    required
                >
                <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')" tabindex="-1">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                </button>
            </div>
        </div>

        <div class="form-group" style="display: flex; justify-content: center; margin: 20px 0;">
            <div class="cf-turnstile" data-sitekey="{{ env('TURNSTILE_SITE_KEY') }}" data-theme="light"></div>
        </div>

        <button type="submit" class="btn-primary">Criar conta</button>
    </form>

    <div class="auth-divider">ou continue com</div>

    <div class="oauth-buttons">
        <a href="{{ route('oauth.redirect', 'github') }}" class="btn-oauth">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0 1 12 6.844a9.59 9.59 0 0 1 2.504.337c1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.02 10.02 0 0 0 22 12.017C22 6.484 17.522 2 12 2z"/></svg>
            Continuar com GitHub
        </a>
    </div>

    <div class="auth-footer">
        Já tem uma conta? <a href="{{ route('login') }}">Entrar</a>
    </div>
@endsection
