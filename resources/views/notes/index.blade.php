@extends('layouts.app')
@section('page-title', __('app.nav_notes'))

@section('topbar-actions')
    <button class="btn btn-primary" id="btn-new-note">
        <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 2v12M2 8h12"/></svg>
        Nova Nota
    </button>
@endsection

@push('styles')
<style>
/* ── Filter bar ───────────────────────────────────────────────────────── */
.notes-filterbar {
    display: flex; align-items: center; gap: 8px;
    margin-bottom: 20px; flex-wrap: wrap;
}

/* Search */
.notes-search-wrap {
    position: relative; flex: 1; min-width: 180px; max-width: 320px;
}
.notes-search-wrap .search-icon {
    position: absolute; left: 11px; top: 50%; transform: translateY(-50%);
    color: var(--muted); pointer-events: none; display: flex; align-items: center;
}
.notes-search-wrap .search-clear {
    position: absolute; right: 8px; top: 50%; transform: translateY(-50%);
    background: none; border: none; color: var(--muted); cursor: pointer;
    font-size: 11px; padding: 3px 5px; border-radius: 4px;
    display: none; transition: color .12s;
}
.notes-search-wrap .search-clear:hover { color: var(--text); }
#notes-search {
    width: 100%; padding: 8px 30px 8px 33px;
    background: var(--surface2); border: 1px solid var(--border);
    border-radius: 8px; color: var(--text); font-size: 12.5px;
    font-family: inherit; outline: none;
    transition: border-color .15s, box-shadow .15s;
}
#notes-search:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(255,145,77,.10);
}
#notes-search::placeholder { color: var(--muted); opacity: .6; }
html[data-theme=light] #notes-search { background: #fff; border-color: #dddde8; }

/* Divider between filter groups */
.filter-divider {
    width: 1px; height: 20px;
    background: var(--border); flex-shrink: 0; opacity: .7;
}

