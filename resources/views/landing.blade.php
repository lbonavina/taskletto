<!DOCTYPE html>
<html lang="pt-BR" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taskletto — Organize tudo. Sem distrações.</title>
    <meta name="description" content="Taskletto é um gerenciador de tarefas e notas para desktop e web. Organize sua vida com categorias, subtarefas, notas ricas e muito mais.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --accent:       #ff914d;
            --accent-dim:   #e87d3e;
            --accent-glow:  rgba(255,145,77,.14);
            --accent-glow2: rgba(255,145,77,.06);
            --radius:       12px;
            --font:         'Montserrat', sans-serif;
            --ease:         cubic-bezier(.4,0,.2,1);
        }

        [data-theme="dark"] {
            --bg:         #0a0a0a;
            --bg2:        #111111;
            --bg3:        #0f0f0f;
            --surface:    #181818;
            --surface2:   #202020;
            --surface3:   #272727;
            --border:     rgba(255,255,255,.07);
            --border2:    rgba(255,255,255,.12);
            --text:       #efefef;
            --text-muted: #888;
            --text-faint: #444;
            --nav-bg:     rgba(10,10,10,.85);
            --shadow:     rgba(0,0,0,.6);
        }

        [data-theme="light"] {
            --bg:         #f7f6f3;
            --bg2:        #eeece9;
            --bg3:        #f2f0ed;
            --surface:    #ffffff;
            --surface2:   #f4f3f0;
            --surface3:   #eceae6;
            --border:     rgba(0,0,0,.08);
            --border2:    rgba(0,0,0,.14);
            --text:       #1a1a1a;
            --text-muted: #666;
            --text-faint: #bbb;
            --nav-bg:     rgba(247,246,243,.88);
            --shadow:     rgba(0,0,0,.12);
        }

        html { scroll-behavior: smooth; }
        body { font-family: var(--font); background: var(--bg); color: var(--text); line-height: 1.6; overflow-x: hidden; }

        /* ─────────────────────────── NAV ─────────────────────────── */
        .nav {
            position: sticky; top: 0; z-index: 200;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 max(24px, calc(50vw - 640px));
            height: 60px;
            background: var(--nav-bg);
            backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            transition: background .3s var(--ease);
        }
        .nav-logo { display: flex; align-items: center; text-decoration: none; flex-shrink: 0; }
        .logo-img  { height: 28px; width: auto; display: block; }
        [data-theme="dark"]  .logo-dark  { display: block; }
        [data-theme="dark"]  .logo-light { display: none; }
        [data-theme="light"] .logo-dark  { display: none; }
        [data-theme="light"] .logo-light { display: block; }

        .nav-links { display: flex; align-items: center; gap: 6px; list-style: none; }
        .nav-links a {
            color: var(--text-muted); text-decoration: none;
            font-size: .82rem; font-weight: 600;
            padding: 6px 12px; border-radius: 7px;
            transition: color .2s, background .2s;
        }
        .nav-links a:hover { color: var(--text); background: var(--surface2); }

        .nav-actions { display: flex; align-items: center; gap: 8px; }
        .btn-theme {
            width: 34px; height: 34px; border: 1px solid var(--border);
            border-radius: 8px; background: transparent; color: var(--text-muted);
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            font-size: .9rem; transition: color .2s, border-color .2s, background .2s;
        }
        .btn-theme:hover { color: var(--text); background: var(--surface2); }

        .btn-primary {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; background: var(--accent); color: #1a1a1a;
            font-family: var(--font); font-weight: 700; font-size: .82rem;
            border-radius: 8px; text-decoration: none; border: none; cursor: pointer;
            transition: background .2s, transform .2s, box-shadow .2s;
        }
        .btn-primary:hover { background: var(--accent-dim); transform: translateY(-1px); box-shadow: 0 6px 20px rgba(255,145,77,.3); }

        .btn-outline {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; background: transparent; color: var(--text);
            font-family: var(--font); font-weight: 600; font-size: .82rem;
            border-radius: 8px; text-decoration: none; border: 1px solid var(--border2); cursor: pointer;
            transition: border-color .2s, color .2s, background .2s;
        }
        .btn-outline:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-glow2); }

        /* ─────────────────────────── HERO ─────────────────────────── */
        .hero {
            position: relative; overflow: hidden;
            padding: 96px max(24px, calc(50vw - 640px)) 80px;
            display: grid; grid-template-columns: 1fr 1fr;
            align-items: center; gap: 64px;
            min-height: calc(100vh - 60px);
        }

        /* radial glow from top-left */
        .hero::before {
            content: ''; position: absolute; inset: 0; pointer-events: none;
            background:
                radial-gradient(ellipse 700px 500px at 15% 0%, rgba(255,145,77,.12) 0%, transparent 70%),
                radial-gradient(ellipse 500px 400px at 85% 100%, rgba(255,145,77,.06) 0%, transparent 70%);
        }
        /* subtle dot grid */
        .hero::after {
            content: ''; position: absolute; inset: 0; pointer-events: none;
            background-image: radial-gradient(circle, var(--text-faint) 1px, transparent 1px);
            background-size: 32px 32px; opacity: .18;
            mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 40%, transparent 100%);
        }

        .hero-text { position: relative; z-index: 1; }

        .hero-badge {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 5px 14px 5px 8px;
            background: var(--accent-glow); border: 1px solid rgba(255,145,77,.25);
            border-radius: 999px; margin-bottom: 28px;
            font-size: .72rem; font-weight: 700; color: var(--accent);
            letter-spacing: .6px; text-transform: uppercase;
        }
        .hero-badge-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: var(--accent); animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: .5; transform: scale(.7); }
        }

        .hero h1 {
            font-size: clamp(2.4rem, 4.5vw, 3.8rem);
            font-weight: 900; line-height: 1.08; letter-spacing: -2.5px;
            margin-bottom: 20px;
        }
        .hero h1 em { font-style: normal; color: var(--accent); }

        .hero-sub {
            font-size: 1.05rem; color: var(--text-muted); max-width: 440px;
            line-height: 1.7; margin-bottom: 36px;
        }

        .hero-cta { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 48px; }
        .hero-cta .btn-primary { font-size: .92rem; padding: 11px 22px; }
        .hero-cta .btn-outline  { font-size: .92rem; padding: 11px 22px; }

        .hero-meta {
            display: flex; gap: 24px; align-items: center; flex-wrap: wrap;
        }
        .hero-meta-item {
            display: flex; align-items: center; gap: 7px;
            font-size: .78rem; color: var(--text-muted); font-weight: 500;
        }
        .hero-meta-item svg { color: var(--accent); flex-shrink: 0; }

        /* ── Hero app mockup ── */
        .hero-visual { position: relative; z-index: 1; }

        .app-frame {
            border-radius: 14px; overflow: hidden;
            border: 1px solid var(--border2);
            box-shadow: 0 48px 100px var(--shadow), 0 0 0 1px rgba(255,255,255,.04);
            background: var(--surface);
        }
        .app-frame-bar {
            display: flex; align-items: center; gap: 8px;
            padding: 10px 16px; background: var(--surface2);
            border-bottom: 1px solid var(--border);
        }
        .wdot { width: 11px; height: 11px; border-radius: 50%; }
        .wdot.r { background: #ff5f57; }
        .wdot.y { background: #febc2e; }
        .wdot.g { background: #28c840; }
        .app-frame-bar-title {
            flex: 1; text-align: center; font-size: .7rem;
            color: var(--text-faint); font-weight: 500;
        }

        /* ── Hero app mockup (Taskletto Actual UI) ── */
        .hero-visual { position: relative; z-index: 1; }

        .app-frame {
            border-radius: 14px; overflow: hidden;
            border: 1px solid var(--border2);
            box-shadow: 0 48px 100px var(--shadow), 0 0 0 1px rgba(255,255,255,.04);
            background: #fdfdfd; /* White theme inside mockup */
            color: #333;
        }
        .app-frame-bar {
            display: flex; align-items: center; gap: 8px;
            padding: 10px 16px; background: #f0f0f0;
            border-bottom: 1px solid #ddd;
        }
        .wdot { width: 11px; height: 11px; border-radius: 50%; }
        .wdot.r { background: #ff5f57; }
        .wdot.y { background: #febc2e; }
        .wdot.g { background: #28c840; }
        .app-frame-bar-title {
            flex: 1; text-align: center; font-size: .7rem;
            color: #999; font-weight: 600;
        }

        .app-body { display: flex; height: 420px; overflow: hidden; }

        .app-sidebar {
            width: 200px; background: #fff;
            border-right: 1px solid #e5e7eb;
            display: flex; flex-direction: column; padding: 16px 0;
        }
        .sidebar-brand { padding: 0 16px 20px; }
        .sidebar-brand img { height: 22px; }

        .btn-new-task {
            margin: 0 12px 24px; background: var(--accent);
            color: #fff; padding: 10px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            font-size: .75rem; font-weight: 700; text-decoration: none;
        }

        .sidebar-section {
            font-size: .65rem; font-weight: 800; color: #aaa;
            letter-spacing: 1px; text-transform: uppercase;
            padding: 0 16px 8px;
        }
        .sidebar-item {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 16px; font-size: .78rem; font-weight: 600;
            color: #555; cursor: default;
        }
        .sidebar-item svg { width: 16px; height: 16px; color: #888; }
        .sidebar-item.active { background: #fff8f4; color: var(--accent); }
        .sidebar-item.active svg { color: var(--accent); }

        .sidebar-footer { margin-top: auto; padding: 16px; border-top: 1px solid #f0f0f0; }
        .user-pill {
            display: flex; align-items: center; gap: 8px;
            background: #f9fafb; padding: 6px 10px; border-radius: 8px;
            border: 1px solid #eee;
        }
        .user-avatar { width: 24px; height: 24px; border-radius: 6px; background: #ddd; }
        .user-info { flex: 1; overflow: hidden; }
        .user-name { font-size: .65rem; font-weight: 800; color: #333; }
        .user-email { font-size: .55rem; color: #888; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        .app-content { flex: 1; background: #f9fafb; display: flex; flex-direction: column; position: relative; }

        /* Top Pills Area */
        .content-tabs {
            background: #fff; border-bottom: 1px solid #eee;
            padding: 12px 16px; display: flex; gap: 8px; flex-wrap: wrap;
        }
        .tab-pill {
            display: flex; align-items: center; gap: 6px;
            padding: 6px 12px; border-radius: 999px;
            font-size: .68rem; font-weight: 700; color: #666;
            background: #f3f4f6; border: 1px solid #e5e7eb;
        }
        .tab-pill.active { background: var(--accent); color: #fff; border-color: var(--accent); }
        .tab-pill .dot { width: 6px; height: 6px; border-radius: 50%; background: #ccc; }
        .tab-pill .count { font-size: .62rem; opacity: .7; }

        /* Action Bar */
        .action-bar {
            padding: 10px 16px; display: flex; align-items: center; gap: 12px;
            border-bottom: 1px solid #eee; background: #fff;
        }
        .mock-search {
            flex: 1; background: #f3f4f6; border: 1px solid #e5e7eb;
            border-radius: 8px; padding: 6px 12px; font-size: .7rem; color: #aaa;
            display: flex; align-items: center; gap: 6px;
        }
        .mock-select {
            padding: 6px 10px; background: #fff; border: 1px solid #e5e7eb;
            border-radius: 8px; font-size: .65rem; color: #666; font-weight: 600;
        }
        .mock-toggle { display: flex; align-items: center; gap: 8px; font-size: .62rem; font-weight: 800; color: #444; }
        .tag-switch { width: 32px; height: 16px; background: #d1d5db; border-radius: 999px; position: relative; }
        .tag-switch::after { content: ''; position: absolute; left: 2px; top: 2px; width: 12px; height: 12px; background: #fff; border-radius: 50%; }

        /* Task Table */
        .task-table { width: 100%; border-collapse: collapse; background: #fff; border: 1px solid #eee; margin: 16px; border-radius: 8px; overflow: hidden; }
        .task-table th {
            text-align: left; padding: 10px 12px; font-size: .6rem; font-weight: 800;
            color: #aaa; text-transform: uppercase; letter-spacing: .5px; background: #fafafa;
            border-bottom: 1px solid #eee;
        }
        .task-table td { padding: 12px; font-size: .7rem; color: #444; border-bottom: 1px solid #f3f4f6; }
        .row-pill { padding: 3px 10px; border-radius: 999px; font-size: .62rem; font-weight: 800; }
        .row-pill.green { background: #ecfdf5; color: #10b981; border: 1px solid #10b98133; }
        .row-pill.blue { background: #eff6ff; color: #3b82f6; border: 1px solid #3b82f633; }
        .row-pill.orange { background: #fff7ed; color: #f97316; border: 1px solid #f9731633; }
        .row-pill.gray { background: #f9fafb; color: #6b7280; border: 1px solid #e5e7eb; }

        .tag-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 4px; }
        .table-footer { padding: 10px 12px; color: #aaa; font-size: .7rem; font-weight: 600; }

        /* ─────────────────────────── SOCIAL PROOF BAR ─────────────────────────── */
        .proof-bar {
            display: flex; align-items: center; justify-content: center;
            gap: 0; padding: 0 max(24px, calc(50vw - 640px));
            border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);
            background: var(--bg2); overflow: hidden;
        }
        .proof-item {
            display: flex; align-items: center; gap: 10px;
            padding: 20px 40px; flex: 1; justify-content: center;
        }
        .proof-item + .proof-item { border-left: 1px solid var(--border); }
        .proof-icon {
            width: 36px; height: 36px; border-radius: 9px;
            background: var(--accent-glow); color: var(--accent);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .proof-icon svg { width: 17px; height: 17px; }
        .proof-label { font-size: .8rem; font-weight: 600; color: var(--text-muted); line-height: 1.3; }
        .proof-label strong { display: block; font-size: .95rem; font-weight: 800; color: var(--text); }

        /* ─────────────────────────── FEATURES ─────────────────────────── */
        #features { padding: 96px max(24px, calc(50vw - 640px)); background: var(--bg3); }

        .section-eyebrow {
            font-size: .7rem; font-weight: 700; letter-spacing: 1.8px;
            text-transform: uppercase; color: var(--accent); margin-bottom: 12px;
        }
        .section-title {
            font-size: clamp(1.7rem, 3.5vw, 2.6rem);
            font-weight: 900; letter-spacing: -1.2px; margin-bottom: 14px; line-height: 1.15;
        }
        .section-sub {
            font-size: .95rem; color: var(--text-muted);
            max-width: 460px; margin-bottom: 56px; line-height: 1.7;
            margin-left: auto; margin-right: auto;
        }

        .features-grid {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;
        }

        .feature-card {
            padding: 28px 26px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            transition: border-color .2s, transform .2s, box-shadow .2s;
            position: relative; overflow: hidden;
        }
        .feature-card::before {
            content: ''; position: absolute; inset: 0; opacity: 0;
            background: radial-gradient(ellipse 60% 50% at 50% 0%, var(--accent-glow) 0%, transparent 100%);
            transition: opacity .3s;
        }
        .feature-card:hover { border-color: rgba(255,145,77,.3); transform: translateY(-3px); box-shadow: 0 12px 40px rgba(0,0,0,.15); }
        .feature-card:hover::before { opacity: 1; }

        .feature-icon-wrap {
            width: 44px; height: 44px; border-radius: 11px;
            background: var(--accent-glow); color: var(--accent);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 18px; position: relative; z-index: 1;
        }
        .feature-icon-wrap svg { width: 22px; height: 22px; }
        .feature-card h3 { font-size: .95rem; font-weight: 800; margin-bottom: 8px; position: relative; z-index: 1; }
        .feature-card p  { font-size: .82rem; color: var(--text-muted); line-height: 1.6; position: relative; z-index: 1; }

        /* ─────────────────────────── SHOWCASE (tasks + notes) ─────────────────────────── */
        .showcase {
            padding: 96px max(24px, calc(50vw - 640px));
            display: grid; grid-template-columns: 1fr 1fr;
            align-items: center; gap: 72px;
        }
        .showcase.alt { background: var(--bg2); }
        .showcase.reverse .showcase-visual { order: -1; }

        .showcase-text {}
        .showcase-checklist {
            list-style: none; display: flex; flex-direction: column; gap: 14px; margin-top: 32px;
        }
        .showcase-checklist li {
            display: flex; align-items: flex-start; gap: 11px;
            font-size: .88rem; color: var(--text-muted);
        }
        .check-circle {
            width: 22px; height: 22px; border-radius: 50%;
            background: var(--accent-glow); color: var(--accent);
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
            margin-top: 1px;
        }
        .check-circle svg { width: 11px; height: 11px; }

        .showcase-visual .app-frame { box-shadow: 0 32px 80px var(--shadow); }

        /* task mock (reused from original) */
        .task-mock {
            padding: 16px; display: flex; flex-direction: column;
            gap: 6px; background: var(--surface);
        }
        .task-mock-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 4px 0 10px; border-bottom: 1px solid var(--border); margin-bottom: 4px;
        }
        .task-mock-header-title { font-size: .68rem; font-weight: 700; color: var(--text-muted); letter-spacing: .5px; text-transform: uppercase; }
        .task-mock-count { font-size: .65rem; font-weight: 700; color: var(--accent); background: var(--accent-glow); padding: 2px 8px; border-radius: 999px; }
        .task-item {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 10px 12px; border-radius: 8px;
            border: 1px solid var(--border); background: var(--surface2); transition: border-color .15s;
        }
        .task-item.done { opacity: .55; }
        .task-cb {
            width: 16px; height: 16px; border-radius: 50%;
            border: 2px solid var(--border2); flex-shrink: 0; margin-top: 2px;
            display: flex; align-items: center; justify-content: center;
        }
        .task-item.done .task-cb { background: var(--accent); border-color: var(--accent); }
        .task-item.done .task-cb::after {
            content: ''; display: block; width: 6px; height: 4px;
            border-left: 1.5px solid #1a1a1a; border-bottom: 1.5px solid #1a1a1a;
            transform: rotate(-45deg) translateY(-1px);
        }
        .task-body { flex: 1; min-width: 0; }
        .task-title { font-size: .82rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .task-item.done .task-title { text-decoration: line-through; color: var(--text-muted); }
        .task-meta { display: flex; align-items: center; gap: 5px; margin-top: 4px; flex-wrap: wrap; }
        .task-badge { font-size: .62rem; font-weight: 700; padding: 1px 7px; border-radius: 999px; letter-spacing: .3px; }
        .task-badge.high   { background: rgba(239,68,68,.15);  color: #ef4444; }
        .task-badge.mid    { background: rgba(234,179,8,.15);  color: #ca8a04; }
        .task-badge.low    { background: rgba(99,102,241,.15); color: #818cf8; }
        .task-cat { font-size: .62rem; font-weight: 600; padding: 1px 7px; border-radius: 999px; background: var(--accent-glow); color: var(--accent); }
        .task-date { font-size: .62rem; color: var(--text-faint); display: flex; align-items: center; gap: 3px; margin-left: auto; }
        .task-date.overdue { color: #ef4444; }
        .subtask-list { margin-top: 6px; margin-left: 26px; display: flex; flex-direction: column; gap: 4px; }
        .subtask-item { display: flex; align-items: center; gap: 7px; font-size: .72rem; color: var(--text-muted); }
        .subtask-cb { width: 12px; height: 12px; border-radius: 50%; border: 1.5px solid var(--border2); flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
        .subtask-item.done .subtask-cb { background: var(--accent); border-color: var(--accent); }
        .subtask-item.done .subtask-cb::after { content: ''; display: block; width: 5px; height: 3px; border-left: 1.5px solid #1a1a1a; border-bottom: 1.5px solid #1a1a1a; transform: rotate(-45deg) translateY(-1px); }
        .subtask-item.done span { text-decoration: line-through; opacity: .6; }

        /* note mock */
        .note-mock { padding: 20px 24px; background: var(--surface); }
        .note-mock-title { font-size: .95rem; font-weight: 800; color: var(--text); margin-bottom: 4px; }
        .note-mock-date { font-size: .65rem; color: var(--text-faint); margin-bottom: 16px; }
        .note-block { margin-bottom: 10px; }
        .note-h2 { font-size: .8rem; font-weight: 800; color: var(--text); margin-bottom: 6px; }
        .note-p { font-size: .72rem; color: var(--text-muted); line-height: 1.65; margin-bottom: 6px; }
        .note-code {
            background: #0d0d10; border: 1px solid var(--border2);
            border-radius: 8px; padding: 10px 14px;
            font-size: .68rem; font-family: 'Courier New', monospace;
            color: #c3e88d; line-height: 1.6; margin-bottom: 10px;
        }
        .note-code .kw { color: #c792ea; }
        .note-code .str { color: #c3e88d; }
        .note-code .fn { color: #82aaff; }
        .note-tag {
            display: inline-block; font-size: .62rem; font-weight: 700;
            padding: 2px 8px; border-radius: 999px; margin-right: 4px; margin-bottom: 4px;
        }
        .note-tag.blue   { background: rgba(96,165,250,.15); color: #60a5fa; }
        .note-tag.purple { background: rgba(167,139,250,.15); color: #a78bfa; }
        .note-tag.green  { background: rgba(52,211,153,.15);  color: #34d399; }
        .note-toolbar {
            display: flex; gap: 4px; padding-bottom: 10px;
            border-bottom: 1px solid var(--border); margin-bottom: 12px;
        }
        .note-tb-btn {
            width: 26px; height: 26px; border-radius: 6px;
            background: var(--surface2); border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            font-size: .65rem; font-weight: 700; color: var(--text-muted);
        }

        /* ─────────────────────────── HOW IT WORKS ─────────────────────────── */
        #how {
            padding: 96px max(24px, calc(50vw - 640px)); background: var(--bg3);
            text-align: center;
        }
        #how .section-sub { margin-left: auto; margin-right: auto; }

        .steps {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px;
            margin-top: 56px; position: relative;
        }
        .steps::before {
            content: ''; position: absolute; top: 28px; left: calc(16.66% + 24px); right: calc(16.66% + 24px);
            height: 1px; background: linear-gradient(90deg, var(--accent), transparent 50%, var(--accent));
            opacity: .3;
        }

        .step { display: flex; flex-direction: column; align-items: center; text-align: center; }
        .step-num {
            width: 56px; height: 56px; border-radius: 50%;
            border: 2px solid rgba(255,145,77,.4);
            background: var(--accent-glow);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; font-weight: 900; color: var(--accent);
            margin-bottom: 20px; position: relative; z-index: 1;
        }
        .step h3 { font-size: .95rem; font-weight: 800; margin-bottom: 10px; }
        .step p   { font-size: .82rem; color: var(--text-muted); line-height: 1.6; max-width: 220px; }

        /* ─────────────────────────── CTA BANNER ─────────────────────────── */
        .cta-banner {
            padding: 96px max(24px, calc(50vw - 640px));
            background: var(--bg2);
            text-align: center; position: relative; overflow: hidden;
        }
        .cta-banner::before {
            content: ''; position: absolute; inset: 0; pointer-events: none;
            background: radial-gradient(ellipse 600px 400px at 50% 50%, rgba(255,145,77,.1) 0%, transparent 70%);
        }
        .cta-inner { position: relative; z-index: 1; }
        .cta-banner h2 {
            font-size: clamp(1.9rem, 4vw, 3rem);
            font-weight: 900; letter-spacing: -1.5px; margin-bottom: 14px;
        }
        .cta-banner p { font-size: 1rem; color: var(--text-muted); max-width: 460px; margin: 0 auto 40px; line-height: 1.7; }
        .cta-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; margin-bottom: 20px; }
        .cta-actions .btn-primary { font-size: 1rem; padding: 13px 28px; }
        .cta-actions .btn-outline  { font-size: 1rem; padding: 13px 28px; }
        .cta-note { font-size: .75rem; color: var(--text-faint); }

        /* ─────────────────────────── PRICING ─────────────────────────── */
        #pricing { padding: 96px max(24px, calc(50vw - 640px)); background: var(--bg); text-align: center; }
        .pricing-grid {
            display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px;
            max-width: 900px; margin: 56px auto 0;
        }

        .pricing-card {
            padding: 48px 40px; border-radius: 20px;
            background: var(--surface); border: 1px solid var(--border);
            text-align: left; display: flex; flex-direction: column;
            transition: transform .3s var(--ease), border-color .3s, box-shadow .3s;
            position: relative; overflow: hidden;
        }
        .pricing-card:hover { transform: translateY(-8px); border-color: rgba(255,145,77,.4); box-shadow: 0 30px 60px var(--shadow); }
        .pricing-card.popular { border-color: var(--accent); }

        .popular-badge {
            position: absolute; top: 16px; right: 16px;
            background: var(--accent); color: #1a1a1a;
            font-size: .62rem; font-weight: 800; padding: 4px 10px;
            border-radius: 6px; letter-spacing: .5px;
            box-shadow: 0 4px 12px rgba(255,145,77,.2);
        }

        .pricing-head { margin-bottom: 32px; }
        .pricing-name { font-size: .85rem; font-weight: 800; color: var(--accent); text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 12px; }
        .pricing-price { font-size: 3.5rem; font-weight: 900; letter-spacing: -2px; display: flex; align-items: baseline; gap: 6px; line-height: 1; }
        .pricing-price .price-period { font-size: .95rem; color: var(--text-muted); font-weight: 600; letter-spacing: 0; }

        .pricing-features { list-style: none; display: flex; flex-direction: column; gap: 16px; margin-bottom: 40px; flex: 1; }
        .pricing-features li { display: flex; align-items: center; gap: 12px; font-size: .88rem; color: var(--text-muted); }
        .pricing-features li svg { color: var(--accent); flex-shrink: 0; }

        .pricing-card .btn-primary, .pricing-card .btn-outline { width: 100%; justify-content: center; padding: 14px; font-size: .9rem; }

        /* Pricing Toggle */
        .pricing-toggle-wrap {
            display: flex; align-items: center; justify-content: center;
            gap: 0; background: var(--surface2); border: 1px solid var(--border);
            border-radius: 12px; padding: 4px; width: fit-content; margin: 0 auto 32px;
        }
        .billing-toggle {
            padding: 8px 20px; border-radius: 9px; border: none; background: none;
            font-family: var(--font); font-size: .82rem; font-weight: 700;
            color: var(--text-muted); cursor: pointer; transition: all .2s;
        }
        .billing-toggle.active { background: var(--surface); color: var(--text); box-shadow: 0 4px 12px var(--shadow); }
        .discount-badge {
            background: rgba(74,222,128,.15); color: #4ade80;
            font-size: .65rem; font-weight: 800; padding: 2px 8px;
            border-radius: 20px; margin-left: 6px;
        }

        #price-note {
            font-size: 0.85rem; font-weight: 600; color: var(--accent);
            margin-top: 8px; opacity: 0.9;
        }


        /* ─────────────────────────── FOOTER ─────────────────────────── */
        footer {
            padding: 40px max(24px, calc(50vw - 640px));
            border-top: 1px solid var(--border);
            display: grid; grid-template-columns: auto 1fr auto;
            align-items: center; gap: 24px;
        }
        .footer-copy { font-size: .75rem; color: var(--text-faint); text-align: center; }
        .footer-links { display: flex; gap: 20px; justify-content: flex-end; }
        .footer-links a {
            font-size: .75rem; color: var(--text-faint);
            text-decoration: none; transition: color .2s;
        }
        .footer-links a:hover { color: var(--accent); }

        /* ─────────────────────────── REVEAL ─────────────────────────── */
        .reveal { opacity: 0; transform: translateY(20px); transition: opacity .55s ease, transform .55s ease; }
        .reveal.visible { opacity: 1; transform: none; }
        @media (prefers-reduced-motion: reduce) { .reveal { opacity: 1; transform: none; transition: none; } html { scroll-behavior: auto; } }

        /* ─────────────────────────── RESPONSIVE ─────────────────────────── */
        @media (max-width: 900px) {
            .hero { grid-template-columns: 1fr; text-align: center; padding-top: 72px; min-height: auto; }
            .hero-sub, .hero-cta, .hero-meta { justify-content: center; text-align: center; }
            .hero-visual { display: none; }
            .features-grid { grid-template-columns: 1fr 1fr; }
            .showcase { grid-template-columns: 1fr; gap: 40px; }
            .showcase.reverse .showcase-visual { order: 0; }
            .steps { grid-template-columns: 1fr; }
            .steps::before { display: none; }
            footer { grid-template-columns: 1fr; text-align: center; }
            .footer-links { justify-content: center; }
        }
        @media (max-width: 600px) {
            .nav-links { display: none; }
            .features-grid { grid-template-columns: 1fr; }
            .proof-bar { flex-direction: column; }
            .proof-item + .proof-item { border-left: none; border-top: 1px solid var(--border); }
            .cta-actions { flex-direction: column; align-items: center; }
        }
    </style>
</head>
<body>

<!-- NAV -->
<nav class="nav">
    <a href="#" class="nav-logo">
        <img src="/logo-taskletto-light.png" alt="Taskletto" class="logo-img logo-dark">
        <img src="/logo-taskletto.png"       alt="Taskletto" class="logo-img logo-light">
    </a>
    <ul class="nav-links">
        <li><a href="#features">Funcionalidades</a></li>
        <li><a href="#tasks">Tarefas</a></li>
        <li><a href="#notes">Notas</a></li>
        <li><a href="#how">Como funciona</a></li>
        <li><a href="#pricing">Preços</a></li>
    </ul>
    <div class="nav-actions">
        <button class="btn-theme" id="themeToggle" title="Alternar tema" aria-label="Alternar tema">
            <svg id="themeIcon" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="5"/>
                <line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
            </svg>
        </button>
        <a href="{{ route('login') }}"    class="btn-outline">Entrar</a>
        <a href="{{ route('register') }}" class="btn-primary">Começar grátis →</a>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="hero-text">
        <div class="hero-badge">
            <div class="hero-badge-dot"></div>
            Open Source · NativePHP + Laravel
        </div>
        <h1>Organize tudo.<br><em>Foco no que importa.</em></h1>
        <p class="hero-sub">
            Taskletto reúne tarefas, subtarefas, categorias e notas ricas
            em um único app minimalista — para desktop e web.
        </p>
        <div class="hero-cta">
            <a href="{{ route('register') }}" class="btn-primary">Criar conta grátis →</a>
            <a href="#features" class="btn-outline">Ver funcionalidades</a>
        </div>
        <div class="hero-meta">
            <div class="hero-meta-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                Gratuito para sempre
            </div>
            <div class="hero-meta-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                App nativo para Windows
            </div>
            <div class="hero-meta-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                Sem anúncios
            </div>
        </div>
    </div>

    <!-- App mockup com 3 painéis -->
    <div class="hero-visual">
        <div class="app-frame">
            <div class="app-frame-bar">
                <div class="wdot r"></div><div class="wdot y"></div><div class="wdot g"></div>
                <div class="app-frame-bar-title">Taskletto</div>
            </div>
            <div class="app-body">
                <!-- Sidebar -->
                <div class="app-sidebar">
                    <div class="sidebar-brand">
                        <img src="/logo-taskletto-light.png" alt="Taskletto" class="logo-img-dark">
                        <img src="/logo-taskletto.png"       alt="Taskletto" class="logo-img-light">
                    </div>

                    <a href="#" class="btn-new-task">+ Nova Tarefa</a>

                    <div class="sidebar-section">Principal</div>
                    <div class="sidebar-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                        Dashboard
                    </div>
                    <div class="sidebar-item active">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                        Tarefas
                    </div>
                    <div class="sidebar-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                        Categorias
                    </div>
                    <div class="sidebar-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        Notas
                    </div>

                    <div class="sidebar-footer">
                        <div class="user-pill">
                            <div class="user-avatar"></div>
                            <div class="user-info">
                                <div class="user-name">Usuário de Teste</div>
                                <div class="user-email">usuario@exemplo.com</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="app-content">
                    <div class="content-tabs">
                        <div class="tab-pill active">Todas <span class="count">8</span></div>
                        <div class="tab-pill"><div class="dot" style="background:#ef4444"></div> Urgentes <span class="count">0</span></div>
                        <div class="tab-pill"><div class="dot" style="background:#6366f1"></div> Vence hoje <span class="count">0</span></div>
                        <div class="tab-pill">Atrasadas</div>
                        <div class="tab-pill"><div class="dot" style="background:#f97316"></div> Em progresso</div>
                    </div>

                    <div class="action-bar">
                        <div class="mock-search">Buscar tarefas...</div>
                        <div class="mock-select">Todos os status</div>
                        <div class="mock-select">Todas as prioridades</div>
                        <div class="mock-toggle">
                            APENAS ATRASADAS
                            <div class="tag-switch"></div>
                        </div>
                    </div>

                    <table class="task-table">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Status</th>
                                <th>Prioridade</th>
                                <th>Categoria</th>
                                <th>Vencimento</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Revisar relatório do Q1</td>
                                <td><span class="row-pill green">Concluída</span></td>
                                <td><span class="row-pill blue">Média</span></td>
                                <td>—</td>
                                <td>—</td>
                            </tr>
                            <tr>
                                <td>Enviar proposta para cliente</td>
                                <td><span class="row-pill blue">Pendente</span></td>
                                <td><span class="row-pill green">Baixa</span></td>
                                <td><span class="tag-dot" style="background:orange"></span> Projetos</td>
                                <td>29/04/2026</td>
                            </tr>
                            <tr>
                                <td>Organizar arquivos do projeto</td>
                                <td><span class="row-pill blue">Pendente</span></td>
                                <td><span class="row-pill blue">Média</span></td>
                                <td>—</td>
                                <td>—</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="table-footer">+ Adicionar tarefa</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SOCIAL PROOF BAR -->
<div class="proof-bar">
    <div class="proof-item reveal">
        <div class="proof-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        </div>
        <div class="proof-label"><strong>Tarefas & Subtarefas</strong>Tudo com prioridade e prazo</div>
    </div>
    <div class="proof-item reveal">
        <div class="proof-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        </div>
        <div class="proof-label"><strong>Editor de Notas Rico</strong>Headings, código, tabelas e mais</div>
    </div>
    <div class="proof-item reveal">
        <div class="proof-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
        </div>
        <div class="proof-label"><strong>App Desktop Nativo</strong>Windows · Sincronizado · Nuvem</div>
    </div>
    <div class="proof-item reveal">
        <div class="proof-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
        </div>
        <div class="proof-label"><strong>Web + Desktop</strong>Acesse de qualquer lugar</div>
    </div>
</div>

<!-- FEATURES -->
<section id="features">
    <div class="section-eyebrow">Funcionalidades</div>
    <h2 class="section-title reveal" style="margin-bottom: 25px">Tudo que você precisa.<br>Nada que você não precisa.</h2>

    <div class="features-grid">
        <div class="feature-card reveal">
            <div class="feature-icon-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            </div>
            <h3>Tarefas &amp; Subtarefas</h3>
            <p>Crie tarefas com prioridade, data de vencimento e subtarefas aninhadas. Arraste para reordenar.</p>
        </div>
        <div class="feature-card reveal">
            <div class="feature-icon-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            </div>
            <h3>Editor de Notas Rico</h3>
            <p>Editor estilo Notion com headings, listas, tabelas, callouts, código com highlight e mais. Auto-save.</p>
        </div>
        <div class="feature-card reveal">
            <div class="feature-icon-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
            </div>
            <h3>Categorias com Cores</h3>
            <p>Organize tarefas e notas por categorias coloridas. Filtre e visualize só o que importa agora.</p>
        </div>
        <div class="feature-card reveal">
            <div class="feature-icon-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            </div>
            <h3>Dashboard Inteligente</h3>
            <p>Visão geral de pendentes, concluídas, próximas do prazo e atividade recente em um só lugar.</p>
        </div>
        <div class="feature-card reveal">
            <div class="feature-icon-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            </div>
            <h3>App Desktop Nativo</h3>
            <p>Instalável no Windows como app nativo com inicialização automática e atalho de teclado global.</p>
        </div>
        <div class="feature-card reveal">
            <div class="feature-icon-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            </div>
            <h3>Exportação</h3>
            <p>Exporte suas notas para Markdown e PDF. Seus dados sempre ao seu alcance, no formato que quiser.</p>
        </div>
    </div>
</section>

<!-- TASKS SHOWCASE -->
<section id="tasks" class="showcase alt">
    <div class="showcase-visual reveal">
        <div class="app-frame">
            <div class="app-frame-bar">
                <div class="wdot r"></div><div class="wdot y"></div><div class="wdot g"></div>
                <div class="app-frame-bar-title">Taskletto — Tarefas</div>
            </div>
            <div class="task-mock">
                <div class="task-mock-header">
                    <span class="task-mock-header-title">Hoje</span>
                    <span class="task-mock-count">3 pendentes</span>
                </div>
                <div class="task-item">
                    <div class="task-cb"></div>
                    <div class="task-body">
                        <div class="task-title">Revisar relatório do Q1</div>
                        <div class="task-meta">
                            <span class="task-badge high">Alta</span><span class="task-cat">Trabalho</span>
                            <span class="task-date overdue">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                Hoje
                            </span>
                        </div>
                        <div class="subtask-list">
                            <div class="subtask-item"><div class="subtask-cb"></div><span>Verificar números de vendas</span></div>
                            <div class="subtask-item done"><div class="subtask-cb"></div><span>Atualizar gráficos</span></div>
                        </div>
                    </div>
                </div>
                <div class="task-item">
                    <div class="task-cb"></div>
                    <div class="task-body">
                        <div class="task-title">Enviar proposta para cliente</div>
                        <div class="task-meta">
                            <span class="task-badge mid">Média</span><span class="task-cat">Comercial</span>
                            <span class="task-date"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Amanhã</span>
                        </div>
                    </div>
                </div>
                <div class="task-item done">
                    <div class="task-cb"></div>
                    <div class="task-body">
                        <div class="task-title">Reunião de planejamento semanal</div>
                        <div class="task-meta"><span class="task-badge high">Alta</span><span class="task-cat">Equipe</span></div>
                    </div>
                </div>
                <div class="task-item">
                    <div class="task-cb"></div>
                    <div class="task-body">
                        <div class="task-title">Organizar pasta de arquivos</div>
                        <div class="task-meta">
                            <span class="task-badge low">Baixa</span><span class="task-cat">Pessoal</span>
                            <span class="task-date"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Sex, 04 abr</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="showcase-text reveal">
        <div class="section-eyebrow">Gerenciamento de Tarefas</div>
        <h2 class="section-title">Do caos à clareza,<br>em segundos.</h2>
        <ul class="showcase-checklist">
            <li><div class="check-circle"><svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="2,6 5,9 10,3"/></svg></div><span>Prioridades alta, média e baixa com indicadores visuais</span></li>
            <li><div class="check-circle"><svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="2,6 5,9 10,3"/></svg></div><span>Subtarefas aninhadas para dividir o trabalho em partes menores</span></li>
            <li><div class="check-circle"><svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="2,6 5,9 10,3"/></svg></div><span>Categorias coloridas para organizar por contexto ou projeto</span></li>
            <li><div class="check-circle"><svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="2,6 5,9 10,3"/></svg></div><span>Datas de vencimento com alertas visuais de atraso</span></li>
            <li><div class="check-circle"><svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="2,6 5,9 10,3"/></svg></div><span>Filtros rápidos por status, categoria e prazo</span></li>
        </ul>
    </div>
</section>

<!-- NOTES SHOWCASE -->
<section id="notes" class="showcase reverse">
    <div class="showcase-text reveal">
        <div class="section-eyebrow">Editor de Notas</div>
        <h2 class="section-title">Escreva do jeito<br>que você pensa.</h2>
        <ul class="showcase-checklist">
            <li><div class="check-circle"><svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="2,6 5,9 10,3"/></svg></div><span>Headings, listas, tabelas e checklists integrados</span></li>
            <li><div class="check-circle"><svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="2,6 5,9 10,3"/></svg></div><span>Blocos de código com syntax highlight em 20+ linguagens</span></li>
            <li><div class="check-circle"><svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="2,6 5,9 10,3"/></svg></div><span>Callouts coloridos, citações e divisores visuais</span></li>
            <li><div class="check-circle"><svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="2,6 5,9 10,3"/></svg></div><span>Exportação para Markdown e PDF com um clique</span></li>
            <li><div class="check-circle"><svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="2,6 5,9 10,3"/></svg></div><span>Salvamento automático enquanto você digita</span></li>
        </ul>
    </div>
    <div class="showcase-visual reveal">
        <div class="app-frame">
            <div class="app-frame-bar">
                <div class="wdot r"></div><div class="wdot y"></div><div class="wdot g"></div>
                <div class="app-frame-bar-title">Taskletto — Editor de Notas</div>
            </div>
            <div class="note-mock">
                <div class="note-toolbar">
                    <div class="note-tb-btn">B</div>
                    <div class="note-tb-btn"><i>I</i></div>
                    <div class="note-tb-btn">U</div>
                    <div class="note-tb-btn">H1</div>
                    <div class="note-tb-btn">H2</div>
                    <div class="note-tb-btn">&lt;&gt;</div>
                    <div class="note-tb-btn">≡</div>
                    <div class="note-tb-btn">⊞</div>
                </div>
                <div class="note-mock-title">Anotações da Sprint #14</div>
                <div class="note-mock-date">Editado há 2 minutos · auto-saved</div>
                <div class="note-block">
                    <div class="note-h2">Objetivos da semana</div>
                    <div class="note-p">Implementar o novo módulo de autenticação e revisar as APIs de integração com o painel de controle.</div>
                </div>
                <div class="note-code">
<span class="kw">const</span> auth = <span class="kw">await</span> <span class="fn">authenticate</span>({<br>
&nbsp;&nbsp;provider: <span class="str">'oauth2'</span>,<br>
&nbsp;&nbsp;scope: [<span class="str">'read'</span>, <span class="str">'write'</span>]<br>
});</div>
                <div>
                    <span class="note-tag blue">backend</span>
                    <span class="note-tag purple">sprint-14</span>
                    <span class="note-tag green">em andamento</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<section id="how">
    <div class="section-eyebrow">Como funciona</div>
    <h2 class="section-title reveal">Comece em menos de um minuto.</h2>
    <p class="section-sub reveal">Sem configuração complicada. Crie sua conta e comece a organizar.</p>

    <div class="steps">
        <div class="step reveal">
            <div class="step-num">1</div>
            <h3>Crie sua conta</h3>
            <p>Cadastro gratuito em segundos. Sem cartão de crédito, sem período de teste.</p>
        </div>
        <div class="step reveal">
            <div class="step-num">2</div>
            <h3>Organize seu espaço</h3>
            <p>Crie categorias, adicione tarefas e escreva suas primeiras notas com o editor rico.</p>
        </div>
        <div class="step reveal">
            <div class="step-num">3</div>
            <h3>Acesse em qualquer lugar</h3>
            <p>Use no navegador ou baixe o app nativo para Windows e tenha tudo offline e sempre aberto.</p>
        </div>
    </div>
</section>

<!-- PRICING -->
<section id="pricing">
    <div class="section-eyebrow">Preços</div>
    <h2 class="section-title reveal">O plano certo para o seu foco.</h2>
    <p class="section-sub reveal" style="margin-bottom: 32px">Comece grátis e evolua conforme sua necessidade.</p>

    <div class="pricing-toggle-wrap reveal">
        <button id="toggle-monthly" onclick="setBillingPeriod('monthly')" class="billing-toggle active">Mensal</button>
        <button id="toggle-annual"  onclick="setBillingPeriod('annual')"  class="billing-toggle">
            Anual
            <span class="discount-badge">-33%</span>
        </button>
    </div>

    <div class="pricing-grid">
        <!-- Free -->
        <div class="pricing-card reveal">
            <div class="pricing-head">
                <div class="pricing-name">Personal</div>
                <div class="pricing-price">R$ 0<span class="price-period">/mês</span></div>
            </div>
            <ul class="pricing-features">
                <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg> Até 5 notas ricas</li>
                <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg> 50MB de armazenamento</li>
                <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg> Tarefas e subtarefas</li>
                <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg> App Desktop + Acesso Web</li>
            </ul>
            <a href="{{ route('register') }}" class="btn-outline">Começar agora</a>
        </div>

        <!-- Pro -->
        <div class="pricing-card popular reveal">
            <div class="popular-badge">RECOMENDADO</div>
            <div class="pricing-head">
                <div class="pricing-name">Pro</div>
                <div class="pricing-price" style="color: var(--accent);">
                    <span id="price-display">R$ 14,99</span><span class="price-period" id="price-period">/mês</span>
                </div>
                <div id="price-note"></div>
            </div>
            <ul class="pricing-features">
                <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg> Até 50 notas ricas</li>
                <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg> 3GB de armazenamento</li>
                <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg> Tarefas ilimitadas</li>
                <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg> App Desktop + Acesso Web</li>
                <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg> Suporte prioritário</li>
            </ul>
            <a href="{{ route('register') }}" class="btn-primary">Assinar Pro →</a>
        </div>
    </div>
</section>

<!-- CTA BANNER -->
<section class="cta-banner">
    <div class="cta-inner">
        <h2 class="reveal">Pronto para ter mais foco?</h2>
        <p class="reveal">Junte-se a quem já usa o Taskletto para organizar o dia com menos ruído e mais resultado.</p>
        <div class="cta-actions">
            <a href="{{ route('register') }}" class="btn-primary">Criar conta grátis →</a>
            <a href="#" class="btn-outline">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Baixar para Windows
            </a>
        </div>
        <p class="cta-note">Windows 10/11 · Gratuito e open source</p>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <a href="#" class="nav-logo">
        <img src="/logo-taskletto-light.png" alt="Taskletto" class="logo-img logo-dark" style="height:24px;">
        <img src="/logo-taskletto.png" alt="Taskletto" class="logo-img logo-light" style="height:24px;">
    </a>
    <span class="footer-copy">© {{ date('Y') }} Taskletto · Feito com Laravel &amp; NativePHP.</span>
    <div class="footer-links">
        <a href="https://github.com/lbonavina/taskletto" target="_blank" rel="noopener">GitHub</a>
        <a href="{{ route('pricing') }}">Preços</a>
        <a href="{{ route('login') }}">Entrar</a>
        <a href="{{ route('register') }}">Criar conta</a>
    </div>
</footer>

<script>
    (function () {
        const saved = localStorage.getItem('taskletto-theme') || 'dark';
        document.documentElement.setAttribute('data-theme', saved);
        updateIcon(saved);
    })();

    function updateIcon(theme) {
        const icon = document.getElementById('themeIcon');
        if (!icon) return;
        if (theme === 'light') {
            icon.innerHTML = `<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>`;
        } else {
            icon.innerHTML = `
                <circle cx="12" cy="12" r="5"/>
                <line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
            `;
        }
    }

    document.getElementById('themeToggle').addEventListener('click', function () {
        const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', next);
        localStorage.setItem('taskletto-theme', next);
        updateIcon(next);
    });

    function setBillingPeriod(period) {
        const isAnnual = period === 'annual';
        document.getElementById('toggle-monthly').classList.toggle('active', !isAnnual);
        document.getElementById('toggle-annual').classList.toggle('active', isAnnual);

        const priceDisplay = document.getElementById('price-display');
        const priceNote = document.getElementById('price-note');

        if (isAnnual) {
            priceDisplay.innerText = 'R$ 9,99';
            priceNote.innerHTML = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="margin-right:4px"><polyline points="20 6 9 17 4 12"/></svg> Cobrado anualmente (R$ 119,88/ano)';
        } else {
            priceDisplay.innerText = 'R$ 14,99';
            priceNote.innerText = '';
        }
    }

    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) { entry.target.classList.add('visible'); observer.unobserve(entry.target); }
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(function (el) { observer.observe(el); });
</script>
</body>
</html>
