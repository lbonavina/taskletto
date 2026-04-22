<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo — Taskletto</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --accent: #ff914d;
            --accent-hover: #e87d3e;
            --accent-glow: rgba(255,145,77,.15);
            --font: 'Montserrat', sans-serif;
            --transition: .2s cubic-bezier(.4,0,.2,1);
        }

        [data-theme="dark"] {
            --bg: #0c0c0e;
            --surface: #161618;
            --border: rgba(255,255,255,.08);
            --text: #f0f0f0;
            --text-muted: #888;
            --text-faint: #444;
        }

        [data-theme="light"] {
            --bg: #f0eeeb;
            --surface: #ffffff;
            --border: rgba(0,0,0,.09);
            --text: #1a1a1a;
            --text-muted: #666;
            --text-faint: #ccc;
        }

        html, body {
            height: 100%;
            font-family: var(--font);
            background: var(--bg);
            color: var(--text);
            overflow: hidden;
        }

        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
            position: relative;
        }

        /* ambient glow */
        body::before {
            content: '';
            position: fixed;
            top: -20%;
            left: 50%;
            transform: translateX(-50%);
            width: 700px; height: 700px;
            background: radial-gradient(circle, rgba(255,145,77,.08) 0%, transparent 65%);
            pointer-events: none;
        }

        /* theme toggle */
        .theme-toggle {
            position: fixed;
            top: 16px; right: 16px;
            width: 34px; height: 34px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: var(--surface);
            color: var(--text-muted);
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: border-color var(--transition), color var(--transition);
        }
        .theme-toggle:hover { border-color: var(--accent); color: var(--accent); }

        /* logo */
        .logo-wrap {
            margin-bottom: 36px;
        }
        .logo-wrap img { height: 36px; width: auto; }
        .logo-img-dark  { display: block; }
        .logo-img-light { display: none; }
        [data-theme="light"] .logo-img-dark  { display: none; }
        [data-theme="light"] .logo-img-light { display: block; }

        /* headline */
        .welcome-title {
            font-size: clamp(1.8rem, 4vw, 2.8rem);
            font-weight: 900;
            letter-spacing: -1.5px;
            text-align: center;
            margin-bottom: 10px;
            line-height: 1.1;
        }

        .welcome-title span { color: var(--accent); }

        .welcome-sub {
            font-size: .95rem;
            color: var(--text-muted);
            text-align: center;
            max-width: 380px;
            margin-bottom: 48px;
            line-height: 1.6;
        }

        /* feature pills */
        .feature-pills {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 52px;
        }

        .pill {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 999px;
            font-size: .82rem;
            font-weight: 600;
        }

        .pill-icon {
            width: 28px; height: 28px;
            border-radius: 50%;
            background: var(--accent-glow);
            display: flex; align-items: center; justify-content: center;
            font-size: .9rem;
        }

        /* actions */
        .welcome-actions {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            width: 100%;
            max-width: 320px;
        }

        .btn-primary {
            width: 100%;
            padding: 12px;
            background: var(--accent);
            color: #1a1a1a;
            font-family: var(--font);
            font-weight: 700;
            font-size: .95rem;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: block;
            transition: background var(--transition), transform var(--transition);
        }
        .btn-primary:hover { background: var(--accent-hover); transform: translateY(-1px); }

        .btn-outline {
            width: 100%;
            padding: 12px;
            background: transparent;
            color: var(--text);
            font-family: var(--font);
            font-weight: 600;
            font-size: .95rem;
            border: 1px solid var(--border);
            border-radius: 10px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: block;
            transition: border-color var(--transition), color var(--transition);
        }
        .btn-outline:hover { border-color: var(--accent); color: var(--accent); }

        .btn-local {
            font-size: .78rem;
            color: var(--text-faint);
            background: none;
            border: none;
            cursor: pointer;
            font-family: var(--font);
            padding: 4px 0;
            transition: color var(--transition);
        }
        .btn-local:hover { color: var(--text-muted); }
    </style>
</head>
<body>
    <button class="theme-toggle" id="themeToggle" aria-label="Alternar tema">
        <svg id="themeIcon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="5"/>
            <line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
            <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
        </svg>
    </button>

    <div class="logo-wrap">
        <img src="/logo-taskletto-light.png" alt="Taskletto" class="logo-img-dark">
        <img src="/logo-taskletto.png"       alt="Taskletto" class="logo-img-light">
    </div>

    <h1 class="welcome-title">Organize tudo.<br><span>Sem distrações.</span></h1>
    <p class="welcome-sub">Tarefas, notas e categorias reunidos em um único app minimalista.</p>

    <div class="feature-pills">
        <div class="pill"><div class="pill-icon">✅</div> Tarefas & Subtarefas</div>
        <div class="pill"><div class="pill-icon">📝</div> Notas ricas</div>
        <div class="pill"><div class="pill-icon">🏷️</div> Categorias</div>
    </div>

    <div class="welcome-actions">
        <a href="{{ route('register') }}" class="btn-primary">Criar conta gratuita</a>
        <a href="{{ route('login') }}" class="btn-outline">Já tenho conta — Entrar</a>

    </div>

    <script>
        (function () {
            var saved = localStorage.getItem('taskletto-theme') || 'dark';
            document.documentElement.setAttribute('data-theme', saved);
            updateIcon(saved);
        })();

        function updateIcon(theme) {
            var icon = document.getElementById('themeIcon');
            if (!icon) return;
            if (theme === 'light') {
                icon.innerHTML = '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>';
            } else {
                icon.innerHTML = '<circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>';
            }
        }

        document.getElementById('themeToggle').addEventListener('click', function () {
            var current = document.documentElement.getAttribute('data-theme');
            var next = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('taskletto-theme', next);
            updateIcon(next);
        });
    </script>
</body>
</html>
