@extends('layouts.app')

@section('page-title', __('app.nav_categories'))

@section('topbar-actions')
    <button class="btn btn-primary" onclick="openModal()">
        <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M8 2v12M2 8h12" />
        </svg>
        {{ __('app.cat_new_btn') }}
    </button>
@endsection

@push('styles')
<style>
/* ── Category grid ───────────────────────────────────────────────────── */
.cat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 14px;
}

/* ── Card ────────────────────────────────────────────────────────────── */
.cat-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    position: relative;
    cursor: default;
    transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
    animation: catIn .22s cubic-bezier(.25,.46,.45,.94) both;
}

.cat-card:hover {
    border-color: var(--cat-color, var(--accent));
    box-shadow: 0 0 0 1px color-mix(in srgb, var(--cat-color, var(--accent)) 30%, transparent),
                0 8px 28px rgba(0,0,0,.18);
    transform: translateY(-2px);
}

@keyframes catIn {
    from { opacity: 0; transform: translateY(6px) scale(.98); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}

/* stagger cards */
.cat-card:nth-child(1) { animation-delay: 0ms; }
.cat-card:nth-child(2) { animation-delay: 40ms; }
.cat-card:nth-child(3) { animation-delay: 80ms; }
.cat-card:nth-child(4) { animation-delay: 120ms; }
.cat-card:nth-child(5) { animation-delay: 160ms; }
.cat-card:nth-child(6) { animation-delay: 200ms; }
.cat-card:nth-child(n+7) { animation-delay: 240ms; }

/* ── Colored top band with emoji ─────────────────────────────────────── */
.cat-card-header {
    padding: 22px 20px 16px;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    position: relative;
}

/* Subtle gradient wash from cat color */
.cat-card-header::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg,
        color-mix(in srgb, var(--cat-color, var(--accent)) 14%, transparent) 0%,
        transparent 70%);
    pointer-events: none;
}

.cat-icon-wrap {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    background: color-mix(in srgb, var(--cat-color, var(--accent)) 18%, transparent);
    border: 1px solid color-mix(in srgb, var(--cat-color, var(--accent)) 30%, transparent);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
    transition: transform .2s ease, background .2s ease;
}

.cat-card:hover .cat-icon-wrap {
    transform: scale(1.06);
    background: color-mix(in srgb, var(--cat-color, var(--accent)) 26%, transparent);
}

/* ── Hover actions (edit / delete) ───────────────────────────────────── */
.cat-actions {
    display: flex;
    gap: 5px;
    opacity: 0;
    transform: translateY(-4px);
    transition: opacity .18s ease, transform .18s ease;
    flex-shrink: 0;
    position: relative;
    z-index: 1;
}

.cat-card:hover .cat-actions {
    opacity: 1;
    transform: translateY(0);
}

.cat-action-btn {
    width: 28px;
    height: 28px;
    border-radius: 7px;
    border: 1px solid var(--border);
    background: var(--surface);
    color: var(--muted);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background .12s, color .12s, border-color .12s;
}

.cat-action-btn:hover {
    background: var(--surface2);
    color: var(--text);
    border-color: color-mix(in srgb, currentColor 30%, transparent);
}

.cat-action-btn.danger:hover {
    background: rgba(224,84,84,.12);
    color: var(--danger);
    border-color: rgba(224,84,84,.3);
}

/* ── Body ────────────────────────────────────────────────────────────── */
.cat-card-body {
    padding: 4px 20px 16px;
    flex: 1;
    display: flex;
    flex-direction: column;
    border-top: 1px solid var(--border);
}

.cat-card-name {
    font-family: 'Montserrat', sans-serif;
    font-size: 14px;
    font-weight: 700;
    color: var(--text);
    letter-spacing: -.2px;
    margin-bottom: 4px;
    margin-top: 4px;
    line-height: 1.25;
}

.cat-card-desc {
    font-size: 11.5px;
    color: var(--muted);
    line-height: 1.6;
    margin-bottom: 14px;
    flex: 1;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* ── Task count + mini bar ───────────────────────────────────────────── */
.cat-card-count-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
}

.cat-card-count {
    font-size: 10px;
    color: var(--muted);
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 5px;
}

.cat-card-count strong {
    font-size: 13px;
    font-weight: 700;
    color: var(--text);
    font-family: 'Montserrat', sans-serif;
}

.cat-mini-bar {
    height: 3px;
    background: color-mix(in srgb, var(--cat-color, var(--accent)) 15%, var(--border));
    border-radius: 99px;
    overflow: hidden;
}

.cat-mini-bar-fill {
    height: 100%;
    border-radius: 99px;
    background: var(--cat-color, var(--accent));
    width: 0;
    transition: width 1s cubic-bezier(.34,1.2,.64,1);
}

/* ── Footer link ─────────────────────────────────────────────────────── */
.cat-card-footer {
    padding: 10px 20px 14px;
    border-top: 1px solid var(--border);
}

.cat-card-link {
    font-size: 11px;
    color: var(--muted);
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    letter-spacing: .1px;
    transition: color .15s, gap .15s;
}

.cat-card-link:hover {
    color: var(--cat-color, var(--accent));
    gap: 8px;
}

/* ── Empty state ─────────────────────────────────────────────────────── */
.cat-empty {
    grid-column: 1 / -1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 72px 20px;
    text-align: center;
    color: var(--muted);
    border: 1px dashed var(--border);
    border-radius: 14px;
    gap: 12px;
}

.cat-empty-icon {
    font-size: 40px;
    opacity: .35;
    margin-bottom: 4px;
}

.cat-empty p {
    font-size: 13px;
    max-width: 280px;
    line-height: 1.6;
}

/* ── New category card (ghost slot) ─────────────────────────────────── */
.cat-card-new {
    border: 1.5px dashed var(--border);
    background: none;
    border-radius: 14px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 32px 20px;
    cursor: pointer;
    color: var(--muted);
    font-size: 12px;
    font-weight: 600;
    font-family: inherit;
    transition: border-color .2s, color .2s, background .2s;
    min-height: 160px;
    animation: catIn .22s cubic-bezier(.25,.46,.45,.94) both;
}

.cat-card-new:hover {
    border-color: var(--accent);
    color: var(--accent);
    background: rgba(255,145,77,.04);
}

