<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Taskletto') — Taskletto</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --accent: #ff914d;
            --accent-hover: #e87d3e;
            --accent-glow: rgba(255,145,77,.15);
            --radius: 12px;
            --font: 'Montserrat', sans-serif;
            --transition: .2s cubic-bezier(.4,0,.2,1);
            --danger: #ef4444;
            --danger-bg: rgba(239,68,68,.1);
            
            --bg: #f0eeeb;
            --surface: #ffffff;
            --surface2: #f5f4f2;
            --border: rgba(0,0,0,.09);
            --text: #1a1a1a;
            --text-muted: #666;
            --text-faint: #bbb;
            --input-bg: #f8f7f5;
            --input-border: rgba(0,0,0,.12);
            --input-focus: rgba(255,145,77,.35);
        }

        html { height: 100%; }

        body {
            font-family: var(--font);
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient glow background */
        body::before {
            content: '';
            position: fixed;
            top: -30%;
            left: 50%;
            transform: translateX(-50%);
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(255,145,77,.07) 0%, transparent 70%);
            pointer-events: none;
        }

        /* ── LOGO ── */
        .auth-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 28px;
            text-decoration: none;
        }

        .auth-logo img {
            height: 32px;
            width: auto;
        }

        .logo-img-dark  { display: none; }
        .logo-img-light { display: block; }

        /* ── CARD ── */
        .auth-card {
            width: 100%;
            max-width: 420px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 40px 36px;
            position: relative;
        }

        .auth-card-title {
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: -.5px;
            margin-bottom: 6px;
        }

        .auth-card-sub {
            font-size: .875rem;
            color: var(--text-muted);
            margin-bottom: 28px;
        }

        /* ── FORM ── */
        .form-group { margin-bottom: 16px; }

        .form-label {
            display: block;
            font-size: .78rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 6px;
            letter-spacing: .3px;
        }

        .form-input {
            width: 100%;
            padding: 10px 14px;
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 9px;
            color: var(--text);
            font-family: var(--font);
            font-size: .9rem;
            outline: none;
            transition: border-color var(--transition), box-shadow var(--transition);
        }

        .form-input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--input-focus);
        }

        .form-input.error {
            border-color: var(--danger);
            box-shadow: 0 0 0 3px rgba(239,68,68,.15);
        }

        .form-error {
            margin-top: 5px;
            font-size: .75rem;
            color: var(--danger);
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: .82rem;
            color: var(--text-muted);
            cursor: pointer;
        }

        .form-check input { accent-color: var(--accent); cursor: pointer; }

        /* ── BUTTONS ── */
        .btn-primary {
            width: 100%;
            padding: 11px;
            background: var(--accent);
            color: #1a1a1a;
            font-family: var(--font);
            font-weight: 700;
            font-size: .9rem;
            border: none;
            border-radius: 9px;
            cursor: pointer;
            transition: background var(--transition), transform var(--transition);
            margin-top: 8px;
        }

        .btn-primary:hover {
            background: var(--accent-hover);
            transform: translateY(-1px);
        }

        .btn-primary:focus-visible {
            outline: 2px solid var(--accent);
            outline-offset: 3px;
        }

        /* ── DIVIDER ── */
        .auth-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 20px 0;
            color: var(--text-faint);
            font-size: .75rem;
            font-weight: 500;
        }

        .auth-divider::before,
        .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        /* ── OAUTH BUTTONS ── */
        .oauth-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn-oauth {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 10px;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 9px;
            color: var(--text);
            font-family: var(--font);
            font-weight: 600;
            font-size: .875rem;
            text-decoration: none;
            cursor: pointer;
            transition: border-color var(--transition), background var(--transition);
        }

        .btn-oauth:hover {
            border-color: var(--accent);
            background: var(--input-bg);
        }

        .btn-oauth svg { flex-shrink: 0; }

        /* ── FOOTER LINKS ── */
        .auth-footer {
            margin-top: 24px;
            text-align: center;
            font-size: .82rem;
            color: var(--text-muted);
        }

        .auth-footer a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
        }

        .auth-footer a:hover { text-decoration: underline; }

        /* ── ALERTS ── */
        .alert-success {
            background: rgba(34,197,94,.1);
            border: 1px solid rgba(34,197,94,.25);
            border-radius: 8px;
            padding: 10px 14px;
            font-size: .82rem;
            color: #22c55e;
            margin-bottom: 16px;
        }

        .alert-error {
            background: var(--danger-bg);
            border: 1px solid rgba(239,68,68,.25);
            border-radius: 8px;
            padding: 10px 14px;
            font-size: .82rem;
            color: var(--danger);
            margin-bottom: 16px;
        }

        /* ── FORGOT LINK ── */
        .forgot-link {
            float: right;
            font-size: .75rem;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            margin-top: -2px;
        }

        .forgot-link:hover { color: var(--accent); }

        /* ── PASSWORD TOGGLE ── */
        .password-wrapper { position: relative; }
        .password-input { padding-right: 44px !important; }
        .password-toggle {
            position: absolute; right: 12px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none; padding: 4px;
            color: var(--text-muted); cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: color var(--transition);
            z-index: 2;
        }
        .password-toggle:hover { color: var(--accent); }

        @media (max-width: 480px) {
            .auth-card { padding: 28px 20px; border-radius: 14px; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <a href="{{ url('/') }}" class="auth-logo">
        <img src="/logo-taskletto.png" alt="Taskletto" class="logo-img-light" />
    </a>

    <div class="auth-card">
        @yield('content')
    </div>

    @yield('below-card')

    <script>

        function togglePassword(id) {
            const input = document.getElementById(id);
            const btn = event.currentTarget;
            if (!input) return;

            if (input.type === 'password') {
                input.type = 'text';
                btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
            } else {
                input.type = 'password';
                btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
