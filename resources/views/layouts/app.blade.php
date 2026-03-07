<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Taskletto') — Taskletto</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap" rel="stylesheet">
    <style>
        @font-face {
            font-family: 'Codec Pro';
            src: url('/fonts/codec-pro/CodecPro-Regular.ttf') format('TrueType'),
                 url('/fonts/codec-pro/CodecPro-Regular.ttf')  format('TrueType');
            font-weight: 400; font-style: normal; font-display: swap;
        }
        @font-face {
            font-family: 'Codec Pro';
            src: url('/fonts/codec-pro/CodecPro-Medium.ttf') format('TrueType'),
                 url('/fonts/codec-pro/CodecPro-Medium.ttf')  format('TrueType');
            font-weight: 500; font-style: normal; font-display: swap;
        }
        @font-face {
            font-family: 'Codec Pro';
            src: url('/fonts/codec-pro/CodecPro-Bold.ttf') format('TrueType'),
                 url('/fonts/codec-pro/CodecPro-Bold.ttf')  format('TrueType');
            font-weight: 700; font-style: normal; font-display: swap;
        }
        @font-face {
            font-family: 'Codec Pro';
            src: url('/fonts/codec-pro/CodecPro-ExtraBold.ttf') format('TrueType'),
                 url('/fonts/codec-pro/CodecPro-ExtraBold.ttf')  format('TrueType');
            font-weight: 800; font-style: normal; font-display: swap;
        }
        @font-face {
            font-family: 'Codec Pro';
            src: url('/fonts/codec-pro/CodecPro-Italic.ttf') format('TrueType'),
                 url('/fonts/codec-pro/CodecPro-Italic.ttf')  format('TrueType');
            font-weight: 400; font-style: italic; font-display: swap;
        }
    </style>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #0f0f11;
            --surface:   #16161a;
            --surface2:  #1e1e24;
            --border:    #2a2a32;
            --accent:    #ff914d;
            --accent2:   #ff6b1a;
            --text:      #e8e8ec;
            --muted:     #7a7a8a;
            --danger:    #e05454;
            --success:   #4ade80;
            --info:      #60a5fa;
            --sidebar-w: 240px;
            --status-pending:     #60a5fa;
            --status-in_progress: #f0a05a;
            --status-completed:   #4ade80;
            --status-cancelled:   #7a7a8a;
            --priority-low:    #4ade80;
            --priority-medium: #60a5fa;
            --priority-high:   #f0a05a;
            --priority-urgent: #e05454;
        }

        /* ── Light theme ── */
        html[data-theme=light] {
            --bg:       #f4f4f6;
            --surface:  #ffffff;
            --surface2: #eeeef2;
            --border:   #dddde6;
            --text:     #18181c;
            --muted:    #8888a0;
            --status-pending:     #3b82f6;
            --status-in_progress: #ea7c2b;
            --status-completed:   #22c55e;
            --status-cancelled:   #94a3b8;
        }
        html[data-theme=light] select option { background: #ffffff; }
        html[data-theme=light] input[type=date] { color-scheme: light; }
        html[data-theme=light] .card { box-shadow: 0 1px 4px rgba(0,0,0,.06); }
        html[data-theme=light] tr:hover td { background: #f0f0f5; }
        html[data-theme=light] .overdue-row td { background: rgba(224,84,84,.05); }
        html[data-theme=light] #page-loader { box-shadow: 0 0 8px var(--accent); }
        html[data-theme=light] .pagination .page-item .page-link:hover { background: var(--surface2); border-color: #c0c0cc; }
        html[data-theme=light] .qf:hover { background: #eeeef2; }

        /* ── Scrollbar global ── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--muted); }
        * { scrollbar-width: thin; scrollbar-color: var(--border) transparent; }

        /* Smooth theme transition */
        *, *::before, *::after { transition: background-color .2s ease, border-color .2s ease, color .15s ease !important; }
        /* But keep animation-based transitions instant */
        .page-enter, .toast, .modal, #inline-popup { transition: none !important; }

        html, body { height: 100%; background: var(--bg); color: var(--text); font-family: 'DM Sans', sans-serif; font-size: 14px; line-height: 1.5; }

        /* ── Page loader bar ── */
        #page-loader {
            position: fixed; top: 0; left: 0; height: 2px; width: 0;
            background: linear-gradient(90deg, var(--accent), var(--accent2));
            z-index: 9999;
            transition: width .4s ease, opacity .3s ease;
            box-shadow: 0 0 8px var(--accent);
        }
        #page-loader.done { width: 100% !important; opacity: 0; }

        /* ── Layout ── */
        .app-shell { display: flex; height: 100vh; }

        /* ── Sidebar ── */
        .sidebar {
            width: var(--sidebar-w);
            flex-shrink: 0;
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }
        .sidebar-logo { padding: 20px 20px 18px; border-bottom: 1px solid var(--border); }
        .sidebar-logo a { text-decoration: none; display: flex; align-items: center; }
        .logo-img {
            height: 36px; width: auto;
            transition: opacity .2s, transform .2s;
        }
        .sidebar-logo a:hover .logo-img { opacity: .85; transform: scale(1.03); }
        /* esconde ambas por padrão, JS/tema define qual mostrar */
        .logo-light, .logo-dark { display: none; }
        html[data-theme=dark]  .logo-dark  { display: block; }
        html[data-theme=light] .logo-light { display: block; }

        .sidebar-nav { padding: 12px 0; flex: 1; }
        .nav-label { font-size: 10px; font-weight: 600; letter-spacing: 1.2px; text-transform: uppercase; color: var(--muted); padding: 8px 20px 4px; }

        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 20px;
            color: var(--muted);
            text-decoration: none;
            font-size: 13.5px; font-weight: 400;
            transition: color .15s, background .15s, padding-left .15s;
            border-left: 2px solid transparent;
            position: relative;
        }
        .nav-item:hover { color: var(--text); background: var(--surface2); padding-left: 24px; }
        .nav-item.active { color: var(--accent); border-left-color: var(--accent); background: rgba(255,145,77,.07); font-weight: 500; }
        .nav-icon { width: 16px; height: 16px; flex-shrink: 0; opacity: .8; transition: opacity .15s; }
        .nav-item:hover .nav-icon, .nav-item.active .nav-icon { opacity: 1; }
        .nav-badge {
            margin-left: auto;
            background: var(--danger); color: #fff;
            font-size: 10px; font-weight: 600;
            padding: 1px 6px; border-radius: 20px;
            font-family: 'DM Mono', monospace;
            animation: pulse-badge 2s infinite;
        }
        @keyframes pulse-badge {
            0%, 100% { box-shadow: 0 0 0 0 rgba(224,84,84,.4); }
            50%       { box-shadow: 0 0 0 4px rgba(224,84,84,0); }
        }

        .sidebar-footer {
            border-top: 1px solid var(--border);
            padding: 12px 14px 14px;
            display: flex; flex-direction: column; gap: 1px;
        }
        .sidebar-version {
            display: flex; align-items: center; justify-content: space-between;
            padding: 6px 8px 10px; margin-bottom: 2px;
        }
        .sidebar-version-name {
            font-family: 'Codec Pro', sans-serif;
            font-size: 12px; font-weight: 700;
            color: var(--text); letter-spacing: -.2px;
        }
        .sidebar-version-badge {
            font-size: 10px; font-weight: 600; font-family: 'DM Mono', monospace;
            padding: 2px 7px; border-radius: 20px;
            background: rgba(255,145,77,.12); color: var(--accent);
            border: 1px solid rgba(255,145,77,.2);
        }
        .sidebar-footer-btn {
            display: flex; align-items: center; gap: 9px;
            width: 100%; padding: 8px 10px; border-radius: 9px;
            border: none; background: none; cursor: pointer;
            color: var(--muted); font-size: 12.5px; font-weight: 500;
            font-family: inherit; text-align: left; text-decoration: none;
            transition: background .12s, color .12s;
        }
        .sidebar-footer-btn:hover { background: var(--surface2); color: var(--text); }
        .sidebar-footer-btn .sfb-icon {
            width: 28px; height: 28px; border-radius: 7px;
            display: flex; align-items: center; justify-content: center;
            background: var(--surface2); flex-shrink: 0;
            font-size: 14px; transition: background .12s, transform .12s;
        }
        .sidebar-footer-btn:hover .sfb-icon { background: rgba(255,145,77,.12); transform: scale(1.08); }
        .sidebar-footer-btn .sfb-label { flex: 1; }
        .sidebar-footer-btn .sfb-hint {
            font-size: 10px; font-family: 'DM Mono', monospace;
            opacity: .35; letter-spacing: .3px;
        }
        #theme-toggle .sfb-icon { font-size: 15px; }
        html[data-theme=light] .sidebar-version-badge { background: rgba(255,145,77,.08); border-color: rgba(255,145,77,.25); }

        /* ── Keyboard shortcuts modal ── */
        #kbd-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.7); backdrop-filter: blur(8px);
            z-index: 10002; align-items: center; justify-content: center;
        }
        #kbd-overlay.open { display: flex; }
        #kbd-modal {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: 20px; width: 100%; max-width: 560px;
            max-height: 88vh; overflow: hidden;
            display: flex; flex-direction: column;
            box-shadow: 0 40px 100px rgba(0,0,0,.7), 0 4px 20px rgba(0,0,0,.3),
                        inset 0 1px 0 rgba(255,255,255,.05);
            animation: modalIn .24s cubic-bezier(.34,1.4,.64,1) both;
        }
        html[data-theme=light] #kbd-modal {
            box-shadow: 0 40px 100px rgba(0,0,0,.18), 0 4px 20px rgba(0,0,0,.08);
        }
        #kbd-modal-header {
            padding: 22px 24px 18px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 14px;
            flex-shrink: 0;
            background: linear-gradient(180deg, rgba(255,145,77,.04) 0%, transparent 100%);
        }
        .kbd-header-icon {
            width: 38px; height: 38px; border-radius: 10px;
            background: rgba(255,145,77,.12); border: 1px solid rgba(255,145,77,.2);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .kbd-header-text { flex: 1; }
        .kbd-header-text h2 {
            font-family: 'Codec Pro', sans-serif; font-size: 17px; font-weight: 700;
            letter-spacing: -.3px; margin-bottom: 2px;
        }
        .kbd-header-text p {
            font-size: 11.5px; color: var(--muted);
        }
        #kbd-modal-close {
            width: 30px; height: 30px; border-radius: 8px;
            border: 1px solid var(--border); background: none;
            color: var(--muted); cursor: pointer; font-size: 13px;
            display: flex; align-items: center; justify-content: center;
            transition: background .12s, color .12s, border-color .12s, transform .1s;
        }
        #kbd-modal-close:hover { background: var(--surface2); color: var(--text); border-color: var(--muted); transform: scale(1.05); }
        .kbd-search-wrap { padding: 14px 24px 0; flex-shrink: 0; }
        .kbd-search-wrap input {
            width: 100%; padding: 8px 14px 8px 36px;
            background: var(--surface2); border: 1px solid var(--border);
            border-radius: 10px; color: var(--text); font-size: 13px;
            font-family: inherit; outline: none;
            transition: border-color .15s, box-shadow .15s;
            box-sizing: border-box;
        }
        .kbd-search-wrap input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(255,145,77,.1); }
        .kbd-search-wrap input::placeholder { color: var(--muted); opacity: .7; }
        .kbd-search-icon {
            position: absolute; left: 10px; top: 50%; transform: translateY(-50%);
            color: var(--muted); font-size: 12px; pointer-events: none;
        }
        .kbd-search-wrap-inner { position: relative; }
        html[data-theme=light] .kbd-search-wrap input { background: #f4f4f6; border-color: #dddde6; }
        .kbd-tabs { display: flex; gap: 4px; padding: 12px 24px 0; flex-shrink: 0; }
        .kbd-tab {
            padding: 5px 12px; border-radius: 7px; font-size: 12px; font-weight: 500;
            border: 1px solid transparent; cursor: pointer; background: none;
            color: var(--muted); font-family: inherit;
            transition: background .12s, color .12s, border-color .12s;
        }
        .kbd-tab:hover { background: var(--surface2); color: var(--text); }
        .kbd-tab.active { background: rgba(255,145,77,.12); color: var(--accent); border-color: rgba(255,145,77,.25); }
        #kbd-modal-body {
            overflow-y: auto; padding: 10px 24px 24px;
            scrollbar-width: thin; scrollbar-color: var(--border) transparent;
            flex: 1;
        }
        .kbd-section { margin-top: 8px; }
        .kbd-section-title {
            font-size: 10px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 1px; color: var(--muted);
            padding: 14px 0 8px;
            display: flex; align-items: center; gap: 8px;
        }
        .kbd-section-title::after { content: ''; flex: 1; height: 1px; background: var(--border); }
        .kbd-section-title:first-child { padding-top: 6px; }
        .kbd-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 4px; }
        .kbd-row {
            display: flex; align-items: center; justify-content: space-between;
            padding: 9px 12px; border-radius: 10px; transition: background .1s;
        }
        .kbd-row:hover { background: var(--surface2); }
        .kbd-desc { font-size: 12.5px; color: var(--text); }
        .kbd-desc small { color: var(--muted); font-size: 11px; display: block; margin-top: 1px; }
        .kbd-keys { display: flex; align-items: center; gap: 3px; flex-shrink: 0; margin-left: 12px; }
        kbd {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 26px; height: 22px; padding: 0 7px;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-bottom: 2px solid color-mix(in srgb, var(--border) 150%, black);
            border-radius: 6px; font-size: 10.5px; font-family: 'DM Mono', monospace;
            color: var(--text); font-weight: 600; white-space: nowrap;
            box-shadow: 0 1px 2px rgba(0,0,0,.2);
        }
        html[data-theme=light] kbd {
            background: #eeeef2; border-color: #c8c8d8;
            border-bottom-color: #aaaabc; color: #333344;
            box-shadow: 0 1px 2px rgba(0,0,0,.08);
        }
        .kbd-plus { font-size: 9px; color: var(--muted); opacity: .7; }
        .kbd-empty { text-align: center; padding: 32px 20px; color: var(--muted); font-size: 13px; }
        .kbd-empty-icon { font-size: 32px; margin-bottom: 8px; opacity: .4; }

        /* ── Main ── */
        .main { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .topbar { padding: 0 32px; height: 56px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px; flex-shrink: 0; background: var(--surface); }
        .topbar-title { font-family: 'Codec Pro', sans-serif; font-size: 19px; font-weight: 700; letter-spacing: -0.4px; flex: 1; }
        .topbar-actions { display: flex; gap: 8px; align-items: center; }
        .page-content { flex: 1; overflow-y: auto; padding: 32px; }

        /* ── Page enter animation ── */
        .page-enter { animation: pageEnter .25s ease both; }
        @keyframes pageEnter {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Buttons ── */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 14px; border-radius: 8px;
            font-size: 13px; font-weight: 500;
            cursor: pointer; border: 1px solid transparent;
            transition: all .15s; text-decoration: none;
            font-family: inherit; white-space: nowrap;
            position: relative; overflow: hidden;
        }
        .btn::after { content: ''; position: absolute; inset: 0; background: white; opacity: 0; transition: opacity .15s; }
        .btn:active::after { opacity: .08; }
        .btn-primary { background: var(--accent); color: #0f0f11; border-color: var(--accent); }
        .btn-primary:hover { background: #f0d060; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(255,145,77,.25); }
        .btn-primary:active { transform: translateY(0); box-shadow: none; }
        .btn-ghost { background: transparent; color: var(--muted); border-color: var(--border); }
        .btn-ghost:hover { color: var(--text); border-color: var(--muted); background: var(--surface2); }
        .btn-danger { background: transparent; color: var(--danger); border-color: var(--danger); }
        .btn-danger:hover { background: var(--danger); color: #fff; }
        .btn-sm { padding: 4px 10px; font-size: 12px; }
        .btn:disabled { opacity: .5; cursor: not-allowed; transform: none !important; box-shadow: none !important; }

        /* ── Cards ── */
        .card { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 20px; }

        /* ── Stat cards ── */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 16px; margin-bottom: 28px; }
        .stat-card {
            background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 18px 20px;
            transition: border-color .2s, transform .2s, box-shadow .2s; cursor: default;
        }
        .stat-card:hover { border-color: rgba(255,145,77,.3); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.2); }
        .stat-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .8px; color: var(--muted); margin-bottom: 6px; }
        .stat-value { font-family: 'DM Mono', monospace; font-size: 28px; font-weight: 500; color: var(--text); line-height: 1; }
        .stat-sub { font-size: 11px; color: var(--muted); margin-top: 4px; }

        /* ── Badges ── */
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 500; font-family: 'DM Mono', monospace; }
        .badge-dot { width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0; }
        .status-pending     { background: rgba(96,165,250,.12);  color: var(--status-pending); }
        .status-in_progress { background: rgba(240,160,90,.12);  color: var(--status-in_progress); }
        .status-completed   { background: rgba(74,222,128,.12);  color: var(--status-completed); }
        .status-cancelled   { background: rgba(122,122,138,.12); color: var(--status-cancelled); }
        .priority-low    { background: rgba(74,222,128,.12);  color: var(--priority-low); }
        .priority-medium { background: rgba(96,165,250,.12);  color: var(--priority-medium); }
        .priority-high   { background: rgba(240,160,90,.12);  color: var(--priority-high); }
        .priority-urgent { background: rgba(224,84,84,.12);   color: var(--priority-urgent); }

        /* ── Table ── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .7px; color: var(--muted); padding: 10px 16px; border-bottom: 1px solid var(--border); }
        td { padding: 13px 16px; border-bottom: 1px solid var(--border); color: var(--text); vertical-align: middle; transition: background .1s; }
        tr:last-child td { border-bottom: none; }
        tr { transition: background .1s; }
        tr:hover td { background: var(--surface2); }
        .td-title a { color: var(--text); text-decoration: none; font-weight: 500; transition: color .15s; }
        .td-title a:hover { color: var(--accent); }
        .overdue-row td { background: rgba(224,84,84,.04); }

        /* ── Forms ── */
        .form-group { margin-bottom: 18px; position: relative; }
        label {
            display: block; font-size: 11px; font-weight: 600; letter-spacing: .6px;
            text-transform: uppercase; color: var(--muted); margin-bottom: 7px; transition: color .15s;
        }
        .form-group:focus-within > label { color: var(--accent); }
        input[type=text], input[type=date], input[type=email], input[type=password], input[type=number], textarea {
            width: 100%; background: var(--surface2); border: 1px solid var(--border);
            border-radius: 10px; color: var(--text); padding: 10px 14px;
            font-size: 13.5px; font-family: inherit; line-height: 1.5;
            transition: border-color .15s, box-shadow .15s, background .15s;
            outline: none; -webkit-appearance: none;
        }
        input[type=text]::placeholder, textarea::placeholder { color: var(--muted); opacity: .5; }
        input[type=text]:hover, input[type=date]:hover, textarea:hover { border-color: #3a3a46; }
        input[type=text]:focus, input[type=date]:focus, input[type=email]:focus,
        input[type=password]:focus, input[type=number]:focus, textarea:focus {
            border-color: var(--accent); background: #1a1a20;
            box-shadow: 0 0 0 3px rgba(255,145,77,.1), 0 1px 4px rgba(0,0,0,.3);
        }
        input[type=date] { color-scheme: dark; }
        textarea { resize: vertical; min-height: 90px; }

        /* ── Custom select ── */
        .select-wrap { position: relative; display: block; }
        select.native-select-hidden { display: none !important; }
        .csel-trigger {
            width: 100%; background: var(--surface2); border: 1px solid var(--border);
            border-radius: 10px; color: var(--text); padding: 10px 36px 10px 14px;
            font-size: 13.5px; font-family: inherit; line-height: 1.5;
            cursor: pointer; display: flex; align-items: center; gap: 8px;
            position: relative; transition: border-color .15s, box-shadow .15s, background .15s;
            user-select: none; outline: none;
        }
        .csel-trigger:hover { border-color: #3a3a46; }
        .csel-trigger:focus, .csel-trigger.open {
            border-color: var(--accent); background: #1a1a20;
            box-shadow: 0 0 0 3px rgba(255,145,77,.1), 0 1px 4px rgba(0,0,0,.3);
        }
        .csel-trigger-label { flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .csel-trigger-icon  { font-size: 15px; line-height: 1; flex-shrink: 0; }
        .csel-arrow {
            position: absolute; right: 13px; top: 50%; transform: translateY(-50%);
            color: var(--muted); transition: transform .2s, color .15s; pointer-events: none;
        }
        .csel-trigger.open .csel-arrow { transform: translateY(-50%) rotate(180deg); color: var(--accent); }
        .csel-dropdown {
            position: absolute; top: calc(100% + 4px); left: 0; right: 0;
            background: #1e1e26; border: 1px solid var(--border); border-radius: 10px;
            box-shadow: 0 8px 32px rgba(0,0,0,.55), 0 2px 8px rgba(0,0,0,.3);
            z-index: 9999; overflow: hidden; display: none; max-height: 260px; overflow-y: auto;
        }
        .csel-dropdown.open { display: block; animation: cselDropIn .15s ease; }
        @keyframes cselDropIn {
            from { opacity:0; transform: translateY(-6px); }
            to   { opacity:1; transform: translateY(0); }
        }
        .csel-option {
            display: flex; align-items: center; gap: 9px; padding: 9px 14px;
            cursor: pointer; font-size: 13.5px; transition: background .1s; white-space: nowrap;
        }
        .csel-option:hover    { background: rgba(255,145,77,.08); }
        .csel-option.selected { background: rgba(255,145,77,.13); color: var(--accent); font-weight: 500; }
        .csel-option-dot   { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; display: inline-block; }
        .csel-option-emoji { font-size: 15px; line-height: 1; }
        html[data-theme=light] .csel-dropdown { background: #ffffff; }
        html[data-theme=light] .csel-trigger  { background: #f4f4f6; }
        html[data-theme=light] .csel-trigger:focus, html[data-theme=light] .csel-trigger.open { background: #ffffff; }
        html[data-theme=light] .csel-option:hover { background: rgba(255,145,77,.07); }

        /* ── Checkbox toggle ── */
        .check-label {
            display: inline-flex; align-items: center; gap: 8px;
            cursor: pointer; user-select: none; white-space: nowrap;
            padding: 9px 13px; border: 1px solid var(--border); border-radius: 10px;
            background: var(--surface2); color: var(--muted); font-size: 13px;
            transition: border-color .15s, color .15s, background .15s;
        }
        .check-label:hover { border-color: #3a3a46; color: var(--text); }
        .check-label input[type=checkbox] { display: none; }
        .check-label .toggle-track {
            width: 32px; height: 18px; border-radius: 9px; background: var(--border);
            position: relative; flex-shrink: 0; transition: background .2s;
        }
        .check-label .toggle-track::after {
            content: ''; position: absolute; top: 3px; left: 3px;
            width: 12px; height: 12px; border-radius: 50%;
            background: var(--muted); transition: transform .2s, background .2s;
        }
        .check-label input:checked ~ .toggle-track { background: var(--accent); }
        .check-label input:checked ~ .toggle-track::after { transform: translateX(14px); background: #0f0f11; }
        .check-label:has(input:checked) { border-color: var(--accent); color: var(--text); background: rgba(255,145,77,.07); }

        /* ── Modal ── */
        .modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0); z-index: 10000; align-items: center; justify-content: center;
            backdrop-filter: blur(0px); transition: background .2s, backdrop-filter .2s;
        }
        .modal-overlay.open { display: flex; animation: overlayIn .2s ease forwards; }
        @keyframes overlayIn {
            from { background: rgba(0,0,0,0); backdrop-filter: blur(0px); }
            to   { background: rgba(0,0,0,.7); backdrop-filter: blur(4px); }
        }
        .modal {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: 16px; padding: 28px; width: 100%; max-width: 480px;
            position: relative; animation: modalIn .22s cubic-bezier(.34,1.56,.64,1) both;
        }
        @keyframes modalIn {
            from { opacity: 0; transform: scale(.92) translateY(12px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }
        .modal-title { font-family: 'Codec Pro', sans-serif; font-size: 19px; font-weight: 700; letter-spacing: -0.3px; margin-bottom: 20px; }
        .modal-close { position: absolute; top: 16px; right: 16px; background: none; border: none; color: var(--muted); cursor: pointer; font-size: 20px; line-height: 1; transition: color .15s, transform .15s; }
        .modal-close:hover { color: var(--text); transform: rotate(90deg); }

        /* ── Toast ── */
        #toast-container {
            position: fixed; bottom: 24px; right: 24px;
            display: flex; flex-direction: column; gap: 8px;
            z-index: 10100; pointer-events: none;
        }
        .toast {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 16px; border-radius: 10px;
            font-size: 13px; font-weight: 500; border: 1px solid;
            pointer-events: auto;
            animation: toastIn .3s cubic-bezier(.34,1.56,.64,1) both;
            min-width: 240px; max-width: 360px;
            box-shadow: 0 8px 32px rgba(0,0,0,.4);
        }
        .toast.out { animation: toastOut .25s ease forwards; }
        @keyframes toastIn {
            from { opacity: 0; transform: translateX(24px) scale(.95); }
            to   { opacity: 1; transform: translateX(0) scale(1); }
        }
        @keyframes toastOut {
            from { opacity: 1; transform: translateX(0) scale(1); max-height: 60px; margin-bottom: 0; }
            to   { opacity: 0; transform: translateX(24px) scale(.95); max-height: 0; margin-bottom: -8px; }
        }
        .toast-success { background: rgba(74,222,128,.1);  color: var(--success); border-color: rgba(74,222,128,.25); }
        .toast-error   { background: rgba(224,84,84,.1);   color: var(--danger);  border-color: rgba(224,84,84,.25); }
        .toast-info    { background: rgba(96,165,250,.1);  color: var(--info);    border-color: rgba(96,165,250,.25); }

        /* ── Custom confirm dialog ── */
        #confirm-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.7); backdrop-filter: blur(4px);
            z-index: 10001; align-items: center; justify-content: center;
        }
        #confirm-overlay.open { display: flex; }
        #confirm-box {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: 14px; padding: 24px; width: 100%; max-width: 360px;
            animation: modalIn .22s cubic-bezier(.34,1.56,.64,1) both;
        }
        #confirm-box h3 { font-family: 'Codec Pro', sans-serif; font-size: 17px; font-weight: 700; letter-spacing: -0.3px; margin-bottom: 8px; }
        #confirm-box p  { color: var(--muted); font-size: 13px; margin-bottom: 20px; line-height: 1.5; }
        #confirm-box .actions { display: flex; gap: 8px; justify-content: flex-end; }

        /* ── Pagination ── */
        nav[role=navigation] { display: flex; flex-direction: column; align-items: center; gap: 12px; }
        nav[role=navigation] > div:first-child { font-size: 12px; color: var(--muted); }
        .pagination { display: flex; align-items: center; gap: 4px; list-style: none; padding: 0; margin: 0; }
        .pagination .page-item .page-link, .pagination .page-item span {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 32px; height: 32px; padding: 0 6px;
            border-radius: 8px; font-size: 13px; text-decoration: none; color: var(--muted);
            border: 1px solid var(--border); background: transparent;
            transition: all .15s; line-height: 1; font-family: 'DM Mono', monospace;
        }
        .pagination .page-item .page-link:hover { color: var(--text); border-color: #3a3a46; background: var(--surface2); }
        .pagination .page-item.active .page-link, .pagination .page-item.active span {
            background: var(--accent); color: #0f0f11; border-color: var(--accent); font-weight: 700;
        }
        .pagination .page-item.disabled .page-link, .pagination .page-item.disabled span {
            opacity: .3; cursor: not-allowed; pointer-events: none;
        }

        /* ── Alert ── */
        .alert { padding: 12px 16px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; animation: pageEnter .2s ease both; }
        .alert-success { background: rgba(74,222,128,.1); color: var(--success); border: 1px solid rgba(74,222,128,.2); }
        .alert-error   { background: rgba(224,84,84,.1);  color: var(--danger);  border: 1px solid rgba(224,84,84,.2); }

        /* ── Empty state ── */
        .empty-state { text-align: center; padding: 60px 20px; color: var(--muted); }
        .empty-state svg { opacity: .3; margin-bottom: 16px; }
        .empty-state p { font-size: 15px; margin-bottom: 20px; }

        /* ── Overdue chip ── */
        .overdue-chip { color: var(--danger); font-size: 11px; font-weight: 500; }

        /* ── Filter bar ── */
        .filter-bar { display: flex; gap: 8px; flex-wrap: wrap; align-items: stretch; margin-bottom: 20px; }
        .filter-bar > * { height: 40px; box-sizing: border-box; }
        .filter-bar input[type=text] {
            min-width: 200px; width: auto; padding: 0 12px 0 36px; font-size: 13px; height: 40px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 16 16' fill='none' stroke='%237a7a8a' stroke-width='1.5'%3E%3Ccircle cx='6.5' cy='6.5' r='4'/%3E%3Cpath d='M11 11l2.5 2.5'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: 11px center;
        }
        .filter-bar .select-wrap { width: auto; height: 40px; }
        .filter-bar .select-wrap .csel-trigger { height: 40px; padding-top: 0; padding-bottom: 0; }
        .filter-bar select { padding: 0 32px 0 12px; font-size: 13px; height: 40px; }
        .filter-bar .check-label { height: 40px; padding: 0 13px; font-size: 13px; }
        .filter-bar .btn { height: 40px; padding-top: 0; padding-bottom: 0; }

        /* ── Spinner ── */
        .spinner { width: 14px; height: 14px; border: 2px solid currentColor; border-top-color: transparent; border-radius: 50%; animation: spin .6s linear infinite; display: inline-block; }
        @keyframes spin { to { transform: rotate(360deg); } }

        @media (max-width: 768px) { .sidebar { display: none; } .page-content { padding: 16px; } }
    </style>
    @stack('styles')
    <script>
        // Apply theme before paint (no flash)
        (function() {
            const t = localStorage.getItem('taskletto-theme') || 'dark';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
</head>
<body>

{{-- Page loader --}}
<div id="page-loader"></div>

{{-- Toast container --}}
<div id="toast-container"></div>

{{-- Custom confirm --}}
<div id="confirm-overlay">
    <div id="confirm-box">
        <h3 id="confirm-title">{{ __("app.layout_confirm_title") }}</h3>
        <p id="confirm-message">{{ __("app.layout_confirm_msg") }}</p>
        <div class="actions">
            <button class="btn btn-ghost" id="confirm-cancel">{{ __("app.layout_confirm_cancel") }}</button>
            <button class="btn btn-danger" id="confirm-ok">{{ __("app.layout_confirm_ok") }}</button>
        </div>
    </div>
</div>

<div class="app-shell">
    {{-- Sidebar --}}
    <aside class="sidebar">
        <div class="sidebar-logo">
            <a href="/dashboard">
                <img src="/logo-taskletto-light.png" alt="Taskletto" class="logo-img logo-dark">
                <img src="/logo-taskletto.png" alt="Taskletto" class="logo-img logo-light">
            </a>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-label">{{ __('app.nav_principal') }}</div>
            <a href="/dashboard" class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="1" width="6" height="6" rx="1.5"/><rect x="9" y="1" width="6" height="6" rx="1.5"/><rect x="1" y="9" width="6" height="6" rx="1.5"/><rect x="9" y="9" width="6" height="6" rx="1.5"/></svg>
                {{ __('app.nav_dashboard') }}
            </a>
            <a href="/tasks" class="nav-item {{ request()->is('tasks*') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 4h12M2 8h8M2 12h10"/></svg>
                {{ __('app.nav_tasks') }}
                @if(($overdueCount ?? 0) > 0)
                    <span class="nav-badge">{{ $overdueCount }}</span>
                @endif
            </a>
            <a href="/categories" class="nav-item {{ request()->is('categories*') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M1.5 4.5h3a1 1 0 011 1v5a1 1 0 01-1 1h-3a1 1 0 01-1-1v-5a1 1 0 011-1zM8 2.5h3a1 1 0 011 1v9a1 1 0 01-1 1H8a1 1 0 01-1-1v-9a1 1 0 011-1z"/></svg>
                {{ __('app.nav_categories') }}
            </a>
            <a href="/notes" class="nav-item {{ request()->is('notes*') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 2h7l3 3v9a1 1 0 01-1 1H3a1 1 0 01-1-1V3a1 1 0 011-1z"/><path d="M10 2v3h3M5 7h6M5 10h4"/></svg>
                {{ __('app.nav_notes') }}
            </a>
            <div class="nav-label" style="margin-top:8px">{{ __('app.nav_system') }}</div>
            <a href="/settings" class="nav-item {{ request()->is('settings') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="8" cy="8" r="2.5"/><path d="M8 1.5v1M8 13.5v1M1.5 8h1M13.5 8h1M3.2 3.2l.7.7M12.1 12.1l.7.7M12.8 3.2l-.7.7M3.9 12.1l-.7.7"/></svg>
                {{ __('app.nav_settings') }}
            </a>
        </nav>
        <div class="sidebar-footer">
            <div class="sidebar-version">
                <span class="sidebar-version-name">Taskletto</span>
                <span class="sidebar-version-badge">v1.0</span>
            </div>

            <button id="theme-toggle" onclick="toggleTheme()" class="sidebar-footer-btn" title="Alternar tema">
                <span class="sfb-icon" id="theme-icon">🌙</span>
                <span class="sfb-label" id="theme-label">{{ __('app.nav_light_mode') }}</span>
            </button>

            <button id="btn-shortcuts" class="sidebar-footer-btn" title="Atalhos (?)">
                <span class="sfb-icon">
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="3" width="14" height="10" rx="2"/><rect x="2.5" y="5.5" width="2" height="1.5" rx=".4"/><rect x="6" y="5.5" width="2" height="1.5" rx=".4"/><rect x="9.5" y="5.5" width="2" height="1.5" rx=".4"/><rect x="2.5" y="8.5" width="2" height="1.5" rx=".4"/><rect x="6" y="8.5" width="4.5" height="1.5" rx=".4"/><rect x="11.5" y="8.5" width="2" height="1.5" rx=".4"/></svg>
                </span>
                <span class="sfb-label">{{ __('app.nav_shortcuts') }}</span>
                <span class="sfb-hint">?</span>
            </button>

            <div style="border-top:1px solid var(--border);margin:6px 0 4px;"></div>
            <div style="padding:4px 10px 2px;font-size:10px;font-weight:600;letter-spacing:.8px;text-transform:uppercase;color:var(--muted);opacity:.6">{{ __('app.nav_creator') }}</div>

            <a href="/open-external?url=https://github.com/lbonavina" class="sidebar-footer-btn" title="GitHub de Lucas Bonavina">
                <span class="sfb-icon">
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor"><path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"/></svg>
                </span>
                <span class="sfb-label">Lucas Bonavina</span>
                <svg style="opacity:.3" width="9" height="9" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 3h7v7M13 3L3 13"/></svg>
            </a>

            <a href="/open-external?url=https://www.linkedin.com/in/lbonavina/" class="sidebar-footer-btn" title="LinkedIn de Lucas Bonavina">
                <span class="sfb-icon">
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor"><path d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854V1.146zm4.943 12.248V6.169H2.542v7.225h2.401zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248-.822 0-1.359.54-1.359 1.248 0 .694.521 1.248 1.327 1.248h.016zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016a5.54 5.54 0 0 1 .016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225h2.4z"/></svg>
                </span>
                <span class="sfb-label">LinkedIn</span>
                <svg style="opacity:.3" width="9" height="9" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 3h7v7M13 3L3 13"/></svg>
            </a>

            <a href="/open-external?url=https://ko-fi.com/lbonavina" class="sidebar-footer-btn" title="Me pague um café ☕">
                <span class="sfb-icon">☕</span>
                <span class="sfb-label">{{ __("app.layout_buy_coffee") }}</span>
                <svg style="opacity:.3" width="9" height="9" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 3h7v7M13 3L3 13"/></svg>
            </a>
        </div>
    </aside>

    {{-- Main content --}}
    <div class="main">
        <div class="topbar">
            <h1 class="topbar-title">@yield('page-title', 'Dashboard')</h1>
            <div class="topbar-actions">@yield('topbar-actions')</div>
        </div>
        <div class="page-content">
            @if(session('success'))
                <div class="alert alert-success" id="flash-alert">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error" id="flash-alert">{{ session('error') }}</div>
            @endif
            <div class="page-enter">
                @yield('content')
            </div>
        </div>
    </div>
</div>

<script>
// ── Theme toggle ─────────────────────────────────────────────────────────────
function toggleTheme() {
    const html    = document.documentElement;
    const current = html.getAttribute('data-theme') || 'dark';
    const next    = current === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem('taskletto-theme', next);
    updateThemeBtn(next);
}
function updateThemeBtn(theme) {
    const icon  = document.getElementById('theme-icon');
    const label = document.getElementById('theme-label');
    if (!icon) return;
    if (theme === 'light') { icon.textContent = '☀️'; label.textContent = '{{ __('app.nav_dark_mode') }}'; }
    else                   { icon.textContent = '🌙'; label.textContent = '{{ __('app.nav_light_mode') }}'; }
}
updateThemeBtn(localStorage.getItem('taskletto-theme') || 'dark');

// ── Page loader ──────────────────────────────────────────────────────────────
const loader = document.getElementById('page-loader');
let loaderW = 0;
const loaderInt = setInterval(() => {
    loaderW = Math.min(loaderW + Math.random() * 15, 85);
    loader.style.width = loaderW + '%';
}, 120);
window.addEventListener('load', () => {
    clearInterval(loaderInt);
    loader.classList.add('done');
});

// ── Toast system ─────────────────────────────────────────────────────────────
window.toast = function(message, type = 'info', duration = 3500) {
    const icons = { success: '✓', error: '✕', info: 'ℹ' };
    const el = document.createElement('div');
    el.className = `toast toast-${type}`;
    el.innerHTML = `<span style="font-size:15px">${icons[type]}</span> ${message}`;
    document.getElementById('toast-container').appendChild(el);
    setTimeout(() => {
        el.classList.add('out');
        el.addEventListener('animationend', () => el.remove());
    }, duration);
};

@if(session('success'))
    window.addEventListener('load', () => toast("{{ session('success') }}", 'success'));
    document.getElementById('flash-alert')?.remove();
@endif
@if(session('error'))
    window.addEventListener('load', () => toast("{{ session('error') }}", 'error'));
    document.getElementById('flash-alert')?.remove();
@endif

// ── Custom confirm ────────────────────────────────────────────────────────────
window.confirmDialog = function(title, message, onConfirm, danger = true) {
    const overlay = document.getElementById('confirm-overlay');
    document.getElementById('confirm-title').textContent = title;
    document.getElementById('confirm-message').textContent = message;
    const okBtn = document.getElementById('confirm-ok');
    okBtn.className = danger ? 'btn btn-danger' : 'btn btn-primary';
    overlay.classList.add('open');
    const close = () => overlay.classList.remove('open');
    document.getElementById('confirm-cancel').onclick = close;
    overlay.onclick = e => { if (e.target === overlay) close(); };
    okBtn.onclick = () => { close(); onConfirm(); };
};

// ── Nav link loader ───────────────────────────────────────────────────────────
document.querySelectorAll('.nav-item').forEach(link => {
    link.addEventListener('click', function() {
        loader.style.width = '40%';
        loader.style.opacity = '1';
        loaderW = 40;
    });
});
</script>

@stack('modals')

@stack('scripts')

<script>
/* ── Global Custom Select ─────────────────────────────────────────────────── */
(function() {
    const ARROW_SVG = '<svg class="csel-arrow" width="10" height="10" viewBox="0 0 10 10" fill="none"><path d="M2 3.5L5 6.5L8 3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';

    function buildCustomSelect(select) {
        if (select.dataset.cselInit) return;
        if (select.dataset.noCsel !== undefined) return;
        select.dataset.cselInit = '1';
        select.classList.add('native-select-hidden');

        const wrap = select.closest('.select-wrap') || (() => {
            const w = document.createElement('div');
            w.className = 'select-wrap';
            select.parentNode.insertBefore(w, select);
            w.appendChild(select);
            return w;
        })();
        wrap.style.position = 'relative';

        const trigger = document.createElement('div');
        trigger.className = 'csel-trigger';
        trigger.tabIndex  = 0;
        trigger.setAttribute('role', 'combobox');
        trigger.setAttribute('aria-expanded', 'false');

        const iconEl  = document.createElement('span');
        iconEl.className = 'csel-trigger-icon';
        const labelEl = document.createElement('span');
        labelEl.className = 'csel-trigger-label';

        trigger.appendChild(iconEl);
        trigger.appendChild(labelEl);
        trigger.insertAdjacentHTML('beforeend', ARROW_SVG);

        const dropdown = document.createElement('div');
        dropdown.className = 'csel-dropdown';

        let selectedOpt = null;

        Array.from(select.options).forEach(opt => {
            const item = document.createElement('div');
            item.className  = 'csel-option';
            item.dataset.value = opt.value;

            const color = opt.dataset.color || '';
            const icon  = opt.dataset.icon  || '';

            if (color) {
                const dot = document.createElement('span');
                dot.className = 'csel-option-dot';
                dot.style.background  = color;
                dot.style.boxShadow   = `0 0 5px ${color}66`;
                item.appendChild(dot);
            }
            if (icon) {
                const emojiEl = document.createElement('span');
                emojiEl.className   = 'csel-option-emoji';
                emojiEl.textContent = icon;
                item.appendChild(emojiEl);
            }

            const text = document.createElement('span');
            text.textContent = opt.text;
            item.appendChild(text);

            if (opt.selected) {
                item.classList.add('selected');
                selectedOpt = { item, opt, icon, color, text: opt.text };
            }

            item.addEventListener('click', e => {
                e.stopPropagation();
                selectItem(item, opt, icon, color, opt.text);
                close();
            });

            dropdown.appendChild(item);
        });

        function syncTrigger(icon, color, text, isEmpty) {
            iconEl.textContent    = icon || '';
            labelEl.textContent   = text;
            labelEl.style.color   = isEmpty ? 'var(--muted)' : '';
        }

        function selectItem(item, opt, icon, color, text) {
            dropdown.querySelectorAll('.csel-option').forEach(o => o.classList.remove('selected'));
            item.classList.add('selected');
            select.value = opt.value;
            select.dispatchEvent(new Event('change', { bubbles: true }));
            syncTrigger(icon, color, text, !opt.value);
        }

        if (selectedOpt) {
            syncTrigger(selectedOpt.icon, selectedOpt.color, selectedOpt.text, !selectedOpt.opt.value);
        } else if (select.options.length) {
            const first = select.options[0];
            syncTrigger(first.dataset.icon || '', first.dataset.color || '', first.text, !first.value);
        }

        function open() {
            document.querySelectorAll('.csel-trigger.open').forEach(t => {
                if (t !== trigger) {
                    t.classList.remove('open');
                    t.setAttribute('aria-expanded','false');
                    t.nextElementSibling?.classList.remove('open');
                }
            });
            trigger.classList.add('open');
            trigger.setAttribute('aria-expanded', 'true');
            dropdown.classList.add('open');
        }
        function close() {
            trigger.classList.remove('open');
            trigger.setAttribute('aria-expanded', 'false');
            dropdown.classList.remove('open');
        }

        trigger.addEventListener('click', e => {
            e.stopPropagation();
            trigger.classList.contains('open') ? close() : open();
        });
        trigger.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); open(); }
            if (e.key === 'Escape') close();
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                const opts = dropdown.querySelectorAll('.csel-option');
                const cur  = dropdown.querySelector('.csel-option.selected');
                const idx  = Array.from(opts).indexOf(cur);
                if (idx < opts.length - 1) opts[idx + 1].click();
            }
            if (e.key === 'ArrowUp') {
                e.preventDefault();
                const opts = dropdown.querySelectorAll('.csel-option');
                const cur  = dropdown.querySelector('.csel-option.selected');
                const idx  = Array.from(opts).indexOf(cur);
                if (idx > 0) opts[idx - 1].click();
            }
        });

        wrap.appendChild(trigger);
        wrap.appendChild(dropdown);

        const mo = new MutationObserver(() => rebuildOptions());
        mo.observe(select, { childList: true, attributes: true, subtree: true });

        function rebuildOptions() {
            dropdown.innerHTML = '';
            Array.from(select.options).forEach(opt => {
                const item = document.createElement('div');
                item.className  = 'csel-option';
                item.dataset.value = opt.value;
                const icon  = opt.dataset.icon  || '';
                const color = opt.dataset.color || '';
                if (color) {
                    const dot = document.createElement('span');
                    dot.className = 'csel-option-dot';
                    dot.style.background = color;
                    dot.style.boxShadow  = `0 0 5px ${color}66`;
                    item.appendChild(dot);
                }
                if (icon) {
                    const emojiEl = document.createElement('span');
                    emojiEl.className   = 'csel-option-emoji';
                    emojiEl.textContent = icon;
                    item.appendChild(emojiEl);
                }
                const text = document.createElement('span');
                text.textContent = opt.text;
                item.appendChild(text);
                if (opt.selected) {
                    item.classList.add('selected');
                    syncTrigger(icon, color, opt.text, !opt.value);
                }
                item.addEventListener('click', e => {
                    e.stopPropagation();
                    selectItem(item, opt, icon, color, opt.text);
                    close();
                });
                dropdown.appendChild(item);
            });
        }
    }

    function initAll() {
        document.querySelectorAll('.select-wrap select').forEach(buildCustomSelect);
    }

    document.addEventListener('click', () => {
        document.querySelectorAll('.csel-trigger.open').forEach(t => {
            t.classList.remove('open');
            t.setAttribute('aria-expanded','false');
            t.nextElementSibling?.classList.remove('open');
        });
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

    const globalObserver = new MutationObserver(() => initAll());
    globalObserver.observe(document.body, { childList: true, subtree: true });

    window.initCustomSelects = initAll;
})();
</script>

{{-- Keyboard shortcuts modal --}}
<div id="kbd-overlay">
    <div id="kbd-modal">
        <div id="kbd-modal-header">
            <div class="kbd-header-icon">
                <svg width="18" height="18" viewBox="0 0 16 16" fill="none" stroke="var(--accent)" stroke-width="1.5"><rect x="1" y="3" width="14" height="10" rx="2"/><rect x="2.5" y="5.5" width="2" height="1.5" rx=".4"/><rect x="6" y="5.5" width="2" height="1.5" rx=".4"/><rect x="9.5" y="5.5" width="2" height="1.5" rx=".4"/><rect x="2.5" y="8.5" width="2" height="1.5" rx=".4"/><rect x="6" y="8.5" width="4.5" height="1.5" rx=".4"/><rect x="11.5" y="8.5" width="2" height="1.5" rx=".4"/></svg>
            </div>
            <div class="kbd-header-text">
                <h2>{{ __('app.nav_shortcuts') }}</h2>
                <p>{{ __("app.layout_nav_faster") }}</p>
            </div>
            <button id="kbd-modal-close" title="{{ __("app.layout_close_esc") }}">✕</button>
        </div>

        <div class="kbd-search-wrap">
            <div class="kbd-search-wrap-inner">
                <span class="kbd-search-icon">🔍</span>
                <input id="kbd-search" type="text" placeholder="{{ __("app.layout_search_shortcut") }}" autocomplete="off">
            </div>
        </div>

        <div class="kbd-tabs" id="kbd-tabs">
            <button class="kbd-tab active" data-tab="all">{{ __("app.layout_tab_all") }}</button>
            <button class="kbd-tab" data-tab="nav">{{ __("app.layout_tab_nav") }}</button>
            <button class="kbd-tab" data-tab="tasks">{{ __("app.layout_tab_tasks") }}</button>
            <button class="kbd-tab" data-tab="notes">{{ __("app.layout_tab_notes") }}</button>
            <button class="kbd-tab" data-tab="editor">{{ __("app.layout_tab_editor") }}</button>
        </div>

        <div id="kbd-modal-body">
            <div class="kbd-section" data-section="nav">
                <div class="kbd-section-title">{{ __("app.layout_kbd_navigation") }}</div>
                <div class="kbd-grid">
                    <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_dashboard") }}</span><div class="kbd-keys"><kbd>G</kbd><span class="kbd-plus">→</span><kbd>D</kbd></div></div>
                    <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_tasks") }}</span><div class="kbd-keys"><kbd>G</kbd><span class="kbd-plus">→</span><kbd>T</kbd></div></div>
                    <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_notes") }}</span><div class="kbd-keys"><kbd>G</kbd><span class="kbd-plus">→</span><kbd>N</kbd></div></div>
                    <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_categories") }}</span><div class="kbd-keys"><kbd>G</kbd><span class="kbd-plus">→</span><kbd>C</kbd></div></div>
                    <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_settings") }}</span><div class="kbd-keys"><kbd>G</kbd><span class="kbd-plus">→</span><kbd>S</kbd></div></div>
                </div>
            </div>

            <div class="kbd-section" data-section="tasks">
                <div class="kbd-section-title">{{ __("app.layout_kbd_global") }}</div>
                <div class="kbd-grid">
                    <div class="kbd-row"><span class="kbd-desc">{{ __('app.nav_shortcuts') }}</span><div class="kbd-keys"><kbd>?</kbd></div></div>
                    <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_toggle_theme") }}</span><div class="kbd-keys"><kbd>Ctrl</kbd><span class="kbd-plus">+</span><kbd>⇧</kbd><span class="kbd-plus">+</span><kbd>L</kbd></div></div>
                    <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_close") }}</span><div class="kbd-keys"><kbd>Esc</kbd></div></div>
                    <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_new_task") }}</span><div class="kbd-keys"><kbd>C</kbd></div></div>
                    <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_search_tasks") }}</span><div class="kbd-keys"><kbd>/</kbd></div></div>
                    <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_filter_status") }}</span><div class="kbd-keys"><kbd>F</kbd></div></div>
                </div>
            </div>

            <div class="kbd-section" data-section="notes">
                <div class="kbd-section-title">{{ __("app.layout_kbd_notes_search") }}</div>
                <div class="kbd-grid">
                    <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_new_note") }}</span><div class="kbd-keys"><kbd>C</kbd></div></div>
                    <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_focus_search") }}</span><div class="kbd-keys"><kbd>/</kbd></div></div>
                </div>
            </div>

            <div class="kbd-section" data-section="editor">
                <div class="kbd-section-title">{{ __("app.layout_kbd_editor") }}</div>
                <div class="kbd-grid">
                    <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_save") }}</span><div class="kbd-keys"><kbd>Ctrl</kbd><span class="kbd-plus">+</span><kbd>S</kbd></div></div>
                    <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_bold") }}</span><div class="kbd-keys"><kbd>Ctrl</kbd><span class="kbd-plus">+</span><kbd>B</kbd></div></div>
                    <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_italic") }}</span><div class="kbd-keys"><kbd>Ctrl</kbd><span class="kbd-plus">+</span><kbd>I</kbd></div></div>
                    <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_underline") }}</span><div class="kbd-keys"><kbd>Ctrl</kbd><span class="kbd-plus">+</span><kbd>U</kbd></div></div>
                    <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_undo") }}</span><div class="kbd-keys"><kbd>Ctrl</kbd><span class="kbd-plus">+</span><kbd>Z</kbd></div></div>
                    <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_redo") }}</span><div class="kbd-keys"><kbd>Ctrl</kbd><span class="kbd-plus">+</span><kbd>Y</kbd></div></div>
                    <div class="kbd-row"><span class="kbd-desc">{!! __("app.layout_kbd_slash") !!}</span><div class="kbd-keys"><kbd>/</kbd></div></div>
                    <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_link") }}</span><div class="kbd-keys"><kbd>Ctrl</kbd><span class="kbd-plus">+</span><kbd>K</kbd></div></div>
                </div>
            </div>

            <div id="kbd-no-results" class="kbd-empty" style="display:none">
                <div class="kbd-empty-icon">🔍</div>
                <p>{{ __("app.layout_kbd_no_results") }}</p>
            </div>
        </div>
    </div>
</div>

<script>
// ── Keyboard shortcuts modal ──────────────────────────────────────────────────
(function () {
    const overlay   = document.getElementById('kbd-overlay');
    const btn       = document.getElementById('btn-shortcuts');
    const closeBtn  = document.getElementById('kbd-modal-close');
    const searchIn  = document.getElementById('kbd-search');
    const noResults = document.getElementById('kbd-no-results');

    function open() {
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
        setTimeout(() => searchIn?.focus(), 120);
        filterBySearch('');
        activateTab('all');
    }
    function closeFn() {
        overlay.classList.remove('open');
        document.body.style.overflow = '';
        if (searchIn) searchIn.value = '';
    }

    btn?.addEventListener('click', open);
    closeBtn?.addEventListener('click', closeFn);
    overlay?.addEventListener('click', e => { if (e.target === overlay) closeFn(); });

    function activateTab(tab) {
        document.querySelectorAll('.kbd-tab').forEach(t =>
            t.classList.toggle('active', t.dataset.tab === tab)
        );
        document.querySelectorAll('.kbd-section').forEach(s => {
            s.style.display = (tab === 'all' || s.dataset.section === tab) ? '' : 'none';
        });
    }

    document.getElementById('kbd-tabs')?.addEventListener('click', e => {
        const t = e.target.closest('.kbd-tab');
        if (!t) return;
        if (searchIn) searchIn.value = '';
        activateTab(t.dataset.tab);
        filterBySearch('');
    });

    function filterBySearch(term) {
        term = term.toLowerCase().trim();
        let anyVisible = false;

        document.querySelectorAll('.kbd-section').forEach(section => {
            let sectionHasMatch = false;
            section.querySelectorAll('.kbd-row').forEach(row => {
                const text = row.textContent.toLowerCase();
                const match = !term || text.includes(term);
                row.style.display = match ? '' : 'none';
                if (match) sectionHasMatch = true;
            });
            if (term) {
                section.style.display = sectionHasMatch ? '' : 'none';
            }
            if (sectionHasMatch) anyVisible = true;
        });

        if (noResults) noResults.style.display = anyVisible ? 'none' : '';

        if (term) {
            document.querySelectorAll('.kbd-tab').forEach(t => t.classList.remove('active'));
            document.querySelector('.kbd-tab[data-tab="all"]')?.classList.add('active');
        }
    }

    searchIn?.addEventListener('input', () => filterBySearch(searchIn.value));
    searchIn?.addEventListener('keydown', e => { if (e.key === 'Escape') { closeFn(); } });

    let gBuffer = null, gTimer = null;

    document.addEventListener('keydown', e => {
        const tag    = document.activeElement?.tagName;
        const typing = ['INPUT','TEXTAREA','SELECT'].includes(tag)
                    || document.activeElement?.isContentEditable;

        if (e.key === 'Escape') { closeFn(); return; }
        if (e.key === '?' && !typing) { e.preventDefault(); open(); return; }
        if (e.key === 'L' && e.ctrlKey && e.shiftKey) { e.preventDefault(); toggleTheme(); return; }

        if (typing) return;

        if (gBuffer === 'g') {
            clearTimeout(gTimer); gBuffer = null;
            const map = { d: '/dashboard', t: '/tasks', n: '/notes', c: '/categories', s: '/settings' };
            const dest = map[e.key.toLowerCase()];
            if (dest) { e.preventDefault(); window.location.href = dest; }
            return;
        }
        if (e.key.toLowerCase() === 'g' && !e.ctrlKey && !e.metaKey) {
            gBuffer = 'g';
            gTimer  = setTimeout(() => { gBuffer = null; }, 1200);
            return;
        }
    });
})();
</script>
</body>
</html>