@extends('layouts.app')
@section('page-title', 'Nota')

@section('topbar-actions')
    <div style="display:flex;align-items:center;gap:8px">
        <span id="save-status" style="font-size:12px;color:var(--muted);font-family:'DM Sans',monospace;transition:opacity .3s"></span>
        <button id="btn-pin" class="btn btn-ghost btn-sm" title="{{ $note->pinned ? 'Desafixar' : 'Fixar nota' }}">
            {{ $note->pinned ? '📌 Fixada' : '📌 Fixar' }}
        </button>
        <button id="btn-delete" class="btn btn-danger btn-sm">Excluir</button>
        <a href="/notes" class="btn btn-ghost btn-sm">← Voltar</a>
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
}

/* ── Sidebar ──────────────────────────────────────────────────────────── */
.note-sidebar {
    width: 220px; flex-shrink: 0;
    background: var(--surface);
    border-right: 1px solid var(--border);
    display: flex; flex-direction: column; overflow: hidden;
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
}

/* Title */
.note-title-input {
    width: 100%; background: transparent;
    border: none; outline: none;
    font-family: 'Codec Pro', sans-serif;
    font-size: 26px; font-weight: 700; letter-spacing: -0.5px; color: var(--text);
    padding: 28px 40px 12px; line-height: 1.2;
    border-bottom: 1px solid var(--border);
    resize: none; overflow: hidden;
}
.note-title-input::placeholder { color: var(--muted); opacity: .5; }

/* Toolbar */
.tiptap-toolbar {
    display: flex; align-items: center; gap: 2px;
    padding: 7px 40px;
    border-bottom: 1px solid var(--border);
    background: var(--surface);
    flex-wrap: wrap; flex-shrink: 0;
}
.ttb-btn {
    width: 30px; height: 30px;
    display: flex; align-items: center; justify-content: center;
    border-radius: 6px; border: none; background: none;
    color: var(--muted); cursor: pointer; font-size: 13px;
    transition: background .12s, color .12s, transform .1s;
    flex-shrink: 0;
}
.ttb-btn:hover  { background: rgba(255,145,77,.12); color: var(--text); transform: scale(1.05); }
.ttb-btn.active { background: rgba(255,145,77,.2); color: var(--accent); }
.ttb-sep { width: 1px; height: 18px; background: var(--border); margin: 0 4px; flex-shrink: 0; }
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
    padding: 32px 40px 60px;
    scrollbar-width: thin;
    scrollbar-color: var(--border) transparent;
}

