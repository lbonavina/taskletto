<!DOCTYPE html>
<html lang="pt-BR" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Acesso restrito — Taskletto</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
    --accent: #ff914d;
    --font: 'Montserrat', sans-serif;
}
[data-theme="dark"]  { --bg: #0c0c0e; --surface: #161618; --border: rgba(255,255,255,.08); --text: #f0f0f0; --muted: #888; --danger: #ef4444; --danger-bg: rgba(239,68,68,.1); }
[data-theme="light"] { --bg: #f4f5f9; --surface: #ffffff; --border: #e2e4ee; --text: #0e0f1a; --muted: #636580; --danger: #dc2626; --danger-bg: rgba(220,38,38,.08); }

body {
    font-family: var(--font);
    background: var(--bg);
    color: var(--text);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
}

.gate-card {
    width: 100%;
    max-width: 400px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 32px;
}

.gate-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    background: rgba(255,145,77,.1);
    border: 1px solid rgba(255,145,77,.2);
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 20px;
}

.gate-title { font-size: 17px; font-weight: 700; margin-bottom: 6px; }
.gate-desc  { font-size: 13px; color: var(--muted); line-height: 1.6; margin-bottom: 24px; }

.form-label  { display: block; font-size: 12px; font-weight: 600; color: var(--muted); margin-bottom: 6px; text-transform: uppercase; letter-spacing: .04em; }
.form-input  {
    width: 100%; padding: 10px 14px;
    border: 1px solid var(--border); border-radius: 10px;
    background: var(--bg); color: var(--text);
    font-family: var(--font); font-size: 14px;
    outline: none; transition: border-color .15s;
    margin-bottom: 14px;
}
.form-input:focus { border-color: var(--accent); }
.form-error { font-size: 12px; color: var(--danger); background: var(--danger-bg); padding: 8px 12px; border-radius: 8px; margin-bottom: 14px; }

.btn-primary {
    width: 100%; padding: 11px;
    background: var(--accent); color: #1a1a1a;
    border: none; border-radius: 10px;
    font-family: var(--font); font-size: 14px; font-weight: 700;
    cursor: pointer; transition: opacity .15s;
}
.btn-primary:hover { opacity: .88; }

.gate-brand {
    text-align: center; margin-top: 24px;
    font-size: 11px; color: var(--muted);
}
.gate-brand a { color: var(--accent); text-decoration: none; }
</style>
</head>
<body>
<div class="gate-card">
    <div class="gate-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ff914d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
        </svg>
    </div>
    <div class="gate-title">Nota privada</div>
    <div class="gate-desc">
        Esta nota está protegida. Informe seu e-mail para verificar se você tem acesso.
    </div>

    <form method="POST" action="{{ route('share.gate', $share->token) }}">
        @csrf
        <label class="form-label" for="email">Seu e-mail</label>
        @error('email')
            <div class="form-error">{{ $message }}</div>
        @enderror
        <input type="email" name="email" id="email" class="form-input"
               placeholder="voce@email.com" value="{{ old('email') }}" autofocus required>
        <button type="submit" class="btn-primary">Verificar acesso</button>
    </form>

    <div class="gate-brand">
        Compartilhado via <a href="/">Taskletto</a>
    </div>
</div>
</body>
</html>
