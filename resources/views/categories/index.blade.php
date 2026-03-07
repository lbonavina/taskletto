@extends('layouts.app')

@section('page-title', 'Categorias')

@section('topbar-actions')
    <button class="btn btn-primary" onclick="openModal()">
        <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 2v12M2 8h12"/></svg>
        Nova Categoria
    </button>
@endsection

@section('content')

@if($categories->isEmpty())
    <div class="card">
        <div class="empty-state">
            <svg width="48" height="48" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1"><path d="M1.5 4.5h3a1 1 0 011 1v5a1 1 0 01-1 1h-3a1 1 0 01-1-1v-5a1 1 0 011-1zM8 2.5h3a1 1 0 011 1v9a1 1 0 01-1 1H8a1 1 0 01-1-1v-9a1 1 0 011-1z"/></svg>
            <p>Nenhuma categoria criada.</p>
            <button class="btn btn-primary" onclick="openModal()">Criar categoria</button>
        </div>
    </div>
@else
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px">
        @foreach($categories as $cat)
        <div class="card" style="border-left:3px solid {{ $cat->color }}">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:10px">
                <div style="display:flex;align-items:center;gap:10px">
                    <span style="font-size:22px">{{ $cat->icon }}</span>
                    <div>
                        <div style="font-weight:600;font-size:14px">{{ $cat->name }}</div>
                        <div style="font-size:11px;color:var(--muted);font-family:'DM Sans',monospace">{{ $cat->tasks_count }} tarefa(s)</div>
                    </div>
                </div>
                <div style="display:flex;gap:4px">
                    <button class="btn btn-ghost btn-sm"
                        onclick="openEdit({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ $cat->color }}', '{{ addslashes($cat->icon) }}', '{{ addslashes($cat->description ?? '') }}')">
                        Editar
                    </button>
                    <form method="POST" action="/categories/{{ $cat->id }}" onsubmit="return confirm('Excluir categoria?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">✕</button>
                    </form>
                </div>
            </div>
            @if($cat->description)
                <p style="font-size:12px;color:var(--muted);line-height:1.5">{{ $cat->description }}</p>
            @endif
            <a href="/tasks?category={{ $cat->name }}" style="font-size:11px;color:var(--accent);text-decoration:none;display:inline-block;margin-top:10px;font-weight:500">
                Ver tarefas →
            </a>
        </div>
        @endforeach
    </div>
@endif

@endsection

@push('scripts')
<style>
#modal-cat-portal {
    display: none; position: fixed; inset: 0; z-index: 10000;
    align-items: center; justify-content: center;
    background: rgba(0,0,0,0); backdrop-filter: blur(0px);
}
#modal-cat-portal.open { display: flex; animation: overlayIn .2s ease forwards; }
#modal-cat-portal .modal { animation: modalIn .22s cubic-bezier(.34,1.56,.64,1) both; max-width: 500px; }