/* ── ProseMirror content ──────────────────────────────────────────────── */
.ProseMirror {
    min-height: 400px; outline: none;
    font-size: 14.5px; line-height: 1.8;
    color: var(--text); max-width: 780px;
}
.ProseMirror p { margin: 0 0 6px; }
.ProseMirror p.is-editor-empty:first-child::before {
    content: attr(data-placeholder);
    color: var(--muted); opacity: .5;
    pointer-events: none; height: 0; float: left;
}
.ProseMirror h1 { font-family: 'Codec Pro', sans-serif; font-size: 24px; font-weight: 700; letter-spacing: -0.4px; margin: 20px 0 8px; line-height: 1.2; color: var(--text); }
.ProseMirror h2 { font-family: 'Codec Pro', sans-serif; font-size: 19px; font-weight: 700; letter-spacing: -0.3px; margin: 18px 0 6px; line-height: 1.3; color: var(--text); }
.ProseMirror h3 { font-family: 'Codec Pro', sans-serif; font-size: 15px; font-weight: 700; letter-spacing: -0.1px; margin: 14px 0 4px; color: var(--text); }
.ProseMirror strong { font-weight: 600; }
.ProseMirror em     { font-style: italic; }
.ProseMirror u      { text-decoration: underline; text-underline-offset: 2px; }
.ProseMirror s      { text-decoration: line-through; color: var(--muted); }
.ProseMirror mark   { background: rgba(250,204,21,.3); color: var(--text); border-radius: 3px; padding: 1px 3px; }
.ProseMirror ul, .ProseMirror ol { padding-left: 22px; margin: 6px 0; }
.ProseMirror li { margin: 3px 0; line-height: 1.7; }
.ProseMirror li p { margin: 0; }

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
    border-left: 3px solid var(--accent); padding: 4px 0 4px 16px;
    margin: 10px 0; color: var(--muted); font-style: italic;
}
.ProseMirror code {
    background: rgba(0,0,0,.25); border-radius: 4px;
    font-family: 'DM Sans', monospace; font-size: 12.5px;
    color: var(--accent); padding: 1px 5px;
}
.ProseMirror pre {
    background: rgba(0,0,0,.35); border-radius: 10px;
    padding: 14px 18px; margin: 10px 0; overflow-x: auto;
}
.ProseMirror pre code { background: none; padding: 0; font-size: 12.5px; color: #e2e2e8; line-height: 1.6; }
.ProseMirror hr { border: none; border-top: 1px solid var(--border); margin: 24px 0; }
.ProseMirror a { color: var(--accent); text-decoration: underline; text-underline-offset: 2px; }
.ProseMirror a:hover { opacity: .8; }

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

/* ── Light theme overrides ───────────────────────────────────────────── */
html[data-theme=light] .note-editor-area    { background: #f6f6f9; }
html[data-theme=light] .note-sidebar        { background: #ffffff; }
html[data-theme=light] .note-title-input    { background: #f6f6f9; color: var(--text); }
html[data-theme=light] .tiptap-toolbar      { background: #ffffff; }
html[data-theme=light] .tiptap-wrap         { background: #f6f6f9; }
html[data-theme=light] .ProseMirror         { color: var(--text); }
html[data-theme=light] .ProseMirror code    { background: rgba(0,0,0,.06); color: #c0450a; }
html[data-theme=light] .ProseMirror pre     { background: #eeeef4; }
html[data-theme=light] .ProseMirror pre code { color: #2d2d3a; }

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
#slash-menu::-webkit-scrollbar { width: 4px; }
#slash-menu::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }
</style>
@endpush

@section('content')
<div class="note-shell">

    {{-- Left sidebar: metadata --}}
    <div class="note-sidebar">
        <div class="note-sidebar-section">Informações</div>

        <div class="note-meta-row">
            <span class="note-meta-label">Criada</span>
            <span class="note-meta-value" style="font-family:'DM Sans',monospace;font-size:11px">{{ $note->created_at->format('d/m/Y') }}</span>
        </div>
        <div class="note-meta-row">
            <span class="note-meta-label">Editada</span>
            <span class="note-meta-value" id="sidebar-updated" style="font-family:'DM Sans',monospace;font-size:11px">{{ $note->updated_at->format('d/m/Y H:i') }}</span>
        </div>

        <div class="note-sidebar-section" style="margin-top:8px">Cor</div>
        <div class="note-colors" id="note-colors">
            @php
                $colors = ['#ff914d','#e05454','#4ade80','#60a5fa','#c084fc','#f472b6','#facc15','#34d399','#94a3b8','#f0a05a'];
            @endphp
            @foreach($colors as $c)
                <div class="note-color-dot {{ $note->color === $c ? 'active' : '' }}"
                    style="background:{{ $c }}" data-color="{{ $c }}"></div>
            @endforeach
        </div>

        <div class="note-sidebar-section">Categoria</div>
        <div style="padding:0 12px 12px">
            <div class="select-wrap">
                <select id="note-category">
                    <option value="">— Nenhuma —</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->name }}" data-color="{{ $cat->color }}" data-icon="{{ $cat->icon }}"
                            {{ $note->category === $cat->name ? 'selected' : '' }}>
                            {{ $cat->icon }} {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="note-stats" id="note-stats">
            <span id="stat-words">0 palavras</span>
            <span id="stat-chars">0 caracteres</span>
            <span id="stat-read">0 min leitura</span>
        </div>
    </div>

    {{-- Main editor --}}
    <div class="note-editor-area">
        <textarea id="note-title" class="note-title-input" rows="1"
            placeholder="Título da nota…">{{ $note->title === 'Sem título' ? '' : $note->title }}</textarea>

        {{-- Toolbar --}}
        <div class="tiptap-toolbar" id="tiptap-toolbar">
            {{-- Heading --}}
            <div class="ttb-dropdown" id="ttb-heading-wrap">
                <button class="ttb-dropdown-trigger" id="ttb-heading-trigger" type="button">
                    <span id="ttb-heading-label">Parágrafo</span>
                    <svg width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                </button>
                <div class="ttb-dropdown-menu" id="ttb-heading-menu">
                    <button type="button" data-heading="0" class="ttb-dropdown-item active">Parágrafo</button>
                    <button type="button" data-heading="1" class="ttb-dropdown-item" style="font-size:16px;font-weight:700">Título 1</button>
                    <button type="button" data-heading="2" class="ttb-dropdown-item" style="font-size:14px;font-weight:600">Título 2</button>
                    <button type="button" data-heading="3" class="ttb-dropdown-item" style="font-size:13px;font-weight:600">Título 3</button>
                </div>
            </div>
            <div class="ttb-sep"></div>
            {{-- Formatting --}}
            <button class="ttb-btn" data-cmd="bold"        title="Negrito (Ctrl+B)"><i class="fa fa-bold"></i></button>
            <button class="ttb-btn" data-cmd="italic"      title="Itálico (Ctrl+I)"><i class="fa fa-italic"></i></button>
            <button class="ttb-btn" data-cmd="underline"   title="Sublinhado (Ctrl+U)"><i class="fa fa-underline"></i></button>
            <button class="ttb-btn" data-cmd="strike"      title="Tachado"><i class="fa fa-strikethrough"></i></button>
            <button class="ttb-btn" data-cmd="highlight"   title="Realçar"><i class="fa fa-highlighter"></i></button>
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
            <div class="ttb-sep"></div>
            {{-- Table --}}
            <button class="ttb-btn" data-cmd="insertTable" title="Inserir tabela"><i class="fa fa-table"></i></button>
            <button class="ttb-btn" data-cmd="addRowAfter" title="Adicionar linha"><i class="fa fa-plus"></i></button>
            <button class="ttb-btn" data-cmd="deleteRow"   title="Remover linha"><i class="fa fa-trash" style="font-size:11px"></i></button>
            <div class="ttb-sep"></div>
            {{-- Link & Image --}}
            <button class="ttb-btn" data-cmd="link"        title="Link"><i class="fa fa-link"></i></button>
            <button class="ttb-btn" data-cmd="image"       title="Imagem (URL)"><i class="fa fa-image"></i></button>
            <div class="ttb-sep"></div>
            {{-- Undo/Redo --}}
            <button class="ttb-btn" data-cmd="undo"  title="Desfazer (Ctrl+Z)"><i class="fa fa-undo"></i></button>
            <button class="ttb-btn" data-cmd="redo"  title="Refazer (Ctrl+Y)"><i class="fa fa-redo"></i></button>
        </div>

        {{-- Editor --}}
        <div class="tiptap-wrap">
            <div id="tiptap-editor"></div>
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
        <input id="img-url-input"  type="url"  placeholder="https://exemplo.com/imagem.jpg">
        <input id="img-alt-input"  type="text" placeholder="Texto alternativo (opcional)" style="margin-bottom:4px">
    </div>
    <div id="img-panel-upload" style="display:none">
        <label id="img-drop-zone">
            <span style="font-size:26px;line-height:1">🖼️</span>
            <span id="img-drop-label">Clique ou arraste uma imagem</span>
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
    <input id="link-text-input" type="text" placeholder="Texto de exibição (opcional)">
    <div class="link-popover-footer">
        <button class="btn btn-ghost btn-sm" id="link-remove-btn" title="Remover link">🗑 Remover</button>
        <button class="btn btn-ghost btn-sm" id="link-cancel-btn">Cancelar</button>
        <button class="btn btn-primary btn-sm" id="link-insert-btn">Inserir link</button>
    </div>
</div>
@endpush

@push('scripts')
<script>
window.__NOTE__ = {
    noteId:  {{ $note->id }},
    csrf:    document.querySelector('meta[name=csrf-token]').content,
    content: @json($note->content ?: ''),
};
</script>
@vite('resources/js/editor.js')
@endpush