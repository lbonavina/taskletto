@extends('layouts.auth')

@section('title', 'Recuperar senha')

@section('content')
    <h1 class="auth-card-title">Recuperar senha</h1>
    <p class="auth-card-sub">Informe seu e-mail e enviaremos um link para redefinir sua senha</p>

    @if (session('status'))
        <div class="alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="email">E-mail</label>
            <input
                id="email"
                type="email"
                name="email"
                class="form-input {{ $errors->has('email') ? 'error' : '' }}"
                value="{{ old('email') }}"
                autocomplete="email"
                autofocus
                required
            >
            @error('email')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-primary">Enviar link de recuperação</button>
    </form>

    <div class="auth-footer">
        <a href="{{ route('login') }}">← Voltar para o login</a>
    </div>
@endsection