/* Color picker */
.color-picker-wrap { display: flex; flex-direction: column; gap: 0; }
.color-preview {
    display: flex; align-items: center; gap: 10px;
    padding: 8px 14px; background: var(--surface2);
    border: 1px solid var(--border); border-radius: 10px;
    cursor: pointer; transition: border-color .15s; height: 42px;
    position: relative; overflow: hidden;
}
.color-preview:hover { border-color: #3a3a46; }
.color-preview:focus-within { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(255,145,77,.15); }
.color-swatch { width: 22px; height: 22px; border-radius: 6px; border: 1px solid rgba(255,255,255,.1); flex-shrink: 0; transition: background .15s; position: relative; z-index: 1; pointer-events: none; }
.color-hex { font-family: 'DM Sans', monospace; font-size: 13px; color: var(--text); text-transform: uppercase; flex: 1; position: relative; z-index: 1; pointer-events: none; }
.color-preview input[type=color] { position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; border: none; padding: 0; z-index: 2; }
.color-presets { display: flex; gap: 6px; flex-wrap: wrap; margin-top: 8px; }
.color-preset { width: 24px; height: 24px; border-radius: 6px; border: 2px solid transparent; cursor: pointer; transition: transform .15s, border-color .15s; flex-shrink: 0; }
.color-preset:hover { transform: scale(1.15); }
.color-preset.active { border-color: white; transform: scale(1.1); }

/* Emoji picker */
.emoji-trigger {
    display: flex; align-items: center; justify-content: space-between;
    gap: 10px; padding: 8px 14px;
    background: var(--surface2); border: 1px solid var(--border);
    border-radius: 10px; cursor: pointer; height: 42px;
    transition: border-color .15s, box-shadow .15s;
    user-select: none;
}
.emoji-trigger:hover { border-color: #3a3a46; }
.emoji-trigger.open, .emoji-trigger:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(255,145,77,.1); outline: none; }
.emoji-trigger-preview { font-size: 20px; line-height: 1; }
.emoji-trigger-label { font-size: 13px; color: var(--muted); flex: 1; }
.emoji-trigger-arrow { color: var(--muted); transition: transform .2s, color .15s; font-size: 10px; }
.emoji-trigger.open .emoji-trigger-arrow { transform: rotate(180deg); color: var(--accent); }

.emoji-panel {
    display: none; margin-top: 6px;
    background: #1a1a22; border: 1px solid var(--border);
    border-radius: 12px; overflow: hidden;
    box-shadow: 0 8px 32px rgba(0,0,0,.5);
    animation: cselDropIn .15s ease;
}
.emoji-panel.open { display: block; }

.emoji-search-wrap {
    padding: 10px 12px 8px;
    border-bottom: 1px solid var(--border);
}
.emoji-search {
    width: 100%; background: var(--surface2);
    border: 1px solid var(--border); border-radius: 8px;
    color: var(--text); padding: 7px 12px; font-size: 13px;
    font-family: inherit; outline: none; transition: border-color .15s;
}
.emoji-search:focus { border-color: var(--accent); }
.emoji-search::placeholder { color: var(--muted); }

.emoji-tabs {
    display: flex; gap: 2px; padding: 8px 10px 0;
    overflow-x: auto; scrollbar-width: none;
}
.emoji-tabs::-webkit-scrollbar { display: none; }
.emoji-tab {
    flex-shrink: 0; padding: 5px 8px; font-size: 16px;
    border-radius: 7px; cursor: pointer; background: none;
    border: none; transition: background .12s;
    line-height: 1; title: attr(data-label);
}
.emoji-tab:hover { background: rgba(255,145,77,.1); }
.emoji-tab.active { background: rgba(255,145,77,.18); }

.emoji-grid-wrap {
    padding: 8px 10px 10px;
    max-height: 200px; overflow-y: auto;
    scrollbar-width: thin; scrollbar-color: var(--border) transparent;
}
.emoji-section-label {
    font-size: 10px; font-weight: 600; letter-spacing: .6px;
    text-transform: uppercase; color: var(--muted);
    padding: 4px 2px 6px; display: block;
}
.emoji-grid {
    display: grid; grid-template-columns: repeat(8, 1fr); gap: 2px;
}
.emoji-btn {
    font-size: 20px; line-height: 1; padding: 6px;
    border-radius: 7px; cursor: pointer; border: none;
    background: none; transition: background .1s, transform .1s;
    text-align: center;
}
.emoji-btn:hover { background: rgba(255,145,77,.12); transform: scale(1.15); }
.emoji-btn.selected { background: rgba(255,145,77,.22); }
.emoji-empty { color: var(--muted); font-size: 13px; padding: 20px; text-align: center; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.body.appendChild(document.getElementById('modal-cat-portal'));
});

