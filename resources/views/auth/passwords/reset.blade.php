@extends('layouts.auth')

@section('title', 'Nova senha')

@section('content')
    <h1 class="auth-card-title">Definir nova senha</h1>
    <p class="auth-card-sub">Escolha uma senha segura para a sua conta</p>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-group">
            <label class="form-label" for="email">E-mail</label>
            <input
                id="email"
                type="email"
                name="email"
                class="form-input {{ $errors->has('email') ? 'error' : '' }}"
                value="{{ old('email', $email) }}"
                autocomplete="email"
                required
            >
            @error('email')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Nova senha</label>
            <input
                id="password"
                type="password"
                name="password"
                class="form-input {{ $errors->has('password') ? 'error' : '' }}"
                autocomplete="new-password"
                autofocus
                required
            >
            @error('password')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password_confirmation">Confirmar nova senha</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                class="form-input"
                autocomplete="new-password"
                required
            >
        </div>

        <button type="submit" class="btn-primary">Redefinir senha</button>
    </form>
@endsection
