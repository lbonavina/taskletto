@extends('layouts.app')
@section('page-title', __('app.nav_notes'))

@section('topbar-actions')
    <div style="display:flex;align-items:center;gap:8px">
        <span id="save-status" style="font-size:12px;color:var(--muted);font-family:'DM Sans',monospace;transition:opacity .3s"></span>
        <button id="btn-pin" class="btn btn-ghost btn-sm" title="{{ $note->pinned ? __('app.note_unpin_title') : __('app.note_pin_title') }}">
            {{ $note->pinned ? __('app.note_pinned') : __('app.note_pin') }}
        </button>
        <a href="{{ route('notes.export', $note) }}" class="btn btn-ghost btn-sm" title="{{ __('app.note_export_title') }}" download>
            {{ __('app.note_export_md') }}
        </a>
        <a href="{{ route('notes.export.pdf', $note) }}" class="btn btn-ghost btn-sm" target="_blank" title="{{ __('app.note_export_pdf_title') }}">
            🖨 PDF
        </a>
        <button id="btn-delete" class="btn btn-danger btn-sm">{{ __('app.note_delete') }}</button>
        <a href="/notes" class="btn btn-ghost btn-sm">{{ __('app.note_back') }}</a>
    </div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
/* ── Animations ──────────────────────────────────────────────────────── */
@keyframes popoverIn {
    from { opacity: 0; transform: translateY(-6px) scale(.97); }
    to   { opacity: 1; transform: translateY(0)    scale(1);   }
}
@keyframes popoverOut {
    from { opacity: 1; transform: translateY(0)    scale(1);   }
    to   { opacity: 0; transform: translateY(-6px) scale(.97); }
}

/* ── Note layout ─────────────────────────────────────────────────────── */
.note-shell {
    display: flex; gap: 0;
    height: calc(100vh - 56px);
    margin: -32px; overflow: hidden;
    position: relative;
}

/* ── Sidebar ──────────────────────────────────────────────────────────── */
.note-sidebar {
    width: 220px; flex-shrink: 0;
    background: var(--surface);
    border-right: 1px solid var(--border);
    display: flex; flex-direction: column; overflow: hidden;
    transition: width .28s cubic-bezier(.4,0,.2,1);
    position: relative;
}
.note-sidebar.collapsed {
    width: 0;
    border-right: none;
}

/* ── Sidebar toggle button — tab-style, always visible ──────────────── */
.sidebar-toggle-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    /* left set by JS */
    width: 16px; height: 48px;
    background: var(--surface2);
    border: 1px solid var(--border);
    border-left: none;
    border-radius: 0 6px 6px 0;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    z-index: 50;
    box-shadow: 2px 0 8px rgba(0,0,0,.2);
    color: var(--muted);
    font-size: 10px;
    line-height: 1;
    transition: background .15s, color .15s, width .15s, left .28s cubic-bezier(.4,0,.2,1);
    padding: 0;
}
.sidebar-toggle-btn:hover {
    background: var(--accent);
    color: #fff;
    border-color: var(--accent);
    width: 20px;
}
html[data-theme=light] .sidebar-toggle-btn {
    background: #eeeef2;
    box-shadow: 2px 0 6px rgba(0,0,0,.08);
}
html[data-theme=light] .sidebar-toggle-btn:hover {
    background: var(--accent);
    color: #fff;
}
.note-sidebar-section {
    padding: 16px 16px 8px;
    font-size: 10px; font-weight: 700; letter-spacing: .8px;
    text-transform: uppercase; color: var(--muted);
}
.note-meta-row {
    display: flex; align-items: center; gap: 10px;
    padding: 6px 16px; font-size: 12px;
}
.note-meta-label { color: var(--muted); width: 70px; flex-shrink: 0; font-size: 11px; }
.note-meta-value { color: var(--text); flex: 1; }

