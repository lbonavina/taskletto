<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Taskletto') — Taskletto</title>

    <script>
        (function () {
            const t = localStorage.getItem('taskletto-theme') || 'light';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap"
        rel="stylesheet">
    <style>
        
        
        
        
        </style>

    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --bg: #0c0c0e;
            --surface: #0f0f12;
            --surface2: #131318;
            --border: #1a1a22;
            --border-hover: var(--border-hover);
            --accent: #ff914d;
            --accent2: #ff6b1a;
            --text: #e8e8ec;
            --muted: #7a7a8a;
            --danger: #e05454;
            --success: #4ade80;
            --info: #60a5fa;
            --sidebar-w: 216px;
            --status-pending: #60a5fa;
            --status-in_progress: #f0a05a;
            --status-completed: #4ade80;
            --status-cancelled: #7a7a8a;
            --priority-low: #4ade80;
            --priority-medium: #60a5fa;
            --priority-high: #f0a05a;
            --priority-urgent: #e05454;
        }

        /* ── Light theme ── */
        html[data-theme=light] {
            /* Page background: warm off-white with very subtle warmth */
            --bg:       #f4f5f9;
            /* Cards / surfaces: crisp white */
            --surface:  #ffffff;
            /* Inputs, hover states, secondary surfaces */
            --surface2: #eef0f6;
            /* Borders: clean, light */
            --border:   #e2e4ee;
            --border-hover: #c8cadb;
            /* Primary text: deep navy-black for warmth */
            --text:     #0e0f1a;
            /* Secondary text: balanced slate */
            --muted:    #636580;
            --status-pending:     #2563eb;
            --status-in_progress: #d97706;
            --status-completed:   #16a34a;
            --status-cancelled:   #94a3b8;
            --danger: #dc2626;
            --success: #16a34a;
            --info: #2563eb;
        }

        html[data-theme=light] select option {
            background: #ffffff;
        }

        /* Sidebar: pure white, clean right border */
        html[data-theme=light] .sidebar {
            background: #ffffff;
            border-right: 1px solid #e2e4ee;
        }

        /* Removed legacy active state background to unify with modern aesthetic */

        html[data-theme=light] .sidebar-nav .nav-item:hover {
            background: #eef0f6;
            color: #0e0f1a;
        }

        html[data-theme=light] .sidebar-footer-btn:hover {
            background: #eef0f6;
        }

        html[data-theme=light] .sidebar-footer-btn .sfb-icon {
            background: #eef0f6;
        }

        /* Topbar: white with very subtle shadow */
        html[data-theme=light] .topbar {
            background: #ffffff;
            border-bottom: 1px solid #e2e4ee;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .04);
        }

        html[data-theme=light] input[type=date],
        html[data-theme=light] input[type=time],
        html[data-theme=light] input[type=datetime-local] {
            color-scheme: light;
        }

        /* Cards: white with refined shadow system */
        html[data-theme=light] .card {
            box-shadow: 0 1px 2px rgba(0, 0, 0, .04), 0 2px 6px rgba(0, 0, 0, .03);
            border-color: #e2e4ee;
        }

        /* Stat cards in light mode */
        html[data-theme=light] .stat-card {
            box-shadow: 0 1px 2px rgba(0, 0, 0, .04), 0 2px 6px rgba(0, 0, 0, .03);
        }

        html[data-theme=light] .stat-card:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, .07), 0 0 0 1px rgba(255, 145, 77, .15);
            border-color: rgba(255, 145, 77, .3);
            background: #ffffff;
        }

        html[data-theme=light] tr:hover td {
            background: #f0f2f9;
        }

        html[data-theme=light] .overdue-row td {
            background: rgba(220, 38, 38, .04);
        }

        html[data-theme=light] #page-loader {
            box-shadow: 0 0 8px var(--accent);
        }

        html[data-theme=light] .pagination .page-item .page-link:hover {
            background: var(--surface2);
            border-color: #c8cadb;
        }

        html[data-theme=light] .qf:hover {
            background: #eef0f6;
        }

        /* Buttons in light mode */
        html[data-theme=light] .btn-primary {
            color: #ffffff;
            background: var(--accent);
            box-shadow: 0 1px 3px rgba(255, 145, 77, .35);
        }

        html[data-theme=light] .btn-primary:hover {
            background: #ff7a30;
            box-shadow: 0 4px 12px rgba(255, 145, 77, .45);
        }

        html[data-theme=light] .btn-ghost {
            background: #ffffff;
            color: var(--muted);
            border-color: #e2e4ee;
        }

        html[data-theme=light] .btn-ghost:hover {
            background: #eef0f6;
            border-color: #c8cadb;
            color: var(--text);
        }

        /* Form inputs in light mode */
        html[data-theme=light] input[type=text],
        html[data-theme=light] input[type=date],
        html[data-theme=light] input[type=email],
        html[data-theme=light] input[type=password],
        html[data-theme=light] input[type=number],
        html[data-theme=light] textarea {
            background: #ffffff;
            border-color: #e2e4ee;
        }

        html[data-theme=light] input[type=text]:focus,
        html[data-theme=light] input[type=date]:focus,
        html[data-theme=light] input[type=email]:focus,
        html[data-theme=light] input[type=password]:focus,
        html[data-theme=light] input[type=number]:focus,
        html[data-theme=light] textarea:focus {
            background: #ffffff;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(255, 145, 77, .12);
        }

        html[data-theme=light] input[type=text]:hover,
        html[data-theme=light] input[type=date]:hover,
        html[data-theme=light] textarea:hover {
            border-color: #c8cadb;
        }

        /* Toasts in light mode */
        html[data-theme=light] .toast {
            box-shadow: 0 4px 16px rgba(0, 0, 0, .1), 0 1px 4px rgba(0, 0, 0, .06);
        }

        html[data-theme=light] .toast-success {
            background: #f0fdf4;
            color: #15803d;
            border-color: rgba(22, 163, 74, .25);
        }

        html[data-theme=light] .toast-error {
            background: #fef2f2;
            color: #dc2626;
            border-color: rgba(220, 38, 38, .25);
        }

        html[data-theme=light] .toast-info {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: rgba(37, 99, 235, .25);
        }

        /* Modals in light mode */
        html[data-theme=light] .modal {
            box-shadow: 0 20px 60px rgba(0, 0, 0, .12), 0 4px 16px rgba(0, 0, 0, .07);
        }

        html[data-theme=light] #kbd-modal {
            box-shadow: 0 20px 60px rgba(0, 0, 0, .12), 0 4px 16px rgba(0, 0, 0, .07);
        }

        html[data-theme=light] .sidebar-version-badge {
            background: rgba(255, 145, 77, .10);
            border-color: rgba(255, 145, 77, .25);
        }

        /* Quick filters in light mode */
        html[data-theme=light] .qf {
            background: #ffffff;
            border-color: #e2e4ee;
            color: var(--muted);
        }

        html[data-theme=light] .qf.active {
            background: var(--accent);
            color: #ffffff;
            border-color: var(--accent);
            box-shadow: 0 2px 8px rgba(255, 145, 77, .3);
        }

        /* Badge improvements in light mode */
        html[data-theme=light] .status-pending {
            background: rgba(37, 99, 235, .08);
            color: #1d4ed8;
        }

        html[data-theme=light] .status-in_progress {
            background: rgba(217, 119, 6, .08);
            color: #b45309;
        }

        html[data-theme=light] .status-completed {
            background: rgba(22, 163, 74, .08);
            color: #15803d;
        }

        html[data-theme=light] .status-cancelled {
            background: rgba(148, 163, 184, .12);
            color: #64748b;
        }

        html[data-theme=light] .priority-urgent {
            background: rgba(220, 38, 38, .08);
            color: #b91c1c;
        }

        html[data-theme=light] .priority-high {
            background: rgba(217, 119, 6, .08);
            color: #b45309;
        }

        html[data-theme=light] .priority-medium {
            background: rgba(37, 99, 235, .08);
            color: #1d4ed8;
        }

        html[data-theme=light] .priority-low {
            background: rgba(22, 163, 74, .08);
            color: #15803d;
        }

        /* Shortcut items in light mode */
        html[data-theme=light] .shortcut-item:hover {
            background: #eef0f6;
            color: #0e0f1a;
        }

        html[data-theme=light] .shortcut-item.active {
            background: rgba(255, 145, 77, .09);
        }

        /* Custom select in light mode */
        html[data-theme=light] .csel-dropdown {
            background: #ffffff;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .10), 0 1px 4px rgba(0, 0, 0, .06);
            border-color: #e2e4ee;
        }

        html[data-theme=light] .csel-trigger {
            background: #ffffff;
            border-color: #e2e4ee;
        }

        html[data-theme=light] .csel-trigger:hover {
            border-color: #c8cadb;
        }

        html[data-theme=light] .csel-trigger:focus,
        html[data-theme=light] .csel-trigger.open {
            background: #ffffff;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(255, 145, 77, .12);
        }

        /* Kbd in light mode */
        html[data-theme=light] kbd {
            background: #f4f5f9;
            border-color: #c8cadb;
            border-bottom-color: #b4b6c8;
            color: #0e0f1a;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .06);
        }

        /* Confirm box in light mode */
        html[data-theme=light] #confirm-box {
            box-shadow: 0 20px 60px rgba(0, 0, 0, .12);
        }

        /* Nav badge */
        html[data-theme=light] .nav-badge {
            background: #dc2626;
        }
        /* ── Scrollbar oculto globalmente ── */
        ::-webkit-scrollbar {
            display: none;
        }

        * {
            scrollbar-width: none;
        }

        /* Smooth theme transition */
        *,
        *::before,
        *::after {
            transition: background-color .2s ease, border-color .2s ease, color .15s ease !important;
        }

        /* But keep animation-based transitions instant */
        .page-enter,
        .toast,
        .modal,
        #inline-popup {
            transition: none !important;
        }

        html,
        body {
            height: 100%;
            background: var(--bg);
            color: var(--text);
            font-family: 'Montserrat', sans-serif;
            font-size: 13px;
            line-height: 1.5;
        }

        /* ── Page loader bar ── */
        #page-loader {
            position: fixed;
            top: 0;
            left: 0;
            height: 2px;
            width: 0;
            background: linear-gradient(90deg, var(--accent), var(--accent2));
            z-index: 9999;
            transition: width .4s ease, opacity .3s ease;
            box-shadow: 0 0 8px var(--accent);
        }

        #page-loader.done {
            width: 100% !important;
            opacity: 0;
        }

        /* ── Layout ── */
        .app-shell {
            display: flex;
            height: 100vh;
        }

        /* ── Sidebar ── */
        .sidebar {
            width: var(--sidebar-w);
            flex-shrink: 0;
            background: var(--surface);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            overflow-x: hidden;
            border-right: 1px solid var(--border);
        }

        html[data-theme=dark] .sidebar {
            background: #0a0a0c; /* Deeper background for contrast */
        }
        
        html[data-theme=light] .sidebar {
            background: #fbfbfc; /* Soft off-white for depth */
            border-right: 1px solid #e2e4ee;
        }

        /* ── Sidebar Workspace Header ── */
        .sidebar-header {
            display: flex;
            flex-direction: column;
            padding: 16px 16px 8px;
        }

        .sidebar-logo-wrap {
            margin-bottom: 16px;
        }

        .sidebar-logo-link {
            text-decoration: none;
            display: flex;
            align-items: center;
        }



        .logo-img {
            height: 30px;
            width: auto;
            transition: opacity .2s, transform .2s;
        }

        .sidebar-logo a:hover .logo-img {
            opacity: .85;
            transform: scale(1.03);
        }

        /* esconde ambas por padrão, JS/tema define qual mostrar */
        .logo-light,
        .logo-dark {
            display: none;
        }

        html[data-theme=dark] .logo-dark {
            display: block;
        }

        html[data-theme=light] .logo-light {
            display: block;
        }

        .sidebar-nav {
            padding: 8px 8px;
            flex: 1;
        }
        
        .nav-action-create {
            margin-bottom: 12px !important;
            color: var(--text) !important;
        }
        
        .nav-action-create:hover {
            color: var(--accent) !important;
        }
        
        .create-icon-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            border-radius: 6px;
            background: var(--accent);
            color: #fff;
            margin-right: 2px;
        }

        .nav-label {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: var(--muted);
            padding: 12px 14px 4px;
            opacity: 0.5;
            transition: opacity .2s, color .2s;
        }

        .nav-label-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px 0 0;
            cursor: pointer;
            transition: background .2s;
            gap: 6px;
        }

        .nav-label-header .nav-label {
            flex: 1;
            padding-top: 10px;
            padding-bottom: 6px;
        }

        .nav-label-header:hover .nav-label {
            opacity: 1;
            color: var(--text);
        }

        .collapse-arrow {
            width: 12px;
            height: 12px;
            opacity: 0.4;
            color: var(--muted);
            flex-shrink: 0;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity .2s;
        }

        .nav-label-header:hover .collapse-arrow {
            opacity: 1;
        }

        .collapse-arrow.rotated {
            transform: rotate(-90deg);
        }

        .collapsible-content {
            overflow: hidden;
            max-height: 1000px;
            opacity: 1;
            transition: max-height 0.4s cubic-bezier(0, 1, 0, 1), opacity 0.3s;
        }

        .collapsible-content.collapsed {
            max-height: 0;
            opacity: 0;
            pointer-events: none;
            transition: max-height 0.4s cubic-bezier(1, 0, 1, 0), opacity 0.2s;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 2px 4px;
            padding: 6px 10px;
            color: var(--muted);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            border-radius: 6px;
            transition: background 0.1s ease, color 0.1s ease;
            position: relative;
        }

        .nav-item:hover {
            color: var(--text);
            background: var(--surface2);
        }

        .nav-item.active {
            color: var(--text);
            background: var(--surface2);
            font-weight: 600;
            box-shadow: none;
            border: none;
            outline: none;
        }
        
        .nav-item.active .nav-icon {
            color: var(--accent);
            opacity: 1;
        }

        .nav-icon {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            opacity: .7;
            transition: opacity .2s, transform .2s cubic-bezier(.25, .8, .25, 1);
        }

        .nav-item:hover .nav-icon {
            opacity: 1;
        }

        .nav-item.active .nav-icon {
            opacity: 1;
        }

        /* ── Sidebar Shortcut Items ── */
        .shortcut-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin: 1px 12px;
            padding: 6px 12px;
            color: var(--muted);
            text-decoration: none;
            font-size: 12px;
            font-weight: 400;
            border-radius: 8px;
            transition: all .2s ease;
            position: relative;
        }

        .shortcut-item:hover {
            color: var(--text);
            background: var(--surface2);
        }

        .shortcut-item.active {
            color: var(--text);
            background: var(--surface2);
            font-weight: 600;
        }

        .shortcut-item-label {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex: 1;
        }

        .shortcut-sublabel {
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: .9px;
            text-transform: uppercase;
            color: var(--muted);
            opacity: .5;
            padding: 8px 24px 3px;
        }

        .shortcut-remove {
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: none;
            border: none;
            color: var(--muted);
            cursor: pointer;
            font-size: 16px;
            border-radius: 4px;
            opacity: 0;
            transition: all .15s;
            margin-left: 4px;
        }

        .shortcut-item:hover .shortcut-remove {
            opacity: 0.6;
        }

        .shortcut-remove:hover {
            opacity: 1 !important;
            background: rgba(224, 84, 84, .12);
            color: var(--danger);
        }

        .nav-badge {
            margin-left: auto;
            background: var(--danger);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
            font-family: 'Montserrat', sans-serif;
            letter-spacing: 0.2px;
            animation: pulse-badge 2s infinite;
        }

        @keyframes pulse-badge {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(224, 84, 84, .4);
            }

            50% {
                box-shadow: 0 0 0 4px rgba(224, 84, 84, 0);
            }
        }

        /* ── Sidebar user block ── */
        .sidebar-user-block {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 8px;
            border-radius: 8px;
            background: var(--surface2);
            border: 1px solid var(--border);
            transition: border-color 0.15s;
        }
        .sidebar-user-block:hover {
            border-color: var(--border-hover);
        }

        .sidebar-user-avatar {
            width: 30px; height: 30px;
            border-radius: 50%;
            background: var(--accent);
            color: #1a1a1a;
            font-size: .7rem;
            font-weight: 800;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            overflow: hidden;
        }

        .sidebar-user-img {
            width: 100%; height: 100%;
            object-fit: cover;
        }

        .sidebar-user-info {
            flex: 1;
            min-width: 0;
        }

        .sidebar-user-name {
            font-size: .75rem;
            font-weight: 700;
            color: var(--text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-user-email {
            font-size: .65rem;
            color: var(--muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-user-link {
            display: flex; align-items: center; gap: 8px;
            flex: 1; min-width: 0; text-decoration: none;
            border-radius: 6px; padding: 2px 4px;
        }
        .sidebar-user.active .sidebar-user-link { background: rgba(255,145,77,.08); }

        .sidebar-logout-btn {
            width: 26px; height: 26px;
            border-radius: 6px;
            border: 1px solid var(--border);
            background: transparent;
            color: var(--muted);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            transition: color .15s, border-color .15s, background .15s;
            padding: 0;
        }

        .sidebar-logout-btn:hover {
            color: #ef4444;
            border-color: rgba(239,68,68,.3);
            background: rgba(239,68,68,.08);
        }

        .sidebar-footer {
            border-top: 1px solid var(--border);
            padding: 8px 10px 10px;
            display: flex;
            flex-direction: column;
            gap: 1px;
        }

        .sidebar-version {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 4px 8px 8px;
            margin-bottom: 1px;
        }

        .sidebar-version-name {
            font-family: 'Montserrat', sans-serif;
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            letter-spacing: 0px;
        }

        .sidebar-version-badge {
            font-size: 11px;
            font-weight: 700;
            font-family: 'Montserrat', sans-serif;
            letter-spacing: 0.3px;
            padding: 2px 8px;
            border-radius: 20px;
            background: rgba(255, 145, 77, .12);
            color: var(--accent);
            border: 1px solid rgba(255, 145, 77, .2);
        }

        .sidebar-footer-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            padding: 7px 8px;
            border-radius: 7px;
            border: none;
            background: none;
            cursor: pointer;
            color: var(--muted);
            font-size: 13px;
            font-weight: 500;
            font-family: inherit;
            text-align: left;
            text-decoration: none;
            transition: all .25s cubic-bezier(.25, .8, .25, 1);
            transform: translateX(0);
        }

        .sidebar-footer-btn:hover {
            background: var(--surface2);
            color: var(--text);
            transform: translateX(4px);
        }

        .sidebar-footer-btn .sfb-icon {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--surface2);
            flex-shrink: 0;
            font-size: 13px;
            transition: background .12s, transform .12s;
        }

        .sidebar-footer-btn:hover .sfb-icon {
            background: rgba(255, 145, 77, .12);
            transform: scale(1.08);
        }

        .sidebar-footer-btn .sfb-label {
            flex: 1;
        }

        .sidebar-footer-btn .sfb-hint {
            font-size: 11.5px;
            font-family: 'Montserrat', sans-serif;
            opacity: .35;
            letter-spacing: .3px;
        }

        #theme-toggle .sfb-icon {
            font-size: 15px;
        }

        /* Creator section — very subtle */
        .sidebar-creator-btn {
            opacity: .4;
            font-size: 12px;
            padding: 4px 8px;
            transition: background .12s, color .12s, opacity .15s;
        }

        .sidebar-creator-btn:hover {
            opacity: 1;
        }

        .sidebar-creator-btn .sfb-icon {
            width: 20px;
            height: 20px;
            font-size: 11px;
            border-radius: 5px;
            background: transparent;
        }

        .sidebar-creator-btn:hover .sfb-icon {
            background: var(--surface2);
            transform: none;
        }

        html[data-theme=light] .sidebar-version-badge {
            background: rgba(255, 145, 77, .10);
            border-color: rgba(255, 145, 77, .25);
        }

        /* ── Keyboard shortcuts modal ── */
        #kbd-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .7);
            backdrop-filter: blur(8px);
            z-index: 10002;
            align-items: center;
            justify-content: center;
        }

        #kbd-overlay.open {
            display: flex;
        }

        #kbd-modal {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            width: 100%;
            max-width: 560px;
            max-height: 88vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 40px 100px rgba(0, 0, 0, .7), 0 4px 20px rgba(0, 0, 0, .3),
                inset 0 1px 0 rgba(255, 255, 255, .05);
            animation: modalIn .24s cubic-bezier(.34, 1.4, .64, 1) both;
        }

        html[data-theme=light] #kbd-modal {
            box-shadow: 0 20px 60px rgba(0, 0, 0, .12), 0 4px 16px rgba(0, 0, 0, .07);
        }

        #kbd-modal-header {
            padding: 22px 24px 18px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 14px;
            flex-shrink: 0;
            background: linear-gradient(180deg, rgba(255, 145, 77, .04) 0%, transparent 100%);
        }

        .kbd-header-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(255, 145, 77, .12);
            border: 1px solid rgba(255, 145, 77, .2);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .kbd-header-text {
            flex: 1;
        }

        .kbd-header-text h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: -0.1px;
            margin-bottom: 2px;
        }

        .kbd-header-text p {
            font-size: 11.5px;
            color: var(--muted);
        }

        #kbd-modal-close {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: none;
            color: var(--muted);
            cursor: pointer;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .12s, color .12s, border-color .12s, transform .1s;
        }

        #kbd-modal-close:hover {
            background: var(--surface2);
            color: var(--text);
            border-color: var(--muted);
            transform: scale(1.05);
        }

        .kbd-search-wrap {
            padding: 14px 24px 0;
            flex-shrink: 0;
        }

        .kbd-search-wrap input {
            width: 100%;
            padding: 8px 14px 8px 36px;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text);
            font-size: 13px;
            font-family: inherit;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
            box-sizing: border-box;
        }

        .kbd-search-wrap input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(255, 145, 77, .1);
        }

        .kbd-search-wrap input::placeholder {
            color: var(--muted);
            opacity: .7;
        }

        .kbd-search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 12px;
            pointer-events: none;
        }

        .kbd-search-wrap-inner {
            position: relative;
        }

        html[data-theme=light] .kbd-search-wrap input {
            background: #f0f2f7;
            border-color: #dddde8;
        }

        .kbd-tabs {
            display: flex;
            gap: 4px;
            padding: 12px 24px 0;
            flex-shrink: 0;
        }

        .kbd-tab {
            padding: 5px 12px;
            border-radius: 7px;
            font-size: 12px;
            font-weight: 500;
            border: 1px solid transparent;
            cursor: pointer;
            background: none;
            color: var(--muted);
            font-family: inherit;
            transition: background .12s, color .12s, border-color .12s;
        }

        .kbd-tab:hover {
            background: var(--surface2);
            color: var(--text);
        }

        .kbd-tab.active {
            background: rgba(255, 145, 77, .12);
            color: var(--accent);
            border-color: rgba(255, 145, 77, .25);
        }

        #kbd-modal-body {
            overflow-y: auto;
            padding: 10px 24px 24px;
            scrollbar-width: thin;
            scrollbar-color: var(--border) transparent;
            flex: 1;
        }

        .kbd-section {
            margin-top: 8px;
        }

        .kbd-section-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--muted);
            padding: 14px 0 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .kbd-section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .kbd-section-title:first-child {
            padding-top: 6px;
        }

        .kbd-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px;
        }

        .kbd-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 9px 12px;
            border-radius: 10px;
            transition: background .1s;
        }

        .kbd-row:hover {
            background: var(--surface2);
        }

        .kbd-desc {
            font-size: 12.5px;
            color: var(--text);
        }

        .kbd-desc small {
            color: var(--muted);
            font-size: 11px;
            display: block;
            margin-top: 1px;
        }

        .kbd-keys {
            display: flex;
            align-items: center;
            gap: 3px;
            flex-shrink: 0;
            margin-left: 12px;
        }

        kbd {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 26px;
            height: 22px;
            padding: 0 7px;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-bottom: 2px solid color-mix(in srgb, var(--border) 150%, black);
            border-radius: 6px;
            font-size: 10px;
            font-family: 'Montserrat', sans-serif;
            color: var(--text);
            font-weight: 700;
            letter-spacing: 0.3px;
            white-space: nowrap;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .2);
        }

        html[data-theme=light] kbd {
            background: #f0f2f7;
            border-color: #c8c8da;
            border-bottom-color: #b4b4cc;
            color: #1e1e30;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .06);
        }

        .kbd-plus {
            font-size: 9px;
            color: var(--muted);
            opacity: .7;
        }

        .kbd-empty {
            text-align: center;
            padding: 32px 20px;
            color: var(--muted);
            font-size: 13px;
        }

        .kbd-empty-icon {
            font-size: 32px;
            margin-bottom: 8px;
            opacity: .4;
        }

        /* ── Main ── */
        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .topbar {
            padding: 0 32px;
            height: 48px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
            background: var(--bg);
        }



        /* ── Botão fechar para bandeja ── */
        .btn-tray-close {
            width: 28px;
            height: 28px;
            border-radius: 7px;
            border: 1px solid transparent;
            background: transparent;
            color: var(--muted);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .12s, color .12s, border-color .12s;
            flex-shrink: 0;
        }
        .btn-tray-close:hover {
            background: rgba(224, 84, 84, .12);
            border-color: rgba(224, 84, 84, .25);
            color: var(--danger);
        }
        .btn-tray-close:active {
            background: rgba(224, 84, 84, .22);
            transform: scale(.93);
        }

        .page-content {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 32px;
            min-width: 0;
        }

        /* ── Page enter animation ── */
        .page-enter {
            animation: pageEnter .25s ease both;
            max-width: 1440px;
            margin: 0 auto;
            width: 100%;
        }

        @keyframes pageEnter {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        /* ── Buttons ── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all .15s;
            text-decoration: none;
            font-family: inherit;
            white-space: nowrap;
            position: relative;
            overflow: hidden;
        }

        .btn::after {
            content: '';
            position: absolute;
            inset: 0;
            background: white;
            opacity: 0;
            transition: opacity .15s;
        }

        .btn:active::after {
            opacity: .08;
        }

        .btn-primary {
            background: var(--accent);
            color: #0c0c0e;
            border-color: var(--accent);
        }

        .btn-primary:hover {
            background: #ffab70;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(255, 145, 77, .3);
        }

        .btn-primary:active {
            transform: translateY(0);
            box-shadow: none;
        }

        .btn-ghost {
            background: transparent;
            color: var(--muted);
            border-color: var(--border);
        }

        .btn-ghost:hover {
            color: var(--text);
            border-color: var(--muted);
            background: var(--surface2);
        }

        .btn-danger {
            background: transparent;
            color: var(--danger);
            border-color: var(--danger);
        }

        .btn-danger:hover {
            background: var(--danger);
            color: #fff;
        }

        .btn-sm {
            padding: 4px 10px;
            font-size: 12px;
        }

        .btn:disabled {
            opacity: .5;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        /* ── Design system tokens ── */
        :root {
            --radius-xs: 4px;
            --radius-sm: 6px;
            --radius-md: 8px;
            --radius-lg: 10px;
            --radius-xl: 14px;
            --radius-2xl: 16px;
            --radius-full: 20px;
            --purple: #a78bfa;
            --purple-bg: rgba(167,139,250,.12);
            --purple-border: rgba(167,139,250,.2);
            --notes-color: #c084fc;
            --streak-color: #fb923c;
            --color-get: #60a5fa;
            --color-post: #4ade80;
            --color-put: #f0a05a;
            --color-patch: #c084fc;
            --color-delete: #e05454;
        }

        /* ── Section title (replaces inline font-size:11px uppercase labels) ── */
        .section-title {
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--muted);
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ── Info row (key-value pair inside cards) ── */
        .info-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 10px 0;
            border-bottom: 1px solid var(--border);
            font-size: 12.5px;
        }
        .info-row:last-child { border-bottom: none; }
        .info-row-label { color: var(--muted); font-size: 12px; }
        .info-row-value { color: var(--text); font-weight: 500; }

        /* ── Inline alert (success/error/warning inside cards) ── */
        .alert-inline {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: var(--radius-lg);
            font-size: 12.5px;
            line-height: 1.5;
            margin-bottom: 14px;
        }
        .alert-inline.success {
            background: rgba(74,222,128,.08);
            border: 1px solid rgba(74,222,128,.2);
            color: var(--success);
        }
        .alert-inline.danger {
            background: rgba(224,84,84,.08);
            border: 1px solid rgba(224,84,84,.2);
            color: var(--danger);
        }
        .alert-inline.warning {
            background: rgba(240,160,90,.08);
            border: 1px solid rgba(240,160,90,.2);
            color: var(--status-in_progress);
        }
        .alert-inline.info {
            background: rgba(96,165,250,.08);
            border: 1px solid rgba(96,165,250,.2);
            color: var(--info);
        }

        /* ── Action row (setting row with label + control) ── */
        .action-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }
        .action-row-text { flex: 1; }
        .action-row-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 3px;
        }
        .action-row-desc {
            font-size: 11.5px;
            color: var(--muted);
            line-height: 1.55;
        }

        /* ── Code inline ── */
        code {
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: var(--radius-xs);
            padding: 1px 6px;
            font-size: .9em;
            color: var(--accent);
            font-family: 'SF Mono', 'Fira Code', monospace;
        }

        /* ── Recurrence badge (purple) ── */
        .badge-recurrence {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: var(--radius-xs);
            font-family: 'Montserrat', sans-serif;
            vertical-align: middle;
            line-height: 1.4;
            color: var(--purple);
            background: var(--purple-bg);
            border: 1px solid var(--purple-border);
        }

        /* ── Quick filter ── */
        .qf {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 6px 12px; border-radius: var(--radius-full); font-size: 12px; font-weight: 500;
            color: var(--muted); background: var(--surface); border: 1px solid var(--border);
            cursor: pointer; transition: all .15s; text-decoration: none; white-space: nowrap;
            font-family: inherit;
        }
        .qf:hover { color: var(--text); border-color: var(--border-hover); background: var(--surface2); }
        .qf.active { background: var(--accent); color: #0f0f11; border-color: var(--accent); font-weight: 600; }
        .qf-count { background: rgba(0,0,0,.18); border-radius: 10px; padding: 0 5px; font-family: 'Montserrat',sans-serif; font-size: 10px; }
        .qf.active .qf-count { background: rgba(0,0,0,.15); }
        .qf-danger.active { background: var(--danger); border-color: var(--danger); }

        /* ── Cards ── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 20px;
        }

        /* ── Stat cards ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 18px 20px;
            transition: border-color .2s, transform .2s, box-shadow .2s;
            cursor: default;
        }

        .stat-card:hover {
            border-color: rgba(255, 145, 77, .25);
            background: var(--surface2);
        }

        .stat-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--muted);
            margin-bottom: 6px;
        }

        .stat-value {
            font-family: 'Montserrat', sans-serif;
            font-size: 24px;
            font-weight: 600;
            color: var(--text);
            line-height: 1;
        }

        .stat-sub {
            font-size: 11px;
            color: var(--muted);
            margin-top: 4px;
        }

        /* ── Badges ── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            font-family: 'Montserrat', sans-serif;
            letter-spacing: 0.15px;
        }

        .badge-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .status-pending {
            background: rgba(96, 165, 250, .12);
            color: var(--status-pending);
        }

        .status-in_progress {
            background: rgba(240, 160, 90, .12);
            color: var(--status-in_progress);
        }

        .status-completed {
            background: rgba(74, 222, 128, .12);
            color: var(--status-completed);
        }

        .status-cancelled {
            background: rgba(122, 122, 138, .12);
            color: var(--status-cancelled);
        }

        .priority-low {
            background: rgba(74, 222, 128, .12);
            color: var(--priority-low);
        }

        .priority-medium {
            background: rgba(96, 165, 250, .12);
            color: var(--priority-medium);
        }

        .priority-high {
            background: rgba(240, 160, 90, .12);
            color: var(--priority-high);
        }

        .priority-urgent {
            background: rgba(224, 84, 84, .12);
            color: var(--priority-urgent);
        }

        /* ── Table ── */
        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .7px;
            color: var(--muted);
            padding: 8px 14px;
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 11px 14px;
            border-bottom: 1px solid var(--border);
            color: var(--text);
            font-size: 13px;
            vertical-align: middle;
            transition: background .1s;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr {
            transition: background .1s;
        }

        tr:hover td {
            background: var(--surface2);
        }

        .td-title a {
            color: var(--text);
            text-decoration: none;
            font-weight: 500;
            transition: color .15s;
        }

        .td-title a:hover {
            color: var(--accent);
        }

        .overdue-row td {
            background: rgba(224, 84, 84, .04);
        }

        /* ── Forms ── */
        .form-group {
            margin-bottom: 18px;
            position: relative;
        }

        label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .6px;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 7px;
            transition: color .15s;
        }

        .form-group:focus-within>label {
            color: var(--accent);
        }

        input[type=text],
        input[type=date],
        input[type=email],
        input[type=password],
        input[type=number],
        textarea {
            width: 100%;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text);
            padding: 10px 14px;
            font-size: 13px;
            font-family: inherit;
            line-height: 1.5;
            transition: border-color .15s, box-shadow .15s, background .15s;
            outline: none;
            -webkit-appearance: none;
        }

        input[type=text]::placeholder,
        textarea::placeholder {
            color: var(--muted);
            opacity: .5;
        }

        input[type=text]:hover,
        input[type=date]:hover,
        textarea:hover {
            border-color: var(--border-hover);
        }

        input[type=text]:focus,
        input[type=date]:focus,
        input[type=email]:focus,
        input[type=password]:focus,
        input[type=number]:focus,
        textarea:focus {
            border-color: var(--accent);
            background: var(--surface2);
            box-shadow: 0 0 0 3px rgba(255, 145, 77, .1);
        }

        input[type=date],
        input[type=time],
        input[type=datetime-local] {
            color-scheme: dark;
        }

        textarea {
            resize: vertical;
            min-height: 90px;
        }

        /* ── Custom select ── */
        .select-wrap {
            position: relative;
            display: block;
        }

        select.native-select-hidden {
            display: none !important;
        }

        .csel-trigger {
            width: 100%;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text);
            padding: 10px 36px 10px 14px;
            font-size: 13px;
            font-family: inherit;
            line-height: 1.5;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            position: relative;
            transition: border-color .15s, box-shadow .15s, background .15s;
            user-select: none;
            outline: none;
        }

        .csel-trigger:hover {
            border-color: var(--border-hover);
        }

        .csel-trigger:focus,
        .csel-trigger.open {
            border-color: var(--accent);
            background: #1a1a20;
            box-shadow: 0 0 0 3px rgba(255, 145, 77, .1), 0 1px 4px rgba(0, 0, 0, .3);
        }

        .csel-trigger-label {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .csel-trigger-icon {
            font-size: 15px;
            line-height: 1;
            flex-shrink: 0;
        }

        .csel-arrow {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            transition: transform .2s, color .15s;
            pointer-events: none;
        }

        .csel-trigger.open .csel-arrow {
            transform: translateY(-50%) rotate(180deg);
            color: var(--accent);
        }

        .csel-dropdown {
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            background: #1e1e26;
            border: 1px solid var(--border);
            border-radius: 10px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, .55), 0 2px 8px rgba(0, 0, 0, .3);
            z-index: 9999;
            overflow: hidden;
            display: none;
            max-height: 260px;
            overflow-y: auto;
        }

        /* ── Pagination ───────────────────────────────────────────────────────── */
        /* ── Pagination ───────────────────────────────────────────────────────── */
        .pagination-wrapper {
            padding: 10px 18px;
            border-top: 1px solid var(--border);
            background: rgba(0, 0, 0, 0.05); /* very subtle highlight */
        }
        html[data-theme=light] .pagination-wrapper { background: rgba(0,0,0,0.02); }

        .pagination-wrapper nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }
        /* Laravel default pagination structure adjustments */
        .pagination-wrapper nav > div:first-child { display: none !important; } 
        .pagination-wrapper nav > div:last-child {
            display: flex !important;
            flex-direction: row !important;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }
        .pagination-wrapper .small.text-muted {
            font-size: 11px !important;
            opacity: 0.6;
            margin: 0;
            font-weight: 500;
        }

        .pagination {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
            gap: 4px;
            justify-content: flex-end;
            align-items: center;
        }
        .pagination .page-item {
            display: inline-block;
        }
        .pagination .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 28px;
            height: 28px;
            padding: 0 8px;
            background: transparent;
            border: 1px solid var(--border);
            border-radius: 6px;
            color: var(--muted);
            text-decoration: none;
            font-size: 11px;
            font-weight: 600;
            transition: all .2s cubic-bezier(0.4, 0, 0.2, 1);
            line-height: 1;
        }
        .pagination .page-item.active .page-link {
            background: var(--accent);
            border-color: var(--accent);
            color: #0c0c0e;
            box-shadow: 0 2px 6px rgba(255,145,77,.2);
        }
        .pagination .page-item.disabled .page-link {
            opacity: 0.25;
            cursor: not-allowed;
            pointer-events: none;
            border-color: transparent;
        }
        .pagination .page-item:not(.active):not(.disabled) .page-link:hover {
            border-color: var(--muted);
            color: var(--text);
            background: var(--surface2);
            transform: translateY(-1px);
        }
        
        /* Oculta textos "Next"/"Previous" longos se o template os incluir */
        .pagination .page-item .page-link span { display: inline-block; }
        
        @media (max-width: 640px) {
            .pagination-wrapper nav > div:last-child {
                flex-direction: column !important;
                gap: 12px;
                text-align: center;
            }
            .pagination { justify-content: center; }
        }

        html[data-theme=light] .page-link {
            border-color: #e2e4ee;
        }
        html[data-theme=light] .page-item:not(.active):not(.disabled) .page-link:hover {
            background: #f4f5f9;
        }

        .csel-dropdown.open {
            display: block;
            animation: cselDropIn .15s ease;
        }

        @keyframes cselDropIn {
            from {
                opacity: 0;
                transform: translateY(-6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .csel-option {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 9px 14px;
            cursor: pointer;
            font-size: 13px;
            transition: background .1s;
            white-space: nowrap;
        }

        .csel-option:hover {
            background: rgba(255, 145, 77, .08);
        }

        .csel-option.selected {
            background: rgba(255, 145, 77, .13);
            color: var(--accent);
            font-weight: 500;
        }

        .csel-option-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
            display: inline-block;
        }

        .csel-option-emoji {
            font-size: 15px;
            line-height: 1;
        }

        html[data-theme=light] .csel-dropdown {
            background: #ffffff;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .10), 0 1px 4px rgba(0, 0, 0, .06);
        }

        html[data-theme=light] .csel-trigger {
            background: #f0f2f7;
        }

        html[data-theme=light] .csel-trigger:focus,
        html[data-theme=light] .csel-trigger.open {
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(255, 145, 77, .12);
        }

        html[data-theme=light] .csel-option:hover {
            background: rgba(255, 145, 77, .08);
        }

        /* ── Checkbox toggle ── */
        .check-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            user-select: none;
            white-space: nowrap;
            padding: 9px 13px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: var(--surface2);
            color: var(--muted);
            font-size: 13px;
            transition: border-color .15s, color .15s, background .15s;
        }

        .check-label:hover {
            border-color: var(--border-hover);
            color: var(--text);
        }

        .check-label input[type=checkbox] {
            display: none;
        }

        .check-label .toggle-track {
            width: 32px;
            height: 18px;
            border-radius: 9px;
            background: var(--border);
            position: relative;
            flex-shrink: 0;
            transition: background .2s;
        }

        .check-label .toggle-track::after {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--muted);
            transition: transform .2s, background .2s;
        }

        .check-label input:checked~.toggle-track {
            background: var(--accent);
        }

        .check-label input:checked~.toggle-track::after {
            transform: translateX(14px);
            background: #0c0c0e;
        }

        .check-label:has(input:checked) {
            border-color: var(--accent);
            color: var(--text);
            background: rgba(255, 145, 77, .07);
        }

        /* ── Modal ── */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0);
            z-index: 10000;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(0px);
            transition: background .2s, backdrop-filter .2s;
        }

        .modal-overlay.open {
            display: flex;
            animation: overlayIn .2s ease forwards;
        }

        @keyframes overlayIn {
            from {
                background: rgba(0, 0, 0, 0);
                backdrop-filter: blur(0px);
            }

            to {
                background: rgba(0, 0, 0, .7);
                backdrop-filter: blur(4px);
            }
        }

        .modal {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 28px;
            width: 100%;
            max-width: 480px;
            position: relative;
            animation: modalIn .22s cubic-bezier(.34, 1.56, .64, 1) both;
        }

        @keyframes modalIn {
            from {
                opacity: 0;
                transform: scale(.92) translateY(12px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .modal-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: -0.1px;
            margin-bottom: 18px;
        }

        .modal-close {
            position: absolute;
            top: 16px;
            right: 16px;
            background: none;
            border: none;
            color: var(--muted);
            cursor: pointer;
            font-size: 20px;
            line-height: 1;
            transition: color .15s, transform .15s;
        }

        .modal-close:hover {
            color: var(--text);
            transform: rotate(90deg);
        }

        /* ── Toast ── */
        #toast-container {
            position: fixed;
            bottom: 24px;
            right: 24px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            z-index: 10100;
            pointer-events: none;
        }

        .toast {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 500;
            border: 1px solid;
            pointer-events: auto;
            animation: toastIn .3s cubic-bezier(.34, 1.56, .64, 1) both;
            min-width: 240px;
            max-width: 360px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, .4);
        }

        .toast.out {
            animation: toastOut .25s ease forwards;
        }

        @keyframes toastIn {
            from {
                opacity: 0;
                transform: translateX(24px) scale(.95);
            }

            to {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
        }

        @keyframes toastOut {
            from {
                opacity: 1;
                transform: translateX(0) scale(1);
                max-height: 60px;
                margin-bottom: 0;
            }

            to {
                opacity: 0;
                transform: translateX(24px) scale(.95);
                max-height: 0;
                margin-bottom: -8px;
            }
        }

        .toast-success {
            background: #1a3a28;
            color: #4ade80;
            border-color: rgba(74, 222, 128, .3);
        }

        .toast-error {
            background: #3a1a1a;
            color: #f87171;
            border-color: rgba(224, 84, 84, .3);
        }

        .toast-info {
            background: #1a2a3a;
            color: #60a5fa;
            border-color: rgba(96, 165, 250, .3);
        }

        html[data-theme=light] .toast-success {
            background: #f0fdf4;
            color: #16a34a;
            border-color: rgba(74, 222, 128, .4);
        }

        html[data-theme=light] .toast-error {
            background: #fef2f2;
            color: #dc2626;
            border-color: rgba(224, 84, 84, .4);
        }

        html[data-theme=light] .toast-info {
            background: #eff6ff;
            color: #2563eb;
            border-color: rgba(96, 165, 250, .4);
        }

        /* ── Custom confirm dialog ── */
        #confirm-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .7);
            backdrop-filter: blur(4px);
            z-index: 10001;
            align-items: center;
            justify-content: center;
        }

        #confirm-overlay.open {
            display: flex;
        }

        #confirm-box {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 24px;
            width: 100%;
            max-width: 360px;
            animation: modalIn .22s cubic-bezier(.34, 1.56, .64, 1) both;
        }

        #confirm-box h3 {
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: -0.1px;
            margin-bottom: 8px;
        }

        #confirm-box p {
            color: var(--muted);
            font-size: 13px;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        #confirm-box .actions {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }

        /* ── Pagination ── */
        nav[role=navigation] {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        nav[role=navigation]>div:first-child {
            font-size: 12px;
            color: var(--muted);
        }

        .pagination {
            display: flex;
            align-items: center;
            gap: 4px;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .pagination .page-item .page-link,
        .pagination .page-item span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 6px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 500;
            text-decoration: none;
            color: var(--muted);
            border: 1px solid var(--border);
            background: transparent;
            transition: all .15s;
            line-height: 1;
            font-family: 'Montserrat', sans-serif;
        }

        /* ── Upgrade Modal (Premium Refined) ── */
        #upgrade-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0, 0, 0, .5); backdrop-filter: blur(4px);
            z-index: 11000; align-items: center; justify-content: center;
            opacity: 0; transition: opacity .2s ease;
        }
        #upgrade-overlay.open { display: flex; opacity: 1; }
        
        #upgrade-modal {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: 16px; width: 100%; max-width: 380px;
            padding: 32px 28px; text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            transform: translateY(15px) scale(0.97); transition: all .25s cubic-bezier(.34, 1.56, .64, 1);
        }
        html[data-theme=light] #upgrade-modal { box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
        #upgrade-overlay.open #upgrade-modal { transform: translateY(0) scale(1); }
        
        .upgrade-icon-wrap {
            width: 54px; height: 54px; margin: 0 auto 20px;
            background: rgba(255,145,77,.1); border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            border: 1px solid rgba(255,145,77,.2);
            box-shadow: 0 0 20px rgba(255,145,77,.15);
        }
        
        .upgrade-title {
            font-family: 'Montserrat', sans-serif; font-size: 18px; font-weight: 700;
            color: var(--text); margin-bottom: 8px; letter-spacing: -0.3px;
        }
        
        .upgrade-text {
            font-size: 13.5px; color: var(--muted); line-height: 1.5;
            margin-bottom: 24px;
        }
        
        .upgrade-features {
            text-align: left; background: var(--surface2);
            border: 1px solid var(--border); border-radius: 10px;
            padding: 14px 16px; margin-bottom: 24px;
            display: flex; flex-direction: column; gap: 8px;
        }
        
        .up-feat {
            display: flex; align-items: center; gap: 8px;
            font-size: 12.5px; color: var(--text);
        }
        
        .up-feat svg { color: var(--accent); flex-shrink: 0; }
        
        .upgrade-actions {
            display: flex; gap: 10px; justify-content: stretch;
        }
        
        .upgrade-actions .btn { 
            flex: 1; 
            justify-content: center; 
            height: 38px;
        }

        .pagination .page-item.active .page-link,
        .pagination .page-item.active span {
            background: var(--accent);
            color: #0c0c0e;
            border-color: var(--accent);
            font-weight: 700;
        }

        .pagination .page-item.disabled .page-link,
        .pagination .page-item.disabled span {
            opacity: .3;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* ── Alert ── */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 16px;
            animation: pageEnter .2s ease both;
        }

        .alert-success {
            background: rgba(74, 222, 128, .1);
            color: var(--success);
            border: 1px solid rgba(74, 222, 128, .2);
        }

        .alert-error {
            background: rgba(224, 84, 84, .1);
            color: var(--danger);
            border: 1px solid rgba(224, 84, 84, .2);
        }

        /* ── Empty state ── */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--muted);
        }

        .empty-state svg {
            opacity: .3;
            margin-bottom: 16px;
        }

        .empty-state p {
            font-size: 15px;
            margin-bottom: 20px;
        }

        /* ── Overdue chip ── */
        .overdue-chip {
            color: var(--danger);
            font-size: 11px;
            font-weight: 500;
        }

        /* ── Filter bar ── */
        .filter-bar {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: stretch;
            margin-bottom: 20px;
        }

        .filter-bar>* {
            height: 40px;
            box-sizing: border-box;
        }

        .filter-bar input[type=text] {
            min-width: 200px;
            width: auto;
            padding: 0 12px 0 36px;
            font-size: 12px;
            height: 40px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 16 16' fill='none' stroke='%237a7a8a' stroke-width='1.5'%3E%3Ccircle cx='6.5' cy='6.5' r='4'/%3E%3Cpath d='M11 11l2.5 2.5'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: 11px center;
        }

        .filter-bar .select-wrap {
            width: auto;
            height: 40px;
        }

        .filter-bar .select-wrap .csel-trigger {
            height: 40px;
            padding-top: 0;
            padding-bottom: 0;
        }

        .filter-bar select {
            padding: 0 32px 0 12px;
            font-size: 13px;
            height: 40px;
        }

        .filter-bar .check-label {
            height: 40px;
            padding: 0 13px;
            font-size: 13px;
        }

        .filter-bar .btn {
            height: 40px;
            padding-top: 0;
            padding-bottom: 0;
        }

        /* ── Spinner ── */
        .spinner {
            width: 14px;
            height: 14px;
            border: 2px solid currentColor;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin .6s linear infinite;
            display: inline-block;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }

            .page-content {
                padding: 16px;
            }
        }
    </style>
    <style>


        #shortcuts-label { transition: opacity .2s; }


        /* ── Inline shortcut button (lista de tasks e cards de notas) ── */
        .shortcut-inline-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
            line-height: 1;
            color: var(--muted);
            opacity: 0;
            transition: opacity .15s, color .15s, transform .15s;
            padding: 3px 5px;
            border-radius: 5px;
        }
        tr:hover .shortcut-inline-btn,
        .note-card:hover .note-shortcut-btn { opacity: 1; }
        .shortcut-inline-btn.pinned { opacity: 1 !important; color: var(--accent); }
        .shortcut-inline-btn:hover { color: var(--accent); transform: scale(1.2); }

        /* Wrapper direito do header de nota (atalho + pin lado a lado) */
        .note-card-header-right {
            display: flex;
            align-items: center;
            gap: 4px;
            position: relative;
            z-index: 1;
        }

        /* Botão de atalho no card de nota */
        .note-shortcut-btn {
            position: relative;
            z-index: 1;
            font-size: 15px;
            background: var(--surface);
            border: 1px solid var(--border) !important;
            border-radius: 6px;
            padding: 2px 6px;
            box-shadow: 0 2px 6px rgba(0,0,0,.2);
        }
        .note-shortcut-btn.pinned { color: var(--accent); opacity: 1 !important; }


    </style>
    @stack('styles')

    {{-- ── Phase 1: UI/UX Refinements — Light Mode Polish ── --}}
    <style>
        /* ── Light mode: main content area has subtle depth ── */
        html[data-theme=light] .page-content {
            background: #f4f5f9;
        }

        html[data-theme=light] .main {
            background: #f4f5f9;
        }

        /* ── Light mode: topbar title bolder ── */
        html[data-theme=light] .topbar-title {
            color: #0e0f1a;
            font-weight: 800;
        }

        /* ── Light mode: sidebar logo area ── */
        html[data-theme=light] .sidebar-logo {
            border-bottom: 1px solid #e2e4ee;
        }

        /* ── Light mode: nav labels ── */
        html[data-theme=light] .nav-label {
            color: #9496b0;
        }

        /* ── Light mode: sidebar footer ── */
        html[data-theme=light] .sidebar-footer {
            border-top: 1px solid #e2e4ee;
            background: #fafbfd;
        }

        /* ── Light mode: sidebar version name ── */
        html[data-theme=light] .sidebar-version-name {
            color: #0e0f1a;
            font-weight: 700;
        }

        /* ── Light mode: sidebar creator buttons ── */
        html[data-theme=light] .sidebar-creator-btn {
            color: #9496b0;
        }

        /* ── Light mode: section title text ── */
        html[data-theme=light] .section-title {
            color: #9496b0;
        }

        /* ── Light mode: table headers ── */
        html[data-theme=light] th {
            color: #9496b0;
            background: #f7f8fb;
        }

        /* ── Light mode: table borders ── */
        html[data-theme=light] td {
            border-bottom-color: #eef0f6;
        }

        /* ── Light mode: check-label toggle ── */
        html[data-theme=light] .check-label {
            background: #ffffff;
            border-color: #e2e4ee;
            color: var(--muted);
        }

        html[data-theme=light] .check-label:hover {
            border-color: #c8cadb;
            color: var(--text);
        }

        html[data-theme=light] .check-label:has(input:checked) {
            border-color: var(--accent);
            background: rgba(255, 145, 77, .05);
        }

        html[data-theme=light] .check-label .toggle-track {
            background: #d1d5e0;
        }

        html[data-theme=light] .check-label input:checked ~ .toggle-track {
            background: var(--accent);
        }

        html[data-theme=light] .check-label input:checked ~ .toggle-track::after {
            background: #ffffff;
        }

        /* ── Light mode: empty state ── */
        html[data-theme=light] .empty-state {
            color: #9496b0;
        }

        /* ── Light mode: code inline ── */
        html[data-theme=light] code {
            background: #eef0f6;
            border-color: #e2e4ee;
            color: #d6570a;
        }

        /* ── Light mode: alert boxes ── */
        html[data-theme=light] .alert-success {
            background: rgba(22, 163, 74, .07);
            color: #15803d;
            border-color: rgba(22, 163, 74, .2);
        }

        html[data-theme=light] .alert-error {
            background: rgba(220, 38, 38, .07);
            color: #b91c1c;
            border-color: rgba(220, 38, 38, .2);
        }

        /* ── Light mode: modal overlay ── */
        html[data-theme=light] .modal-overlay.open {
            background: rgba(14, 15, 26, .45);
        }

        /* ── Light mode: badge recurrence ── */
        html[data-theme=light] .badge-recurrence {
            background: rgba(124, 77, 255, .08);
            border-color: rgba(124, 77, 255, .2);
            color: #6d28d9;
        }

        /* ── Light mode: alert-inline ── */
        html[data-theme=light] .alert-inline.success {
            background: rgba(22, 163, 74, .07);
            border-color: rgba(22, 163, 74, .2);
            color: #15803d;
        }

        html[data-theme=light] .alert-inline.danger {
            background: rgba(220, 38, 38, .07);
            border-color: rgba(220, 38, 38, .2);
            color: #b91c1c;
        }

        html[data-theme=light] .alert-inline.warning {
            background: rgba(217, 119, 6, .07);
            border-color: rgba(217, 119, 6, .2);
            color: #b45309;
        }

        html[data-theme=light] .alert-inline.info {
            background: rgba(37, 99, 235, .07);
            border-color: rgba(37, 99, 235, .2);
            color: #1d4ed8;
        }

        /* ── Light mode: dc (dashboard cards) ── */
        html[data-theme=light] .dc {
            box-shadow: 0 1px 3px rgba(0, 0, 0, .05), 0 2px 8px rgba(0, 0, 0, .03);
            border-color: #e2e4ee;
        }

        html[data-theme=light] .dc:hover {
            border-color: rgba(255, 145, 77, .28);
            box-shadow: 0 2px 8px rgba(0, 0, 0, .07), inset 0 0 0 1px rgba(255, 145, 77, .1);
        }

        /* ── Light mode: KPI cards ── */
        html[data-theme=light] .kpi-card {
            background: #ffffff;
            border-color: #e2e4ee;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .04);
        }

        /* ── Light mode: improved page load bar ── */
        html[data-theme=light] #page-loader {
            box-shadow: 0 0 10px rgba(255, 145, 77, .5);
        }

        /* ── Light mode: overdue chip ── */
        html[data-theme=light] .overdue-chip {
            color: #b91c1c;
        }

        /* ── Smooth elevation on interactive elements ── */
        html[data-theme=light] .btn-primary:active {
            transform: translateY(0);
            box-shadow: 0 1px 2px rgba(255, 145, 77, .2);
        }


        /* ── Light mode: kbd modal search input ── */
        html[data-theme=light] .kbd-search-wrap input {
            background: #f4f5f9;
            border-color: #e2e4ee;
        }

        html[data-theme=light] .kbd-search-wrap input:focus {
            background: #ffffff;
        }

        html[data-theme=light] .kbd-tab.active {
            background: rgba(255, 145, 77, .1);
            border-color: rgba(255, 145, 77, .25);
            color: var(--accent);
        }

        html[data-theme=light] .kbd-row:hover {
            background: #f4f5f9;
        }

        /* ── Filter bar inputs in light mode ── */
        html[data-theme=light] .filter-bar input[type=text] {
            background: #ffffff;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 16 16' fill='none' stroke='%239496b0' stroke-width='1.5'%3E%3Ccircle cx='6.5' cy='6.5' r='4'/%3E%3Cpath d='M11 11l2.5 2.5'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: 11px center;
        }

        /* ── Sidebar shortcut remove button in light ── */
        html[data-theme=light] .shortcut-remove:hover {
            background: rgba(220, 38, 38, .1);
            color: #b91c1c;
        }

        /* ── Bulk bar in light mode ── */
        html[data-theme=light] .bulk-bar {
            background: #ffffff;
            border-color: #e2e4ee;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .06);
        }

        /* ── Page transition slightly smoother ── */
        .page-enter {
            animation: pageEnter .3s cubic-bezier(.25, .46, .45, .94) both;
        }
    </style>
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
    
    {{-- Upgrade Modal --}}
    <div id="upgrade-overlay">
        <div id="upgrade-modal">
            <div class="upgrade-modal-body">
                <div class="upgrade-icon-wrap">
                    <svg width="24" height="24" viewBox="0 0 16 16" fill="var(--accent)"><path d="M8 1l1.8 3.6L14 5.4l-3 2.9.7 4.1L8 10.4l-3.7 2L5 8.3 2 5.4l4.2-.8L8 1z"/></svg>
                </div>
                <h3 class="upgrade-title" id="upgrade-title">Limite atingido</h3>
                <p class="upgrade-text" id="upgrade-message">Você atingiu o limite do seu plano atual.</p>
                
                <div class="upgrade-features">
                    <div class="up-feat"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Notas e tarefas ilimitadas</div>
                    <div class="up-feat"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Projetos avançados</div>
                    <div class="up-feat"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Suporte prioritário</div>
                </div>

                <div class="upgrade-actions">
                    <button class="btn btn-ghost" onclick="closeUpgradeModal()">Mais tarde</button>
                    <a href="{{ route('pricing') }}" class="btn btn-primary upgrade-btn-pro" style="flex:1; justify-content:center; text-decoration:none;">Fazer Upgrade PRO</a>
                </div>
            </div>
        </div>
    </div>

    <div class="app-shell">
        {{-- Sidebar --}}
        <aside class="sidebar">
            <div class="sidebar-header">
                {{-- Logo and User combined as Workspace context --}}
                <div class="sidebar-logo-wrap">
                    <a href="/dashboard" class="sidebar-logo-link">
                        <img src="/logo-taskletto-light.png" alt="Taskletto" class="logo-img logo-dark">
                        <img src="/logo-taskletto.png" alt="Taskletto" class="logo-img logo-light">
                    </a>
                </div>
                
                @auth
                <div class="sidebar-user-block">
                    <a href="{{ route('profile') }}" class="sidebar-user-link">
                        <div class="sidebar-user-avatar">
                            @if(Auth::user()->avatar)
                                <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="sidebar-user-img">
                            @else
                                {{ Auth::user()->initials() }}
                            @endif
                        </div>
                        <div class="sidebar-user-info">
                            <div class="sidebar-user-name">{{ Auth::user()->name }}</div>
                            <div class="sidebar-user-email">Meu Espaço Pessoal</div>
                        </div>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="flex-shrink:0;">
                        @csrf
                        <button type="submit" class="sidebar-logout-btn" title="Sair">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                <polyline points="16 17 21 12 16 7"/>
                                <line x1="21" y1="12" x2="9" y2="12"/>
                            </svg>
                        </button>
                    </form>
                </div>
                @endauth
            </div>

            <nav class="sidebar-nav">
                <a href="#" class="nav-item nav-action-create" onclick="openTaskModal(); return false;">
                    <div class="create-icon-wrap">
                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M8 2v12M2 8h12"/>
                        </svg>
                    </div>
                    Nova Tarefa
                </a>
                
                <div class="nav-label" style="margin-top: 8px;">{{ __('app.nav_principal') }}</div>
                <a href="/dashboard" class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="7" height="7" x="3" y="3" rx="1"/>
                        <rect width="7" height="7" x="14" y="3" rx="1"/>
                        <rect width="7" height="7" x="14" y="14" rx="1"/>
                        <rect width="7" height="7" x="3" y="14" rx="1"/>
                    </svg>
                    {{ __('app.nav_dashboard') }}
                </a>
                <a href="/tasks" class="nav-item {{ request()->is('tasks*') ? 'active' : '' }}">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="5" width="6" height="6" rx="1"/>
                        <path d="m3 17 2 2 4-4"/>
                        <path d="M13 6h8"/>
                        <path d="M13 12h8"/>
                        <path d="M13 18h8"/>
                    </svg>
                    {{ __('app.nav_tasks') }}
                    @if(($overdueCount ?? 0) > 0)
                        <span class="nav-badge">{{ $overdueCount }}</span>
                    @endif
                </a>
                <a href="/categories" class="nav-item {{ request()->is('categories*') ? 'active' : '' }}">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z"/>
                    </svg>
                    {{ __('app.nav_categories') }}
                </a>
                <a href="/notes" class="nav-item {{ request()->is('notes*') ? 'active' : '' }}">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/>
                        <path d="M14 2v4a2 2 0 0 0 2 2h4"/>
                        <path d="M10 9H8"/>
                        <path d="M16 13H8"/>
                        <path d="M16 17H8"/>
                    </svg>
                    {{ __('app.nav_notes') }}
                </a>
                {{-- ── Atalhos ── --}}
                <div class="nav-label-header" id="shortcuts-toggle">
                    <div class="nav-label" id="shortcuts-label">Atalhos</div>
                    <svg class="collapse-arrow" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6l5 5 5-5" />
                    </svg>
                </div>
                <div id="sidebar-shortcuts-list" class="collapsible-content">
                    <div id="shortcuts-empty" style="display:none;padding:5px 16px 8px;font-size:11.5px;color:var(--muted);line-height:1.5;opacity:.7">
                        Nenhum atalho ainda.<br>
                        <span style="opacity:.7">Abra uma tarefa ou nota e clique em ⭐</span>
                    </div>
                </div>
                <div class="nav-label" style="margin-top:4px">{{ __('app.nav_system') }}</div>
                <a href="{{ route('billing') }}" class="nav-item {{ request()->is('billing*') ? 'active' : '' }}">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="20" height="14" x="2" y="5" rx="2"/>
                        <line x1="2" x2="22" y1="10" y2="10"/>
                    </svg>
                    @auth
                        @if(Auth::user()->onPro())
                            Assinatura <span style="font-size:9px;background:rgba(255,145,77,.15);color:var(--accent);padding:1px 5px;border-radius:4px;font-weight:700;margin-left:2px">PRO</span>
                        @else
                            Assinatura
                        @endif
                    @else
                        Assinatura
                    @endauth
                </a>
                <a href="/settings" class="nav-item {{ request()->is('settings') ? 'active' : '' }}">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    {{ __('app.nav_settings') }}
                </a>
            </nav>
            <div class="sidebar-footer">

                <div class="sidebar-version">
                    <span class="sidebar-version-name">Taskletto</span>
                    <span class="sidebar-version-badge">v{{ config('app.version') }}</span>
                </div>

                <button id="theme-toggle" onclick="toggleTheme()" class="sidebar-footer-btn" title="Alternar tema">
                    <span class="sfb-icon" id="theme-icon">🌙</span>
                    <span class="sfb-label" id="theme-label">{{ __('app.nav_light_mode') }}</span>
                </button>

                <button id="btn-shortcuts" class="sidebar-footer-btn" title="Atalhos (?)">
                    <span class="sfb-icon">
                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor"
                            stroke-width="1.5">
                            <rect x="1" y="3" width="14" height="10" rx="2" />
                            <rect x="2.5" y="5.5" width="2" height="1.5" rx=".4" />
                            <rect x="6" y="5.5" width="2" height="1.5" rx=".4" />
                            <rect x="9.5" y="5.5" width="2" height="1.5" rx=".4" />
                            <rect x="2.5" y="8.5" width="2" height="1.5" rx=".4" />
                            <rect x="6" y="8.5" width="4.5" height="1.5" rx=".4" />
                            <rect x="11.5" y="8.5" width="2" height="1.5" rx=".4" />
                        </svg>
                    </span>
                    <span class="sfb-label">{{ __('app.nav_shortcuts') }}</span>
                    <span class="sfb-hint">?</span>
                </button>

                <div style="border-top:1px solid var(--border);margin:5px 0 3px;opacity:.5;"></div>
                <div
                    style="padding:2px 8px 1px;font-size:9px;font-weight:600;letter-spacing:.8px;text-transform:uppercase;color:var(--muted);opacity:.35">
                    {{ __('app.nav_creator') }}
                </div>

                <a href="/open-external?url=https://github.com/lbonavina" class="sidebar-footer-btn sidebar-creator-btn"
                    title="GitHub de Lucas Bonavina">
                    <span class="sfb-icon">
                        <svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor">
                            <path
                                d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z" />
                        </svg>
                    </span>
                    <span class="sfb-label">Lucas Bonavina</span>
                    <svg style="opacity:.25" width="8" height="8" viewBox="0 0 16 16" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M6 3h7v7M13 3L3 13" />
                    </svg>
                </a>

                <a href="/open-external?url=https://www.linkedin.com/in/lbonavina/" class="sidebar-footer-btn sidebar-creator-btn"
                    title="LinkedIn de Lucas Bonavina">
                    <span class="sfb-icon">
                        <svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor">
                            <path
                                d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854V1.146zm4.943 12.248V6.169H2.542v7.225h2.401zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248-.822 0-1.359.54-1.359 1.248 0 .694.521 1.248 1.327 1.248h.016zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016a5.54 5.54 0 0 1 .016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225h2.4z" />
                        </svg>
                    </span>
                    <span class="sfb-label">LinkedIn</span>
                    <svg style="opacity:.25" width="8" height="8" viewBox="0 0 16 16" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M6 3h7v7M13 3L3 13" />
                    </svg>
                </a>
            </div>
        </aside>

        {{-- Main content --}}
        <div class="main">
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
            const html = document.documentElement;
            const current = html.getAttribute('data-theme') || 'dark';
            const next = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('taskletto-theme', next);
            updateThemeBtn(next);
        }
        function updateThemeBtn(theme) {
            const icon = document.getElementById('theme-icon');
            const label = document.getElementById('theme-label');
            if (!icon) return;
            if (theme === 'light') { icon.textContent = '☀️'; label.textContent = '{{ __('app.nav_dark_mode') }}'; }
            else { icon.textContent = '🌙'; label.textContent = '{{ __('app.nav_light_mode') }}'; }
        }
        updateThemeBtn(localStorage.getItem('taskletto-theme') || 'light');

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
        window.toast = function (message, type = 'info', duration = 3500) {
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

        // ── Upgrade Modal Helpers ──────────────────────────────────────────────────
        window.showUpgradeModal = function(message) {
            const overlay = document.getElementById('upgrade-overlay');
            if (!overlay) return;
            if (message) document.getElementById('upgrade-message').textContent = message;
            overlay.classList.add('open');
            document.body.style.overflow = 'hidden';
        };
        window.closeUpgradeModal = function() {
            const overlay = document.getElementById('upgrade-overlay');
            if (overlay) overlay.classList.remove('open');
            document.body.style.overflow = '';
        };
        document.getElementById('upgrade-overlay')?.addEventListener('click', e => {
            if (e.target.id === 'upgrade-overlay') closeUpgradeModal();
        });
        document.querySelectorAll('.nav-item').forEach(link => {
            link.addEventListener('click', function () {
                loader.style.width = '40%';
                loader.style.opacity = '1';
                loaderW = 40;
            });
        });

        // ── Sidebar New Task Global Helper ──────────────────────────────────────────
        window.openTaskModal = function() {
            const modal = document.getElementById('modal-new-task');
            if (modal) {
                modal.classList.add('open');
                setTimeout(() => document.getElementById('nt-title')?.focus(), 150);
            } else {
                window.location.href = '/tasks?new=1';
            }
        };
    </script>

    @stack('modals')

    @stack('scripts')

    <script>
    // ── Inline shortcut buttons (listas) ─────────────────────────────────────
    (function() {
        // Exposto globalmente para rows dinâmicas chamarem após inserção
        window.syncShortcutBtns = function() {
            if (!window.Shortcuts) return;
            document.querySelectorAll('.shortcut-inline-btn').forEach(btn => {
                const pinned = window.Shortcuts.has(btn.dataset.url);
                btn.classList.toggle('pinned', pinned);
                btn.textContent = pinned ? '★' : '☆';
                btn.title = pinned ? 'Remover dos atalhos' : 'Adicionar aos atalhos';
            });
        };

        // Usa fase de CAPTURA (true) para rodar antes de qualquer stopPropagation nos filhos
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.shortcut-inline-btn');
            if (!btn || !window.Shortcuts) return;
            e.stopPropagation();
            e.preventDefault(); // evita navegação quando o botão está dentro de <a>
            const item = {
                id:    btn.dataset.url,
                type:  btn.dataset.type,
                label: btn.dataset.label,
                url:   btn.dataset.url,
                emoji: btn.dataset.emoji,
            };
            const pinned = window.Shortcuts.toggle(item);
            window.syncShortcutBtns();
            toast(pinned ? 'Atalho adicionado!' : 'Atalho removido', pinned ? 'success' : 'info', 2200);
        }, true);

        document.addEventListener('shortcut-changed', window.syncShortcutBtns);
        // Roda no DOMContentLoaded (botões já existem) e aguarda Shortcuts estar pronto
        function trySync() {
            if (window.Shortcuts) { window.syncShortcutBtns(); }
            else { setTimeout(trySync, 30); }
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', trySync);
        } else {
            trySync();
        }
    })();
    </script>

    <script>
    // ── Sidebar Shortcuts (localStorage) ─────────────────────────────────────────
    (function() {
        const KEY = 'taskletto-shortcuts-v1';
        const MAX = 8;

        function load() {
            try { return JSON.parse(localStorage.getItem(KEY) || '[]'); }
            catch { return []; }
        }
        function save(list) {
            localStorage.setItem(KEY, JSON.stringify(list));
        }

        window.Shortcuts = {
            list: load,

            add(item) {
                // item: { id, type, label, url, emoji }
                let list = load();
                if (list.find(s => s.url === item.url)) return false; // já existe
                if (list.length >= MAX) list = list.slice(1); // remove o mais antigo
                list.push(item);
                save(list);
                render();
                return true;
            },

            remove(url) {
                save(load().filter(s => s.url !== url));
                render();
            },

            has(url) {
                return !!load().find(s => s.url === url);
            },

            toggle(item) {
                if (this.has(item.url)) { this.remove(item.url); return false; }
                else { this.add(item); return true; }
            },

            updateLabel(url, newLabel) {
                const list = load();
                const entry = list.find(s => s.url === url);
                if (!entry) return; // não está nos atalhos, nada a fazer
                entry.label = newLabel;
                save(list);
                render();
            }
        };

        function render() {
            const list = load();
            const container = document.getElementById('sidebar-shortcuts-list');
            const emptyEl   = document.getElementById('shortcuts-empty');
            const label     = document.getElementById('shortcuts-label');
            if (!container) return;

            // Remove itens antigos (manter o empty placeholder)
            container.querySelectorAll('.shortcut-item, .shortcut-sublabel').forEach(el => el.remove());

            if (list.length === 0) {
                if (emptyEl) emptyEl.style.display = '';
                if (label) label.style.opacity = '.55';
            } else {
                if (emptyEl) emptyEl.style.display = 'none';
                if (label) label.style.opacity = '1';

                const currentUrl = window.location.pathname;
                const groups = [
                    { type: 'task', label: 'Tarefas', items: list.filter(s => s.type === 'task') },
                    { type: 'note', label: 'Notas',   items: list.filter(s => s.type === 'note') },
                ];

                groups.forEach(group => {
                    if (group.items.length === 0) return;

                    const sublabel = document.createElement('div');
                    sublabel.className = 'shortcut-sublabel';
                    sublabel.textContent = group.label;
                    if (emptyEl) container.insertBefore(sublabel, emptyEl);
                    else container.appendChild(sublabel);

                    group.items.forEach(s => {
                        const a = document.createElement('a');
                        a.href = s.url;
                        a.className = 'shortcut-item' + (currentUrl === s.url ? ' active' : '');
                        a.title = s.label;

                        const labelEl = document.createElement('span');
                        labelEl.className = 'shortcut-item-label';
                        labelEl.textContent = s.label;

                        const removeBtn = document.createElement('button');
                        removeBtn.className = 'shortcut-remove';
                        removeBtn.title = 'Remover atalho';
                        removeBtn.innerHTML = '×';
                        removeBtn.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            window.Shortcuts.remove(s.url);
                            document.dispatchEvent(new CustomEvent('shortcut-changed', { detail: { url: s.url, pinned: false } }));
                        });

                        a.appendChild(labelEl);
                        a.appendChild(removeBtn);

                        if (emptyEl) container.insertBefore(a, emptyEl);
                        else container.appendChild(a);
                    });
                });
            }

            // Atualiza botão da página atual se existir
            updatePinBtn();
        }

        function updatePinBtn() {
            const btn = document.getElementById('btn-add-shortcut');
            if (!btn) return;
            const url = btn.dataset.url;
            const pinned = window.Shortcuts.has(url);
            btn.classList.toggle('pinned', pinned);
            const star = btn.querySelector('.pin-star');
            if (star) star.textContent = pinned ? '★' : '☆';
            const lbl = btn.querySelector('.pin-label');
            if (lbl) lbl.textContent = pinned ? 'Nos atalhos' : 'Adicionar atalho';
        }

        // Escuta mudanças disparadas por outras partes
        document.addEventListener('shortcut-changed', updatePinBtn);

        // Render inicial
        render();
    })();
    </script>


    <script>
        /* ── Global Custom Select ─────────────────────────────────────────────────── */
        (function () {
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
                trigger.tabIndex = 0;
                trigger.setAttribute('role', 'combobox');
                trigger.setAttribute('aria-expanded', 'false');

                const iconEl = document.createElement('span');
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
                    item.className = 'csel-option';
                    item.dataset.value = opt.value;

                    const color = opt.dataset.color || '';
                    const icon = opt.dataset.icon || '';

                    if (color) {
                        const dot = document.createElement('span');
                        dot.className = 'csel-option-dot';
                        dot.style.background = color;
                        dot.style.boxShadow = `0 0 5px ${color}66`;
                        item.appendChild(dot);
                    }
                    if (icon) {
                        const emojiEl = document.createElement('span');
                        emojiEl.className = 'csel-option-emoji';
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
                    iconEl.textContent = icon || '';
                    labelEl.textContent = text;
                    labelEl.style.color = isEmpty ? 'var(--muted)' : '';
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
                            t.setAttribute('aria-expanded', 'false');
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
                        const cur = dropdown.querySelector('.csel-option.selected');
                        const idx = Array.from(opts).indexOf(cur);
                        if (idx < opts.length - 1) opts[idx + 1].click();
                    }
                    if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        const opts = dropdown.querySelectorAll('.csel-option');
                        const cur = dropdown.querySelector('.csel-option.selected');
                        const idx = Array.from(opts).indexOf(cur);
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
                        item.className = 'csel-option';
                        item.dataset.value = opt.value;
                        const icon = opt.dataset.icon || '';
                        const color = opt.dataset.color || '';
                        if (color) {
                            const dot = document.createElement('span');
                            dot.className = 'csel-option-dot';
                            dot.style.background = color;
                            dot.style.boxShadow = `0 0 5px ${color}66`;
                            item.appendChild(dot);
                        }
                        if (icon) {
                            const emojiEl = document.createElement('span');
                            emojiEl.className = 'csel-option-emoji';
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
                    t.setAttribute('aria-expanded', 'false');
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
                    <svg width="18" height="18" viewBox="0 0 16 16" fill="none" stroke="var(--accent)"
                        stroke-width="1.5">
                        <rect x="1" y="3" width="14" height="10" rx="2" />
                        <rect x="2.5" y="5.5" width="2" height="1.5" rx=".4" />
                        <rect x="6" y="5.5" width="2" height="1.5" rx=".4" />
                        <rect x="9.5" y="5.5" width="2" height="1.5" rx=".4" />
                        <rect x="2.5" y="8.5" width="2" height="1.5" rx=".4" />
                        <rect x="6" y="8.5" width="4.5" height="1.5" rx=".4" />
                        <rect x="11.5" y="8.5" width="2" height="1.5" rx=".4" />
                    </svg>
                </div>
                <div class="kbd-header-text">
                    <h2>{{ __('app.nav_shortcuts') }}</h2>
                    <p>{{ __("app.layout_nav_faster") }}</p>
                </div>
            </div>

            <div class="kbd-search-wrap">
                <div class="kbd-search-wrap-inner">
                    <span class="kbd-search-icon">🔍</span>
                    <input id="kbd-search" type="text" placeholder="{{ __("app.layout_search_shortcut") }}"
                        autocomplete="off">
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
                        <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_dashboard") }}</span>
                            <div class="kbd-keys"><kbd>G</kbd><span class="kbd-plus">→</span><kbd>D</kbd></div>
                        </div>
                        <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_tasks") }}</span>
                            <div class="kbd-keys"><kbd>G</kbd><span class="kbd-plus">→</span><kbd>T</kbd></div>
                        </div>
                        <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_notes") }}</span>
                            <div class="kbd-keys"><kbd>G</kbd><span class="kbd-plus">→</span><kbd>N</kbd></div>
                        </div>
                        <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_categories") }}</span>
                            <div class="kbd-keys"><kbd>G</kbd><span class="kbd-plus">→</span><kbd>C</kbd></div>
                        </div>
                        <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_settings") }}</span>
                            <div class="kbd-keys"><kbd>G</kbd><span class="kbd-plus">→</span><kbd>S</kbd></div>
                        </div>
                    </div>
                </div>

                <div class="kbd-section" data-section="tasks">
                    <div class="kbd-section-title">{{ __("app.layout_kbd_global") }}</div>
                    <div class="kbd-grid">
                        <div class="kbd-row"><span class="kbd-desc">{{ __('app.nav_shortcuts') }}</span>
                            <div class="kbd-keys"><kbd>?</kbd></div>
                        </div>
                        <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_toggle_theme") }}</span>
                            <div class="kbd-keys"><kbd>Ctrl</kbd><span class="kbd-plus">+</span><kbd>⇧</kbd><span
                                    class="kbd-plus">+</span><kbd>L</kbd></div>
                        </div>
                        <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_close") }}</span>
                            <div class="kbd-keys"><kbd>Esc</kbd></div>
                        </div>
                        <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_new_task") }}</span>
                            <div class="kbd-keys"><kbd>C</kbd></div>
                        </div>
                        <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_search_tasks") }}</span>
                            <div class="kbd-keys"><kbd>/</kbd></div>
                        </div>
                        <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_filter_status") }}</span>
                            <div class="kbd-keys"><kbd>F</kbd></div>
                        </div>
                    </div>
                </div>

                <div class="kbd-section" data-section="notes">
                    <div class="kbd-section-title">{{ __("app.layout_kbd_notes_search") }}</div>
                    <div class="kbd-grid">
                        <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_new_note") }}</span>
                            <div class="kbd-keys"><kbd>C</kbd></div>
                        </div>
                        <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_focus_search") }}</span>
                            <div class="kbd-keys"><kbd>/</kbd></div>
                        </div>
                    </div>
                </div>

                <div class="kbd-section" data-section="editor">
                    <div class="kbd-section-title">{{ __("app.layout_kbd_editor") }}</div>
                    <div class="kbd-grid">
                        <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_save") }}</span>
                            <div class="kbd-keys"><kbd>Ctrl</kbd><span class="kbd-plus">+</span><kbd>S</kbd></div>
                        </div>
                        <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_bold") }}</span>
                            <div class="kbd-keys"><kbd>Ctrl</kbd><span class="kbd-plus">+</span><kbd>B</kbd></div>
                        </div>
                        <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_italic") }}</span>
                            <div class="kbd-keys"><kbd>Ctrl</kbd><span class="kbd-plus">+</span><kbd>I</kbd></div>
                        </div>
                        <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_underline") }}</span>
                            <div class="kbd-keys"><kbd>Ctrl</kbd><span class="kbd-plus">+</span><kbd>U</kbd></div>
                        </div>
                        <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_undo") }}</span>
                            <div class="kbd-keys"><kbd>Ctrl</kbd><span class="kbd-plus">+</span><kbd>Z</kbd></div>
                        </div>
                        <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_redo") }}</span>
                            <div class="kbd-keys"><kbd>Ctrl</kbd><span class="kbd-plus">+</span><kbd>Y</kbd></div>
                        </div>
                        <div class="kbd-row"><span class="kbd-desc">{!! __("app.layout_kbd_slash") !!}</span>
                            <div class="kbd-keys"><kbd>/</kbd></div>
                        </div>
                        <div class="kbd-row"><span class="kbd-desc">{{ __("app.layout_kbd_link") }}</span>
                            <div class="kbd-keys"><kbd>Ctrl</kbd><span class="kbd-plus">+</span><kbd>K</kbd></div>
                        </div>
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
            const overlay = document.getElementById('kbd-overlay');
            const btn = document.getElementById('btn-shortcuts');
            const closeBtn = document.getElementById('kbd-modal-close');
            const searchIn = document.getElementById('kbd-search');
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
                const tag = document.activeElement?.tagName;
                const typing = ['INPUT', 'TEXTAREA', 'SELECT'].includes(tag)
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
                    gTimer = setTimeout(() => { gBuffer = null; }, 1200);
                    return;
                }
            });
        })();
    </script>
    <script>
    // ── Minimizar para bandeja (botão X customizado) ─────────────────────────────
    function closeToTray() {
        fetch('/window/hide', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            keepalive: true
        });
    }

    // Fallback: Alt+F4 ou outros eventos de fechamento forçado
    if (window.__nativephp_electron) {
        window.addEventListener('beforeunload', function () {
            window.__nativephp_electron.hideWindow();
        });
    }

    // ── Sidebar Shortcuts Collapse ──────────────────────────────────────────
    (function() {
        const toggle = document.getElementById('shortcuts-toggle');
        const content = document.getElementById('sidebar-shortcuts-list');
        if (!toggle || !content) return;

        const arrow = toggle.querySelector('.collapse-arrow');
        let isCollapsed = localStorage.getItem('shortcuts-collapsed') === '1';

        function updateUI(instant) {
            if (instant) {
                content.style.transition = 'none';
                if (arrow) arrow.style.transition = 'none';
            }
            
            content.classList.toggle('collapsed', isCollapsed);
            if (arrow) arrow.classList.toggle('rotated', isCollapsed);

            if (instant) {
                requestAnimationFrame(() => {
                    content.style.transition = '';
                    if (arrow) arrow.style.transition = '';
                });
            }
        }

        updateUI(true);

        toggle.addEventListener('click', () => {
            isCollapsed = !isCollapsed;
            localStorage.setItem('shortcuts-collapsed', isCollapsed ? '1' : '0');
            updateUI(false);
        });
    })();
    </script>
</body>

</html>