.cat-card-new svg {
    opacity: .4;
    transition: opacity .2s, transform .2s;
}

.cat-card-new:hover svg {
    opacity: 1;
    transform: scale(1.1) rotate(90deg);
}
</style>
@endpush

@section('content')

    <div class="cat-grid">

        @if($categories->isEmpty())
            <div class="cat-empty">
                <div class="cat-empty-icon">🗂</div>
                <p>{{ __('app.cat_none') }}</p>
                <button class="btn btn-primary" onclick="openModal()">Criar categoria</button>
            </div>
        @else
            @foreach($categories as $i => $cat)
                @php
                    $maxTasks = $categories->max('tasks_count') ?: 1;
                    $barPct   = round($cat->tasks_count / $maxTasks * 100);
                @endphp
                <div class="cat-card" style="--cat-color:{{ $cat->color }};animation-delay:{{ $i * 45 }}ms">

                    {{-- Header: icon + actions --}}
                    <div class="cat-card-header">
                        <div class="cat-icon-wrap">{{ $cat->icon ?: '📁' }}</div>
                        <div class="cat-actions">
                            <button class="cat-action-btn"
                                onclick="openEdit({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ $cat->color }}', '{{ addslashes($cat->icon) }}', '{{ addslashes($cat->description ?? '') }}')"
                                title="{{ __('app.cat_edit_btn') }}">
                                <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round">
                                    <path d="M11.5 2.5a1.5 1.5 0 012.12 2.12L5 13.24 2 14l.76-3L11.5 2.5z"/>
                                </svg>
                            </button>
                            <form method="POST" action="/categories/{{ $cat->id }}" id="del-form-{{ $cat->id }}" onsubmit="return false">
                                @csrf @method('DELETE')
                                <button type="button" class="cat-action-btn danger"
                                    onclick="confirmDelete({{ $cat->id }}, '{{ addslashes($cat->name) }}')"
                                    title="{{ __('app.cat_delete_btn') ?? 'Excluir' }}">
                                    <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round">
                                        <path d="M2 4h12M5 4V2.5h6V4M6 7v5M10 7v5M3 4l1 9.5h8L13 4"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="cat-card-body">
                        <div class="cat-card-name">{{ $cat->name }}</div>
                        @if($cat->description)
                            <div class="cat-card-desc">{{ $cat->description }}</div>
                        @else
                            <div class="cat-card-desc" style="opacity:.4;font-style:italic">Sem descrição</div>
                        @endif

                        <div class="cat-card-count-row">
                            <div class="cat-card-count">
                                <strong>{{ $cat->tasks_count }}</strong>
                                {{ __('app.cat_tasks_count') }}
                            </div>
                        </div>
                        <div class="cat-mini-bar">
                            <div class="cat-mini-bar-fill" data-w="{{ $barPct }}"></div>
                        </div>
                    </div>

                    <div class="cat-card-footer">
                        <a href="/tasks?category={{ $cat->id }}" class="cat-card-link">
                            {{ __('app.cat_view_tasks') }}
                            <svg width="11" height="11" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                                <path d="M2 6h8M6 2l4 4-4 4"/>
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach

            {{-- Ghost "new" slot --}}
            <button class="cat-card-new" onclick="openModal()" style="animation-delay:{{ $categories->count() * 45 }}ms">
                <svg width="22" height="22" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round">
                    <path d="M8 2v12M2 8h12"/>
                </svg>
                {{ __('app.cat_new_btn') }}
            </button>
        @endif

    </div>

@endsection

