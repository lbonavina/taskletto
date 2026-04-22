<!DOCTYPE html>
<html lang="pt-BR" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $share->note->title ?: 'Nota sem título' }} — Taskletto</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root { --accent: #ff914d; --font: 'Montserrat', sans-serif; }
[data-theme="dark"]  { --bg: #0c0c0e; --surface: #111113; --border: rgba(255,255,255,.07); --text: #e8e8ec; --muted: #7a7a8a; --code-bg: #1a1a22; }
[data-theme="light"] { --bg: #f4f5f9; --surface: #ffffff; --border: #e2e4ee; --text: #0e0f1a; --muted: #636580; --code-bg: #f0f2f7; }

body { font-family: var(--font); background: var(--bg); color: var(--text); min-height: 100vh; }

/* ── Header ── */
.share-header {
    position: sticky; top: 0; z-index: 100;
    background: var(--surface);
    border-bottom: 1px solid var(--border);
    padding: 12px 24px;
    display: flex; align-items: center; justify-content: space-between;
    gap: 12px;
}
.share-header-brand {
    font-size: 13px; font-weight: 700; color: var(--accent);
    text-decoration: none; flex-shrink: 0;
}
.share-header-title {
    font-size: 13px; color: var(--muted);
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    flex: 1; text-align: center;
}
.share-header-cta {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 14px; border-radius: 8px;
    background: var(--accent); color: #1a1a1a;
    font-size: 12px; font-weight: 700; text-decoration: none;
    white-space: nowrap; flex-shrink: 0;
    transition: opacity .15s;
}
.share-header-cta:hover { opacity: .88; }

/* ── Content ── */
.share-body { max-width: 720px; margin: 0 auto; padding: 40px 24px 80px; }

.share-note-title {
    font-size: 28px; font-weight: 800; line-height: 1.25;
    margin-bottom: 8px; color: var(--text);
}
.share-meta {
    font-size: 12px; color: var(--muted); margin-bottom: 32px;
    display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
}
.share-meta-dot { width: 3px; height: 3px; border-radius: 50%; background: var(--muted); }

/* ── ProseMirror styles (read-only) ── */
.share-content { font-size: 15px; line-height: 1.75; color: var(--text); }
.share-content h1 { font-size: 22px; font-weight: 700; margin: 28px 0 12px; border-bottom: 1px solid var(--border); padding-bottom: 8px; }
.share-content h2 { font-size: 18px; font-weight: 700; margin: 24px 0 10px; }
.share-content h3 { font-size: 15px; font-weight: 600; margin: 20px 0 8px; }
.share-content p  { margin-bottom: 14px; }
.share-content ul, .share-content ol { padding-left: 20px; margin-bottom: 14px; }
.share-content li { margin-bottom: 4px; }
.share-content blockquote { border-left: 3px solid var(--accent); padding: 8px 16px; margin: 16px 0; color: var(--muted); font-style: italic; }
.share-content pre { background: var(--code-bg); border-radius: 8px; padding: 16px; overflow-x: auto; margin: 16px 0; }
.share-content code { background: var(--code-bg); padding: 2px 6px; border-radius: 4px; font-size: 13px; font-family: 'Fira Code', monospace; }
.share-content pre code { background: none; padding: 0; }
.share-content strong { font-weight: 700; }
.share-content em { font-style: italic; }
.share-content a { color: var(--accent); }
.share-content hr { border: none; border-top: 1px solid var(--border); margin: 24px 0; }
.share-content table { width: 100%; border-collapse: collapse; margin: 16px 0; font-size: 13px; }
.share-content th, .share-content td { border: 1px solid var(--border); padding: 8px 12px; text-align: left; }
.share-content th { background: var(--code-bg); font-weight: 600; }

/* ── Footer ── */
.share-footer {
    text-align: center; padding: 24px;
    border-top: 1px solid var(--border);
    font-size: 12px; color: var(--muted);
}
.share-footer a { color: var(--accent); text-decoration: none; }

/* ── Theme toggle ── */
.btn-theme {
    background: none; border: 1px solid var(--border);
    color: var(--muted); border-radius: 8px;
    padding: 5px 9px; cursor: pointer; font-size: 13px;
    transition: border-color .15s, color .15s;
}
.btn-theme:hover { border-color: var(--accent); color: var(--accent); }
</style>
</head>
<body>

<header class="share-header">
    <a href="/" class="share-header-brand">Taskletto</a>
    <div class="share-header-title">{{ $share->note->title ?: 'Nota sem título' }}</div>
    <div style="display:flex;align-items:center;gap:8px">
        <button class="btn-theme" onclick="toggleTheme()" title="Alternar tema">🌙</button>
        <a href="{{ route('register') }}" class="share-header-cta">Criar conta grátis →</a>
    </div>
</header>

<div class="share-body">
    <h1 class="share-note-title">{{ $share->note->title ?: 'Nota sem título' }}</h1>
    <div class="share-meta">
        <span>Compartilhado em {{ $share->created_at->format('d/m/Y') }}</span>
        @if($share->visibility === 'private')
            <span class="share-meta-dot"></span>
            <span>🔒 Acesso privado</span>
        @endif
        @if($share->expires_at)
            <span class="share-meta-dot"></span>
            <span>Expira em {{ $share->expires_at->format('d/m/Y') }}</span>
        @endif
    </div>

    <div class="share-content">
        {!! $share->note->content !!}
    </div>
</div>

<footer class="share-footer">
    Criado com <a href="/">Taskletto</a> — organize tudo, sem distrações.
</footer>

<script>
function toggleTheme() {
    const html = document.documentElement;
    const next = html.dataset.theme === 'dark' ? 'light' : 'dark';
    html.dataset.theme = next;
    localStorage.setItem('theme', next);
}
(function() {
    const t = localStorage.getItem('theme');
    if (t) document.documentElement.dataset.theme = t;
})();
</script>
</body>
</html>