// ── Emoji data ────────────────────────────────────────────────────────────────
const EMOJI_CATS = [
    { id: 'popular', label: '⭐', name: 'Populares', emojis: ['📁','📂','🗂','💼','🏠','🏢','🎯','✅','📌','🔖','💡','🚀','🎨','🛠','💰','📊','📈','📋','🔔','❤️','⭐','🌟','🔥','💪','🎓','🏆','📝','🔍','💬','🌐'] },
    { id: 'work',    label: '💼', name: 'Trabalho',  emojis: ['💼','🏢','📊','📈','📉','💹','📋','📌','📍','🗓','📅','⏰','🔔','📧','📨','📩','💡','🔑','🔒','🖥','💻','🖱','⌨️','🖨','📠','📞','☎️','🔧','🔨','⚙️','🛠','📐','📏','✏️','🖊','🖋','📝','📓','📔','📒','📕','📗','📘','📙','📚','🗃','🗄','🗑'] },
    { id: 'personal',label: '🏠', name: 'Pessoal',   emojis: ['🏠','🏡','🛒','🛍','🍽','🍳','🥗','🏃','🏋','🧘','🚗','✈️','🏖','🎮','🎬','🎵','📚','🎯','🎁','❤️','👨‍👩‍👧','🐶','🐱','🌱','🌺','☀️','⛅','🌙','🌈'] },
    { id: 'health',  label: '❤️', name: 'Saúde',     emojis: ['❤️','🏥','💊','🩺','🩻','💉','🧬','🦷','👁','🏃','🏋','🧘','🚴','🥗','🥦','🍎','💧','😴','🧠','💪','🫁','🫀','🦴','🩹','🩼','🏊','⚽','🎾','🏓','🧗'] },
    { id: 'finance', label: '💰', name: 'Finanças',  emojis: ['💰','💵','💴','💶','💷','💳','🏦','📊','📈','📉','💹','🪙','💎','🏠','🚗','✈️','🛒','🧾','📋','💼','🤝','📌','🔐','⚖️','🪙','💱','📦','🎁','🛡','🏆'] },
    { id: 'study',   label: '🎓', name: 'Estudos',   emojis: ['🎓','📚','📖','📝','✏️','🖊','📐','📏','🔬','🔭','🧪','🧫','🧬','💡','🏫','📓','📔','📒','📕','📗','📘','📙','🗒','📄','🗂','📊','🖥','💻','🎯','🏆','⭐','🌟'] },
    { id: 'tech',    label: '💻', name: 'Tecnologia',emojis: ['💻','🖥','📱','⌨️','🖱','🖨','📡','🔋','💾','💿','📀','🖲','⌚','📷','🎥','📹','🔭','🛰','🚀','🤖','👾','🕹','🎮','📺','📻','⚙️','🔧','🔨','🛠','🔌','💡','🔦'] },
    { id: 'creative',label: '🎨', name: 'Criativo',  emojis: ['🎨','🖌','🖍','✏️','📸','🎭','🎬','🎵','🎸','🎹','🎺','🎻','🥁','🎤','🎧','🎼','📻','📺','🎮','🕹','♟','🎲','🎯','🎪','🎠','🎡','🎢','🃏','🀄','🎴','🖼','🗿','🏛'] },
    { id: 'nature',  label: '🌿', name: 'Natureza',  emojis: ['🌿','🌱','🌲','🌳','🌴','🌵','🌾','🍀','🍁','🍂','🍃','🌺','🌸','🌼','🌻','🌹','🌷','🌿','☘','🪴','🌍','🌊','🏔','⛰','🌋','🏜','🏕','🌅','🌄','☀️','🌙','⭐','🌈','⛅','❄️'] },
    { id: 'food',    label: '🍕', name: 'Comida',    emojis: ['🍕','🍔','🌮','🍜','🍣','🍱','🥗','🍳','🥘','🫕','🥩','🍗','🥚','🧀','🥓','🥞','🧇','🍞','🥖','🥨','🧁','🎂','🍰','🍩','🍪','🍫','🍬','🍭','☕','🍵','🧋','🥤','🍺','🥂','🍷'] },
    { id: 'travel',  label: '✈️', name: 'Viagem',    emojis: ['✈️','🚀','🛸','🚁','🛥','🚢','🚂','🚗','🚕','🛻','🚙','🏎','🛵','🚲','🛴','🏍','🚦','🗺','🌍','🏖','🏝','🏔','🗼','🗽','🏰','🏯','⛩','🕌','🕍','🎡','🎢','🎠','🎪','🏟','🎭'] },
    { id: 'symbols', label: '✨', name: 'Símbolos',  emojis: ['✨','⭐','🌟','💫','⚡','🔥','❄️','🌊','💥','🎯','✅','❌','⚠️','🔔','💬','💭','❓','❗','♾','🔁','🔀','▶️','⏸','⏹','⏺','🔼','🔽','⬆️','⬇️','➡️','⬅️','🔐','🔓','🏆','🥇','🎖','🏅','🎗','🎀','🎁','💝','💖','❤️','🖤','🤍','🤎','💛','💚','💙','💜'] },
];

// ── Color picker ──────────────────────────────────────────────────────────────
const PRESETS = ['#ff914d','#e05454','#4ade80','#60a5fa','#c084fc','#f0a05a','#f472b6','#34d399','#facc15','#94a3b8'];