@push('scripts')
    <style>

        #modal-cat-portal .modal {
            max-width: 500px;
            overflow: visible;
        }

        /* Color picker */
        .color-picker-wrap {
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .color-preview {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 10px;
            cursor: pointer;
            transition: border-color .15s;
            height: 42px;
            position: relative;
            overflow: hidden;
        }

        .color-preview:hover {
            border-color: color-mix(in srgb, var(--border) 60%, var(--muted));
        }

        .color-preview:focus-within {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(255, 145, 77, .15);
        }

        .color-swatch {
            width: 22px;
            height: 22px;
            border-radius: 6px;
            border: 1px solid rgba(255, 255, 255, .1);
            flex-shrink: 0;
            transition: background .15s;
            position: relative;
            z-index: 1;
            pointer-events: none;
        }

        .color-hex {
            font-family: 'Montserrat', sans-serif;
            font-size: 12px;
            color: var(--text);
            text-transform: uppercase;
            flex: 1;
            position: relative;
            z-index: 1;
            pointer-events: none;
        }

        .color-preview input[type=color] {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
            border: none;
            padding: 0;
            z-index: 2;
        }

        .color-presets {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin-top: 8px;
        }

        .color-preset {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            border: 2px solid transparent;
            cursor: pointer;
            transition: transform .15s, border-color .15s;
            flex-shrink: 0;
        }

        .color-preset:hover {
            transform: scale(1.15);
        }

        .color-preset.active {
            border-color: white;
            transform: scale(1.1);
        }

        /* Emoji picker */
        .emoji-trigger {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 8px 14px;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 10px;
            cursor: pointer;
            height: 42px;
            transition: border-color .15s, box-shadow .15s;
            user-select: none;
        }

        .emoji-trigger:hover {
            border-color: color-mix(in srgb, var(--border) 60%, var(--muted));
        }

        .emoji-trigger.open,
        .emoji-trigger:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(255, 145, 77, .1);
            outline: none;
        }

        .emoji-trigger-preview {
            font-size: 17px;
            line-height: 1;
        }

        .emoji-trigger-label {
            font-size: 12px;
            color: var(--muted);
            flex: 1;
        }

        .emoji-trigger-arrow {
            color: var(--muted);
            transition: transform .2s, color .15s;
            font-size: 10px;
        }

        .emoji-trigger.open .emoji-trigger-arrow {
            transform: rotate(180deg);
            color: var(--accent);
        }

        .emoji-panel {
            display: none;
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            right: 0;
            z-index: 99999;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, .5);
            animation: cselDropIn .15s ease;
        }

        .emoji-panel.open {
            display: block;
        }

        .emoji-search-wrap {
            padding: 10px 12px 8px;
            border-bottom: 1px solid var(--border);
        }

        .emoji-search {
            width: 100%;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            padding: 6px 10px;
            font-size: 12px;
            font-family: inherit;
            outline: none;
            transition: border-color .15s;
        }

        .emoji-search:focus {
            border-color: var(--accent);
        }

        .emoji-search::placeholder {
            color: var(--muted);
        }

        .emoji-tabs {
            display: flex;
            gap: 2px;
            padding: 8px 10px 0;
            overflow-x: auto;
            scrollbar-width: none;
        }

        .emoji-tabs::-webkit-scrollbar {
            display: none;
        }

        .emoji-tab {
            flex-shrink: 0;
            padding: 5px 8px;
            font-size: 16px;
            border-radius: 7px;
            cursor: pointer;
            background: none;
            border: none;
            transition: background .12s;
            line-height: 1;
            title: attr(data-label);
        }

        .emoji-tab:hover {
            background: rgba(255, 145, 77, .1);
        }

        .emoji-tab.active {
            background: rgba(255, 145, 77, .18);
        }

        .emoji-grid-wrap {
            padding: 8px 10px 10px;
            max-height: 200px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--border) transparent;
        }

        .emoji-section-label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: .6px;
            text-transform: uppercase;
            color: var(--muted);
            padding: 4px 2px 6px;
            display: block;
        }

        .emoji-grid {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 2px;
        }

        .emoji-btn {
            font-size: 17px;
            line-height: 1;
            padding: 5px;
            border-radius: 7px;
            cursor: pointer;
            border: none;
            background: none;
            transition: background .1s, transform .1s;
            text-align: center;
        }

        .emoji-btn:hover {
            background: rgba(255, 145, 77, .12);
            transform: scale(1.15);
        }

        .emoji-btn.selected {
            background: rgba(255, 145, 77, .22);
        }

        .emoji-empty {
            color: var(--muted);
            font-size: 13px;
            padding: 20px;
            text-align: center;
            grid-column: 4 / -4;
            width: 300px;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.body.appendChild(document.getElementById('modal-cat-portal'));
            // Animate mini bars
            setTimeout(() => {
                document.querySelectorAll('.cat-mini-bar-fill[data-w]').forEach(el => {
                    el.style.width = el.dataset.w + '%';
                });
            }, 300);
        });

        // ── Emoji data ────────────────────────────────────────────────────────────────
        const EMOJI_CATS = [
            { id: 'popular', label: '⭐', name: '{{ __('app.cat_popular') }}', emojis: ['📁', '📂', '🗂', '💼', '🏠', '🏢', '🎯', '✅', '📌', '🔖', '💡', '🚀', '🎨', '🛠', '💰', '📊', '📈', '📋', '🔔', '❤️', '⭐', '🌟', '🔥', '💪', '🎓', '🏆', '📝', '🔍', '💬', '🌐'] },
            { id: 'work', label: '💼', name: '{{ __('app.cat_work') }}', emojis: ['💼', '🏢', '📊', '📈', '📉', '💹', '📋', '📌', '📍', '🗓', '📅', '⏰', '🔔', '📧', '📨', '📩', '💡', '🔑', '🔒', '🖥', '💻', '🖱', '⌨️', '🖨', '📠', '📞', '☎️', '🔧', '🔨', '⚙️', '🛠', '📐', '📏', '✏️', '🖊', '🖋', '📝', '📓', '📔', '📒', '📕', '📗', '📘', '📙', '📚', '🗃', '🗄', '🗑'] },
            { id: 'personal', label: '🏠', name: '{{ __('app.cat_personal') }}', emojis: ['🏠', '🏡', '🛒', '🛍', '🍽', '🍳', '🥗', '🏃', '🏋', '🧘', '🚗', '✈️', '🏖', '🎮', '🎬', '🎵', '📚', '🎯', '🎁', '❤️', '👨‍👩‍👧', '🐶', '🐱', '🌱', '🌺', '☀️', '⛅', '🌙', '🌈'] },
            { id: 'health', label: '❤️', name: '{{ __('app.cat_health') }}', emojis: ['❤️', '🏥', '💊', '🩺', '🩻', '💉', '🧬', '🦷', '👁', '🏃', '🏋', '🧘', '🚴', '🥗', '🥦', '🍎', '💧', '😴', '🧠', '💪', '🫁', '🫀', '🦴', '🩹', '🩼', '🏊', '⚽', '🎾', '🏓', '🧗'] },
            { id: 'finance', label: '💰', name: '{{ __('app.cat_finance') }}', emojis: ['💰', '💵', '💴', '💶', '💷', '💳', '🏦', '📊', '📈', '📉', '💹', '🪙', '💎', '🏠', '🚗', '✈️', '🛒', '🧾', '📋', '💼', '🤝', '📌', '🔐', '⚖️', '🪙', '💱', '📦', '🎁', '🛡', '🏆'] },
            { id: 'study', label: '🎓', name: '{{ __('app.cat_study') }}', emojis: ['🎓', '📚', '📖', '📝', '✏️', '🖊', '📐', '📏', '🔬', '🔭', '🧪', '🧫', '🧬', '💡', '🏫', '📓', '📔', '📒', '📕', '📗', '📘', '📙', '🗒', '📄', '🗂', '📊', '🖥', '💻', '🎯', '🏆', '⭐', '🌟'] },
            { id: 'tech', label: '💻', name: '{{ __('app.cat_tech') }}', emojis: ['💻', '🖥', '📱', '⌨️', '🖱', '🖨', '📡', '🔋', '💾', '💿', '📀', '🖲', '⌚', '📷', '🎥', '📹', '🔭', '🛰', '🚀', '🤖', '👾', '🕹', '🎮', '📺', '📻', '⚙️', '🔧', '🔨', '🛠', '🔌', '💡', '🔦'] },
            { id: 'creative', label: '🎨', name: '{{ __('app.cat_creative') }}', emojis: ['🎨', '🖌', '🖍', '✏️', '📸', '🎭', '🎬', '🎵', '🎸', '🎹', '🎺', '🎻', '🥁', '🎤', '🎧', '🎼', '📻', '📺', '🎮', '🕹', '♟', '🎲', '🎯', '🎪', '🎠', '🎡', '🎢', '🃏', '🀄', '🎴', '🖼', '🗿', '🏛'] },
            { id: 'nature', label: '🌿', name: '{{ __('app.cat_nature') }}', emojis: ['🌿', '🌱', '🌲', '🌳', '🌴', '🌵', '🌾', '🍀', '🍁', '🍂', '🍃', '🌺', '🌸', '🌼', '🌻', '🌹', '🌷', '🌿', '☘', '🪴', '🌍', '🌊', '🏔', '⛰', '🌋', '🏜', '🏕', '🌅', '🌄', '☀️', '🌙', '⭐', '🌈', '⛅', '❄️'] },
            { id: 'food', label: '🍕', name: '{{ __('app.cat_food') }}', emojis: ['🍕', '🍔', '🌮', '🍜', '🍣', '🍱', '🥗', '🍳', '🥘', '🫕', '🥩', '🍗', '🥚', '🧀', '🥓', '🥞', '🧇', '🍞', '🥖', '🥨', '🧁', '🎂', '🍰', '🍩', '🍪', '🍫', '🍬', '🍭', '☕', '🍵', '🧋', '🥤', '🍺', '🥂', '🍷'] },
            { id: 'travel', label: '✈️', name: '{{ __('app.cat_travel') }}', emojis: ['✈️', '🚀', '🛸', '🚁', '🛥', '🚢', '🚂', '🚗', '🚕', '🛻', '🚙', '🏎', '🛵', '🚲', '🛴', '🏍', '🚦', '🗺', '🌍', '🏖', '🏝', '🏔', '🗼', '🗽', '🏰', '🏯', '⛩', '🕌', '🕍', '🎡', '🎢', '🎠', '🎪', '🏟', '🎭'] },
            { id: 'symbols', label: '✨', name: '{{ __('app.cat_symbols') }}', emojis: ['✨', '⭐', '🌟', '💫', '⚡', '🔥', '❄️', '🌊', '💥', '🎯', '✅', '❌', '⚠️', '🔔', '💬', '💭', '❓', '❗', '♾', '🔁', '🔀', '▶️', '⏸', '⏹', '⏺', '🔼', '🔽', '⬆️', '⬇️', '➡️', '⬅️', '🔐', '🔓', '🏆', '🥇', '🎖', '🏅', '🎗', '🎀', '🎁', '💝', '💖', '❤️', '🖤', '🤍', '🤎', '💛', '💚', '💙', '💜'] },
        ];

        // ── Emoji keywords (for search) ──────────────────────────────────────────────
        const EMOJI_KEYWORDS = {
            '📁': ['pasta', 'folder', 'arquivo', 'file'], '📂': ['pasta aberta', 'open folder'], '🗂': ['fichario', 'index', 'organizar'],
            '💼': ['trabalho', 'work', 'maleta', 'briefcase', 'negocio', 'business'], '🏠': ['casa', 'home', 'residencia', 'house'],
            '🏢': ['empresa', 'office', 'edificio', 'building', 'trabalho'], '🎯': ['alvo', 'goal', 'meta', 'target', 'foco'],
            '✅': ['concluido', 'done', 'check', 'tarefa', 'task'], '📌': ['fixar', 'pin', 'importante', 'mark'],
            '🔖': ['marcador', 'bookmark', 'salvar'], '💡': ['ideia', 'idea', 'luz', 'light', 'dica', 'tip'],
            '🚀': ['foguete', 'rocket', 'lancamento', 'launch', 'rapido'], '🎨': ['arte', 'art', 'design', 'criativo', 'pintura'],
            '🛠': ['ferramenta', 'tool', 'construir', 'build', 'configurar'], '💰': ['dinheiro', 'money', 'financas', 'grana'],
            '📊': ['grafico', 'chart', 'dados', 'data', 'relatorio'], '📈': ['crescimento', 'growth', 'aumento', 'grafico'],
            '📋': ['lista', 'list', 'clipboard', 'tarefas'], '🔔': ['notificacao', 'notification', 'alerta', 'sino'],
            '❤️': ['amor', 'love', 'coracao', 'heart', 'favorito'], '⭐': ['estrela', 'star', 'favorito', 'destaque'],
            '🌟': ['estrela', 'star', 'especial', 'destaque'], '🔥': ['fogo', 'fire', 'urgente', 'urgent', 'quente', 'hot'],
            '💪': ['forca', 'strength', 'fitness', 'exercicio', 'strong'], '🎓': ['formatura', 'graduation', 'educacao', 'estudo'],
            '🏆': ['trofeu', 'trophy', 'vitoria', 'premio', 'award'], '📝': ['nota', 'note', 'escrever', 'write', 'anotacao'],
            '🔍': ['busca', 'search', 'lupa', 'pesquisa', 'find'], '💬': ['mensagem', 'message', 'chat', 'conversa', 'comment'],
            '🌐': ['internet', 'web', 'global', 'mundo', 'rede', 'network'], '📉': ['baixa', 'down', 'queda', 'grafico'],
            '💹': ['mercado', 'market', 'financas'], '📅': ['calendario', 'calendar', 'data', 'agenda'],
            '🗓': ['agenda', 'calendar', 'data', 'planejamento'], '⏰': ['alarme', 'alarm', 'horario', 'time', 'relogio', 'lembrete'],
            '📧': ['email', 'correio', 'mail', 'mensagem'], '📨': ['email', 'envelope', 'mensagem'],
            '🔑': ['chave', 'key', 'acesso', 'senha', 'password'], '🔒': ['cadeado', 'lock', 'seguranca', 'security'],
            '🖥': ['computador', 'computer', 'desktop', 'monitor', 'tela'], '💻': ['notebook', 'laptop', 'computador', 'codigo', 'code'],
            '🖱': ['mouse', 'cursor'], '⌨️': ['teclado', 'keyboard', 'digitar'], '📞': ['telefone', 'phone', 'ligar', 'call'],
            '☎️': ['telefone', 'phone', 'fixo', 'ligar'], '🔧': ['chave', 'wrench', 'ferramenta', 'consertar', 'fix'],
            '🔨': ['martelo', 'hammer', 'construir', 'ferramenta'], '⚙️': ['engrenagem', 'gear', 'configuracao', 'settings'],
            '📐': ['esquadro', 'ruler', 'medir', 'geometria'], '📏': ['regua', 'ruler', 'medir'],
            '✏️': ['lapis', 'pencil', 'escrever', 'write', 'editar'], '🖊': ['caneta', 'pen', 'escrever', 'write'],
            '🖋': ['caneta', 'pen', 'assinar', 'sign'], '📓': ['caderno', 'notebook', 'notas', 'notes'],
            '📔': ['caderno', 'notebook', 'diario', 'diary'], '📒': ['caderno', 'notebook', 'amarelo', 'notas'],
            '📕': ['livro', 'book', 'vermelho', 'ler'], '📗': ['livro', 'book', 'verde', 'ler'],
            '📘': ['livro', 'book', 'azul', 'ler'], '📙': ['livro', 'book', 'laranja', 'ler'],
            '📚': ['livros', 'books', 'biblioteca', 'library', 'estudar'], '🗃': ['arquivo', 'organizar', 'caixas'],
            '🗄': ['arquivo', 'cabinet', 'gaveta', 'organizar'], '🗑': ['lixo', 'trash', 'deletar', 'remover'],
            '🏡': ['casa', 'home', 'residencia', 'confortavel'], '🛒': ['carrinho', 'cart', 'compras', 'shopping'],
            '🛍': ['sacola', 'bag', 'compras', 'shopping', 'loja'], '🍽': ['prato', 'plate', 'comida', 'food', 'refeicao'],
            '🍳': ['frigideira', 'pan', 'cozinhar', 'cook', 'ovos'], '🥗': ['salada', 'salad', 'saudavel', 'healthy', 'dieta'],
            '🏃': ['correr', 'run', 'exercicio', 'exercise', 'atividade'], '🏋': ['academia', 'gym', 'treino', 'workout'],
            '🧘': ['yoga', 'meditacao', 'meditation', 'relaxar', 'paz'], '🚗': ['carro', 'car', 'transporte', 'dirigir'],
            '✈️': ['aviao', 'plane', 'viagem', 'travel', 'voo', 'flight'], '🏖': ['praia', 'beach', 'ferias', 'vacation', 'mar'],
            '🎮': ['jogo', 'game', 'videogame', 'jogar', 'play', 'controle'], '🎬': ['filme', 'movie', 'cinema', 'video'],
            '🎵': ['musica', 'music', 'som', 'sound', 'nota'], '🎁': ['presente', 'gift', 'surpresa', 'surprise'],
            '🐶': ['cachorro', 'dog', 'pet', 'animal', 'cao'], '🐱': ['gato', 'cat', 'pet', 'animal', 'felino'],
            '🌱': ['planta', 'plant', 'crescimento', 'natureza'], '🌺': ['flor', 'flower', 'natureza', 'bonito'],
            '☀️': ['sol', 'sun', 'verao', 'summer', 'calor', 'brilho'], '🌙': ['lua', 'moon', 'noite', 'night', 'dormir'],
            '🌈': ['arco-iris', 'rainbow', 'colorido', 'esperanca'], '🏥': ['hospital', 'saude', 'health', 'medico'],
            '💊': ['remedio', 'medicine', 'pill', 'saude'], '🩺': ['estetoscopio', 'doctor', 'medico', 'consulta'],
            '💉': ['seringa', 'syringe', 'vacina', 'vaccine'], '🧬': ['dna', 'genetica', 'ciencia', 'biologia'],
            '🦷': ['dente', 'tooth', 'dentista', 'saude'], '💧': ['agua', 'water', 'hidratacao', 'beber'],
            '😴': ['dormir', 'sleep', 'descanso', 'rest', 'cansado'], '🧠': ['cerebro', 'brain', 'mente', 'inteligencia'],
            '🚴': ['bicicleta', 'bike', 'ciclismo', 'exercicio'], '🥦': ['brocolis', 'broccoli', 'vegetal', 'saudavel'],
            '🍎': ['maca', 'apple', 'fruta', 'fruit', 'saudavel'], '💵': ['dolar', 'dollar', 'dinheiro', 'money'],
            '💳': ['cartao', 'card', 'credito', 'pagamento'], '🏦': ['banco', 'bank', 'financas'],
            '🪙': ['moeda', 'coin', 'dinheiro', 'money'], '💎': ['diamante', 'diamond', 'joia', 'valor'],
            '🧾': ['recibo', 'receipt', 'nota fiscal', 'compra'], '🤝': ['aperto', 'handshake', 'acordo', 'parceria'],
            '🔐': ['cadeado', 'lock', 'seguranca', 'protegido'], '⚖️': ['balanca', 'balance', 'justica', 'lei'],
            '📦': ['caixa', 'box', 'pacote', 'entrega'], '🛡': ['escudo', 'shield', 'protecao', 'seguranca'],
            '📖': ['livro aberto', 'ler', 'estudar'], '🔬': ['microscopio', 'microscope', 'ciencia'],
            '🔭': ['telescopio', 'telescope', 'astronomia', 'espaco'], '🧪': ['tubo de ensaio', 'experimento'],
            '🧫': ['placa de petri', 'biologia'], '🏫': ['escola', 'school', 'colegio', 'educacao'],
            '🗒': ['bloco de notas', 'rascunho', 'anotacao'], '📄': ['documento', 'paper', 'arquivo'],
            '📱': ['celular', 'phone', 'smartphone', 'mobile', 'app'], '📡': ['antena', 'sinal', 'transmissao'],
            '🔋': ['bateria', 'battery', 'energia', 'carga'], '💾': ['disquete', 'salvar', 'save'],
            '💿': ['cd', 'disco', 'musica'], '📀': ['dvd', 'disco', 'backup'], '🤖': ['robo', 'robot', 'ia', 'ai', 'automatico'],
            '👾': ['alien', 'game', 'jogo', 'pixel'], '🕹': ['joystick', 'controle', 'game', 'jogo'],
            '📺': ['televisao', 'tv', 'video', 'assistir'], '📻': ['radio', 'musica', 'am', 'fm'],
            '🔌': ['tomada', 'plug', 'energia', 'conectar'], '🔦': ['lanterna', 'flashlight', 'luz'],
            '🖌': ['pincel', 'brush', 'pintar', 'arte'], '🖍': ['giz de cera', 'crayon', 'colorir', 'desenhar'],
            '📸': ['camera', 'foto', 'photo', 'fotografia', 'imagem'], '🎭': ['teatro', 'theater', 'arte', 'performance'],
            '🎸': ['guitarra', 'guitar', 'musica', 'rock'], '🎹': ['piano', 'teclado', 'musica'],
            '🎺': ['trompete', 'trumpet', 'musica'], '🎻': ['violino', 'violin', 'musica'],
            '🥁': ['bateria', 'drums', 'musica', 'ritmo'], '🎤': ['microfone', 'microphone', 'cantar', 'apresentar'],
            '🎧': ['fone', 'headphone', 'musica', 'ouvir'], '🎼': ['partitura', 'sheet music', 'musica'],
            '♟': ['xadrez', 'chess', 'estrategia', 'pensar'], '🎲': ['dado', 'dice', 'jogo', 'sorte'],
            '🎪': ['circo', 'circus', 'festival', 'evento'], '🖼': ['quadro', 'picture', 'arte', 'galeria'],
            '🌿': ['planta', 'plant', 'natureza', 'verde'], '🌲': ['arvore', 'tree', 'floresta', 'natureza'],
            '🌳': ['arvore', 'tree', 'parque', 'natureza'], '🌴': ['palmeira', 'palm', 'tropical', 'praia'],
            '🌵': ['cacto', 'cactus', 'deserto', 'seco'], '🌾': ['trigo', 'wheat', 'campo', 'fazenda'],
            '🍀': ['trevo', 'clover', 'sorte', 'verde'], '🍁': ['folha', 'leaf', 'outono'],
            '🍂': ['folha caida', 'outono'], '🍃': ['folha', 'leaf', 'vento', 'natureza'],
            '🌸': ['flor de cerejeira', 'japao', 'primavera'], '🌼': ['flor', 'flower', 'amarela', 'jardim'],
            '🌻': ['girassol', 'sunflower', 'sol', 'amarelo'], '🌹': ['rosa', 'rose', 'amor', 'flor'],
            '🌷': ['tulipa', 'tulip', 'flor', 'primavera'], '☘': ['trevo', 'clover', 'irlanda', 'sorte'],
            '🪴': ['vaso de planta', 'decoracao', 'interior'], '🌍': ['terra', 'earth', 'mundo', 'world', 'global'],
            '🌊': ['onda', 'wave', 'mar', 'sea', 'oceano'], '🏔': ['montanha', 'mountain', 'neve', 'trilha'],
            '⛰': ['montanha', 'mountain', 'colina'], '🌋': ['vulcao', 'volcano', 'erupcao'],
            '🏜': ['deserto', 'desert', 'arido', 'quente'], '🏕': ['acampamento', 'camping', 'barraca', 'natureza'],
            '🌅': ['nascer do sol', 'sunrise', 'manha', 'amanhecer'], '🌄': ['paisagem', 'landscape', 'montanha'],
            '❄️': ['neve', 'snow', 'frio', 'cold', 'inverno'], '🍕': ['pizza', 'comida', 'food', 'italiana', 'jantar'],
            '🍔': ['hamburguer', 'burger', 'comida', 'lanche'], '🌮': ['taco', 'comida', 'mexicana'],
            '🍜': ['macarrao', 'noodles', 'sopa', 'asiatico'], '🍣': ['sushi', 'japones', 'comida'],
            '🍱': ['bento', 'japones', 'comida', 'almoco'], '🥘': ['ensopado', 'stew', 'panela', 'comida'],
            '🥩': ['carne', 'meat', 'proteina', 'churrasco'], '🍗': ['frango', 'chicken', 'comida'],
            '🥚': ['ovo', 'egg', 'cafe da manha', 'proteina'], '🧀': ['queijo', 'cheese', 'lanche', 'pizza'],
            '🥓': ['bacon', 'carne', 'cafe da manha'], '🥞': ['panqueca', 'pancake', 'cafe da manha'],
            '🍞': ['pao', 'bread', 'padaria', 'cafe'], '🥖': ['baguete', 'baguette', 'pao', 'frances'],
            '🧁': ['cupcake', 'bolo', 'doce', 'aniversario'], '🎂': ['bolo', 'cake', 'aniversario', 'celebracao'],
            '🍰': ['fatia de bolo', 'sobremesa', 'doce'], '🍩': ['rosquinha', 'donut', 'doce'],
            '🍪': ['biscoito', 'cookie', 'doce', 'lanche'], '🍫': ['chocolate', 'doce', 'cacau'],
            '☕': ['cafe', 'coffee', 'quente', 'manha'], '🍵': ['cha', 'tea', 'xicara', 'quente'],
            '🧋': ['bubble tea', 'boba', 'bebida'], '🥤': ['refrigerante', 'soda', 'bebida', 'gelado'],
            '🏎': ['carro de corrida', 'race car', 'rapido', 'formula'], '🛻': ['picape', 'pickup', 'transporte'],
            '🛵': ['scooter', 'moto', 'transporte'], '🚲': ['bicicleta', 'bike', 'pedalar', 'transporte'],
            '🛴': ['patinete', 'scooter', 'transporte'], '🏍': ['moto', 'motorcycle', 'transporte'],
            '🗺': ['mapa', 'map', 'viagem', 'localizacao'], '🏝': ['ilha', 'island', 'tropical', 'praia'],
            '🗼': ['torre', 'tower', 'paris', 'eiffel', 'turismo'], '🗽': ['estatua', 'liberdade', 'new york'],
            '🏰': ['castelo', 'castle', 'medieval', 'historia'], '🏯': ['castelo japones', 'samurai'],
            '⛩': ['portao', 'gate', 'japao', 'templo'], '🕌': ['mesquita', 'mosque', 'islamico'],
            '🕍': ['sinagoga', 'judaico', 'religiao'], '🎡': ['roda gigante', 'parque', 'amusement'],
            '🎢': ['montanha russa', 'parque', 'amusement'], '🎠': ['carrossel', 'parque', 'divertimento'],
            '🏟': ['estadio', 'stadium', 'esporte', 'arena'], '✨': ['brilho', 'sparkle', 'magico', 'especial'],
            '💫': ['estrela cadente', 'vertigem'], '⚡': ['raio', 'lightning', 'energia', 'eletrico'],
            '💥': ['explosao', 'explosion', 'impacto', 'boom'], '❌': ['errado', 'wrong', 'deletar', 'fechar'],
            '⚠️': ['aviso', 'warning', 'cuidado', 'alerta'], '💭': ['pensamento', 'thought', 'ideia'],
            '❓': ['pergunta', 'question', 'duvida', 'help'], '❗': ['exclamacao', 'importante'],
            '♾': ['infinito', 'infinite', 'loop', 'eterno'], '🔁': ['repetir', 'repeat', 'loop', 'reload'],
            '🔀': ['embaralhar', 'shuffle', 'aleatorio'], '🎖': ['medalha', 'medal', 'premio', 'conquista'],
            '🏅': ['medalha', 'medal', 'esporte', 'competicao'], '🎗': ['fita', 'ribbon', 'apoio'],
            '🎀': ['laco', 'bow', 'presente', 'decoracao'], '💝': ['coracao', 'heart', 'amor', 'presente'],
            '💖': ['coracao brilhante', 'amor', 'love'], '🖤': ['coracao preto', 'elegante'],
            '🤍': ['coracao branco', 'puro'], '💛': ['coracao amarelo', 'amizade'],
            '💚': ['coracao verde', 'natureza', 'saude'], '💙': ['coracao azul', 'confianca'],
            '💜': ['coracao roxo', 'espiritualidade'], '👨‍👩‍👧': ['familia', 'family', 'pais', 'filhos'],
            '🫁': ['pulmao', 'lung', 'respiracao', 'saude'], '🫀': ['coracao', 'heart', 'cardiaco', 'saude'],
            '🦴': ['osso', 'bone', 'saude', 'esqueleto'], '🩹': ['curativo', 'bandaid', 'ferida', 'saude'],
            '🩼': ['muleta', 'crutch', 'saude'], '🏊': ['nadar', 'swim', 'piscina', 'exercicio'],
            '⚽': ['futebol', 'soccer', 'esporte', 'bola'], '🎾': ['tenis', 'tennis', 'esporte', 'raquete'],
            '🏓': ['ping pong', 'table tennis', 'esporte'], '🧗': ['escalada', 'climbing', 'aventura'],
            '💱': ['cambio', 'exchange', 'moeda', 'financas'], '🤎': ['coracao marrom', 'terra'],
        };

        // ── Color picker ──────────────────────────────────────────────────────────────
        const PRESETS = ['#ff914d', '#e05454', '#4ade80', '#60a5fa', '#c084fc', '#f0a05a', '#f472b6', '#34d399', '#facc15', '#94a3b8'];

        function buildColorPicker(wrap, initialColor) {
            const swatch = wrap.querySelector('.color-swatch');
            const hexEl = wrap.querySelector('.color-hex');
            const input = wrap.querySelector('input[type=color]');
            const presets = wrap.querySelector('.color-presets');

            presets.innerHTML = '';
            PRESETS.forEach(c => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'color-preset' + (c === initialColor ? ' active' : '');
                btn.style.background = c;
                btn.title = c;
                btn.addEventListener('click', () => { input.value = c; updateColor(c); });
                presets.appendChild(btn);
            });

            function updateColor(val) {
                swatch.style.background = val;
                hexEl.textContent = val.toUpperCase();
                presets.querySelectorAll('.color-preset').forEach(b => {
                    b.classList.toggle('active', b.title.toLowerCase() === val.toLowerCase());
                });
            }

            input.addEventListener('input', () => updateColor(input.value));
            updateColor(initialColor);
        }

        // ── Emoji picker ──────────────────────────────────────────────────────────────
        let currentEmoji = '📁';
        let activeTab = 'popular';

        function buildEmojiPicker(initial) {
            currentEmoji = initial || '📁';
            activeTab = 'popular';

            const trigger = document.getElementById('emoji-trigger');
            const panel = document.getElementById('emoji-panel');
            const preview = document.getElementById('emoji-preview');
            const search = document.getElementById('emoji-search');
            const tabs = document.getElementById('emoji-tabs');
            const grid = document.getElementById('emoji-grid');
            const hidden = document.getElementById('cat-icon');

            preview.textContent = currentEmoji;
            hidden.value = currentEmoji;

            // Build tabs
            tabs.innerHTML = '';
            EMOJI_CATS.forEach(cat => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'emoji-tab' + (cat.id === activeTab ? ' active' : '');
                btn.textContent = cat.label;
                btn.title = cat.name;
                btn.dataset.id = cat.id;
                btn.addEventListener('click', () => {
                    activeTab = cat.id;
                    tabs.querySelectorAll('.emoji-tab').forEach(t => t.classList.remove('active'));
                    btn.classList.add('active');
                    search.value = '';
                    renderGrid();
                });
                tabs.appendChild(btn);
            });

            function selectEmoji(e) {
                currentEmoji = e;
                hidden.value = e;
                preview.textContent = e;
                // highlight selected
                (grid.querySelector('.emoji-grid') || grid).querySelectorAll('.emoji-btn').forEach(b => {
                    b.classList.toggle('selected', b.textContent === e);
                });
                // close panel after short delay
                setTimeout(() => {
                    panel.classList.remove('open');
                    trigger.classList.remove('open');
                }, 120);
            }

            function renderGrid(query = '') {
                const inner = grid.querySelector('.emoji-grid') || grid;
                inner.innerHTML = '';

                let emojis = [];

                if (query) {
                    const q = query.toLowerCase().trim();
                    const seen = new Set();
                    EMOJI_CATS.forEach(c => {
                        c.emojis.forEach(e => {
                            if (seen.has(e)) return;
                            const keywords = EMOJI_KEYWORDS[e] || [];
                            const matches = keywords.some(k => k.includes(q)) || e === q;
                            if (matches) { seen.add(e); emojis.push(e); }
                        });
                    });
                } else {
                    const cat = EMOJI_CATS.find(c => c.id === activeTab);
                    emojis = cat ? [...cat.emojis] : [];
                }

                if (!emojis.length) {
                    inner.innerHTML = '<div class="emoji-empty">{{ __('app.cat_no_emoji') }} "' + query + '"</div>';
                    return;
                }

                emojis.forEach(e => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'emoji-btn' + (e === currentEmoji ? ' selected' : '');
                    btn.textContent = e;
                    btn.title = (EMOJI_KEYWORDS[e] || [e])[0];
                    btn.addEventListener('click', () => selectEmoji(e));
                    inner.appendChild(btn);
                });
            }

            // Toggle panel
            trigger.onclick = (ev) => {
                ev.stopPropagation();
                const isOpen = panel.classList.contains('open');
                if (isOpen) {
                    panel.classList.remove('open');
                    trigger.classList.remove('open');
                } else {
                    panel.classList.add('open');
                    trigger.classList.add('open');
                    search.value = '';
                    renderGrid();
                    setTimeout(() => search.focus(), 50);
                }
            };

            // Search
            search.oninput = () => renderGrid(search.value.trim());

            // Close on outside click
            document.addEventListener('click', (e) => {
                if (!panel.contains(e.target) && e.target !== trigger && !trigger.contains(e.target)) {
                    panel.classList.remove('open');
                    trigger.classList.remove('open');
                }
            });

            renderGrid();
        }

        // ── Modal open/close ──────────────────────────────────────────────────────────
        function openModal() {
            document.getElementById('modal-cat-title').textContent = '{{ __('app.cat_new_title') }}';
            document.getElementById('cat-form').action = '/categories';
            document.getElementById('cat-method').value = 'POST';
            document.getElementById('cat-name').value = '';
            document.getElementById('cat-description').value = '';
            buildColorPicker(document.querySelector('.color-picker-wrap'), '#ff914d');
            buildEmojiPicker('📁');
            document.getElementById('modal-cat-portal').classList.add('open');
        }

        function openEdit(id, name, color, icon, description) {
            document.getElementById('modal-cat-title').textContent = '{{ __('app.cat_edit_modal_title') }}';
            document.getElementById('cat-form').action = '/categories/' + id;
            document.getElementById('cat-method').value = 'PUT';
            document.getElementById('cat-name').value = name;
            document.getElementById('cat-description').value = description;
            buildColorPicker(document.querySelector('.color-picker-wrap'), color);
            buildEmojiPicker(icon);
            document.getElementById('modal-cat-portal').classList.add('open');
        }

        function closeModal() {
            document.getElementById('modal-cat-portal').classList.remove('open');
        }

        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
    </script>

    {{-- Portal --}}
    <div id="modal-cat-portal" class="modal-overlay" onclick="if(event.target===this)closeModal()">
        <div class="modal" style="max-width:500px;overflow:visible">
            <button class="modal-close" onclick="closeModal()">×</button>
            <div class="modal-title" id="modal-cat-title">{{ __('app.cat_new_title') }}</div>

            <form id="cat-form" method="POST" action="/categories">
                @csrf
                <input type="hidden" name="_method" id="cat-method" value="POST">
                <input type="hidden" name="icon" id="cat-icon" value="📁">

                <div class="form-group">
                    <label>{{ __('app.cat_label_name') }}</label>
                    <input type="text" name="name" id="cat-name" placeholder="{{ __('app.cat_name_ph') }}" required maxlength="100">
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:18px">
                    <div class="form-group" style="margin:0">
                        <label>{{ __('app.cat_label_color') }}</label>
                        <div class="color-picker-wrap">
                            <div class="color-preview">
                                <div class="color-swatch" style="background:#ff914d"></div>
                                <span class="color-hex">#FF914D</span>
                                <input type="color" name="color" id="cat-color" value="#ff914d">
                            </div>
                            <div class="color-presets"></div>
                        </div>
                    </div>

                    <div class="form-group" style="margin:0">
                        <label>{{ __('app.cat_label_icon') }}</label>
                        <div style="position:relative;overflow:visible">
                            <div id="emoji-trigger" class="emoji-trigger" tabindex="0">
                                <span id="emoji-preview" class="emoji-trigger-preview">📁</span>
                                <span class="emoji-trigger-label">{{ __('app.cat_choose_emoji') }}</span>
                                <svg class="emoji-trigger-arrow" width="10" height="10" viewBox="0 0 10 10" fill="none">
                                    <path d="M2 3.5L5 6.5L8 3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div id="emoji-panel" class="emoji-panel">
                                <div class="emoji-search-wrap">
                                    <input type="text" id="emoji-search" class="emoji-search" placeholder="{{ __('app.cat_search_emoji_ph') }}">
                                </div>
                                <div id="emoji-tabs" class="emoji-tabs"></div>
                                <div id="emoji-grid" class="emoji-grid-wrap">
                                    <div class="emoji-grid"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>{{ __('app.cat_label_desc') }}</label>
                    <textarea name="description" id="cat-description" placeholder="{{ __('app.cat_desc_ph') }}"></textarea>
                </div>

                <div style="display:flex;gap:8px;justify-content:flex-end">
                    <button type="button" class="btn btn-ghost" onclick="closeModal()">{{ __('app.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('app.save') }}</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete confirmation modal --}}
    <div id="del-confirm-portal" class="modal-overlay" onclick="if(event.target===this)closeDeleteConfirm()">
        <div id="del-confirm-box" class="modal" style="max-width:360px">
            <div class="action-row" style="margin-bottom:16px;align-items:flex-start">
                <div style="width:36px;height:36px;border-radius:50%;background:rgba(224,84,84,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8 2.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM7.25 5.5h1.5v3.5h-1.5V5.5zm0 4.5h1.5v1.5h-1.5V10z" fill="var(--danger)"/></svg>
                </div>
                <div class="action-row-text">
                    <div class="modal-title" style="margin-bottom:2px">{{ __('app.cat_delete_confirm') }}</div>
                    <div id="del-confirm-name" class="action-row-desc"></div>
                </div>
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end">
                <button class="btn btn-ghost" onclick="closeDeleteConfirm()">{{ __('app.cancel') }}</button>
                <button class="btn btn-danger" id="del-confirm-btn">{{ __('app.cat_delete_btn') ?? 'Excluir' }}</button>
            </div>
        </div>
    </div>

    <script>
        let _delFormId = null;

        function confirmDelete(id, name) {
            _delFormId = id;
            document.getElementById('del-confirm-name').textContent = name;
            document.getElementById('del-confirm-portal').classList.add('open');
        }

        function closeDeleteConfirm() {
            document.getElementById('del-confirm-portal').classList.remove('open');
            setTimeout(() => { _delFormId = null; }, 200);
        }

        document.getElementById('del-confirm-btn').addEventListener('click', () => {
            if (_delFormId) document.getElementById('del-form-' + _delFormId).submit();
        });

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeDeleteConfirm();
        });
    </script>
@endpush