/* Selects */
.notes-filterbar .select-wrap { min-width: 0; }
.notes-filterbar .csel-trigger {
    padding: 9px 30px 8px 8px !important;
    font-size: 12px !important;
    border-radius: 8px !important;
    background: var(--surface2) !important;
    white-space: nowrap;
}
.notes-filterbar .csel-trigger.filter-active {
    border-color: var(--accent) !important;
    color: var(--accent) !important;
    background: rgba(255,145,77,.08) !important;
}
.notes-filterbar .csel-trigger.filter-active .csel-arrow { color: var(--accent); }
html[data-theme=light] .notes-filterbar .csel-trigger { background: #fff !important; }
html[data-theme=light] .notes-filterbar .csel-trigger.filter-active { background: rgba(255,145,77,.06) !important; }

/* Color filter — agrupado e sem label solto */
.color-filter-group {
    display: flex; align-items: center; gap: 6px;
    padding: 10px 10px 10px 10px !important;
    background: var(--surface2); border: 1px solid var(--border);
    border-radius: 8px;
}
html[data-theme=light] .color-filter-group { background: #fff; border-color: #dddde8; }

.color-filter-group-label {
    font-size: 9.5px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .8px; color: var(--muted); opacity: .6;
    margin-right: 2px; white-space: nowrap;
    user-select: none;
}

.color-filter-wrap { display: flex; align-items: center; gap: 5px; }
.color-pill {
    width: 14px; height: 14px; border-radius: 50%;
    border: 1.5px solid transparent; cursor: pointer;
    transition: transform .15s, border-color .15s, box-shadow .15s, opacity .15s;
    flex-shrink: 0; opacity: .85;
}
.color-pill:hover { transform: scale(1.25); opacity: 1; }
.color-pill.active {
    border-color: #fff; transform: scale(1.2);
    box-shadow: 0 0 0 2px var(--accent); opacity: 1;
}
html[data-theme=light] .color-pill.active { border-color: #555; }

/* Clear btn */
#btn-clear-filters {
    font-size: 11px; padding: 4px 9px;
    opacity: 0; pointer-events: none; transition: opacity .15s;
}
#btn-clear-filters.visible { opacity: 1; pointer-events: all; }

/* ── Results info ─────────────────────────────────────────────────────── */
.notes-results-info {
    font-size: 11.5px; color: var(--muted);
    margin-bottom: 14px; min-height: 18px;
    display: flex; align-items: center; gap: 8px;
}
.notes-results-info mark {
    background: rgba(255,145,77,.2); color: var(--accent);
    border-radius: 3px; padding: 0 4px; font-style: normal;
}
.results-badge {
    background: rgba(255,145,77,.12); color: var(--accent);
    border-radius: 5px; padding: 2px 8px;
    font-size: 10.5px; font-weight: 600;
}

/* ── Skeleton loader ──────────────────────────────────────────────────── */
@keyframes shimmer {
    0%   { background-position: -400px 0; }
    100% { background-position: 400px 0; }
}
.note-skeleton {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: 8px; padding: 16px; height: 130px;
}
.skeleton-line {
    border-radius: 4px; margin-bottom: 10px;
    background: linear-gradient(90deg, var(--surface2) 25%, var(--border) 50%, var(--surface2) 75%);
    background-size: 400px 100%;
    animation: shimmer 1.4s ease infinite;
}
html[data-theme=light] .skeleton-line {
    background: linear-gradient(90deg, #e8eaf2 25%, #d4d6e4 50%, #e8eaf2 75%);
    background-size: 400px 100%;
}

/* ── Grid & cards ─────────────────────────────────────────────────────── */
.notes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 10px;
}
.note-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 0;
    cursor: pointer;
    transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
    animation: cardIn .22s cubic-bezier(.25,.46,.45,.94) both;
}
@keyframes cardIn {
    from { opacity: 0; transform: translateY(6px) scale(.98); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
.note-card:hover {
    border-color: var(--note-color, var(--accent));
    box-shadow: 0 0 0 1px color-mix(in srgb, var(--note-color, var(--accent)) 25%, transparent),
                0 8px 24px rgba(0,0,0,.16);
    transform: translateY(-2px);
}

/* Header area matching cat-card-header style */
.note-card-header {
    padding: 18px 18px 14px;
    position: relative;
    flex-shrink: 0;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 10px;
}
.note-card-header::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg,
        color-mix(in srgb, var(--note-color, var(--accent)) 14%, transparent) 0%,
        transparent 70%);
    pointer-events: none;
}

/* Color dot as icon — matches cat-icon-wrap size/feel */
.note-card-dot-wrap {
    width: 36px; height: 36px;
    border-radius: 10px;
    background: color-mix(in srgb, var(--note-color, var(--accent)) 16%, transparent);
    border: 1px solid color-mix(in srgb, var(--note-color, var(--accent)) 28%, transparent);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    position: relative; z-index: 1;
    transition: transform .2s ease, background .2s ease;
}
.note-card:hover .note-card-dot-wrap {
    transform: scale(1.06);
    background: color-mix(in srgb, var(--note-color, var(--accent)) 24%, transparent);
}
.note-card-dot {
    width: 10px; height: 10px;
    border-radius: 50%;
    background: var(--note-color, var(--accent));
    opacity: .9;
}

.note-card-pin {
    font-size: 11px; opacity: .55;
    position: relative; z-index: 1;
    flex-shrink: 0; margin-top: 2px;
}

.note-card-body {
    padding: 12px 18px 12px;
    border-top: 1px solid var(--border);
    flex: 1;
}
.note-card-title {
    font-family: 'Montserrat', sans-serif;
    font-size: 13.5px;
    font-weight: 700;
    letter-spacing: -0.2px;
    color: var(--text);
    margin-bottom: 5px;
    line-height: 1.25;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.note-card-title mark, .note-card-excerpt mark {
    background: rgba(255,145,77,.25); color: inherit;
    border-radius: 2px; padding: 0 2px; font-style: normal;
}
.note-card-excerpt {
    font-size: 11.5px; color: var(--muted); line-height: 1.6;
    margin-bottom: 10px; display: -webkit-box;
    -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
}
.note-card-footer {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap;
    padding: 8px 18px 12px;
    border-top: 1px solid var(--border);
    gap: 5px;
}
.note-card-meta {
    display: flex; align-items: center; gap: 6px;
    font-size: 10px; color: var(--muted); font-family: 'Montserrat', sans-serif;
}
.note-cat-badge {
    font-size: 10px; padding: 2px 7px;
    border-radius: var(--radius-sm);
    background: var(--surface2);
    border: 1px solid var(--border);
    color: var(--muted); white-space: nowrap;
    font-weight: 500;
}

/* Card tags row */
.note-card-tags {
    display: flex; flex-wrap: wrap; gap: 4px;
    margin-top: 8px;
}
.note-card-tag {
    font-size: 9.5px; padding: 1px 6px;
    border-radius: var(--radius-xs); font-weight: 600;
    letter-spacing: .1px;
    border: 1px solid;
    line-height: 1.6;
    transition: color .12s, background .12s, border-color .12s;
    background: var(--surface2); color: var(--muted); border-color: var(--border);
}
.note-card-tag:hover { border-color: var(--accent); color: var(--accent); background: rgba(255,145,77,.06); }
html[data-theme=light] .note-card-tag { background: #e8eaf2; color: #555570; border-color: #c8c8da; }

.notes-section-label {
    font-size: 9.5px; font-weight: 700; letter-spacing: 1px;
    text-transform: uppercase; color: var(--muted); margin: 24px 0 12px;
}
.notes-empty {
    text-align: center; padding: 60px 20px; color: var(--muted);
}
.notes-empty-icon { font-size: 48px; margin-bottom: 12px; opacity: .4; }
.notes-empty p { font-size: 13px; margin-bottom: 16px; }
</style>
@endpush

@section('content')

{{-- Filter bar --}}
<div class="notes-filterbar">

    {{-- Search --}}
    <div class="notes-search-wrap">
        <span class="search-icon">
            <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="6.5" cy="6.5" r="5"/><path d="M10.5 10.5l3.5 3.5"/></svg>
        </span>
        <input id="notes-search" type="text" placeholder="{{ __('app.notes_search_ph') }}" autocomplete="off">
        <button class="search-clear" id="btn-search-clear" title="{{ __('app.notes_search_clear') }}">✕</button>
    </div>

    <div class="filter-divider"></div>

    {{-- Category --}}
    <div class="select-wrap">
        <select id="filter-category">
            <option value="">{{ __('app.notes_all_cats') }}</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->name }}" data-icon="{{ $cat->icon }}" data-color="{{ $cat->color }}">{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Sort --}}
    <div class="select-wrap">
        <select id="filter-sort">
            <option value="updated_desc">{{ __('app.notes_sort_updated_desc') }}</option>
            <option value="updated_asc">{{ __('app.notes_sort_updated_asc') }}</option>
            <option value="created_desc">{{ __('app.notes_sort_created_desc') }}</option>
            <option value="created_asc">{{ __('app.notes_sort_created_asc') }}</option>
            <option value="title_asc">{{ __('app.notes_sort_title_asc') }}</option>
            <option value="title_desc">{{ __('app.notes_sort_title_desc') }}</option>
        </select>
    </div>

    <div class="filter-divider"></div>

    {{-- Color — agrupado num pill container --}}
    <div class="color-filter-group">
        <span class="color-filter-group-label">Cor</span>
        <div class="color-filter-wrap" id="color-filter-wrap">
            @php $colors = ['#ff914d','#e05454','#4ade80','#60a5fa','#c084fc','#f472b6','#facc15','#34d399','#94a3b8','#f0a05a']; @endphp
            @foreach($colors as $c)
                <div class="color-pill" style="background:{{ $c }}" data-color="{{ $c }}" title="{{ $c }}"></div>
            @endforeach
        </div>
    </div>

    <button class="btn btn-ghost btn-sm" id="btn-clear-filters">✕ Limpar</button>
</div>

{{-- Results info --}}
<div class="notes-results-info" id="notes-results-info"></div>

{{-- Notes output --}}
<div id="notes-output">
    @if($pinned->isEmpty() && $others->isEmpty())
        <div class="notes-empty">
            <div class="notes-empty-icon">📝</div>
            <p>{{ __('app.no_notes') }}</p>
            <button class="btn btn-primary" id="btn-new-note-empty">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 2v12M2 8h12"/></svg>
                Nova Nota
            </button>
        </div>
    @else
        @if($pinned->isNotEmpty())
            <div class="notes-section-label">📌 Fixadas</div>
            <div class="notes-grid">
                @foreach($pinned as $note)
                <a href="/notes/{{ $note->id }}" class="note-card" style="--note-color:{{ $note->color }}">
                    <div class="note-card-header">
                        <div class="note-card-dot-wrap"><div class="note-card-dot"></div></div>
                        <span class="note-card-pin">📌</span>
                    </div>
                    <div class="note-card-body">
                        <div class="note-card-title">{{ $note->title ?: __('app.notes_untitled') }}</div>
                        <div class="note-card-excerpt">{{ $note->excerpt() ?: __('app.notes_blank') }}</div>
                        @if(count($note->tags_array ?? []))
                        <div class="note-card-tags">
                            @foreach(array_slice($note->tags_array, 0, 4) as $tag)
                                <span class="note-card-tag">#{{ $tag }}</span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    <div class="note-card-footer">
                        <span class="note-card-meta">
                            <svg width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="8" cy="8" r="7"/><path d="M8 4v4l3 2"/></svg>
                            {{ $note->updated_at->diffForHumans() }}
                        </span>
                        @if($note->category)<span class="note-cat-badge">{{ $note->category }}</span>@endif
                    </div>
                </a>
                @endforeach
            </div>
        @endif
        @if($others->isNotEmpty())
            @if($pinned->isNotEmpty())<div class="notes-section-label">Todas as notas</div>@endif
            <div class="notes-grid">
                @foreach($others as $note)
                <a href="/notes/{{ $note->id }}" class="note-card" style="--note-color:{{ $note->color }}">
                    <div class="note-card-header">
                        <div class="note-card-dot-wrap"><div class="note-card-dot"></div></div>
                    </div>
                    <div class="note-card-body">
                        <div class="note-card-title">{{ $note->title ?: __('app.notes_untitled') }}</div>
                        <div class="note-card-excerpt">{{ $note->excerpt() ?: __('app.notes_blank') }}</div>
                        @if(count($note->tags_array ?? []))
                        <div class="note-card-tags">
                            @foreach(array_slice($note->tags_array, 0, 4) as $tag)
                                <span class="note-card-tag">#{{ $tag }}</span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    <div class="note-card-footer">
                        <span class="note-card-meta">
                            <svg width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="8" cy="8" r="7"/><path d="M8 4v4l3 2"/></svg>
                            {{ $note->updated_at->diffForHumans() }}
                        </span>
                        @if($note->category)<span class="note-cat-badge">{{ $note->category }}</span>@endif
                    </div>
                </a>
                @endforeach
            </div>
        @endif
    @endif
</div>

@endsection

@push('scripts')
<script>
// ── Create note ───────────────────────────────────────────────────────────────
async function createNote() {
    const res  = await fetch('/notes', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
    });
    const data = await res.json();
    window.location.href = '/notes/' + data.id;
}
document.getElementById('btn-new-note')?.addEventListener('click', createNote);
document.getElementById('btn-new-note-empty')?.addEventListener('click', createNote);