/* Color dots */
.note-colors { display: flex; gap: 7px; flex-wrap: wrap; padding: 4px 16px 12px; }
.note-color-dot {
    width: 20px; height: 20px; border-radius: 50%;
    border: 2px solid transparent; cursor: pointer;
    transition: transform .15s, border-color .15s, box-shadow .15s;
}
.note-color-dot:hover { transform: scale(1.2); box-shadow: 0 2px 8px rgba(0,0,0,.3); }
.note-color-dot.active { border-color: white; transform: scale(1.1); box-shadow: 0 2px 10px rgba(0,0,0,.4); }
html[data-theme=light] .note-color-dot.active { border-color: #555; }

/* Sidebar select */
.note-sidebar .select-wrap select,
.note-sidebar .csel-trigger {
    font-size: 12px !important;
    padding: 6px 28px 6px 10px !important;
    border-radius: 7px !important;
}

/* Stats */
.note-stats {
    margin-top: auto; padding: 12px 16px;
    border-top: 1px solid var(--border);
    font-size: 11px; color: var(--muted);
    font-family: 'DM Sans', monospace;
    display: flex; flex-direction: column; gap: 3px;
}

/* ── Editor area ──────────────────────────────────────────────────────── */
.note-editor-area {
    flex: 1; display: flex; flex-direction: column;
    overflow: hidden; background: var(--bg);
    min-width: 0;
}

/* Title */
.note-title-input {
    width: 100%; background: transparent;
    border: none; outline: none;
    font-family: 'Codec Pro', sans-serif;
    font-size: 30px; font-weight: 800; letter-spacing: -0.8px; color: var(--text);
    padding: 28px 48px 14px; line-height: 1.15;
    border-bottom: 1px solid var(--border);
    resize: none; overflow: hidden;
    transition: none;
}
.note-title-input::placeholder { color: var(--muted); opacity: .35; font-style: italic; }

/* Toolbar */
.tiptap-toolbar {
    display: flex; align-items: center; gap: 2px;
    padding: 6px 48px;
    border-bottom: 1px solid var(--border);
    background: var(--surface);
    flex-wrap: wrap; flex-shrink: 0;
}
.ttb-btn {
    width: 28px; height: 28px;
    display: flex; align-items: center; justify-content: center;
    border-radius: 6px; border: none; background: none;
    color: var(--muted); cursor: pointer; font-size: 12px;
    transition: background .1s, color .1s;
    flex-shrink: 0;
}
.ttb-btn:hover  { background: var(--surface2); color: var(--text); }
.ttb-btn.active { background: rgba(255,145,77,.18); color: var(--accent); }
.ttb-sep { width: 1px; height: 16px; background: var(--border); margin: 0 3px; flex-shrink: 0; opacity: .7; }
/* Toolbar heading dropdown */
.ttb-dropdown { position: relative; }
.ttb-dropdown-trigger {
    display: flex; align-items: center; gap: 6px;
    padding: 5px 10px; border-radius: 7px;
    border: 1px solid transparent; background: var(--surface2);
    color: var(--muted); font-size: 12px; font-family: inherit;
    cursor: pointer; white-space: nowrap; min-width: 90px;
    transition: background .12s, color .12s, border-color .12s;
}
.ttb-dropdown-trigger:hover,
.ttb-dropdown-trigger.open { background: rgba(255,145,77,.1); color: var(--text); border-color: rgba(255,145,77,.2); }
.ttb-dropdown-trigger svg { flex-shrink: 0; opacity: .5; transition: transform .15s; }
.ttb-dropdown-trigger.open svg { transform: rotate(180deg); opacity: 1; }
.ttb-dropdown-trigger span { flex: 1; text-align: left; }
.ttb-dropdown-menu {
    display: none; position: absolute; top: calc(100% + 4px); left: 0;
    background: var(--surface); border: 1px solid var(--border);
    border-radius: 10px; padding: 4px; min-width: 130px;
    box-shadow: 0 8px 24px rgba(0,0,0,.4); z-index: 9999;
    animation: cselDropIn .15s ease;
}
.ttb-dropdown-menu.open { display: block; }
.ttb-dropdown-item {
    display: block; width: 100%; padding: 7px 12px;
    border: none; background: none; border-radius: 7px;
    color: var(--text); font-family: inherit;
    cursor: pointer; text-align: left; transition: background .1s;
}
.ttb-dropdown-item:hover  { background: rgba(255,145,77,.1); }
.ttb-dropdown-item.active { background: rgba(255,145,77,.15); color: var(--accent); }

/* Editor wrap */
.tiptap-wrap {
    flex: 1; overflow-y: auto;
    padding: 36px 48px 80px;
    scrollbar-width: thin;
    scrollbar-color: var(--border) transparent;
}

/* ── ProseMirror content ──────────────────────────────────────────────── */
.ProseMirror {
    min-height: 400px; outline: none;
    font-size: 15px; line-height: 1.85;
    color: var(--text); max-width: 780px;
    font-family: 'DM Sans', sans-serif;
}
.ProseMirror p { margin: 0 0 8px; }
.ProseMirror p.is-editor-empty:first-child::before {
    content: attr(data-placeholder);
    color: var(--muted); opacity: .45;
    pointer-events: none; height: 0; float: left;
    font-style: italic;
}
/* Headings — rich hierarchy */
.ProseMirror h1 {
    font-family: 'Codec Pro', sans-serif;
    font-size: 28px; font-weight: 800; letter-spacing: -0.6px;
    margin: 28px 0 10px; line-height: 1.15; color: var(--text);
    padding-bottom: 10px;
    border-bottom: 2px solid var(--border);
}
.ProseMirror h2 {
    font-family: 'Codec Pro', sans-serif;
    font-size: 21px; font-weight: 700; letter-spacing: -0.3px;
    margin: 22px 0 8px; line-height: 1.25; color: var(--text);
}
.ProseMirror h3 {
    font-family: 'Codec Pro', sans-serif;
    font-size: 16px; font-weight: 700; letter-spacing: -0.1px;
    margin: 16px 0 5px; color: var(--text);
}
.ProseMirror strong { font-weight: 700; }
.ProseMirror em     { font-style: italic; color: inherit; }
.ProseMirror u      { text-decoration: underline; text-underline-offset: 3px; text-decoration-color: var(--accent); }
.ProseMirror s      { text-decoration: line-through; color: var(--muted); }
.ProseMirror mark   {
    background: rgba(250,204,21,.28); color: var(--text);
    border-radius: 3px; padding: 1px 4px;
    border-bottom: 1.5px solid rgba(250,204,21,.6);
}
.ProseMirror ul, .ProseMirror ol { padding-left: 24px; margin: 8px 0; }
.ProseMirror li { margin: 4px 0; line-height: 1.75; }
.ProseMirror li p { margin: 0; }

/* Colored text — vibrant, keep weight */
.ProseMirror [style*="color:#e05454"], .ProseMirror [style*="color: #e05454"] { font-weight: inherit; }
.ProseMirror [style*="color"] { letter-spacing: inherit; }

/* ── Focus / reading mode feel ──────────────────────────────────────── */
.tiptap-wrap {
    background: var(--bg);
}
/* Subtle paper texture on the editing surface */
@media (prefers-color-scheme: dark) {
    .tiptap-wrap { background: var(--bg); }
}

/* ── Word count bar at bottom of editor ─────────────────────────────── */
.note-wordcount-bar {
    display: flex; align-items: center; gap: 16px;
    padding: 6px 48px;
    border-top: 1px solid var(--border);
    background: var(--surface);
    font-size: 11px; color: var(--muted);
    font-family: 'DM Mono', monospace;
    flex-shrink: 0;
}
.note-wordcount-bar span { display: flex; align-items: center; gap: 5px; }
.note-wordcount-bar svg { opacity: .5; }
html[data-theme=light] .note-wordcount-bar { background: #fff; }

/* ── Checklist ───────────────────────────────────────────────────────── */
.ProseMirror ul[data-type="taskList"] { list-style: none; padding-left: 2px; margin: 6px 0; }
.ProseMirror ul[data-type="taskList"] li {
    display: flex; align-items: flex-start; gap: 10px;
    margin: 5px 0; line-height: 1.6;
}
.ProseMirror ul[data-type="taskList"] li > label {
    flex-shrink: 0; margin-top: 3px; cursor: pointer; display: flex;
}
.ProseMirror ul[data-type="taskList"] li > label input[type=checkbox] {
    appearance: none; -webkit-appearance: none;
    width: 16px; height: 16px;
    border: 1.5px solid var(--border); border-radius: 4px;
    background: transparent; cursor: pointer; position: relative;
    flex-shrink: 0; transition: background .15s, border-color .15s, transform .12s, box-shadow .15s;
}
.ProseMirror ul[data-type="taskList"] li > label input[type=checkbox]:hover {
    border-color: var(--accent); transform: scale(1.1);
    box-shadow: 0 0 0 3px rgba(255,145,77,.15);
}
.ProseMirror ul[data-type="taskList"] li > label input[type=checkbox]:checked {
    background: var(--accent); border-color: var(--accent);
    box-shadow: 0 2px 6px rgba(255,145,77,.4);
}
.ProseMirror ul[data-type="taskList"] li > label input[type=checkbox]:checked::after {
    content: ''; position: absolute;
    left: 4px; top: 1px; width: 5px; height: 9px;
    border: 2px solid #fff; border-top: none; border-left: none;
    transform: rotate(45deg);
}
.ProseMirror ul[data-type="taskList"] li[data-checked=true] > div {
    color: var(--muted); text-decoration: line-through;
    text-decoration-color: var(--muted); opacity: .6;
}

/* ── Other blocks ────────────────────────────────────────────────────── */
.ProseMirror blockquote {
    border-left: 4px solid var(--accent);
    padding: 8px 0 8px 18px;
    margin: 14px 0;
    color: var(--muted);
    font-style: italic;
    background: rgba(255,145,77,.04);
    border-radius: 0 8px 8px 0;
    font-size: 15px;
}
.ProseMirror code {
    background: rgba(96,165,250,.12);
    border: 1px solid rgba(96,165,250,.2);
    border-radius: 4px;
    font-family: 'DM Mono', monospace; font-size: 12.5px;
    color: #60a5fa; padding: 1px 6px;
}
.ProseMirror pre {
    background: #0d0d10;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 16px 20px; margin: 12px 0; overflow-x: auto;
    position: relative;
}
.ProseMirror pre::before {
    content: '</>';
    position: absolute; top: 10px; right: 14px;
    font-family: 'DM Mono', monospace; font-size: 10px;
    color: var(--muted); opacity: .4;
}
.ProseMirror pre code { background: none; border: none; padding: 0; font-size: 13px; color: #e2e8f0; line-height: 1.65; }

/* ── Syntax highlighting (lowlight / highlight.js tokens) ────────────── */
.hljs-comment, .hljs-quote             { color: #6b737e; font-style: italic; }
.hljs-keyword, .hljs-selector-tag,
.hljs-addition                         { color: #c792ea; }
.hljs-number, .hljs-string,
.hljs-meta .hljs-meta-string,
.hljs-literal, .hljs-doctag,
.hljs-regexp                           { color: #c3e88d; }
.hljs-title, .hljs-section,
.hljs-name, .hljs-selector-id,
.hljs-selector-class                   { color: #82aaff; }
.hljs-attribute, .hljs-attr,
.hljs-variable, .hljs-template-variable,
.hljs-class .hljs-title,
.hljs-type                             { color: #f78c6c; }
.hljs-symbol, .hljs-bullet,
.hljs-subst, .hljs-meta,
.hljs-meta .hljs-keyword,
.hljs-selector-attr, .hljs-selector-pseudo,
.hljs-link                             { color: #89ddff; }
.hljs-built_in, .hljs-deletion         { color: #f07178; }
.hljs-formula                          { font-style: italic; }
.hljs-emphasis                         { font-style: italic; }
.hljs-strong                           { font-weight: bold; }
.ProseMirror hr {
    border: none;
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--border) 20%, var(--border) 80%, transparent);
    margin: 28px 0;
}
.ProseMirror a { color: var(--accent); text-decoration: underline; text-underline-offset: 2px; text-decoration-color: rgba(255,145,77,.4); }
.ProseMirror a:hover { opacity: .8; text-decoration-color: var(--accent); }

/* Table */
.ProseMirror table { border-collapse: collapse; width: 100%; margin: 12px 0; font-size: 13.5px; }
.ProseMirror table td, .ProseMirror table th { border: 1px solid var(--border); padding: 7px 12px; min-width: 80px; }
.ProseMirror table th { background: var(--surface2); font-weight: 600; color: var(--text); }
.ProseMirror table .selectedCell { background: rgba(255,145,77,.08); }
.ProseMirror table .column-resize-handle { width: 3px; background: var(--accent); position: absolute; right: -1px; top: 0; bottom: 0; cursor: col-resize; pointer-events: all; }
.tableWrapper { overflow-x: auto; margin: 10px 0; }

/* Image */
.ProseMirror img { max-width: 100%; border-radius: 8px; margin: 8px 0; display: block; transition: box-shadow .15s; }
.ProseMirror img:hover { box-shadow: 0 4px 20px rgba(0,0,0,.3); }
.ProseMirror img.ProseMirror-selectednode { outline: 2px solid var(--accent); box-shadow: 0 0 0 4px rgba(255,145,77,.2); }

/* ── Callout blocks ──────────────────────────────────────────────────── */
.callout-block {
    display: flex; gap: 12px; align-items: flex-start;
    border-radius: 0 10px 10px 0;
    padding: 12px 16px 12px 14px;
    margin: 10px 0; position: relative;
    transition: box-shadow .15s;
}
.callout-block:hover { box-shadow: 0 2px 12px rgba(0,0,0,.12); }
.callout-block .callout-content { flex: 1; min-width: 0; padding-top: 1px; }
.callout-block .callout-content p { margin: 0; line-height: 1.7; }
.callout-block .callout-content p + p { margin-top: 6px; }
.callout-block .callout-content *:first-child { margin-top: 0; }
.callout-block .callout-content *:last-child  { margin-bottom: 0; }

/* Placeholder inside callout */
.callout-block .callout-content p.is-empty::before {
    content: attr(data-placeholder);
    color: var(--muted); opacity: .45; pointer-events: none; float: left; height: 0;
}

/* Icon button */
.callout-icon-btn {
    background: none; border: none; cursor: pointer;
    font-size: 18px; padding: 0; line-height: 1;
    flex-shrink: 0; margin-top: 1px;
    border-radius: 4px; transition: transform .15s;
    user-select: none; -webkit-user-select: none;
}
.callout-icon-btn:hover { transform: scale(1.2); }

/* Type picker dropdown */
.callout-type-picker {
    position: absolute; left: 10px; top: calc(100% + 6px);
    border-radius: 10px; padding: 5px; z-index: 9000;
    box-shadow: 0 10px 30px rgba(0,0,0,.3);
    flex-direction: column; gap: 2px; min-width: 130px;
}

/* Callout toolbar dropdown items */
.ttb-callout-item { display: flex; align-items: center; gap: 8px; }
.ttb-callout-item span { display: inline-flex; align-items: center; justify-content: center; width: 18px; flex-shrink: 0; }
/* Make the callout trigger compact — no text label, just icon + chevron */
.ttb-callout-trigger-btn { gap: 3px; padding: 4px 6px; min-width: 0; }

/* Selected state for callout block in ProseMirror */
.ProseMirror-selectednode .callout-block,
.callout-block.ProseMirror-selectednode {
    outline: 2px solid var(--accent);
    outline-offset: 2px;
}

/* ── Light theme overrides ───────────────────────────────────────────── */
html[data-theme=light] .note-editor-area    { background: #f6f6f9; }
html[data-theme=light] .note-sidebar        { background: #ffffff; }
html[data-theme=light] .note-title-input    { background: #f6f6f9; color: var(--text); }
html[data-theme=light] .tiptap-toolbar      { background: #ffffff; }
html[data-theme=light] .tiptap-wrap         { background: #f6f6f9; }
html[data-theme=light] .ProseMirror         { color: var(--text); }
html[data-theme=light] .ProseMirror code    { background: rgba(59,130,246,.08); border-color: rgba(59,130,246,.2); color: #2563eb; }
html[data-theme=light] .ProseMirror pre     { background: #1e1e2e; border-color: #2a2a3a; }
html[data-theme=light] .ProseMirror pre code { color: #e2e8f0; }
html[data-theme=light] .ProseMirror blockquote { background: rgba(255,145,77,.04); }
html[data-theme=light] .ProseMirror h1 { border-bottom-color: #dddde6; }

/* Light theme — toolbar dropdown */
html[data-theme=light] .ttb-dropdown-trigger  { background: #eeeeF2; border-color: #dddde6; color: #555566; }
html[data-theme=light] .ttb-dropdown-menu     { background: #ffffff; border-color: #dddde6; box-shadow: 0 8px 24px rgba(0,0,0,.12); }
html[data-theme=light] .ttb-dropdown-item     { color: #18181c; }
html[data-theme=light] .ttb-dropdown-item:hover { background: rgba(255,145,77,.1); }

/* Light theme — bubble menu */
html[data-theme=light] #bubble-menu   { background: #ffffff; border-color: #dddde6; box-shadow: 0 4px 16px rgba(0,0,0,.12); }
html[data-theme=light] .bbl-btn       { color: #555566; }
html[data-theme=light] .bbl-btn:hover { background: rgba(255,145,77,.1); color: #18181c; }

/* Light theme — slash menu */
html[data-theme=light] #slash-menu                  { background: #ffffff !important; border-color: #dddde6 !important; box-shadow: 0 12px 40px rgba(0,0,0,.15) !important; }
html[data-theme=light] #slash-menu button           { color: #18181c !important; }
html[data-theme=light] #slash-menu button:hover,
html[data-theme=light] #slash-menu button.slash-selected { background: rgba(255,145,77,.1) !important; }
html[data-theme=light] #slash-menu [style*="surface2"] { background: #eeeeF2 !important; }

/* Light theme — image popover */
html[data-theme=light] #image-popover { background: #ffffff; border-color: #dddde6; box-shadow: 0 12px 40px rgba(0,0,0,.15); }
html[data-theme=light] .img-tab       { border-color: #dddde6; color: #555566; }
html[data-theme=light] #img-drop-zone { border-color: #dddde6; color: #888899; }

/* ── Image popover ───────────────────────────────────────────────────── */
#image-popover {
    display: none; position: fixed; z-index: 9999;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 14px; padding: 18px;
    width: 340px;
    box-shadow: 0 20px 60px rgba(0,0,0,.4), 0 4px 16px rgba(0,0,0,.2);
    animation: popoverIn .18s cubic-bezier(.34,1.4,.64,1) both;
    transform-origin: top left;
}
#image-popover.closing {
    animation: popoverOut .14s ease forwards;
}
html[data-theme=light] #image-popover {
    background: #ffffff;
    box-shadow: 0 20px 60px rgba(0,0,0,.15), 0 4px 16px rgba(0,0,0,.08);
}
#image-popover input[type=url],
#image-popover input[type=text] {
    width: 100%; box-sizing: border-box;
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 8px; color: var(--text);
    padding: 8px 12px; font-size: 13px; font-family: inherit;
    outline: none; transition: border-color .15s, box-shadow .15s;
    margin-bottom: 8px;
}
#image-popover input[type=url]:focus,
#image-popover input[type=text]:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(255,145,77,.15);
}
html[data-theme=light] #image-popover input[type=url],
html[data-theme=light] #image-popover input[type=text] {
    background: #f4f4f6; border-color: #dddde6; color: #18181c;
}
html[data-theme=light] #image-popover input[type=url]:focus,
html[data-theme=light] #image-popover input[type=text]:focus {
    background: #ffffff;
}
.img-popover-title {
    font-size: 11px; font-weight: 700; color: var(--muted);
    text-transform: uppercase; letter-spacing: .7px; margin-bottom: 14px;
}

/* Tabs */
.img-tabs { display: flex; gap: 4px; margin-bottom: 14px; }
.img-tab {
    flex: 1; padding: 7px 10px; font-size: 12.5px; font-family: inherit;
    border-radius: 8px; border: 1px solid var(--border);
    background: transparent; color: var(--muted); cursor: pointer;
    transition: background .12s, color .12s, border-color .12s, transform .1s;
    font-weight: 500;
}
.img-tab:hover { background: rgba(255,145,77,.08); color: var(--text); transform: translateY(-1px); }
.img-tab.active {
    background: rgba(255,145,77,.15); color: var(--accent);
    border-color: rgba(255,145,77,.35); font-weight: 600;
}
html[data-theme=light] .img-tab { border-color: #dddde6; color: #8888a0; background: #f4f4f6; }
html[data-theme=light] .img-tab:hover { background: rgba(255,145,77,.08); color: #18181c; }
html[data-theme=light] .img-tab.active { background: rgba(255,145,77,.12); border-color: rgba(255,145,77,.4); }

/* Drop zone */
#img-drop-zone {
    display: flex; flex-direction: column; align-items: center;
    justify-content: center; gap: 6px; height: 90px;
    border: 2px dashed var(--border); border-radius: 10px;
    cursor: pointer; transition: border-color .15s, background .15s, transform .15s;
    margin-bottom: 10px; font-size: 12.5px; color: var(--muted); text-align: center;
}
#img-drop-zone:hover, #img-drop-zone.dragover {
    border-color: var(--accent); background: rgba(255,145,77,.06);
    color: var(--text); transform: scale(1.01);
}
html[data-theme=light] #img-drop-zone { border-color: #dddde6; color: #8888a0; }
html[data-theme=light] #img-drop-zone:hover,
html[data-theme=light] #img-drop-zone.dragover { background: rgba(255,145,77,.05); color: #18181c; }

/* Popover footer */
.img-popover-footer {
    display: flex; gap: 8px; justify-content: flex-end; margin-top: 6px;
}

/* ── Link popover ────────────────────────────────────────────────────── */
#link-popover {
    display: none; position: fixed; z-index: 9999;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 14px; padding: 18px;
    width: 320px;
    box-shadow: 0 20px 60px rgba(0,0,0,.4), 0 4px 16px rgba(0,0,0,.2);
    animation: popoverIn .18s cubic-bezier(.34,1.4,.64,1) both;
    transform-origin: top left;
}
html[data-theme=light] #link-popover {
    background: #ffffff;
    border-color: #dddde6;
    box-shadow: 0 20px 60px rgba(0,0,0,.15), 0 4px 16px rgba(0,0,0,.08);
}
.link-popover-title {
    font-size: 11px; font-weight: 700; color: var(--muted);
    text-transform: uppercase; letter-spacing: .7px; margin-bottom: 14px;
}
#link-popover input[type=url],
#link-popover input[type=text] {
    width: 100%; box-sizing: border-box;
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 8px; color: var(--text);
    padding: 8px 12px; font-size: 13px; font-family: inherit;
    outline: none; transition: border-color .15s, box-shadow .15s;
    margin-bottom: 8px;
}
#link-popover input[type=url]:focus,
#link-popover input[type=text]:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(255,145,77,.15);
}
html[data-theme=light] #link-popover input[type=url],
html[data-theme=light] #link-popover input[type=text] {
    background: #f4f4f6; border-color: #dddde6; color: #18181c;
}
html[data-theme=light] #link-popover input[type=url]:focus,
html[data-theme=light] #link-popover input[type=text]:focus {
    background: #ffffff;
}
.link-popover-hint {
    font-size: 11.5px; color: var(--muted);
    margin-bottom: 12px; line-height: 1.5;
}
.link-popover-footer {
    display: flex; gap: 8px; justify-content: flex-end; margin-top: 6px;
}
#link-remove-btn {
    margin-right: auto;
}

/* Bubble menu */
#bubble-menu { display: none; position: fixed; }
.bbl-btn {
    width: 28px; height: 28px;
    display: flex; align-items: center; justify-content: center;
    border-radius: 6px; border: none; background: none;
    color: var(--muted); cursor: pointer; font-size: 12px;
    transition: background .1s, color .1s;
}
.bbl-btn:hover  { background: rgba(255,145,77,.1); color: var(--text); }
.bbl-btn.active { background: rgba(255,145,77,.18); color: var(--accent); }

/* Slash menu — force project theme regardless of system */
#slash-menu {
    background: var(--surface) !important;
    border-color: var(--border) !important;
    color: var(--text) !important;
    font-family: inherit !important;
}
#slash-menu button {
    color: var(--text) !important;
    font-family: inherit !important;
}
#slash-menu button span { color: inherit !important; }
#slash-menu button span[style*="color:var(--muted)"],
#slash-menu button span + span span:last-child { color: var(--muted) !important; }
#slash-menu button:hover,
#slash-menu button.slash-selected {
    background: rgba(255,145,77,.12) !important;
}
#slash-menu div[style*="background:var(--border)"],
#slash-menu [style*="height:1px"] { background: var(--border) !important; }
#slash-menu [style*="text-transform:uppercase"] { color: var(--muted) !important; }
#slash-menu [style*="surface2"] { background: var(--surface2) !important; }
/* ── Tags ────────────────────────────────────────────────────────────── */
.note-tags-section { padding: 0 12px 12px; }

/* Chips row above input */
.note-tags-chips {
    display: flex; flex-wrap: wrap; gap: 5px;
    margin-bottom: 6px; min-height: 0;
}
.note-tags-chips:empty { display: none; }

/* Input row — clean single line */
.note-tags-input-wrap {
    display: flex; align-items: center; gap: 6px;
    background: var(--surface2); border: 1px solid var(--border);
    border-radius: 8px; padding: 5px 10px; min-height: 32px;
    cursor: text; transition: border-color .15s, box-shadow .15s;
}
.note-tags-input-wrap:focus-within {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(255,145,77,.1);
}
.note-tags-input-icon {
    font-size: 11px; color: var(--muted); opacity: .6; flex-shrink: 0;
}

/* Tag chips */
.tag-chip {
    display: inline-flex; align-items: center; gap: 2px;
    background: var(--tag-bg, rgba(255,145,77,.1));
    color: var(--tag-color, var(--accent));
    border: 1px solid var(--tag-border, rgba(255,145,77,.2));
    border-radius: 20px; padding: 2px 8px 2px 9px;
    font-size: 11px; font-weight: 500; letter-spacing: .15px;
    animation: tagIn .15s cubic-bezier(.34,1.4,.64,1) both;
    cursor: default;
    transition: background .12s, box-shadow .12s, transform .1s;
    line-height: 1.6; white-space: nowrap;
}
.tag-chip:hover {
    background: var(--tag-hover, rgba(255,145,77,.2));
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,.15);
}
.tag-chip::before { content: '#'; opacity: .5; font-weight: 400; margin-right: 1px; }

/* Color cycling */
.tag-chip:nth-child(5n+1) { --tag-bg: rgba(96,165,250,.12);  --tag-color: #60a5fa; --tag-border: rgba(96,165,250,.25);  --tag-hover: rgba(96,165,250,.2);  }
.tag-chip:nth-child(5n+2) { --tag-bg: rgba(74,222,128,.11);  --tag-color: #4ade80; --tag-border: rgba(74,222,128,.24);  --tag-hover: rgba(74,222,128,.2);  }
.tag-chip:nth-child(5n+3) { --tag-bg: rgba(192,132,252,.12); --tag-color: #c084fc; --tag-border: rgba(192,132,252,.26); --tag-hover: rgba(192,132,252,.2); }
.tag-chip:nth-child(5n+4) { --tag-bg: rgba(244,114,182,.11); --tag-color: #f472b6; --tag-border: rgba(244,114,182,.24); --tag-hover: rgba(244,114,182,.2); }
.tag-chip:nth-child(5n+5) { --tag-bg: rgba(255,145,77,.1);   --tag-color: var(--accent); --tag-border: rgba(255,145,77,.22); --tag-hover: rgba(255,145,77,.2); }

html[data-theme=light] .tag-chip:nth-child(5n+1) { --tag-bg: rgba(59,130,246,.09);  --tag-color: #2563eb; --tag-border: rgba(59,130,246,.22); --tag-hover: rgba(59,130,246,.16); }
html[data-theme=light] .tag-chip:nth-child(5n+2) { --tag-bg: rgba(34,197,94,.09);   --tag-color: #16a34a; --tag-border: rgba(34,197,94,.22);  --tag-hover: rgba(34,197,94,.16);  }
html[data-theme=light] .tag-chip:nth-child(5n+3) { --tag-bg: rgba(168,85,247,.09);  --tag-color: #7c3aed; --tag-border: rgba(168,85,247,.22); --tag-hover: rgba(168,85,247,.16); }
html[data-theme=light] .tag-chip:nth-child(5n+4) { --tag-bg: rgba(236,72,153,.09);  --tag-color: #db2777; --tag-border: rgba(236,72,153,.22); --tag-hover: rgba(236,72,153,.16); }
html[data-theme=light] .tag-chip:nth-child(5n+5) { --tag-bg: rgba(234,88,12,.09);   --tag-color: #c2410c; --tag-border: rgba(234,88,12,.22);  --tag-hover: rgba(234,88,12,.16);  }

@keyframes tagIn { from { opacity:0; transform: scale(.82) translateY(2px); } to { opacity:1; transform: scale(1); } }

.tag-chip-remove {
    background: none; border: none; color: inherit; cursor: pointer;
    padding: 0 0 0 3px; font-size: 13px; line-height: 1; opacity: .4;
    transition: opacity .12s; display: flex; align-items: center; margin-left: 1px;
}
.tag-chip-remove:hover { opacity: .9; }

.tag-input {
    border: none; outline: none; background: transparent;
    color: var(--text); font-size: 12px; font-family: inherit;
    min-width: 80px; flex: 1; padding: 0;
}
.tag-input::placeholder { color: var(--muted); opacity: .5; font-size: 11.5px; }

.tag-suggestions {
    position: absolute; z-index: 200;
    background: var(--surface); border: 1px solid var(--border);
    border-radius: 9px; padding: 4px;
    box-shadow: 0 8px 24px rgba(0,0,0,.3);
    min-width: 150px; max-height: 160px; overflow-y: auto;
    top: calc(100% + 4px); left: 0;
}
.tag-suggestion-item {
    padding: 6px 10px; border-radius: 6px; font-size: 12px;
    cursor: pointer; color: var(--text);
    transition: background .1s;
    display: flex; align-items: center; justify-content: space-between;
}
.tag-suggestion-item:hover, .tag-suggestion-item.selected {
    background: rgba(255,145,77,.12); color: var(--accent);
}
.tag-suggestion-count { font-size: 10px; color: var(--muted); }

/* ── Toolbar color picker ───────────────────────────────────────────── */
.ttb-color-wrap { position: relative; }
.ttb-color-palette {
    display: none; position: absolute; top: calc(100% + 4px); left: 0;
    background: var(--surface); border: 1px solid var(--border);
    border-radius: 10px; padding: 10px; z-index: 9999;
    box-shadow: 0 8px 24px rgba(0,0,0,.4);
    animation: cselDropIn .15s ease;
}
.ttb-color-palette.open { display: block; }
.ttb-color-grid { display: grid; grid-template-columns: repeat(5, 20px); gap: 5px; }
.ttb-color-swatch {
    width: 20px; height: 20px; border-radius: 4px;
    border: 2px solid transparent; cursor: pointer;
    transition: transform .12s, border-color .12s;
}
.ttb-color-swatch:hover { transform: scale(1.2); }
.ttb-color-swatch.active { border-color: white; }
html[data-theme=light] .ttb-color-swatch.active { border-color: #555; }
.ttb-color-remove {
    margin-top: 7px; width: 100%; padding: 5px 8px;
    border: 1px solid var(--border); border-radius: 6px;
    background: none; color: var(--muted); font-size: 11px;
    cursor: pointer; font-family: inherit; transition: background .1s;
}
.ttb-color-remove:hover { background: rgba(255,145,77,.1); color: var(--text); }
html[data-theme=light] .ttb-color-palette { background: #fff; border-color: #dddde6; box-shadow: 0 8px 24px rgba(0,0,0,.12); }
</style>
@endpush

@section('content')
<div class="note-shell">

    {{-- Sidebar toggle — fixed to shell, NOT inside sidebar --}}
    <button class="sidebar-toggle-btn" id="sidebar-toggle" title="Esconder painel lateral">
        <svg width="9" height="9" viewBox="0 0 9 9" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path id="sidebar-toggle-arrow" d="M5.5 1.5L3 4.5l2.5 3"/></svg>
    </button>

    {{-- Left sidebar: metadata --}}
    <div class="note-sidebar" id="note-sidebar">
        <div class="note-sidebar-section">{{ __('app.note_section_info') }}</div>

        <div class="note-meta-row">
            <span class="note-meta-label">{{ __('app.note_created') }}</span>
            <span class="note-meta-value" style="font-family:'DM Sans',monospace;font-size:11px">{{ $note->created_at->format('d/m/Y') }}</span>
        </div>
        <div class="note-meta-row">
            <span class="note-meta-label">{{ __('app.note_edited') }}</span>
            <span class="note-meta-value" id="sidebar-updated" style="font-family:'DM Sans',monospace;font-size:11px">{{ $note->updated_at->format('d/m/Y H:i') }}</span>
        </div>

        <div class="note-sidebar-section" style="margin-top:8px">{{ __('app.note_section_color') }}</div>
        <div class="note-colors" id="note-colors">
            @php
                $colors = ['#ff914d','#e05454','#4ade80','#60a5fa','#c084fc','#f472b6','#facc15','#34d399','#94a3b8','#f0a05a'];
            @endphp
            @foreach($colors as $c)
                <div class="note-color-dot {{ $note->color === $c ? 'active' : '' }}"
                    style="background:{{ $c }}" data-color="{{ $c }}"></div>
            @endforeach
        </div>

        <div class="note-sidebar-section">{{ __('app.note_section_cat') }}</div>
        <div style="padding:0 12px 12px">
            <div class="select-wrap">
                <select id="note-category">
                    <option value="">{{ __('app.note_no_category') }}</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->name }}" data-color="{{ $cat->color }}" data-icon="{{ $cat->icon }}"
                            {{ $note->category === $cat->name ? 'selected' : '' }}>
                            {{ $cat->icon }} {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Tags --}}
        <div class="note-sidebar-section">🏷 Tags</div>
        <div class="note-tags-section" style="position:relative">
            {{-- Chips row --}}
            <div class="note-tags-chips" id="tags-chips-wrap">
                @foreach($note->tags_array as $tag)
                    <span class="tag-chip" data-tag="{{ $tag }}">
                        {{ $tag }}<button class="tag-chip-remove" type="button" aria-label="Remover tag">×</button>
                    </span>
                @endforeach
            </div>
            {{-- Input row --}}
            <div class="note-tags-input-wrap" id="tags-input-wrap">
                <span class="note-tags-input-icon">#</span>
                <input class="tag-input" id="tag-input" type="text"
                    placeholder="Nova tag…"
                    autocomplete="off" spellcheck="false">
            </div>
            <div class="tag-suggestions" id="tag-suggestions" style="display:none"></div>
        </div>

        <div class="note-stats" id="note-stats">
            <span id="stat-words">0 {{ __('app.note_words') }}</span>
            <span id="stat-chars">0 {{ __('app.note_chars') }}</span>
            <span id="stat-read">0 {{ __('app.note_read_min') }}</span>
        </div>
    </div>

    {{-- Main editor --}}
    <div class="note-editor-area">
        <textarea id="note-title" class="note-title-input" rows="1"
            placeholder="{{ __('app.note_title_ph') }}">{{ $note->title === __('app.note_untitled_val') ? '' : $note->title }}</textarea>

        {{-- Toolbar --}}
        <div class="tiptap-toolbar" id="tiptap-toolbar">
            {{-- Heading --}}
            <div class="ttb-dropdown" id="ttb-heading-wrap">
                <button class="ttb-dropdown-trigger" id="ttb-heading-trigger" type="button">
                    <span id="ttb-heading-label">{{ __('app.note_paragraph') }}</span>
                    <svg width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                </button>
                <div class="ttb-dropdown-menu" id="ttb-heading-menu">
                    <button type="button" data-heading="0" class="ttb-dropdown-item active">{{ __('app.note_paragraph') }}</button>
                    <button type="button" data-heading="1" class="ttb-dropdown-item" style="font-size:16px;font-weight:700">Título 1</button>
                    <button type="button" data-heading="2" class="ttb-dropdown-item" style="font-size:14px;font-weight:600">Título 2</button>
                    <button type="button" data-heading="3" class="ttb-dropdown-item" style="font-size:13px;font-weight:600">Título 3</button>
                </div>
            </div>
            {{-- Font picker --}}
            <div class="ttb-dropdown" id="ttb-font-wrap">
                <button class="ttb-dropdown-trigger" id="ttb-font-trigger" type="button" title="Fonte do editor">
                    <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M2 13L6 3l4 10M4 9.5h4M11 5v8M10 5h3"/></svg>
                    <span id="ttb-font-label" style="font-size:11px;min-width:52px;text-align:left">Sans</span>
                    <svg width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                </button>
                <div class="ttb-dropdown-menu" id="ttb-font-menu" style="min-width:160px">
                    {{-- populated by editor.js --}}
                </div>
            </div>
            <div class="ttb-sep"></div>
            {{-- Formatting --}}
            <button class="ttb-btn" data-cmd="bold"        title="Negrito (Ctrl+B)"><i class="fa fa-bold"></i></button>
            <button class="ttb-btn" data-cmd="italic"      title="Itálico (Ctrl+I)"><i class="fa fa-italic"></i></button>
            <button class="ttb-btn" data-cmd="underline"   title="Sublinhado (Ctrl+U)"><i class="fa fa-underline"></i></button>
            <button class="ttb-btn" data-cmd="strike"      title="Tachado"><i class="fa fa-strikethrough"></i></button>
            <button class="ttb-btn" data-cmd="highlight"   title="Realçar"><i class="fa fa-highlighter"></i></button>
            {{-- Text color --}}
            <div class="ttb-color-wrap">
                <button class="ttb-btn" id="ttb-color-trigger" title="Cor do texto" style="position:relative">
                    <i class="fa fa-font"></i>
                    <span id="ttb-color-bar" style="position:absolute;bottom:4px;left:5px;right:5px;height:2px;border-radius:1px;background:var(--accent)"></span>
                </button>
                <div class="ttb-color-palette" id="ttb-color-palette">
                    <div class="ttb-color-grid" id="ttb-color-grid">
                        @foreach(['#e05454','#f97316','#facc15','#4ade80','#34d399','#60a5fa','#818cf8','#c084fc','#f472b6','#f0f0f0','#94a3b8','#64748b','#334155','#1e293b','#0f172a'] as $sc)
                            <div class="ttb-color-swatch" style="background:{{ $sc }}" data-color="{{ $sc }}" title="{{ $sc }}"></div>
                        @endforeach
                    </div>
                    <button class="ttb-color-remove" id="ttb-color-remove">✕ Remover cor</button>
                </div>
            </div>
            <div class="ttb-sep"></div>
            {{-- Subscript / Superscript --}}
            <button class="ttb-btn" data-cmd="subscript"   title="Subscrito"><i class="fa fa-subscript"></i></button>
            <button class="ttb-btn" data-cmd="superscript" title="Sobrescrito"><i class="fa fa-superscript"></i></button>
            <div class="ttb-sep"></div>
            {{-- Alignment --}}
            <button class="ttb-btn" data-cmd="alignLeft"   title="Alinhar à esquerda"><i class="fa fa-align-left"></i></button>
            <button class="ttb-btn" data-cmd="alignCenter" title="Centralizar"><i class="fa fa-align-center"></i></button>
            <button class="ttb-btn" data-cmd="alignRight"  title="Alinhar à direita"><i class="fa fa-align-right"></i></button>
            <button class="ttb-btn" data-cmd="alignJustify" title="Justificar"><i class="fa fa-align-justify"></i></button>
            <div class="ttb-sep"></div>
            {{-- Lists --}}
            <button class="ttb-btn" data-cmd="bulletList"  title="Lista com marcadores"><i class="fa fa-list-ul"></i></button>
            <button class="ttb-btn" data-cmd="orderedList" title="Lista numerada"><i class="fa fa-list-ol"></i></button>
            <button class="ttb-btn" data-cmd="taskList"    title="Lista de tarefas"><i class="fa fa-check-square"></i></button>
            <div class="ttb-sep"></div>
            {{-- Blocks --}}
            <button class="ttb-btn" data-cmd="blockquote"  title="Citação"><i class="fa fa-quote-left"></i></button>
            <button class="ttb-btn" data-cmd="codeBlock"   title="Bloco de código"><i class="fa fa-code"></i></button>
            <button class="ttb-btn" data-cmd="horizontalRule" title="Linha horizontal"><i class="fa fa-minus"></i></button>
            {{-- Callout dropdown --}}
            <div class="ttb-dropdown" id="ttb-callout-wrap">
                <button class="ttb-dropdown-trigger ttb-callout-trigger-btn" id="ttb-callout-trigger" type="button" title="Inserir callout">
                    <span id="ttb-callout-icon" style="font-size:15px;line-height:1">💬</span>
                    <svg width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                </button>
                <div class="ttb-dropdown-menu" id="ttb-callout-menu" style="min-width:148px">
                    <button type="button" class="ttb-dropdown-item ttb-callout-item" data-callout="info">
                        <span><svg width="16" height="16" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="8" fill="#3b82f6"/><rect x="8.2" y="8" width="1.6" height="5.5" rx=".8" fill="white"/><circle cx="9" cy="5.5" r="1.1" fill="white"/></svg></span> Info
                    </button>
                    <button type="button" class="ttb-dropdown-item ttb-callout-item" data-callout="success">
                        <span><svg width="16" height="16" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="8" fill="#22c55e"/><path d="M5.5 9.5l2.5 2.5 5-5" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span> Sucesso
                    </button>
                    <button type="button" class="ttb-dropdown-item ttb-callout-item" data-callout="warning">
                        <span><svg width="16" height="16" viewBox="0 0 18 18" fill="none"><path d="M9 2L16.5 15H1.5L9 2Z" fill="#f59e0b"/><rect x="8.2" y="7" width="1.6" height="4" rx=".8" fill="white"/><circle cx="9" cy="13" r=".9" fill="white"/></svg></span> Aviso
                    </button>
                    <button type="button" class="ttb-dropdown-item ttb-callout-item" data-callout="danger">
                        <span><svg width="16" height="16" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="8" fill="#ef4444"/><path d="M6 6l6 6M12 6l-6 6" stroke="white" stroke-width="1.8" stroke-linecap="round"/></svg></span> Perigo
                    </button>
                    <button type="button" class="ttb-dropdown-item ttb-callout-item" data-callout="tip">
                        <span><svg width="16" height="16" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="8" fill="#a855f7"/><path d="M9 5a3.5 3.5 0 011.2 6.8V13H7.8v-1.2A3.5 3.5 0 019 5z" fill="white"/><rect x="8.2" y="13.5" width="1.6" height="1.5" rx=".6" fill="white"/></svg></span> Dica
                    </button>
                    <button type="button" class="ttb-dropdown-item ttb-callout-item" data-callout="note">
                        <span><svg width="16" height="16" viewBox="0 0 18 18" fill="none"><rect x="1" y="1" width="16" height="16" rx="3.5" fill="#64748b"/><path d="M5 6.5h8M5 9h8M5 11.5h5" stroke="white" stroke-width="1.5" stroke-linecap="round"/></svg></span> Nota
                    </button>
                </div>
            </div>
            <div class="ttb-sep"></div>
            {{-- Table --}}
            <button class="ttb-btn" data-cmd="insertTable" title="Inserir tabela"><i class="fa fa-table"></i></button>
            <div class="ttb-sep"></div>
            {{-- Link, Image & Emoji --}}
            <button class="ttb-btn" data-cmd="link"        title="Link"><i class="fa fa-link"></i></button>
            <button class="ttb-btn" data-cmd="image"       title="Imagem"><i class="fa fa-image"></i></button>
            <button class="ttb-btn" data-cmd="emoji"       title="Emoji" style="font-size:14px">😀</button>
            <div class="ttb-sep"></div>
            {{-- Undo/Redo --}}
            <button class="ttb-btn" data-cmd="undo"  title="Desfazer (Ctrl+Z)"><i class="fa fa-undo"></i></button>
            <button class="ttb-btn" data-cmd="redo"  title="Refazer (Ctrl+Y)"><i class="fa fa-redo"></i></button>
        </div>

        {{-- Editor --}}
        <div class="tiptap-wrap">
            <div id="tiptap-editor"></div>
        </div>

        {{-- Word count bar --}}
        <div class="note-wordcount-bar">
            <span>
                <svg width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 4h12M2 8h8M2 12h10"/></svg>
                <span id="stat-words-bar">0 palavras</span>
            </span>
            <span>
                <svg width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="2" width="12" height="12" rx="2"/><path d="M5 8h6M8 5v6"/></svg>
                <span id="stat-chars-bar">0 caracteres</span>
            </span>
            <span>
                <svg width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="8" cy="8" r="6"/><path d="M8 5v3l2 2"/></svg>
                <span id="stat-read-bar">0 min leitura</span>
            </span>
        </div>
    </div>
</div>

@endsection

@push('modals')
{{-- Bubble menu (aparece ao selecionar texto) --}}
<div id="bubble-menu" style="
    display:none; position:fixed; z-index:9997;
    background:var(--surface); border:1px solid var(--border);
    border-radius:10px; padding:4px;
    box-shadow:0 8px 30px rgba(0,0,0,.5);
    align-items:center; gap:1px;
    pointer-events:all;
">
    <button class="bbl-btn" data-mark="bold"      title="Negrito"><i class="fa fa-bold"></i></button>
    <button class="bbl-btn" data-mark="italic"    title="Itálico"><i class="fa fa-italic"></i></button>
    <button class="bbl-btn" data-mark="underline" title="Sublinhado"><i class="fa fa-underline"></i></button>
    <button class="bbl-btn" data-mark="strike"    title="Tachado"><i class="fa fa-strikethrough"></i></button>
    <button class="bbl-btn" data-mark="highlight" title="Realçar"><i class="fa fa-highlighter"></i></button>
    <div style="width:1px;height:16px;background:var(--border);margin:0 2px"></div>
    <button class="bbl-btn" data-mark="link"      title="Link"><i class="fa fa-link"></i></button>
</div>

{{-- Image insert popover --}}
<div id="image-popover">
    <div class="img-popover-title">🖼 Inserir imagem</div>
    <div class="img-tabs">
        <button id="img-tab-url"    class="img-tab active">🔗 URL</button>
        <button id="img-tab-upload" class="img-tab">📁 Upload</button>
    </div>
    <div id="img-panel-url">
        <input id="img-url-input"  type="url"  placeholder="{{ __('app.note_img_url_ph') }}">
        <input id="img-alt-input"  type="text" placeholder="{{ __('app.note_img_alt_ph') }}" style="margin-bottom:4px">
    </div>
    <div id="img-panel-upload" style="display:none">
        <label id="img-drop-zone">
            <span style="font-size:26px;line-height:1">🖼️</span>
            <span id="img-drop-label">{{ __('app.note_click_drag_image') }}</span>
            <span style="font-size:11px;opacity:.6">PNG, JPG, GIF, WebP</span>
            <input id="img-file-input" type="file" accept="image/*" style="display:none">
        </label>
        <div id="img-preview-wrap" style="display:none;margin-bottom:8px;text-align:center">
            <img id="img-preview" style="max-height:90px;border-radius:8px;max-width:100%;box-shadow:0 4px 12px rgba(0,0,0,.3)">
        </div>
    </div>
    <div class="img-popover-footer">
        <button class="btn btn-ghost btn-sm" id="img-cancel-btn">Cancelar</button>
        <button class="btn btn-primary btn-sm" id="img-insert-btn">Inserir imagem</button>
    </div>
</div>

{{-- Link popover --}}
<div id="link-popover">
    <div class="link-popover-title">🔗 Inserir link</div>
    <p class="link-popover-hint">Cole a URL abaixo. O texto selecionado no editor será usado como rótulo.</p>
    <input id="link-url-input"  type="url"  placeholder="https://exemplo.com">
    <input id="link-text-input" type="text" placeholder="{{ __('app.note_link_display_text') }}">
    <div class="link-popover-footer">
        <button class="btn btn-ghost btn-sm" id="link-remove-btn" title="Remover link">🗑 Remover</button>
        <button class="btn btn-ghost btn-sm" id="link-cancel-btn">Cancelar</button>
        <button class="btn btn-primary btn-sm" id="link-insert-btn">Inserir link</button>
    </div>
</div>
@endpush

@push('scripts')
<script>
// ── Sidebar toggle ────────────────────────────────────────────────────────────
(function() {
    const sidebar   = document.getElementById('note-sidebar');
    const toggleBtn = document.getElementById('sidebar-toggle');
    const arrow     = document.getElementById('sidebar-toggle-arrow');
    if (!sidebar || !toggleBtn) return;

    const SIDEBAR_W = 220; // must match CSS width
    let collapsed = localStorage.getItem('note-sidebar-collapsed') === '1';

    function applyState(animated) {
        if (!animated) {
            toggleBtn.style.transition = 'background .15s, color .15s, width .15s';
        }
        if (collapsed) {
            sidebar.classList.add('collapsed');
            toggleBtn.style.left = '0px';
            if (arrow) arrow.setAttribute('d', 'M3.5 1.5L6 4.5l-2.5 3');
            toggleBtn.title = 'Mostrar painel lateral';
        } else {
            sidebar.classList.remove('collapsed');
            toggleBtn.style.left = SIDEBAR_W + 'px';
            if (arrow) arrow.setAttribute('d', 'M5.5 1.5L3 4.5l2.5 3');
            toggleBtn.title = 'Esconder painel lateral';
        }
        if (!animated) {
            requestAnimationFrame(() => {
                toggleBtn.style.transition = 'background .15s, color .15s, width .15s, left .28s cubic-bezier(.4,0,.2,1)';
            });
        }
    }

    applyState(false);

    toggleBtn.addEventListener('click', () => {
        collapsed = !collapsed;
        localStorage.setItem('note-sidebar-collapsed', collapsed ? '1' : '0');
        applyState(true);
    });
})();
</script>
<script>
// ── Word count bar sync ───────────────────────────────────────────────────────
// Mirror stat updates from editor.js to the bottom bar
const _statObs = new MutationObserver(() => {
    const w = document.getElementById('stat-words');
    const c = document.getElementById('stat-chars');
    const r = document.getElementById('stat-read');
    const wb = document.getElementById('stat-words-bar');
    const cb = document.getElementById('stat-chars-bar');
    const rb = document.getElementById('stat-read-bar');
    if (w && wb) wb.textContent = w.textContent;
    if (c && cb) cb.textContent = c.textContent;
    if (r && rb) rb.textContent = r.textContent;
});
document.addEventListener('DOMContentLoaded', () => {
    const stats = document.getElementById('note-stats');
    if (stats) _statObs.observe(stats, { childList: true, subtree: true, characterData: true });
});
</script>
    words:   '{{ __('app.note_words') }}',
    chars:   '{{ __('app.note_chars') }}',
    read_min:'{{ __('app.note_read_min') }}',
};
</script>
<script>
window.__NOTE__ = {
    noteId:  {{ $note->id }},
    csrf:    document.querySelector('meta[name=csrf-token]').content,
    content: @json($note->content ?: ''),
    tags:    @json($note->tags_array),
    allTags: @json($allTags),
};
</script>
@vite('resources/js/editor.js')
@endpush