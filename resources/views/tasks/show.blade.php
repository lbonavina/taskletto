@extends('layouts.app')
@section('title', $task->title)
@section('page-title', __('app.task_detail_title'))


@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* ── Task detail grid ──────────────────────────────────────────── */
        #task-detail-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 280px;
            gap: 20px;
            align-items: start;
        }
        #task-sidebar {
            display: flex;
            flex-direction: column;
            gap: 16px;
            position: sticky;
            top: 20px;
        }
        @media (max-width: 800px) {
            #task-detail-grid { grid-template-columns: 1fr; }
            #task-sidebar { position: static; }
        }

        /* ── Tiptap task editor ────────────────────────────────────────── */
        #task-editor-wrap {
            border: 1px solid var(--border);
            border-radius: 10px;
            overflow: hidden;
            transition: border-color .15s, box-shadow .15s;
        }
        #task-editor-wrap:focus-within {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(255, 145, 77, .1);
        }
        #task-editor-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 2px;
            padding: 8px 10px;
            background: var(--surface2);
            border-bottom: 1px solid var(--border);
        }
        .ttb-task-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border: none;
            border-radius: 5px;
            background: none;
            color: var(--muted);
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: background .12s, color .12s;
            font-family: inherit;
        }
        .ttb-task-btn:hover { background: rgba(255, 145, 77, .1); color: var(--text); }
        .ttb-task-btn.active { background: rgba(255, 145, 77, .15); color: var(--accent); }
        .ttb-task-sep {
            width: 1px;
            height: 20px;
            background: var(--border);
            margin: 4px 3px;
            align-self: center;
        }
        #task-tiptap-editor {
            background: var(--surface2);
            min-height: 160px;
            padding: 12px 14px;
            font-size: 14px;
            font-family: 'Montserrat', sans-serif;
            line-height: 1.7;
            color: var(--text);
            outline: none;
            cursor: text;
        }
        #task-tiptap-editor .tiptap { outline: none; min-height: 136px; }
        #task-tiptap-editor .tiptap p.is-editor-empty:first-child::before {
            content: attr(data-placeholder);
            color: var(--muted);
            font-size: 13.5px;
            pointer-events: none;
            float: left;
            height: 0;
        }
        #task-tiptap-editor .tiptap > * + * { margin-top: 4px; }
        #task-tiptap-editor .tiptap p { margin-bottom: 4px; }
        #task-tiptap-editor .tiptap h1,
        #task-tiptap-editor .tiptap h2,
        #task-tiptap-editor .tiptap h3 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            color: var(--text);
            line-height: 1.25;
            margin: 12px 0 4px;
            letter-spacing: -0.3px;
        }
        #task-tiptap-editor .tiptap h1 { font-size: 22px; letter-spacing: -0.4px; }
        #task-tiptap-editor .tiptap h2 { font-size: 18px; }
        #task-tiptap-editor .tiptap h3 { font-size: 15px; }
        #task-tiptap-editor .tiptap ul,
        #task-tiptap-editor .tiptap ol { padding-left: 20px; }
        #task-tiptap-editor .tiptap li { margin: 2px 0; }
        #task-tiptap-editor .tiptap blockquote {
            border-left: 3px solid var(--accent);
            padding-left: 12px;
            color: var(--muted);
            margin: 8px 0;
        }
        #task-tiptap-editor .tiptap code {
            background: rgba(0,0,0,.3);
            border-radius: 4px;
            font-family: 'Montserrat', sans-serif;
            font-size: 12.5px;
            color: var(--accent);
            padding: 1px 5px;
        }
        #task-tiptap-editor .tiptap pre {
            background: rgba(0,0,0,.3);
            border-radius: 8px;
            padding: 10px 14px;
            font-family: 'Montserrat', sans-serif;
            font-size: 12.5px;
            color: var(--accent);
            margin: 6px 0;
        }
        #task-tiptap-editor .tiptap pre code { background: none; padding: 0; font-size: inherit; }
        #task-tiptap-editor .tiptap strong { font-weight: 600; }
        #task-tiptap-editor .tiptap em { font-style: italic; }
        #task-tiptap-editor .tiptap s { text-decoration: line-through; }
        #task-tiptap-editor .tiptap a { color: var(--accent); text-decoration: underline; text-underline-offset: 2px; }
        html[data-theme=light] #task-tiptap-editor { background: #ffffff; color: #18181c; }
        html[data-theme=light] #task-tiptap-editor .tiptap code,
        html[data-theme=light] #task-tiptap-editor .tiptap pre { background: rgba(0,0,0,.06); }

        /* ── Markdown body ─────────────────────────────────────────────── */
        .md-body { font-size: 13px; line-height: 1.65; color: var(--text); word-break: break-word; }
        .md-body p { margin: 0 0 6px; }
        .md-body p:last-child { margin-bottom: 0; }
        .md-body h1, .md-body h2, .md-body h3 { font-family: 'Montserrat', sans-serif; font-weight: 700; margin: 10px 0 4px; letter-spacing: -.2px; }
        .md-body h1 { font-size: 18px; }
        .md-body h2 { font-size: 15px; }
        .md-body h3 { font-size: 13.5px; }
        .md-body ul, .md-body ol { padding-left: 20px; margin: 4px 0; }
        .md-body li { margin: 2px 0; }
        .md-body code { background: rgba(0,0,0,.25); border-radius: 4px; font-family: 'Montserrat', sans-serif; font-size: 12px; color: var(--accent); padding: 1px 5px; }
        .md-body pre { background: rgba(0,0,0,.25); border-radius: 8px; padding: 10px 14px; margin: 6px 0; overflow-x: auto; }
        .md-body pre code { background: none; padding: 0; }
        .md-body blockquote { border-left: 3px solid var(--accent); padding-left: 10px; color: var(--muted); margin: 6px 0; }
        .md-body strong { font-weight: 600; }
        .md-body em { font-style: italic; }
        .md-body a { color: var(--accent); text-decoration: underline; text-underline-offset: 2px; }
        html[data-theme=light] .md-body code,
        html[data-theme=light] .md-body pre { background: rgba(0,0,0,.07); }

        /* ── Comment edit textarea ─────────────────────────────────────── */
        .comment-edit-textarea {
            width: 100%; resize: vertical; min-height: 60px;
            background: var(--surface2); border: 1px solid var(--accent);
            border-radius: 8px; padding: 8px 10px; font-size: 13px;
            font-family: inherit; color: var(--text); line-height: 1.55;
            outline: none; box-sizing: border-box;
            box-shadow: 0 0 0 3px rgba(255,145,77,.1);
        }

        /* ── Sidebar prop inputs ───────────────────────────────────────── */
        .prop-date-input {
            width: 100%;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 7px 10px;
            font-size: 12px;
            color: var(--text);
            font-family: inherit;
            outline: none;
            transition: border-color .15s;
            box-sizing: border-box;
            color-scheme: dark;
        }
        .prop-date-input:focus { border-color: var(--accent); }
        .prop-date-input::-webkit-calendar-picker-indicator {
            opacity: .4;
            cursor: pointer;
            filter: invert(1);
        }
        .prop-date-input::-webkit-calendar-picker-indicator:hover { opacity: .8; }
        html[data-theme=light] .prop-date-input { color-scheme: light; }
        html[data-theme=light] .prop-date-input::-webkit-calendar-picker-indicator { filter: none; }

        /* ── Reminder Flatpickr input hover ───────────────────────────── */
        #reminder-fp:hover { border-color: rgba(255,145,77,.4); }
        #reminder-fp:focus, #reminder-fp.active { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(255,145,77,.1); }

        /* ── Flatpickr custom theme ────────────────────────────────────── */
        /* Não sobrescrevemos width/max-width — flatpickr calcula em JS   */
        .flatpickr-calendar {
            background: var(--surface) !important;
            border: 1px solid var(--border) !important;
            border-radius: 12px !important;
            box-shadow: 0 8px 32px rgba(0,0,0,.4) !important;
            font-family: 'Montserrat', sans-serif !important;
        }
        .flatpickr-months { padding: 6px 4px 2px; }
        .flatpickr-month { background: transparent !important; color: var(--text) !important; }
        .flatpickr-current-month { font-size: 13px !important; font-weight: 700 !important; color: var(--text) !important; }
        .flatpickr-current-month .cur-month { font-weight: 700 !important; color: var(--text) !important; }
        .flatpickr-current-month input.cur-year { color: var(--text) !important; font-weight: 700 !important; }
        .flatpickr-prev-month, .flatpickr-next-month { fill: var(--muted) !important; padding: 6px 8px !important; top: 8px !important; }
        .flatpickr-prev-month:hover svg, .flatpickr-next-month:hover svg { fill: var(--accent) !important; }
        .flatpickr-weekdays { background: transparent !important; }
        .flatpickr-weekday { background: transparent !important; color: var(--muted) !important; font-size: 10px !important; font-weight: 700 !important; text-transform: uppercase; letter-spacing: .4px; }
        .flatpickr-days { border: none !important; }
        .flatpickr-day {
            background: transparent !important;
            border: none !important;
            border-radius: 8px !important;
            color: var(--text) !important;
            font-size: 12px !important;
            font-weight: 500 !important;
            transition: background .15s, color .15s;
        }
        .flatpickr-day:hover { background: var(--surface2) !important; color: var(--text) !important; }
        .flatpickr-day.today { border: 1px solid rgba(255,145,77,.4) !important; color: var(--accent) !important; font-weight: 700 !important; }
        .flatpickr-day.today:hover { background: rgba(255,145,77,.1) !important; }
        .flatpickr-day.selected, .flatpickr-day.selected:hover {
            background: var(--accent) !important;
            border-color: var(--accent) !important;
            color: #fff !important;
            font-weight: 700 !important;
            box-shadow: 0 2px 8px rgba(255,145,77,.3) !important;
        }
        .flatpickr-day.prevMonthDay, .flatpickr-day.nextMonthDay { color: var(--muted) !important; opacity: .35; }
        .flatpickr-day.flatpickr-disabled { opacity: .2 !important; }
        .flatpickr-time { border-top: 1px solid var(--border) !important; background: transparent !important; }
        .flatpickr-time input { background: transparent !important; color: var(--text) !important; font-family: 'Montserrat', sans-serif !important; font-weight: 700 !important; font-size: 15px !important; border: none !important; }
        .flatpickr-time input:hover, .flatpickr-time input:focus { background: var(--surface2) !important; border-radius: 6px; }
        .flatpickr-time .flatpickr-time-separator { color: var(--muted) !important; font-weight: 700; }
        .flatpickr-time .numInputWrapper span { border: none !important; }
        .flatpickr-time .numInputWrapper span:hover { background: rgba(255,145,77,.1) !important; }
        .numInputWrapper span.arrowUp:after { border-bottom-color: var(--muted) !important; }
        .numInputWrapper span.arrowDown:after { border-top-color: var(--muted) !important; }
        html[data-theme=light] .flatpickr-calendar { box-shadow: 0 4px 20px rgba(0,0,0,.1) !important; }

        /* ── Inline title ─────────────────────────────────────────────── */
        #inline-title:hover { background: var(--surface2); }
        #inline-title:focus { background: var(--surface2); box-shadow: 0 0 0 2px rgba(255,145,77,.15); }
        #inline-title:empty::before { content: attr(placeholder); color: var(--muted); opacity: .5; }

        /* ── Meta chips (created/updated below title) ─────────────────── */
        .task-meta-chip {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 10.5px; color: var(--muted);
            font-family: 'Montserrat', sans-serif;
        }

        /* ── Estimate fields ──────────────────────────────────────────── */
        .est-input-box {
            flex: 1; display: flex; align-items: center; gap: 10px;
            background: var(--surface2); border: 1px solid var(--border);
            border-radius: 12px; padding: 13px 16px;
            transition: border-color .2s, box-shadow .2s; cursor: text;
        }
        .est-input-box:hover { border-color: rgba(255,145,77,.35); }
        .est-input-box:focus-within {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(255,145,77,.08);
        }
        .est-field-input {
            background: transparent; border: none; outline: none;
            font-size: 26px; font-family: 'Montserrat', sans-serif;
            font-weight: 800; color: var(--text);
            flex: 1; padding: 0; line-height: 1; min-width: 0;
            text-align: center;
            -moz-appearance: textfield; appearance: textfield;
        }
        .est-field-input::-webkit-outer-spin-button,
        .est-field-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        .est-field-input::placeholder { color: var(--muted); opacity: .4; }
        .est-unit {
            font-size: 13px; font-weight: 700; color: var(--muted);
            font-family: 'Montserrat', sans-serif; user-select: none; flex-shrink: 0;
        }

        /* ── Subtasks ─────────────────────────────────────────────────── */
        .subtasks-header {
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
        }
        .subtasks-progress-wrap {
            display: flex; align-items: center; gap: 8px; flex: 1; max-width: 200px;
        }
        .subtasks-progress-bar {
            flex: 1; height: 4px; background: var(--surface2);
            border-radius: 99px; overflow: hidden;
        }
        .subtasks-progress-fill {
            height: 100%; border-radius: 99px;
            background: var(--success);
            transition: width .4s cubic-bezier(.34,1.2,.64,1);
            width: 0%;
        }
        .subtasks-progress-label {
            font-size: 10px; font-family: 'Montserrat', sans-serif;
            color: var(--muted); white-space: nowrap; flex-shrink: 0;
        }

        .subtask-item {
            display: flex; align-items: center; gap: 10px;
            padding: 7px 10px; border-radius: 8px;
            transition: background .12s;
            group: true;
        }
        .subtask-item:hover { background: var(--surface2); }
        .subtask-item:hover .subtask-delete { opacity: 1; }

        .subtask-check {
            appearance: none; -webkit-appearance: none;
            width: 16px; height: 16px; flex-shrink: 0;
            border: 1.5px solid var(--border); border-radius: 4px;
            background: var(--surface2); cursor: pointer; position: relative;
            transition: border-color .15s, background .15s;
        }
        .subtask-check:hover { border-color: var(--success); }
        .subtask-check:checked {
            background: var(--success); border-color: var(--success);
        }
        .subtask-check:checked::after {
            content: ''; position: absolute;
            left: 4px; top: 1px; width: 5px; height: 9px;
            border: 2px solid var(--bg); border-top: none; border-left: none;
            transform: rotate(45deg);
        }

        .subtask-title {
            flex: 1; font-size: 13px; color: var(--text);
            cursor: text; border-radius: 4px; padding: 1px 4px;
            transition: background .12s; outline: none;
            line-height: 1.5;
        }
        .subtask-title:focus { background: var(--surface2); }
        .subtask-title.done {
            color: var(--muted); text-decoration: line-through; opacity: .6;
        }

        .subtask-delete {
            width: 22px; height: 22px; border-radius: 5px;
            background: none; border: none; cursor: pointer;
            color: var(--muted); opacity: 0;
            display: flex; align-items: center; justify-content: center;
            transition: color .12s, background .12s, opacity .15s;
            flex-shrink: 0;
        }
        .subtask-delete:hover { color: var(--danger); background: rgba(224,84,84,.1); }

        .subtask-add-wrap { margin-top: 8px; }
        .subtask-add-trigger {
            display: flex; align-items: center; gap: 7px;
            background: none; border: none; cursor: pointer;
            color: var(--muted); font-size: 12px; font-weight: 500;
            font-family: inherit; padding: 5px 6px; border-radius: 7px;
            transition: color .15s, background .15s;
        }
        .subtask-add-trigger:hover { color: var(--accent); background: rgba(255,145,77,.06); }
        .subtask-add-trigger svg { opacity: .5; transition: opacity .15s, transform .2s; }
        .subtask-add-trigger:hover svg { opacity: 1; transform: rotate(90deg); }

        .subtask-add-form {
            display: flex; align-items: center; gap: 8px;
            padding: 4px 2px; animation: pageEnter .15s ease both;
        }
        .subtask-add-input {
            flex: 1; background: var(--surface2); border: 1px solid var(--border);
            border-radius: 8px; color: var(--text); font-size: 12.5px;
            font-family: inherit; padding: 7px 12px; outline: none;
            transition: border-color .15s, box-shadow .15s;
        }
        .subtask-add-input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(255,145,77,.1);
        }
        .subtask-add-input::placeholder { color: var(--muted); opacity: .5; }
        .subtask-add-btn {
            width: 32px; height: 32px; flex-shrink: 0;
            border-radius: 8px; border: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            background: var(--accent); color: #0c0c0e;
            transition: transform .15s, box-shadow .15s, opacity .15s;
            opacity: .4; pointer-events: none;
        }
        .subtask-add-btn.active { opacity: 1; pointer-events: all; }
        .subtask-add-btn:hover { transform: scale(1.08); box-shadow: 0 2px 8px rgba(255,145,77,.35); }
        .subtask-add-esc {
            font-size: 10px; color: var(--muted); opacity: .45;
            white-space: nowrap; user-select: none;
        }
    </style>