// ── AJAX Search & Filters ─────────────────────────────────────────────────────
(function () {
    const searchInput  = document.getElementById('notes-search');
    const clearBtn     = document.getElementById('btn-search-clear');
    const catSelect    = document.getElementById('filter-category');
    const sortSelect   = document.getElementById('filter-sort');
    const colorWrap    = document.getElementById('color-filter-wrap');
    const clearAll     = document.getElementById('btn-clear-filters');
    const output       = document.getElementById('notes-output');
    const resultsInfo  = document.getElementById('notes-results-info');

    let activeColor   = null;
    let debounceTimer = null;
    let lastQuery     = null;

    // ── Helpers ───────────────────────────────────────────────────────────────
    function esc(s) {
        return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
    function highlight(text, term) {
        const safe = esc(text);
        if (!term) return safe;
        const re = new RegExp('(' + term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
        return safe.replace(re, '<mark>$1</mark>');
    }

    // ── Card HTML ─────────────────────────────────────────────────────────────
    function buildCard(note, term) {
        const pin = note.pinned ? '<span class="note-card-pin">📌</span>' : '';
        const cat = note.category ? `<span class="note-cat-badge">${esc(note.category)}</span>` : '';
        const tagsHtml = (note.tags && note.tags.length)
            ? `<div class="note-card-tags">${note.tags.slice(0,4).map(t => `<span class="note-card-tag">#${esc(t)}</span>`).join('')}</div>`
            : '';
        return `<a href="/notes/${note.id}" class="note-card" style="--note-color:${esc(note.color)}">
            <div class="note-card-header">
                <div class="note-card-dot-wrap"><div class="note-card-dot"></div></div>
                ${pin}
            </div>
            <div class="note-card-body">
                <div class="note-card-title">${highlight(note.title, term)}</div>
                <div class="note-card-excerpt">${highlight(note.excerpt, term)}</div>
                ${tagsHtml}
            </div>
            <div class="note-card-footer">
                <span class="note-card-meta">
                    <svg width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="8" cy="8" r="7"/><path d="M8 4v4l3 2"/></svg>
                    ${esc(note.updated_at)}
                </span>
                ${cat}
            </div>
        </a>`;
    }

    // ── Skeleton ──────────────────────────────────────────────────────────────
    function showSkeleton() {
        const skels = Array(6).fill(`
            <div class="note-skeleton">
                <div class="skeleton-line" style="width:55%;height:13px"></div>
                <div class="skeleton-line" style="width:88%;height:10px;margin-top:14px"></div>
                <div class="skeleton-line" style="width:72%;height:10px"></div>
                <div class="skeleton-line" style="width:40%;height:9px;margin-top:18px"></div>
            </div>`).join('');
        output.innerHTML = `<div class="notes-grid">${skels}</div>`;
    }

    // ── Render ────────────────────────────────────────────────────────────────
    function render(data, term) {
        const { pinned, others, total } = data;
        const hasFilters = term || activeColor || catSelect.value || sortSelect.value !== 'updated_desc';

        // Info bar
        if (hasFilters) {
            const badge = `<span class="results-badge">${total} resultado${total !== 1 ? 's' : ''}</span>`;
            const termLabel = term ? `para <mark>"${esc(term)}"</mark>` : '';
            resultsInfo.innerHTML = badge + (termLabel ? ' ' + termLabel : '');
        } else {
            resultsInfo.innerHTML = '';
        }

        if (total === 0) {
            output.innerHTML = `<div class="notes-empty">
                <div class="notes-empty-icon">🔍</div>
                <p>{{ __('app.notes_none_found') }}${term ? ` para <strong>"${esc(term)}"</strong>` : ''}.</p>
            </div>`;
            return;
        }

        let html = '';
        if (pinned.length) {
            html += `<div class="notes-section-label">📌 Fixadas</div>
                     <div class="notes-grid">${pinned.map(n => buildCard(n, term)).join('')}</div>`;
        }
        if (others.length) {
            if (pinned.length) html += `<div class="notes-section-label">Todas as notas</div>`;
            html += `<div class="notes-grid">${others.map(n => buildCard(n, term)).join('')}</div>`;
        }
        output.innerHTML = html;
    }

    // ── Fetch ─────────────────────────────────────────────────────────────────
    async function fetchNotes(skeleton = true) {
        const term     = searchInput.value.trim();
        const category = catSelect.value;
        const sort     = sortSelect.value;
        const color    = activeColor || '';

        const params = new URLSearchParams();
        if (term)     params.set('search',   term);
        if (category) params.set('category', category);
        if (sort)     params.set('sort',     sort);
        if (color)    params.set('color',    color);

        const qs = params.toString();
        if (qs === lastQuery) return;
        lastQuery = qs;

        if (skeleton) showSkeleton();

        try {
            const res  = await fetch('/notes?' + qs, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            render(data, term);
        } catch {
            output.innerHTML = `<div class="dash-empty" style="color:var(--danger)">⚠ Erro ao buscar notas. Tente recarregar a página.</div>`;
        }

        updateState();
    }

    // ── Update UI state ───────────────────────────────────────────────────────
    function updateState() {
        const term = searchInput.value.trim();
        const active = term || catSelect.value || activeColor || sortSelect.value !== 'updated_desc';
        clearAll.classList.toggle('visible', !!active);
        clearBtn.style.display = term ? 'block' : 'none';

        // Mark csel triggers as active when a non-default value is selected
        const catTrigger  = catSelect.closest('.select-wrap')?.querySelector('.csel-trigger');
        const sortTrigger = sortSelect.closest('.select-wrap')?.querySelector('.csel-trigger');
        catTrigger?.classList.toggle('filter-active',  !!catSelect.value);
        sortTrigger?.classList.toggle('filter-active', sortSelect.value !== 'updated_desc');

        document.querySelectorAll('.color-pill').forEach(p =>
            p.classList.toggle('active', p.dataset.color === activeColor)
        );
    }

    // ── Events ────────────────────────────────────────────────────────────────
    searchInput.addEventListener('input', () => {
        clearBtn.style.display = searchInput.value ? 'block' : 'none';
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => fetchNotes(true), 260);
    });

    clearBtn.addEventListener('click', () => {
        searchInput.value = '';
        fetchNotes(true);
    });

    catSelect.addEventListener('change',  () => fetchNotes(true));
    sortSelect.addEventListener('change', () => fetchNotes(false));

    colorWrap.addEventListener('click', e => {
        const pill = e.target.closest('.color-pill');
        if (!pill) return;
        activeColor = (activeColor === pill.dataset.color) ? null : pill.dataset.color;
        fetchNotes(true);
    });

    clearAll.addEventListener('click', () => {
        searchInput.value = '';
        catSelect.value   = '';
        sortSelect.value  = 'updated_desc';
        activeColor       = null;
        lastQuery         = null;
        // Dispatch change so csel updates its label display
        catSelect.dispatchEvent(new Event('change', { bubbles: true }));
        sortSelect.dispatchEvent(new Event('change', { bubbles: true }));
        fetchNotes(true);
    });

    // Pressionar / foca a busca
    document.addEventListener('keydown', e => {
        if (e.key === '/' && !['INPUT','TEXTAREA','SELECT'].includes(document.activeElement.tagName)) {
            e.preventDefault();
            searchInput.focus();
        }
    });
})();
</script>
@endpush