function buildColorPicker(wrap, initialColor) {
    const swatch  = wrap.querySelector('.color-swatch');
    const hexEl   = wrap.querySelector('.color-hex');
    const input   = wrap.querySelector('input[type=color]');
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
let activeTab    = 'popular';

function buildEmojiPicker(initial) {
    currentEmoji = initial || '📁';
    activeTab    = 'popular';

    const trigger  = document.getElementById('emoji-trigger');
    const panel    = document.getElementById('emoji-panel');
    const preview  = document.getElementById('emoji-preview');
    const search   = document.getElementById('emoji-search');
    const tabs     = document.getElementById('emoji-tabs');
    const grid     = document.getElementById('emoji-grid');
    const hidden   = document.getElementById('cat-icon');

    preview.textContent = currentEmoji;
    hidden.value        = currentEmoji;

    // Build tabs
    tabs.innerHTML = '';
    EMOJI_CATS.forEach(cat => {
        const btn = document.createElement('button');
        btn.type       = 'button';
        btn.className  = 'emoji-tab' + (cat.id === activeTab ? ' active' : '');
        btn.textContent = cat.label;
        btn.title      = cat.name;
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
        const cat = EMOJI_CATS.find(c => c.id === activeTab);
        let emojis = cat ? [...cat.emojis] : [];

        if (query) {
            const seen = new Set();
            emojis = [];
            EMOJI_CATS.forEach(c => {
                c.emojis.forEach(e => { if (!seen.has(e)) { seen.add(e); emojis.push(e); } });
            });
        }

        if (!emojis.length) {
            inner.innerHTML = '<div class="emoji-empty">Nenhum emoji encontrado</div>';
            return;
        }

        emojis.forEach(e => {
            const btn = document.createElement('button');
            btn.type      = 'button';
            btn.className = 'emoji-btn' + (e === currentEmoji ? ' selected' : '');
            btn.textContent = e;
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
    document.getElementById('modal-cat-title').textContent = 'Nova Categoria';
    document.getElementById('cat-form').action = '/categories';
    document.getElementById('cat-method').value = 'POST';
    document.getElementById('cat-name').value = '';
    document.getElementById('cat-description').value = '';
    buildColorPicker(document.querySelector('.color-picker-wrap'), '#ff914d');
    buildEmojiPicker('📁');
    document.getElementById('modal-cat-portal').classList.add('open');
}

function openEdit(id, name, color, icon, description) {
    document.getElementById('modal-cat-title').textContent = 'Editar Categoria';
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
<div id="modal-cat-portal">
    <div class="modal" style="max-width:500px;width:100%">
        <button class="modal-close" onclick="closeModal()">×</button>
        <div class="modal-title" id="modal-cat-title">Nova Categoria</div>

        <form id="cat-form" method="POST" action="/categories">
            @csrf
            <input type="hidden" name="_method" id="cat-method" value="POST">
            <input type="hidden" name="icon"    id="cat-icon"   value="📁">

            <div class="form-group">
                <label>Nome *</label>
                <input type="text" name="name" id="cat-name" placeholder="Ex: Trabalho" required maxlength="100">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="form-group">
                    <label>Cor *</label>
                    <div class="color-picker-wrap">
                        <div class="color-preview">
                            <div class="color-swatch" style="background:#ff914d"></div>
                            <span class="color-hex">#FF914D</span>
                            <input type="color" name="color" id="cat-color" value="#ff914d">
                        </div>
                        <div class="color-presets"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Ícone *</label>
                    <div style="position:relative">
                        <div id="emoji-trigger" class="emoji-trigger" tabindex="0">
                            <span id="emoji-preview" class="emoji-trigger-preview">📁</span>
                            <span class="emoji-trigger-label">Escolher emoji</span>
                            <svg class="emoji-trigger-arrow" width="10" height="10" viewBox="0 0 10 10" fill="none"><path d="M2 3.5L5 6.5L8 3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <div id="emoji-panel" class="emoji-panel">
                            <div class="emoji-search-wrap">
                                <input type="text" id="emoji-search" class="emoji-search" placeholder="🔍  Buscar emoji...">
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
                <label>Descrição</label>
                <textarea name="description" id="cat-description" placeholder="Opcional..."></textarea>
            </div>

            <div style="display:flex;gap:8px;justify-content:flex-end">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>
@endpush