@endpush

@section('content')
    <div style="margin-bottom: 20px; display: flex; gap: 8px;">
        <a href="/tasks" class="btn btn-ghost btn-sm" style="color:var(--muted)">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 12L6 8l4-4"/></svg>
            {{ __('app.task_back') }}
        </a>
        <button
            id="btn-add-shortcut"
            class="btn btn-ghost btn-sm"
            data-url="/tasks/{{ $task->id }}"
            data-label="{{ addslashes($task->title) }}"
            data-type="task"
            data-emoji="📋"
            title="Adicionar/remover dos atalhos"
            style="color:var(--muted)">
            <span class="pin-star" style="font-size:14px;line-height:1">☆</span>
            <span class="pin-label">Adicionar atalho</span>
        </button>
    </div>
    {{-- ── Main grid: left column + sidebar ─────────────────────────── --}}
    <div id="task-detail-grid">

        {{-- Left column --}}
        <div>

            {{-- Unified header + edit card --}}
            <div class="card" style="margin-bottom:16px">

                {{-- Title row: inline editable + actions --}}
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:14px">
                    <div style="flex:1;min-width:0">
                        <h2
                            id="inline-title"
                            contenteditable="true"
                            spellcheck="false"
                            style="font-family:'Montserrat',sans-serif;font-size:21px;font-weight:700;letter-spacing:-0.2px;line-height:1.3;margin-bottom:10px;outline:none;border-radius:6px;padding:2px 6px;margin-left:-6px;transition:background .15s;cursor:text;"
                            title="Clique para editar o título"
                        >{{ $task->title }}</h2>
                        <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;padding-left:2px">
                            <span class="badge status-{{ $task->status->value }}">
                                <span class="badge-dot" style="background:var(--status-{{ $task->status->value }})"></span>
                                {{ $task->status->label() }}
                            </span>
                            <span class="badge priority-{{ $task->priority->value }}">{{ $task->priority->label() }}</span>
                            @if($task->isOverdue())
                                <span class="badge" style="background:rgba(224,84,84,.12);color:var(--danger)">⚠ Atrasada</span>
                            @endif
                        </div>

                        {{-- Meta info inline — compact, below badges --}}
                        <div style="display:flex;align-items:center;gap:14px;margin-top:10px;flex-wrap:wrap">
                            <span class="task-meta-chip">
                                <svg width="10" height="10" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="8" cy="8" r="6.5"/><path d="M8 4.5V8l2.5 2"/></svg>
                                Criada {{ $task->created_at->diffForHumans() }}
                            </span>
                            <span class="task-meta-chip">
                                <svg width="10" height="10" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M11.5 2.5a1.5 1.5 0 012.12 2.12L5 13.24 2 14l.76-3L11.5 2.5z"/></svg>
                                Atualizada {{ $task->updated_at->diffForHumans() }}
                            </span>
                            @if($task->completed_at)
                            <span class="task-meta-chip" style="color:var(--success)">
                                <svg width="10" height="10" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 8l4 4 8-8"/></svg>
                                Concluída {{ $task->completed_at->diffForHumans() }}
                            </span>
                            @endif
                        </div>
                    </div>
                    <div style="display:flex;gap:6px;flex-shrink:0">
                        @if(!$task->isCompleted())
                            <button class="btn btn-primary btn-sm" id="btn-complete">{{ __('app.task_complete_btn') }}</button>
                        @else
                            <button class="btn btn-ghost btn-sm" id="btn-reopen">{{ __('app.task_reopen_btn') }}</button>
                        @endif
                        <button class="btn btn-danger btn-sm" id="btn-delete">{{ __('app.task_delete_btn') }}</button>
                    </div>
                </div>

                <div id="edit-alert" style="display:none" class="alert"></div>

                {{-- Divider --}}
                <div style="height:1px;background:var(--border);margin:0 -20px 18px"></div>

                {{-- Description --}}
                <div class="form-group" style="margin-bottom:0">
                    <label>{{ __('app.task_label_description') }}</label>
                    <div id="task-editor-wrap">
                        <div id="task-editor-toolbar">
                            <button class="ttb-task-btn" data-cmd="bold" title="Negrito (Ctrl+B)"><b>B</b></button>
                            <button class="ttb-task-btn" data-cmd="italic" title="Itálico (Ctrl+I)"><i>I</i></button>
                            <button class="ttb-task-btn" data-cmd="underline" title="Sublinhado" style="text-decoration:underline">U</button>
                            <button class="ttb-task-btn" data-cmd="strike" title="Tachado" style="text-decoration:line-through">S</button>
                            <div class="ttb-task-sep"></div>
                            <button class="ttb-task-btn" data-cmd="h1" title="Título 1" style="font-size:11px">H1</button>
                            <button class="ttb-task-btn" data-cmd="h2" title="Título 2" style="font-size:11px">H2</button>
                            <button class="ttb-task-btn" data-cmd="h3" title="Título 3" style="font-size:11px">H3</button>
                            <div class="ttb-task-sep"></div>
                            <button class="ttb-task-btn" data-cmd="bulletList" title="Lista com marcadores">•≡</button>
                            <button class="ttb-task-btn" data-cmd="orderedList" title="Lista numerada">1≡</button>
                            <div class="ttb-task-sep"></div>
                            <button class="ttb-task-btn" data-cmd="blockquote" title="Citação">"</button>
                            <button class="ttb-task-btn" data-cmd="codeBlock" title="Bloco de código" style="font-family:monospace;font-size:11px">&lt;/&gt;</button>
                            <div class="ttb-task-sep"></div>
                            <button class="ttb-task-btn" data-cmd="undo" title="Desfazer (Ctrl+Z)">↩</button>
                            <button class="ttb-task-btn" data-cmd="redo" title="Refazer">↪</button>
                        </div>
                        <div id="task-tiptap-editor"></div>
                    </div>
                </div>

                <div style="text-align:right;margin-top:16px">
                    <button class="btn btn-primary" id="btn-save-edit">{{ __('app.task_save_changes') }}</button>
                </div>

            </div>{{-- /unified card --}}

            {{-- Subtasks card --}}
            <div class="card" id="subtasks-card" style="margin-bottom:16px">
                <div class="subtasks-header">
                    <div class="section-title" style="margin-bottom:0">
                        <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 4h12M2 8h8M2 12h6"/></svg>
                        Subtarefas
                    </div>
                    <div class="subtasks-progress-wrap" id="subtasks-progress-wrap" style="display:none">
                        <div class="subtasks-progress-bar">
                            <div class="subtasks-progress-fill" id="subtasks-progress-fill"></div>
                        </div>
                        <span class="subtasks-progress-label" id="subtasks-progress-label"></span>
                    </div>
                </div>

                <div id="subtasks-list" style="margin-top:12px;display:flex;flex-direction:column;gap:2px"></div>

                <div class="subtask-add-wrap" id="subtask-add-wrap">
                    <button class="subtask-add-trigger" id="subtask-add-trigger">
                        <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 2v12M2 8h12"/></svg>
                        Adicionar subtarefa
                    </button>
                    <div class="subtask-add-form" id="subtask-add-form" style="display:none">
                        <input type="text" class="subtask-add-input" id="subtask-add-input" placeholder="Nome da subtarefa…" maxlength="255" autocomplete="off">
                        <button class="subtask-add-btn" id="subtask-add-submit" title="Adicionar (Enter)">
                            <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M8 2v12M2 8h12"/></svg>
                        </button>
                        <span class="subtask-add-esc">Esc para cancelar</span>
                    </div>
                </div>
            </div>{{-- /subtasks card --}}

            {{-- Comments card --}}
            <div class="card" id="comments-card">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
                    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:var(--muted)">
                        {{ __('app.task_comments_label') }}
                        <span id="comment-count" style="margin-left:6px;background:var(--surface2);color:var(--muted);border-radius:20px;padding:1px 8px;font-size:10px;font-weight:700">{{ $task->comments()->count() }}</span>
                    </div>
                    <span style="font-size:11px;color:var(--muted)">{{ __('app.task_supports_md') }}</span>
                </div>
                <div id="comment-list" style="display:flex;flex-direction:column;gap:0"></div>
                <div id="comments-load-more-wrap" style="display:none;text-align:center;padding:10px 0">
                    <button id="btn-load-more" class="btn btn-ghost btn-sm">{{ __('app.task_load_more') }}</button>
                </div>
                <div style="margin-top:14px;display:flex;flex-direction:column;gap:8px">
                    <div style="display:flex;gap:0;border-bottom:1px solid var(--border);margin-bottom:4px">
                        <button id="tab-write" style="background:none;border:none;padding:5px 14px;font-size:12px;font-weight:600;color:var(--accent);border-bottom:2px solid var(--accent);cursor:pointer;margin-bottom:-1px;font-family:inherit">{{ __('app.task_tab_write') }}</button>
                        <button id="tab-preview" style="background:none;border:none;padding:5px 14px;font-size:12px;font-weight:600;color:var(--muted);border-bottom:2px solid transparent;cursor:pointer;margin-bottom:-1px;font-family:inherit">{{ __('app.task_tab_preview') }}</button>
                    </div>
                    <textarea id="comment-body" rows="3" placeholder="{{ __('app.task_comment_ph') }}" style="width:100%;resize:vertical;min-height:72px;background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:10px 12px;font-size:13.5px;font-family:inherit;color:var(--text);line-height:1.55;transition:border-color .15s,box-shadow .15s;outline:none;box-sizing:border-box" onfocus="this.style.borderColor='var(--accent)';this.style.boxShadow='0 0 0 3px rgba(255,145,77,.1)'" onblur="this.style.borderColor='var(--border)';this.style.boxShadow='none'"></textarea>
                    <div id="comment-preview" style="display:none;min-height:72px;background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:10px 12px;font-size:13.5px;color:var(--text);line-height:1.55" class="md-body"></div>
                    <div style="display:flex;align-items:center;justify-content:space-between">
                        <span id="comment-char-count" style="font-size:11px;color:var(--muted);font-family:'Montserrat',sans-serif">0 / 2000</span>
                        <button id="btn-add-comment" class="btn btn-primary btn-sm">{{ __('app.task_comment_btn') }}</button>
                    </div>
                </div>
            </div>{{-- /comments card --}}

            {{-- History trigger --}}
            @if($task->histories && $task->histories->count())
            <div style="text-align:center;padding:8px 0">
                <button id="btn-open-history" style="background:none;border:none;cursor:pointer;font-size:11px;color:var(--muted);font-family:inherit;display:inline-flex;align-items:center;gap:5px;padding:6px 10px;border-radius:6px;transition:color .15s,background .15s"
                    onmouseover="this.style.color='var(--text)';this.style.background='var(--surface2)'"
                    onmouseout="this.style.color='var(--muted)';this.style.background='none'">
                    <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="8" cy="8" r="6.5"/><path d="M8 4.5V8l2.5 2"/></svg>
                    {{ __('app.task_view_history') }} ({{ $task->histories->count() }})
                </button>
            </div>
            @endif

        </div>{{-- /left column --}}

        {{-- Right sidebar --}}
        <div id="task-sidebar">

            {{-- Properties card --}}
            <div class="card" style="font-size:13px">
                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:var(--muted);margin-bottom:14px">{{ __('app.task_properties') }}</div>
                <div style="display:flex;flex-direction:column;gap:12px">

                    <div>
                        <div style="font-size:11px;color:var(--muted);margin-bottom:4px">{{ __('app.task_label_status') }}</div>
                        <div class="select-wrap">
                            <select id="sidebar-status" style="font-size:12px">
                                <option value="pending"     {{ $task->status->value === 'pending'     ? 'selected' : '' }}>{{ __('app.status_pending') }}</option>
                                <option value="in_progress" {{ $task->status->value === 'in_progress' ? 'selected' : '' }}>{{ __('app.status_in_progress') }}</option>
                                <option value="completed"   {{ $task->status->value === 'completed'   ? 'selected' : '' }}>{{ __('app.status_completed') }}</option>
                                <option value="cancelled"   {{ $task->status->value === 'cancelled'   ? 'selected' : '' }}>{{ __('app.status_cancelled') }}</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <div style="font-size:11px;color:var(--muted);margin-bottom:4px">{{ __('app.task_label_priority') }}</div>
                        <div class="select-wrap">
                            <select id="sidebar-priority" style="font-size:12px">
                                <option value="low"    {{ $task->priority->value === 'low'    ? 'selected' : '' }}>{{ __('app.priority_low') }}</option>
                                <option value="medium" {{ $task->priority->value === 'medium' ? 'selected' : '' }}>{{ __('app.priority_medium') }}</option>
                                <option value="high"   {{ $task->priority->value === 'high'   ? 'selected' : '' }}>{{ __('app.priority_high') }}</option>
                                <option value="urgent" {{ $task->priority->value === 'urgent' ? 'selected' : '' }}>{{ __('app.priority_urgent') }}</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <div style="font-size:11px;color:var(--muted);margin-bottom:4px">{{ __('app.task_label_due') }}</div>
                        <div style="position:relative">
                            <input type="text" id="sidebar-due-date" readonly
                                   value="{{ $task->due_date?->format('d/m/Y') }}"
                                   data-date="{{ $task->due_date?->format('Y-m-d') }}"
                                   placeholder="Selecionar data…"
                                   class="prop-date-input" style="cursor:pointer;padding-right:30px">
                            <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="var(--muted)" stroke-width="1.6" style="position:absolute;right:9px;top:50%;transform:translateY(-50%);pointer-events:none"><rect x="2" y="3" width="12" height="11" rx="2"/><path d="M5 1v3M11 1v3M2 7h12"/></svg>
                        </div>
                    </div>

                    <div>
                        <div style="font-size:11px;color:var(--muted);margin-bottom:4px">{{ __('app.task_label_category') }}</div>
                        <div class="select-wrap">
                            <select id="sidebar-category" style="font-size:12px">
                                <option value="">— Sem categoria —</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $task->category_id === $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <div style="font-size:11px;color:var(--muted);margin-bottom:4px">{{ __('app.task_recurrence') }}</div>
                        <div class="select-wrap">
                            <select id="sidebar-recurrence" style="font-size:12px">
                                <option value="none"    {{ $task->recurrence->value === 'none'    ? 'selected' : '' }}>{{ __('app.task_recurrence_none') }}</option>
                                <option value="daily"   {{ $task->recurrence->value === 'daily'   ? 'selected' : '' }}>{{ __('app.task_recurrence_daily') }}</option>
                                <option value="weekly"  {{ $task->recurrence->value === 'weekly'  ? 'selected' : '' }}>{{ __('app.task_recurrence_weekly') }}</option>
                                <option value="monthly" {{ $task->recurrence->value === 'monthly' ? 'selected' : '' }}>{{ __('app.task_recurrence_monthly') }}</option>
                            </select>
                        </div>
                    </div>

                    <div id="sidebar-recurrence-ends-wrap" style="{{ $task->recurrence->value === 'none' ? 'display:none' : '' }}">
                        <div style="font-size:11px;color:var(--muted);margin-bottom:4px">{{ __('app.task_recurrence_ends') }}</div>
                        <div style="position:relative">
                            <input type="text" id="sidebar-recurrence-ends" readonly
                                   value="{{ $task->recurrence_ends_at?->format('d/m/Y') }}"
                                   data-date="{{ $task->recurrence_ends_at?->format('Y-m-d') }}"
                                   placeholder="Selecionar data…"
                                   class="prop-date-input" style="cursor:pointer;padding-right:30px">
                            <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="var(--muted)" stroke-width="1.6" style="position:absolute;right:9px;top:50%;transform:translateY(-50%);pointer-events:none"><rect x="2" y="3" width="12" height="11" rx="2"/><path d="M5 1v3M11 1v3M2 7h12"/></svg>
                        </div>
                    </div>

                    <div id="props-saved" style="display:none;font-size:11px;color:var(--success);text-align:right">{{ __('app.task_saved_inline') }}</div>

                </div>
            </div>{{-- /properties card --}}

            {{-- Custom Reminder card --}}
            <div class="card" id="reminder-card">
                <div class="section-title" style="margin-bottom:16px">
                    <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M8 1.5v2M3.5 13.5h9M8 13.5v-1.5M4.5 5.5a3.5 3.5 0 017 0v3.5l1.5 1.5v1h-10v-1l1.5-1.5V5.5z"/></svg>
                    Lembrete
                </div>

                {{-- Chip: lembrete ativo --}}
                <div id="reminder-chip" style="display:{{ $task->reminder_at ? 'flex' : 'none' }};align-items:center;justify-content:space-between;background:rgba(255,145,77,.08);border:1px solid rgba(255,145,77,.2);border-radius:10px;padding:9px 11px;margin-bottom:12px">
                    <div style="display:flex;align-items:center;gap:7px">
                        <svg width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="var(--accent)" stroke-width="1.8" style="flex-shrink:0"><path d="M8 1.5v2M3.5 13.5h9M8 13.5v-1.5M4.5 5.5a3.5 3.5 0 017 0v3.5l1.5 1.5v1h-10v-1l1.5-1.5V5.5z"/></svg>
                        <span id="reminder-chip-text" style="font-size:12px;font-weight:600;color:var(--accent)">
                            @if($task->reminder_at){{ $task->reminder_at->format('d/m/Y \à\s H:i') }}@endif
                        </span>
                    </div>
                    <button id="btn-clear-reminder" style="background:none;border:none;cursor:pointer;color:var(--muted);padding:3px 5px;border-radius:5px;line-height:1;transition:color .15s,background .15s" title="Remover lembrete" onmouseover="this.style.color='var(--danger)';this.style.background='rgba(224,84,84,.1)'" onmouseout="this.style.color='var(--muted)';this.style.background='none'">
                        <svg width="10" height="10" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 3l10 10M13 3L3 13"/></svg>
                    </button>
                </div>

                {{-- Input Flatpickr --}}
                <div style="position:relative;margin-bottom:0">
                    <input id="reminder-fp"
                           type="text"
                           placeholder="{{ $task->reminder_at ? '' : 'Selecionar data e hora…' }}"
                           value="{{ $task->reminder_at?->format('Y-m-d H:i') }}"
                           readonly
                           style="width:100%;background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:10px 36px 10px 12px;font-size:13px;font-family:inherit;color:var(--text);outline:none;cursor:pointer;box-sizing:border-box;transition:border-color .2s">
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="var(--muted)" stroke-width="1.6" style="position:absolute;right:11px;top:50%;transform:translateY(-50%);pointer-events:none"><path d="M8 1.5v2M3.5 13.5h9M8 13.5v-1.5M4.5 5.5a3.5 3.5 0 017 0v3.5l1.5 1.5v1h-10v-1l1.5-1.5V5.5z"/></svg>
                </div>
            </div>

            {{-- Estimativa card --}}
            <div class="card" id="estimate-card">
                @php
                    $pct      = $task->estimated_minutes ? min(100, round(($task->tracked_seconds / ($task->estimated_minutes * 60)) * 100)) : 0;
                    $barColor = $pct >= 100 ? 'var(--danger)' : 'var(--accent)';
                    $estH     = $task->estimated_minutes ? intdiv($task->estimated_minutes, 60) : 0;
                    $estM     = $task->estimated_minutes ? $task->estimated_minutes % 60 : 0;
                @endphp

                <div class="section-title" style="margin-bottom:16px">
                    <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="8" cy="8" r="6.5"/><path d="M8 4.5V8l2.5 2"/></svg>
                    Estimativa
                </div>

                {{-- Stats surface --}}
                <div style="background:var(--surface2);border-radius:12px;padding:14px 16px;margin-bottom:16px;border:1px solid var(--border)">
                    <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:14px">
                        <div>
                            <div style="font-size:9px;text-transform:uppercase;letter-spacing:1.2px;color:var(--muted);font-weight:700;margin-bottom:6px">Registrado</div>
                            <div id="tracked-display" style="font-size:28px;font-family:'Montserrat',sans-serif;font-weight:800;color:var(--text);letter-spacing:-1.5px;line-height:1">{{ $task->formattedTrackedTime() }}</div>
                        </div>
                        @if($task->estimated_minutes)
                        <div id="est-summary" style="text-align:right">
                            <div style="font-size:9px;text-transform:uppercase;letter-spacing:1.2px;color:var(--muted);font-weight:700;margin-bottom:6px">Meta</div>
                            <div style="font-size:16px;font-weight:800;color:var(--text);line-height:1;font-family:'Montserrat',sans-serif;letter-spacing:-.5px">{{ $estH > 0 ? $estH.'h' : '' }}{{ $estM > 0 ? ' '.$estM.'m' : '' }}</div>
                        </div>
                        @else
                        <div id="est-summary" style="font-size:11px;color:var(--muted);padding-bottom:2px">Sem meta</div>
                        @endif
                    </div>

                    {{-- Progress bar + percentual --}}
                    <div style="display:flex;align-items:center;gap:10px">
                        <div style="flex:1;background:var(--border);border-radius:99px;height:5px;overflow:hidden">
                            <div id="time-progress-bar" style="height:100%;width:{{ $pct }}%;background:{{ $barColor }};border-radius:99px;transition:width .6s cubic-bezier(.34,1.5,.64,1)"></div>
                        </div>
                        <span style="font-size:10px;font-weight:700;font-family:'Montserrat',sans-serif;color:{{ $pct >= 100 ? 'var(--danger)' : ($pct > 0 ? 'var(--accent)' : 'var(--muted)') }};min-width:28px;text-align:right">{{ $pct > 0 ? $pct.'%' : '—' }}</span>
                    </div>
                </div>

                {{-- Label definir meta --}}
                <div style="font-size:9px;text-transform:uppercase;letter-spacing:1.2px;color:var(--muted);font-weight:700;margin-bottom:8px">Definir meta</div>

                {{-- Inputs h/m --}}
                <div style="display:flex;gap:8px;margin-bottom:12px">
                    <div id="est-h-wrap" class="est-input-box" onclick="document.getElementById('est-h').focus()">
                        <input id="est-h" type="number" min="0" max="99" step="1" value="{{ $estH ?: '' }}" placeholder="0" class="est-field-input">
                        <span class="est-unit">h</span>
                    </div>
                    <div id="est-m-wrap" class="est-input-box" onclick="document.getElementById('est-m').focus()">
                        <input id="est-m" type="number" min="0" max="59" step="5" value="{{ $estM ?: '' }}" placeholder="0" class="est-field-input">
                        <span class="est-unit">m</span>
                    </div>
                </div>

                <button id="btn-save-estimate" class="btn btn-ghost" style="width:100%;justify-content:center;gap:6px;font-size:12px">
                    <svg width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 8l4 4 8-8"/></svg>
                    {{ __('app.task_save_estimate') }}
                </button>
            </div>{{-- /estimate-card --}}

            {{-- Timer card --}}
            @if(!$task->isCompleted())
            <div class="card" id="time-card">
                <div class="section-title" style="margin-bottom:12px">
                    <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M8 1v3M5 2.2l1.5 2.6M11 2.2L9.5 4.8M14 8a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
                    Timer
                </div>

                <button id="btn-timer" class="btn btn-ghost" style="width:100%;justify-content:center;gap:8px">
                    <span id="timer-icon">
                        <svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor"><path d="M6 3l8 5-8 5V3z"/></svg>
                    </span>
                    <span id="timer-label">{{ __('app.task_timer_start') }}</span>
                </button>
                <div id="timer-elapsed" style="display:none;font-size:11px;color:var(--accent);text-align:center;font-family:'Montserrat',sans-serif;margin-top:8px;font-weight:500"></div>
            </div>{{-- /time-card --}}
            @endif

        </div>{{-- /right sidebar --}}

    </div>{{-- /grid --}}

    {{-- History modal --}}
    @if($task->histories && $task->histories->count())
    <div id="history-modal" style="display:none;position:fixed;inset:0;z-index:10000;align-items:center;justify-content:center">
        <div id="history-backdrop" style="position:absolute;inset:0;background:rgba(0,0,0,.3);backdrop-filter:blur(8px)"></div>
        <div style="position:relative;z-index:1;background:var(--surface);border:1px solid var(--border);border-radius:16px;width:100%;max-width:480px;max-height:70vh;display:flex;flex-direction:column;box-shadow:0 32px 80px rgba(0,0,0,.35);margin:0 16px;overflow:hidden">
            <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 20px 14px;border-bottom:1px solid var(--border);flex-shrink:0">
                <div style="display:flex;align-items:center;gap:8px">
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="var(--accent)" stroke-width="1.8"><circle cx="8" cy="8" r="6.5"/><path d="M8 4.5V8l2.5 2"/></svg>
                    <span style="font-size:13px;font-weight:600;color:var(--text)">{{ __('app.task_history_title') }}</span>
                    <span style="background:var(--surface2);color:var(--muted);border-radius:20px;padding:1px 8px;font-size:10px;font-weight:700">{{ $task->histories->count() }}</span>
                </div>
                <button id="btn-close-history" style="background:none;border:none;cursor:pointer;color:var(--muted);padding:4px;border-radius:6px;display:flex;align-items:center"
                    onmouseover="this.style.color='var(--text)';this.style.background='var(--surface2)'"
                    onmouseout="this.style.color='var(--muted)';this.style.background='none'">
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3l10 10M13 3L3 13"/></svg>
                </button>
            </div>
            <div style="overflow-y:auto;padding:8px 0;flex:1">
                @foreach($task->histories->sortByDesc('created_at') as $h)
                <div style="display:flex;gap:12px;padding:10px 20px"
                    onmouseover="this.style.background='var(--surface2)'"
                    onmouseout="this.style.background='none'">
                    <div style="display:flex;flex-direction:column;align-items:center;flex-shrink:0;padding-top:4px">
                        <div style="width:7px;height:7px;border-radius:50%;background:var(--accent)"></div>
                        @if(!$loop->last)
                        <div style="width:1px;flex:1;min-height:18px;background:var(--border);margin-top:3px"></div>
                        @endif
                    </div>
                    <div style="flex:1;min-width:0;padding-bottom:{{ $loop->last ? '0' : '8px' }}">
                        <div style="font-size:13px;color:var(--text);line-height:1.4">{{ $h->label }}</div>
                        <div style="font-size:11px;color:var(--muted);font-family:'Montserrat',sans-serif;margin-top:2px">{{ $h->created_at?->format('d/m/Y \à\s H:i') }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
    // ── Pin button logic ──────────────────────────────────────────────────────
    (function() {
        const btn = document.getElementById('btn-add-shortcut');
        if (!btn) return;

        function sync() {
            if (!window.Shortcuts) return;
            const pinned = window.Shortcuts.has(btn.dataset.url);
            btn.classList.toggle('pinned', pinned);
            btn.querySelector('.pin-star').textContent = pinned ? '★' : '☆';
            btn.querySelector('.pin-label').textContent = pinned ? '{{ __("app.task_shortcut_pinned") }}' : '{{ __("app.task_shortcut_add") }}';
            btn.style.color = pinned ? 'var(--accent)' : '';
            btn.style.borderColor = pinned ? 'rgba(255,145,77,.35)' : '';
        }

        btn.addEventListener('click', function() {
            if (!window.Shortcuts) return;
            const item = {
                id:    btn.dataset.url,
                type:  btn.dataset.type,
                label: btn.dataset.label,
                url:   btn.dataset.url,
                emoji: btn.dataset.emoji,
            };
            const pinned = window.Shortcuts.toggle(item);
            sync();
            toast(pinned ? 'Atalho adicionado!' : 'Atalho removido', pinned ? 'success' : 'info', 2200);
            document.dispatchEvent(new CustomEvent('shortcut-changed', { detail: { url: item.url, pinned } }));
        });

        document.addEventListener('shortcut-changed', sync);
        // Aguarda o Shortcuts ser inicializado
        const t = setInterval(() => { if (window.Shortcuts) { sync(); clearInterval(t); } }, 50);
    })();
    </script>
    @endpush
    @push('scripts')
        <script type="module">
            import { Editor } from 'https://esm.sh/@tiptap/core@3';
            import StarterKit from 'https://esm.sh/@tiptap/starter-kit@3';
            import Underline from 'https://esm.sh/@tiptap/extension-underline@3';
            import Placeholder from 'https://esm.sh/@tiptap/extension-placeholder@3';
            import { marked } from 'https://esm.sh/marked@12';
            import flatpickr from 'https://esm.sh/flatpickr@4';
            import { Portuguese } from 'https://esm.sh/flatpickr@4/dist/l10n/pt.js';

            marked.setOptions({ breaks: true, gfm: true });

            const taskId = {{ $task->id }};
            const csrf   = document.querySelector('meta[name=csrf-token]').content;
            const existingContent = @json($task->description);

            async function apiCall(method, path, body = null) {
                const opts = { method, headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf } };
                if (body !== null) opts.body = JSON.stringify(body);
                return fetch(path, opts);
            }

            // ── Tiptap ───────────────────────────────────────────────────────────────────
            const editor = new Editor({
                element: document.getElementById('task-tiptap-editor'),
                extensions: [
                    StarterKit.configure({ codeBlock: { languageClassPrefix: 'language-' } }),
                    Underline,
                    Placeholder.configure({ placeholder: '{{ __('app.task_desc_ph') }}' }),
                ],
                content: existingContent || '',
                onSelectionUpdate() { updateToolbar(); },
                onTransaction() { updateToolbar(); },
            });

            function updateToolbar() {
                document.querySelectorAll('.ttb-task-btn[data-cmd]').forEach(btn => {
                    const active = ({ bold: editor.isActive('bold'), italic: editor.isActive('italic'), underline: editor.isActive('underline'), strike: editor.isActive('strike'), bulletList: editor.isActive('bulletList'), orderedList: editor.isActive('orderedList'), blockquote: editor.isActive('blockquote'), codeBlock: editor.isActive('codeBlock'), h1: editor.isActive('heading',{level:1}), h2: editor.isActive('heading',{level:2}), h3: editor.isActive('heading',{level:3}) })[btn.dataset.cmd];
                    btn.classList.toggle('active', !!active);
                });
            }

            document.querySelectorAll('.ttb-task-btn[data-cmd]').forEach(btn => {
                btn.addEventListener('mousedown', e => {
                    e.preventDefault();
                    ({ bold:()=>editor.chain().focus().toggleBold().run(), italic:()=>editor.chain().focus().toggleItalic().run(), underline:()=>editor.chain().focus().toggleUnderline().run(), strike:()=>editor.chain().focus().toggleStrike().run(), h1:()=>editor.chain().focus().toggleHeading({level:1}).run(), h2:()=>editor.chain().focus().toggleHeading({level:2}).run(), h3:()=>editor.chain().focus().toggleHeading({level:3}).run(), bulletList:()=>editor.chain().focus().toggleBulletList().run(), orderedList:()=>editor.chain().focus().toggleOrderedList().run(), blockquote:()=>editor.chain().focus().toggleBlockquote().run(), codeBlock:()=>editor.chain().focus().toggleCodeBlock().run(), undo:()=>editor.chain().focus().undo().run(), redo:()=>editor.chain().focus().redo().run() })[btn.dataset.cmd]?.();
                    updateToolbar();
                });
            });

            // ── Sidebar auto-save ────────────────────────────────────────────────────────
            async function saveProp(field, value) {
                const indicator = document.getElementById('props-saved');
                try {
                    const res = await apiCall('PATCH', `/api/v1/tasks/${taskId}`, { [field]: value });
                    if (res.ok) {
                        if (indicator) {
                            indicator.style.display = 'block';
                            clearTimeout(indicator._t);
                            indicator._t = setTimeout(() => { indicator.style.display = 'none'; }, 2000);
                        }
                    } else {
                        toast('{{ __("app.task_err_save") }}', 'error');
                    }
                } catch { toast('{{ __("app.task_err_save") }}', 'error'); }
            }

            document.getElementById('sidebar-status')?.addEventListener('change', function () { saveProp('status', this.value); });
            document.getElementById('sidebar-priority')?.addEventListener('change', function () { saveProp('priority', this.value); });
            document.getElementById('sidebar-category')?.addEventListener('change', function () { saveProp('category_id', this.value ? parseInt(this.value) : null); });

            // ── Flatpickr config base ─────────────────────────────────────────────────────
            const fpBase = {
                locale: Portuguese,
                disableMobile: true,
                time_24hr: true,
                onOpen()  { document.body.style.overflow = 'hidden'; },
                onClose() { document.body.style.overflow = ''; },
            };

            // ── Due date picker ───────────────────────────────────────────────────────────
            const dueDateEl = document.getElementById('sidebar-due-date');
            if (dueDateEl) {
                const dueFp = flatpickr(dueDateEl, {
                    ...fpBase,
                    dateFormat: 'Y-m-d',
                    defaultDate: dueDateEl.dataset.date || null,
                    onChange(selectedDates, dateStr) {
                        const pad = n => String(n).padStart(2, '0');
                        if (dateStr) {
                            const d = selectedDates[0];
                            dueDateEl.value = `${pad(d.getDate())}/${pad(d.getMonth()+1)}/${d.getFullYear()}`;
                        } else {
                            dueDateEl.value = '';
                        }
                        saveProp('due_date', dateStr || null);
                    },
                });
            }

            // ── Reminder picker ───────────────────────────────────────────────────────────
            const reminderEl = document.getElementById('reminder-fp');
            if (reminderEl) {
                const reminderFp = flatpickr(reminderEl, {
                    ...fpBase,
                    enableTime: true,
                    dateFormat: 'Y-m-d H:i',
                    defaultDate: reminderEl.value || null,
                    onChange(selectedDates, dateStr) {
                        if (!dateStr) return;
                        const d = selectedDates[0];
                        const pad = n => String(n).padStart(2, '0');
                        const label = `${pad(d.getDate())}/${pad(d.getMonth()+1)}/${d.getFullYear()} às ${pad(d.getHours())}:${pad(d.getMinutes())}`;
                        reminderEl.value = label;
                        saveProp('reminder_at', dateStr.replace(' ', 'T'));
                        const chip = document.getElementById('reminder-chip');
                        const chipText = document.getElementById('reminder-chip-text');
                        if (chip) chip.style.display = 'flex';
                        if (chipText) chipText.textContent = label;
                    },
                });
                document.getElementById('btn-clear-reminder')?.addEventListener('click', () => {
                    reminderFp.clear();
                    reminderEl.value = '';
                    const chip = document.getElementById('reminder-chip');
                    if (chip) chip.style.display = 'none';
                    saveProp('reminder_at', null);
                });
            }
            document.getElementById('sidebar-recurrence')?.addEventListener('change', function () {
                const wrap = document.getElementById('sidebar-recurrence-ends-wrap');
                wrap.style.display = this.value === 'none' ? 'none' : '';
                if (this.value === 'none') { const el = document.getElementById('sidebar-recurrence-ends'); if (el) el.value = ''; }
                saveProp('recurrence', this.value);
            });

            // ── Recurrence ends picker ────────────────────────────────────────────────────
            const recEndsEl = document.getElementById('sidebar-recurrence-ends');
            if (recEndsEl) {
                flatpickr(recEndsEl, {
                    ...fpBase,
                    dateFormat: 'Y-m-d',
                    defaultDate: recEndsEl.dataset.date || null,
                    onChange(selectedDates, dateStr) {
                        const pad = n => String(n).padStart(2, '0');
                        if (dateStr) {
                            const d = selectedDates[0];
                            recEndsEl.value = `${pad(d.getDate())}/${pad(d.getMonth()+1)}/${d.getFullYear()}`;
                        } else {
                            recEndsEl.value = '';
                        }
                        saveProp('recurrence_ends_at', dateStr || null);
                    },
                });
            }

            // ── Actions ──────────────────────────────────────────────────────────────────
            function launchConfetti() {
                const colors = ['#ff914d','#4ade80','#60a5fa','#f0a05a','#c084fc'];
                const style = document.createElement('style');
                style.textContent = '@keyframes confettiFall{0%{transform:translateY(-10px) rotate(0deg);opacity:1}100%{transform:translateY(100vh) rotate(720deg);opacity:0}}';
                document.head.appendChild(style);
                for (let i = 0; i < 60; i++) {
                    const el = document.createElement('div');
                    const size = Math.random()*8+4;
                    el.style.cssText = `position:fixed;top:0;left:${Math.random()*100}vw;width:${size}px;height:${size}px;background:${colors[i%colors.length]};border-radius:${Math.random()>.5?'50%':'2px'};pointer-events:none;z-index:9999;animation:confettiFall ${1.2+Math.random()*1.5}s ease-in forwards;animation-delay:${Math.random()*0.4}s`;
                    document.body.appendChild(el);
                    el.addEventListener('animationend', () => el.remove());
                }
            }

            const btnComplete = document.getElementById('btn-complete');
            if (btnComplete) {
                btnComplete.addEventListener('click', async function () {
                    this.innerHTML = '<span class="spinner"></span>'; this.disabled = true;
                    const res = await apiCall('PATCH', `/api/v1/tasks/${taskId}/complete`);
                    if (res.ok) { launchConfetti(); toast('{{ __('app.task_toast_completed') }}', 'success'); setTimeout(()=>location.reload(), 1200); }
                    else { toast('{{ __('app.task_toast_err_complete') }}', 'error'); this.innerHTML='{{ __('app.task_complete_btn') }}'; this.disabled=false; }
                });
            }

            const btnReopen = document.getElementById('btn-reopen');
            if (btnReopen) {
                btnReopen.addEventListener('click', async function () {
                    this.innerHTML = '<span class="spinner"></span>'; this.disabled = true;
                    const res = await apiCall('PATCH', `/api/v1/tasks/${taskId}/reopen`);
                    if (res.ok) { toast('{{ __('app.task_toast_reopened') }}', 'info'); setTimeout(()=>location.reload(), 600); }
                    else { toast('{{ __('app.task_toast_err_reopen') }}', 'error'); this.innerHTML='{{ __('app.task_reopen_btn') }}'; this.disabled=false; }
                });
            }

            document.getElementById('btn-delete').addEventListener('click', function () {
                confirmDialog('{{ __('app.task_delete_title') }}', '{{ __('app.task_delete_msg') }}', async () => {
                    const res = await apiCall('DELETE', `/api/v1/tasks/${taskId}`);
                    if (res.ok) {
                        if (window.Shortcuts) window.Shortcuts.remove(`/tasks/${taskId}`);
                        toast('{{ __('app.task_toast_deleted') }}', 'info');
                        setTimeout(()=>window.location.href='/tasks', 600);
                    }
                    else toast('{{ __('app.task_toast_err_delete') }}', 'error');
                });
            });

            document.getElementById('btn-save-edit').addEventListener('click', async function () {
                const btn = this;
                const alertEl = document.getElementById('edit-alert');
                alertEl.style.display = 'none';
                btn.innerHTML = '<span class="spinner"></span> Salvando...';
                btn.disabled = true;
                const html = editor.getHTML();
                const payload = {
                    title: document.getElementById('inline-title').textContent.trim(),
                    description: editor.getText().trim() ? html : null,
                };
                try {
                    const res = await apiCall('PUT', `/api/v1/tasks/${taskId}`, payload);
                    const data = await res.json();
                    if (res.ok) {
                        toast('{{ __('app.task_toast_saved') }}', 'success');
                        // Atualiza label nos atalhos se esta task estiver fixada
                        if (window.Shortcuts) {
                            const url = '/tasks/' + taskId;
                            const newTitle = document.getElementById('inline-title')?.textContent.trim() || '';
                            if (newTitle) window.Shortcuts.updateLabel(url, newTitle);
                            const pinBtn = document.getElementById('btn-add-shortcut');
                            if (pinBtn && newTitle) pinBtn.dataset.label = newTitle;
                        }
                        setTimeout(() => location.reload(), 700);
                    } else {
                        const msgs = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message||'Erro.');
                        alertEl.className = 'alert alert-error';
                        alertEl.textContent = msgs;
                        alertEl.style.display = 'block';
                    }
                } catch { toast('{{ __('app.task_toast_err_save') }}', 'error'); }
                finally { btn.innerHTML='{{ __('app.task_save_changes') }}'; btn.disabled=false; }
            });

            // ── Time tracking ─────────────────────────────────────────────────────────────
            let timerInterval = null, timerRunning = false, timerStartedAt = null;

            function fmtSeconds(s) {
                const h=Math.floor(s/3600), m=Math.floor((s%3600)/60), sec=s%60;
                return h>0 ? `${h}h ${String(m).padStart(2,'0')}m ${String(sec).padStart(2,'0')}s` : `${String(m).padStart(2,'0')}m ${String(sec).padStart(2,'0')}s`;
            }
            function fmtTracked(s) {
                if (s<60) return `${s}s`;
                const m=Math.floor(s/60), h=Math.floor(m/60);
                return h>0 ? `${h}h ${m%60}m` : `${m}m`;
            }
            function setTimerUI(running, startedAt, trackedSeconds) {
                timerRunning=running; timerStartedAt=startedAt?new Date(startedAt):null;
                const btn=document.getElementById('btn-timer'), icon=document.getElementById('timer-icon'), lbl=document.getElementById('timer-label'), elapsed=document.getElementById('timer-elapsed');
                if (!btn) return;
                if (running) {
                    btn.style.borderColor='var(--accent)'; btn.style.color='var(--accent)';
                    icon.innerHTML='<svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor"><rect x="3" y="3" width="10" height="10" rx="1"/></svg>'; lbl.textContent='{{ __("app.task_timer_stop") }}';
                    elapsed.style.display='block';
                    clearInterval(timerInterval);
                    timerInterval=setInterval(()=>{ elapsed.textContent='{{ __("app.task_timer_session") }} '+fmtSeconds(Math.floor((Date.now()-timerStartedAt)/1000)); },1000);
                } else {
                    btn.style.borderColor=''; btn.style.color='';
                    icon.innerHTML='<svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor"><path d="M6 3l8 5-8 5V3z"/></svg>'; lbl.textContent='{{ __("app.task_timer_start") }}';
                    elapsed.style.display='none'; clearInterval(timerInterval);
                }
                if (trackedSeconds!==undefined) { const td=document.getElementById('tracked-display'); if(td) td.textContent=fmtTracked(trackedSeconds); }
            }

            (async()=>{ try { const r=await apiCall('GET',`/api/v1/tasks/${taskId}/time/status`); const d=await r.json(); setTimerUI(d.running,d.started_at,d.tracked_seconds); } catch{} })();

            document.getElementById('btn-timer')?.addEventListener('click', async function () {
                this.disabled=true;
                try {
                    if (timerRunning) {
                        const r=await apiCall('POST',`/api/v1/tasks/${taskId}/time/stop`); const d=await r.json();
                        setTimerUI(false,null,d.tracked_seconds); toast('Timer parado — '+fmtTracked(d.elapsed_seconds)+' registrado','success');
                    } else {
                        const r=await apiCall('POST',`/api/v1/tasks/${taskId}/time/start`); const d=await r.json();
                        setTimerUI(true,d.started_at,d.tracked_seconds);
                    }
                } catch { toast('{{ __("app.task_err_timer") }}','error'); }
                finally { this.disabled=false; }
            });

            // ── Estimate ─────────────────────────────────────────────────────────────────
            async function saveEstimate() {
                const h=parseInt(document.getElementById('est-h')?.value)||0;
                let m=parseInt(document.getElementById('est-m')?.value)||0;
                if (m>59) { m=59; const el=document.getElementById('est-m'); if(el) el.value=59; }
                const totalMinutes=(h===0&&m===0)?null:(h*60)+m;
                const summary=document.getElementById('est-summary');
                if (summary) { if(totalMinutes){const ph=Math.floor(totalMinutes/60),pm=totalMinutes%60;summary.textContent=(ph>0?ph+'h ':'')+( pm>0?pm+'min':'')+' estimados';}else{summary.textContent='';} }
                try {
                    const res=await apiCall('PATCH',`/api/v1/tasks/${taskId}/estimate`,{estimated_minutes:totalMinutes});
                    if (res.ok) {
                        ['est-h-wrap','est-m-wrap'].forEach(id=>{ const el=document.getElementById(id); if(el){el.style.borderColor='var(--success)';el.style.boxShadow='0 0 0 3px rgba(74,222,128,.1)';setTimeout(()=>{el.style.borderColor='';el.style.boxShadow='';},1000);} });
                    } else { toast('{{ __("app.task_err_save_estimate") }}','error'); }
                } catch { toast('Erro ao salvar estimativa.','error'); }
            }

            const estH=document.getElementById('est-h'), estM=document.getElementById('est-m');
            estH?.addEventListener('keydown',e=>{if(e.key==='Tab'){e.preventDefault();estM?.focus();}if(e.key==='Enter'){e.preventDefault();saveEstimate();}});
            estM?.addEventListener('keydown',e=>{if(e.key==='Enter'){e.preventDefault();saveEstimate();}});
            document.getElementById('btn-save-estimate')?.addEventListener('click', saveEstimate);

            // ── History modal ─────────────────────────────────────────────────────────────
            const historyModal=document.getElementById('history-modal');
            document.getElementById('btn-open-history')?.addEventListener('click',()=>{ if(historyModal) historyModal.style.display='flex'; });
            document.getElementById('btn-close-history')?.addEventListener('click',()=>{ if(historyModal) historyModal.style.display='none'; });
            document.getElementById('history-backdrop')?.addEventListener('click',()=>{ if(historyModal) historyModal.style.display='none'; });
            document.addEventListener('keydown',e=>{ if(e.key==='Escape'&&historyModal?.style.display==='flex') historyModal.style.display='none'; });

            // ── Comments ─────────────────────────────────────────────────────────────────
            const commentBody=document.getElementById('comment-body'), commentPreview=document.getElementById('comment-preview'), commentCharCount=document.getElementById('comment-char-count'), btnAddComment=document.getElementById('btn-add-comment'), commentList=document.getElementById('comment-list'), countBadge=document.getElementById('comment-count'), loadMoreWrap=document.getElementById('comments-load-more-wrap'), btnLoadMore=document.getElementById('btn-load-more'), tabWrite=document.getElementById('tab-write'), tabPreview=document.getElementById('tab-preview');
            let currentPage=1, lastPage=1, totalCount=parseInt(countBadge?.textContent)||0;

            function renderMd(text){ return marked.parse(text||''); }
            function setCount(n){ totalCount=n; if(countBadge) countBadge.textContent=n; }
            function deltaCount(d){ setCount(Math.max(0,totalCount+d)); }
            function showEmpty(){ if(document.getElementById('comments-empty'))return; const el=document.createElement('div'); el.id='comments-empty'; el.style.cssText='text-align:center;padding:20px 0;color:var(--muted);font-size:13px'; el.textContent='{{ __("app.task_comment_empty") }}'; commentList.appendChild(el); }

            function setTab(tab){
                const accent='var(--accent)',muted='var(--muted)',none='transparent';
                if(tab==='write'){commentBody.style.display='';commentPreview.style.display='none';tabWrite.style.color=accent;tabWrite.style.borderBottomColor=accent;tabPreview.style.color=muted;tabPreview.style.borderBottomColor=none;}
                else{commentPreview.innerHTML=renderMd(commentBody.value)||'<em style="color:var(--muted)">{{ __("app.task_comment_nothing") }}</em>';commentBody.style.display='none';commentPreview.style.display='';tabPreview.style.color=accent;tabPreview.style.borderBottomColor=accent;tabWrite.style.color=muted;tabWrite.style.borderBottomColor=none;}
            }
            tabWrite?.addEventListener('click',()=>setTab('write'));
            tabPreview?.addEventListener('click',()=>setTab('preview'));
            commentBody?.addEventListener('input',()=>{ const len=commentBody.value.length; commentCharCount.textContent=`${len} / 2000`; commentCharCount.style.color=len>1800?'var(--danger)':'var(--muted)'; });
            commentBody?.addEventListener('keydown',e=>{ if((e.ctrlKey||e.metaKey)&&e.key==='Enter'){e.preventDefault();btnAddComment.click();} });

            function buildCommentEl(comment){
                const div=document.createElement('div'); div.className='comment-item'; div.dataset.id=comment.id; div.dataset.body=comment.body;
                div.style.cssText='display:flex;gap:10px;padding:10px 0;border-bottom:1px solid var(--border)';
                const editedLabel=comment.edited?`<span style="color:var(--muted);font-size:10px;margin-left:6px">({{ __("app.task_comment_edited") }})</span>`:'';
                div.innerHTML=`<div style="width:28px;height:28px;border-radius:50%;background:rgba(255,145,77,.15);display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;margin-top:1px">💬</div><div style="flex:1;min-width:0"><div class="comment-body-display md-body">${renderMd(comment.body)}</div><div style="display:flex;align-items:center;justify-content:space-between;margin-top:5px;flex-wrap:wrap;gap:4px"><span style="color:var(--muted);font-size:11px;font-family:'Montserrat',sans-serif">${comment.created_at}${editedLabel}</span><div style="display:flex;gap:4px"><button class="btn-edit-comment" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:11px;padding:2px 6px;border-radius:4px;transition:color .15s,background .15s">{{ __("app.task_comment_edit") }}</button><button class="btn-delete-comment" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:11px;padding:2px 6px;border-radius:4px;transition:color .15s,background .15s">{{ __("app.task_comment_delete") }}</button></div></div></div>`;
                div.querySelectorAll('button').forEach(btn=>{ btn.addEventListener('mouseenter',()=>{ const d=btn.classList.contains('btn-delete-comment'); btn.style.color=d?'var(--danger)':'var(--accent)'; btn.style.background=d?'rgba(224,84,84,.1)':'rgba(255,145,77,.1)'; }); btn.addEventListener('mouseleave',()=>{ btn.style.color='var(--muted)'; btn.style.background='none'; }); });
                div.querySelector('.btn-edit-comment').addEventListener('click',()=>startEdit(div));
                div.querySelector('.btn-delete-comment').addEventListener('click',()=>handleDelete(div));
                return div;
            }

            function startEdit(el){
                if(el.querySelector('.comment-edit-textarea'))return;
                const display=el.querySelector('.comment-body-display'); display.style.display='none';
                const wrap=document.createElement('div'); const textarea=document.createElement('textarea'); textarea.className='comment-edit-textarea'; textarea.value=el.dataset.body;
                textarea.style.cssText='width:100%;resize:vertical;min-height:72px;background:var(--surface2);border:1px solid var(--accent);border-radius:8px;padding:10px 12px;font-size:13px;font-family:inherit;color:var(--text);line-height:1.55;outline:none;box-sizing:border-box;box-shadow:0 0 0 3px rgba(255,145,77,.1)';
                const counter=document.createElement('div'); counter.style.cssText='font-size:11px;color:var(--muted);text-align:right;margin-top:3px'; counter.textContent=`${textarea.value.length} / 2000`;
                textarea.addEventListener('input',()=>{ counter.textContent=`${textarea.value.length} / 2000`; counter.style.color=textarea.value.length>1800?'var(--danger)':'var(--muted)'; });
                const actions=document.createElement('div'); actions.style.cssText='display:flex;gap:6px;justify-content:flex-end;margin-top:6px';
                const btnCancel=document.createElement('button'); btnCancel.textContent='{{ __("app.task_comment_cancel") }}'; btnCancel.className='btn btn-ghost btn-sm';
                const btnSave=document.createElement('button'); btnSave.textContent='{{ __("app.task_comment_save") }}'; btnSave.className='btn btn-primary btn-sm';
                btnCancel.addEventListener('click',()=>{ wrap.remove(); display.style.display=''; });
                btnSave.addEventListener('click',()=>saveEdit(el,textarea,display,wrap,btnSave));
                textarea.addEventListener('keydown',e=>{ if((e.ctrlKey||e.metaKey)&&e.key==='Enter'){e.preventDefault();btnSave.click();} if(e.key==='Escape'){wrap.remove();display.style.display='';} });
                actions.append(btnCancel,btnSave); wrap.append(textarea,counter,actions); display.after(wrap); textarea.focus();
            }

            async function saveEdit(el,textarea,display,wrap,btnSave){
                const body=textarea.value.trim(); if(!body){textarea.focus();return;}
                btnSave.innerHTML='<span class="spinner"></span>'; btnSave.disabled=true;
                const res=await apiCall('PATCH',`/api/v1/tasks/${taskId}/comments/${el.dataset.id}`,{body});
                if(res.ok){ const updated=await res.json(); el.dataset.body=updated.body; display.innerHTML=renderMd(updated.body); const ts=el.querySelector('[style*="monospace"]'); if(ts&&!ts.querySelector('[data-edited]'))ts.insertAdjacentHTML('beforeend','<span style="color:var(--muted);font-size:10px;margin-left:6px" data-edited>{{ __("app.task_comment_edited") }}</span>'); wrap.remove(); display.style.display=''; toast('{{ __("app.task_comment_updated") }}','success'); }
                else { const d=await res.json().catch(()=>({})); toast(d.errors?Object.values(d.errors).flat().join(' '):(d.message||'Erro.'),'error'); btnSave.innerHTML='{{ __("app.task_comment_save") }}'; btnSave.disabled=false; }
            }

            async function handleDelete(el){
                confirmDialog('{{ __("app.task_comment_delete_title") }}','{{ __("app.task_comment_delete_msg") }}',async()=>{
                    const res=await apiCall('DELETE',`/api/v1/tasks/${taskId}/comments/${el.dataset.id}`);
                    if(res.ok){el.remove();deltaCount(-1);if(!commentList.querySelector('.comment-item'))showEmpty();toast('{{ __("app.task_comment_deleted") }}','info');}
                    else toast('{{ __("app.task_err_delete") }}','error');
                });
            }

            async function loadComments(page=1){
                if(btnLoadMore){btnLoadMore.innerHTML='<span class="spinner"></span>';btnLoadMore.disabled=true;}
                try {
                    const res=await apiCall('GET',`/api/v1/tasks/${taskId}/comments?page=${page}`); const data=await res.json();
                    if(page===1){commentList.innerHTML='';document.getElementById('comments-empty')?.remove();}
                    data.data.length===0&&page===1?showEmpty():data.data.forEach(c=>commentList.appendChild(buildCommentEl(c)));
                    currentPage=data.current_page; lastPage=data.last_page; setCount(data.total);
                    if(loadMoreWrap) loadMoreWrap.style.display=currentPage<lastPage?'':'none';
                } catch { toast('{{ __("app.task_err_load_comments") }}','error'); }
                finally { if(btnLoadMore){btnLoadMore.innerHTML='{{ __("app.task_load_more") }}';btnLoadMore.disabled=false;} }
            }

            btnLoadMore?.addEventListener('click',()=>loadComments(currentPage+1));

            btnAddComment?.addEventListener('click', async function(){
                const body=commentBody.value.trim(); if(!body){setTab('write');commentBody.focus();return;}
                this.innerHTML='<span class="spinner"></span>'; this.disabled=true;
                try {
                    const res=await apiCall('POST',`/api/v1/tasks/${taskId}/comments`,{body});
                    if(res.ok){ const comment=await res.json(); document.getElementById('comments-empty')?.remove(); commentList.insertBefore(buildCommentEl(comment),commentList.firstChild); commentBody.value=''; commentCharCount.textContent='0 / 2000'; setTab('write'); deltaCount(+1); toast('{{ __("app.task_comment_added") }}','success'); }
                    else { const d=await res.json(); toast(d.errors?Object.values(d.errors).flat().join(' '):(d.message||'Erro.'),'error'); }
                } catch { toast('{{ __("app.task_err_connection") }}','error'); }
                finally { this.innerHTML='Comentar'; this.disabled=false; }
            });

            loadComments(1);

            // ── Subtasks ──────────────────────────────────────────────────────────────────
            (function () {
                const TASK_ID   = {{ $task->id }};
                const CSRF      = document.querySelector('meta[name=csrf-token]').content;
                const list      = document.getElementById('subtasks-list');
                const progWrap  = document.getElementById('subtasks-progress-wrap');
                const progFill  = document.getElementById('subtasks-progress-fill');
                const progLabel = document.getElementById('subtasks-progress-label');
                const trigger   = document.getElementById('subtask-add-trigger');
                const form      = document.getElementById('subtask-add-form');
                const input     = document.getElementById('subtask-add-input');
                const submitBtn = document.getElementById('subtask-add-submit');

                async function api(method, url, body) {
                    const r = await fetch(url, {
                        method,
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                        body: body ? JSON.stringify(body) : undefined,
                    });
                    return r.json();
                }

                // ── Progress bar ──────────────────────────────────────────────────────
                function updateProgress() {
                    const items = list.querySelectorAll('.subtask-item');
                    const total = items.length;
                    if (total === 0) { progWrap.style.display = 'none'; return; }
                    const done  = list.querySelectorAll('.subtask-check:checked').length;
                    const pct   = Math.round(done / total * 100);
                    progWrap.style.display  = 'flex';
                    progFill.style.width    = pct + '%';
                    progLabel.textContent   = `${done}/${total}`;
                }

                // ── Build row ─────────────────────────────────────────────────────────
                function buildRow(sub) {
                    const row = document.createElement('div');
                    row.className   = 'subtask-item';
                    row.dataset.id  = sub.id;

                    const check = document.createElement('input');
                    check.type      = 'checkbox';
                    check.className = 'subtask-check';
                    check.checked   = sub.completed;

                    const title = document.createElement('span');
                    title.className     = 'subtask-title' + (sub.completed ? ' done' : '');
                    title.textContent   = sub.title;
                    title.contentEditable = 'true';
                    title.spellcheck    = false;

                    const del = document.createElement('button');
                    del.className = 'subtask-delete';
                    del.title     = 'Remover';
                    del.innerHTML = '<svg width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3l10 10M13 3L3 13"/></svg>';

                    // Toggle complete
                    check.addEventListener('change', async () => {
                        title.classList.toggle('done', check.checked);
                        await api('PUT', `/tasks/${TASK_ID}/subtasks/${sub.id}`, { completed: check.checked });
                        updateProgress();
                    });

                    // Inline rename on blur
                    let origTitle = sub.title;
                    title.addEventListener('focus', () => { origTitle = title.textContent.trim(); });
                    title.addEventListener('blur', async () => {
                        const newTitle = title.textContent.trim();
                        if (!newTitle) { title.textContent = origTitle; return; }
                        if (newTitle !== origTitle) {
                            await api('PUT', `/tasks/${TASK_ID}/subtasks/${sub.id}`, { title: newTitle });
                        }
                    });
                    title.addEventListener('keydown', e => {
                        if (e.key === 'Enter')  { e.preventDefault(); title.blur(); }
                        if (e.key === 'Escape') { title.textContent = origTitle; title.blur(); }
                    });

                    // Delete
                    del.addEventListener('click', async () => {
                        row.style.transition = 'opacity .2s, transform .2s';
                        row.style.opacity    = '0';
                        row.style.transform  = 'translateX(8px)';
                        await api('DELETE', `/tasks/${TASK_ID}/subtasks/${sub.id}`);
                        setTimeout(() => { row.remove(); updateProgress(); }, 200);
                    });

                    row.appendChild(check);
                    row.appendChild(title);
                    row.appendChild(del);
                    return row;
                }

                // ── Load ──────────────────────────────────────────────────────────────
                async function loadSubtasks() {
                    const items = await api('GET', `/tasks/${TASK_ID}/subtasks`);
                    list.innerHTML = '';
                    (items || []).forEach(s => list.appendChild(buildRow(s)));
                    updateProgress();
                }

                // ── Add form ──────────────────────────────────────────────────────────
                function openForm() {
                    trigger.style.display = 'none';
                    form.style.display    = 'flex';
                    input.value           = '';
                    submitBtn.classList.remove('active');
                    input.focus();
                }

                function closeForm() {
                    form.style.display    = 'none';
                    trigger.style.display = 'flex';
                    input.value           = '';
                    submitBtn.classList.remove('active');
                }

                async function addSubtask() {
                    const title = input.value.trim();
                    if (!title) { input.focus(); return; }
                    input.value = '';
                    submitBtn.classList.remove('active');
                    const sub = await api('POST', `/tasks/${TASK_ID}/subtasks`, { title });
                    if (sub && sub.id) {
                        list.appendChild(buildRow(sub));
                        updateProgress();
                    }
                    input.focus();
                }

                trigger.addEventListener('click', openForm);
                submitBtn.addEventListener('click', addSubtask);

                input.addEventListener('input', () => {
                    submitBtn.classList.toggle('active', input.value.trim().length > 0);
                });
                input.addEventListener('keydown', e => {
                    if (e.key === 'Enter')  { e.preventDefault(); addSubtask(); }
                    if (e.key === 'Escape') { closeForm(); }
                });

                loadSubtasks();
            })();
        </script>
    @endpush

@endsection