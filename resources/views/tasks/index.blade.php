@extends('layouts.app')
@section('page-title', __('app.nav_tasks'))

@section('topbar-actions')
    <button class="btn btn-ghost btn-sm" id="kb-help-btn" title="Atalhos de teclado (?)">
        <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="3" width="14" height="10" rx="2"/><path d="M4 7h1M7 7h1M10 7h1M4 10h8"/></svg>
    </button>
    <button class="btn btn-primary" id="btn-new-task-top" title="{{ __('app.new_task') }} (N)">
        <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 2v12M2 8h12"/></svg>
        {{ __('app.new_task') }}
    </button>
@endsection

@section('content')

{{-- Quick filters --}}
<div class="quick-filters">
    <a href="/tasks" class="qf {{ !request()->anyFilled(['status','priority','overdue','search','quick']) ? 'active' : '' }}">
        {{ __('app.tasks_all') }}
        @if(isset($stats)) <span class="qf-count">{{ $stats['total'] }}</span> @endif
    </a>
    <a href="/tasks?quick=urgent" class="qf {{ request('quick') === 'urgent' ? 'active' : '' }}">
        {{ __('app.tasks_urgent_filter') }}
        @if(isset($stats)) <span class="qf-count">{{ $stats['by_priority']['urgent'] ?? 0 }}</span> @endif
    </a>
    <a href="/tasks?quick=today" class="qf {{ request('quick') === 'today' ? 'active' : '' }}">
        {{ __('app.tasks_due_today') }}
        @if(isset($stats)) <span class="qf-count">{{ $stats['due_today'] ?? 0 }}</span> @endif
    </a>
    <a href="/tasks?quick=overdue" class="qf {{ request('quick') === 'overdue' ? 'active' : '' }}">
        {{ __('app.tasks_overdue_filter') }}
        @if(($stats['overdue'] ?? 0) > 0)
            <span class="qf-count qf-danger">{{ $stats['overdue'] }}</span>
        @endif
    </a>
    <a href="/tasks?status=in_progress" class="qf {{ request('status') === 'in_progress' ? 'active' : '' }}">
        {{ __('app.tasks_in_progress_filter') }}
    </a>
    <a href="/tasks?quick=recurring" class="qf {{ request('quick') === 'recurring' ? 'active' : '' }}" style="color:#a78bfa;border-color:rgba(167,139,250,.2);background:rgba(167,139,250,.06)">
        ↻ Recorrentes
    </a>
    <div style="flex:1"></div>
</div>

{{-- Advanced filters (collapsible) --}}
<div id="advanced-filters" style="display:none;margin-bottom:16px">
    <form method="GET" action="/tasks" class="filter-bar">
        <input type="text" name="search" placeholder="{{ __('app.tasks_search_ph') }}" value="{{ request('search') }}" id="search-input">
        <div class="select-wrap">
            <select name="status" onchange="this.form.submit()">
                <option value="">{{ __('app.tasks_all_status') }}</option>
                <option value="pending"     {{ request('status') === 'pending'     ? 'selected' : '' }}>{{ __('app.status_pending') }}</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>{{ __('app.status_in_progress') }}</option>
                <option value="completed"   {{ request('status') === 'completed'   ? 'selected' : '' }}>{{ __('app.status_completed') }}</option>
                <option value="cancelled"   {{ request('status') === 'cancelled'   ? 'selected' : '' }}>{{ __('app.status_cancelled') }}</option>
            </select>
        </div>
        <div class="select-wrap">
            <select name="priority" onchange="this.form.submit()">
                <option value="">{{ __('app.tasks_all_priorities') }}</option>
                <option value="low"    {{ request('priority') === 'low'    ? 'selected' : '' }}>{{ __('app.priority_low') }}</option>
                <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>{{ __('app.priority_medium') }}</option>
                <option value="high"   {{ request('priority') === 'high'   ? 'selected' : '' }}>{{ __('app.priority_high') }}</option>
                <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>{{ __('app.priority_urgent') }}</option>
            </select>
        </div>
        <label class="check-label">
            <input type="checkbox" name="overdue" value="1" onchange="this.form.submit()" {{ request('overdue') ? 'checked' : '' }}>
            <span class="toggle-track"></span>
            {{ __('app.tasks_only_overdue') }}
        </label>
        @if(request()->anyFilled(['search','status','priority','overdue','quick']))
            <a href="/tasks" class="btn btn-ghost btn-sm">{{ __('app.tasks_clear') }}</a>
        @endif
        <button type="submit" class="btn btn-ghost btn-sm">{{ __('app.tasks_filter') }}</button>
    </form>
</div>

{{-- Bulk action bar (hidden until selection) --}}
<div id="bulk-bar" style="display:none">
    <div class="bulk-bar">
        <span id="bulk-count" style="font-size:13px;font-weight:600;color:var(--text)">0 selecionadas</span>
        <div style="display:flex;gap:6px;margin-left:auto">
            <button class="btn btn-ghost btn-sm" id="bulk-complete">{{ __('app.tasks_complete') }}</button>
            <button class="btn btn-ghost btn-sm" id="bulk-status">{{ __('app.tasks_change_status') }}</button>
            <button class="btn btn-ghost btn-sm" id="bulk-priority">{{ __('app.tasks_change_priority') }}</button>
            <button class="btn btn-danger btn-sm" id="bulk-delete">{{ __('app.tasks_delete') }}</button>
            <button class="btn btn-ghost btn-sm" id="bulk-cancel">{{ __('app.cancel') }}</button>
        </div>
    </div>
    {{-- Inline selects for bulk change --}}
    <div id="bulk-status-picker" class="bulk-picker" style="display:none">
        <span style="font-size:12px;color:var(--muted)">{{ __('app.tasks_new_status') }}</span>
        <button class="bulk-opt" data-val="pending">{{ __('app.status_pending') }}</button>
        <button class="bulk-opt" data-val="in_progress">{{ __('app.status_in_progress') }}</button>
        <button class="bulk-opt" data-val="completed">{{ __('app.status_completed') }}</button>
        <button class="bulk-opt" data-val="cancelled">{{ __('app.status_cancelled') }}</button>
    </div>
    <div id="bulk-priority-picker" class="bulk-picker" style="display:none">
        <span style="font-size:12px;color:var(--muted)">{{ __('app.tasks_new_priority') }}</span>
        <button class="bulk-opt priority-low"    data-val="low">{{ __('app.priority_low') }}</button>
        <button class="bulk-opt priority-medium" data-val="medium">{{ __('app.priority_medium') }}</button>
        <button class="bulk-opt priority-high"   data-val="high">{{ __('app.priority_high') }}</button>
        <button class="bulk-opt priority-urgent" data-val="urgent">{{ __('app.priority_urgent') }}</button>
    </div>
</div>

{{-- Table --}}
<div class="card" style="padding:0;overflow:hidden" id="tasks-card">
    @if($tasks->isEmpty())
        <div class="empty-state">
            <svg width="48" height="48" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1"><path d="M2 4h12M2 8h8M2 12h10"/></svg>
            <p>{{ __('app.tasks_none_found') }}</p>
            <button class="btn btn-primary" id="btn-empty-new">{{ __('app.tasks_create_first') }}</button>
        </div>
    @else
        <div class="table-wrap">
            <table id="tasks-table">
                <colgroup>
                    <col style="width:44px">
                    <col class="drag-handle-col" style="width:28px;display:none">
                    <col> {{-- título: flex --}}
                    <col style="width:140px">
                    <col style="width:120px">
                    <col style="width:130px">
                    <col style="width:120px">
                    <col style="width:110px">
                </colgroup>
                <thead>
                    <tr>
                        <th style="width:44px;padding-left:14px">
                            <input type="checkbox" id="select-all" class="row-checkbox" title="{{ __('app.tasks_select_all') }}">
                        </th>
                        <th id="drag-handle-header" style="width:28px;display:none"></th>
                        <th>{{ __('app.tasks_col_title') }}</th>
                        <th>{{ __('app.tasks_col_status') }}</th>
                        <th>{{ __('app.tasks_col_priority') }}</th>
                        <th>{{ __('app.tasks_col_category') }}</th>
                        <th>{{ __('app.tasks_col_due') }}</th>
                        <th style="width:110px"></th>
                    </tr>
                </thead>
                <tbody id="sortable-body">
                    @foreach($tasks as $i => $task)
                        @php
                            $isOverdue = $task->isOverdue();
                            $dueLabel  = null;
                            if ($task->due_date && !$task->isCompleted()) {
                                $diff = now()->startOfDay()->diffInDays($task->due_date->startOfDay(), false);
                                $dueLabel = match(true) {
                                    $diff < 0  => ['text' => __('app.tasks_days_overdue', ['n' => abs($diff)]), 'color' => 'var(--danger)'],
                                    $diff === 0 => ['text' => __('app.tasks_today'),  'color' => 'var(--accent2)'],
                                    $diff === 1 => ['text' => __('app.tasks_tomorrow'), 'color' => 'var(--accent)'],
                                    $diff <= 7  => ['text' => __('app.tasks_in_days', ['n' => $diff]), 'color' => 'var(--muted)'],
                                    default     => ['text' => $task->due_date->format('d/m/Y'), 'color' => 'var(--muted)'],
                                };
                            }
                        @endphp
                        <tr class="{{ $isOverdue ? 'overdue-row' : '' }} task-row"
                            data-id="{{ $task->id }}"
                            data-status="{{ $task->status->value }}"
                            data-priority="{{ $task->priority->value }}"
                            style="animation: rowIn .2s ease both; animation-delay: {{ $i * 25 }}ms">
                            <td onclick="event.stopPropagation()" style="padding-left:14px">
                                <input type="checkbox" class="row-checkbox task-checkbox" data-id="{{ $task->id }}">
                            </td>
                            <td class="drag-handle-cell" style="display:none;cursor:grab;color:var(--border);padding:0 4px;text-align:center;font-size:16px">⠿</td>
                            <td class="td-title" onclick="window.location='/tasks/{{ $task->id }}'">
                                <span class="task-title-text">{{ $task->title }}</span>
                                @if($isOverdue)<span class="overdue-chip"> ⚠</span>@endif
                                @if($task->isRecurring())
                                    <span title="{{ $task->recurrence->label() }}" style="display:inline-flex;align-items:center;margin-left:5px;font-size:10px;color:#a78bfa;background:rgba(167,139,250,.12);border-radius:4px;padding:1px 5px;font-family:'DM Sans',monospace;vertical-align:middle;line-height:1.4">↻ {{ $task->recurrence->label() }}</span>
                                @endif
                            </td>
                            <td onclick="event.stopPropagation()">
                                <span class="badge status-{{ $task->status->value }} inline-edit-trigger"
                                      data-field="status" data-id="{{ $task->id }}" title="Clique para editar">
                                    <span class="badge-dot" style="background:var(--status-{{ $task->status->value }})"></span>
                                    {{ $task->status->label() }}
                                </span>
                            </td>
                            <td onclick="event.stopPropagation()">
                                <span class="badge priority-{{ $task->priority->value }} inline-edit-trigger"
                                      data-field="priority" data-id="{{ $task->id }}" title="Clique para editar">
                                    {{ $task->priority->label() }}
                                </span>
                            </td>
                            <td style="color:var(--muted);font-size:13px" onclick="window.location='/tasks/{{ $task->id }}'">
                                @if($task->category)
                                    <span style="display:inline-flex;align-items:center;gap:4px">
                                        <span style="width:8px;height:8px;border-radius:50%;background:{{ $task->category->color }};display:inline-block;flex-shrink:0"></span>
                                        {{ $task->category->name }}
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                            <td onclick="window.location='/tasks/{{ $task->id }}'">
                                @if($dueLabel)
                                    <span style="font-family:'DM Sans',monospace;font-size:12px;color:{{ $dueLabel['color'] }}">
                                        {{ $dueLabel['text'] }}
                                    </span>
                                @else
                                    <span style="color:var(--muted)">—</span>
                                @endif
                            </td>
                            <td style="text-align:right;white-space:nowrap" onclick="event.stopPropagation()">
                                @if(!$task->isCompleted())
                                    <button class="btn btn-ghost btn-sm quick-complete" data-id="{{ $task->id }}" title="{{ __('app.tasks_quick_complete') }}">✓</button>
                                @endif
                                <a href="/tasks/{{ $task->id }}" class="btn btn-ghost btn-sm">{{ __('app.tasks_view') }}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($tasks->hasPages())
            <div style="padding:16px 20px;border-top:1px solid var(--border)">
                {{ $tasks->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @endif
</div>

{{-- Inline edit popup --}}
<div id="inline-popup" style="display:none">
    <div id="inline-popup-inner"></div>
</div>

{{-- Keyboard shortcuts modal --}}
<div id="kb-modal" style="display:none;position:fixed;inset:0;z-index:9200;background:rgba(0,0,0,.7);backdrop-filter:blur(4px);align-items:center;justify-content:center">
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:28px;width:420px;animation:modalIn .2s cubic-bezier(.34,1.56,.64,1) both">
        <div style="font-family:'DM Serif Display',serif;font-size:20px;margin-bottom:20px">{{ __("app.tasks_kbd_title") }}</div>
        <div style="display:flex;flex-direction:column;gap:10px;font-size:13px">
            @foreach([
                ['N', __('app.tasks_kbd_new')],
                ['/', __('app.tasks_kbd_search')],
                ['R', __('app.tasks_kbd_reorder')],
                ['Esc', __('app.tasks_kbd_close')],
                ['A', __('app.tasks_kbd_select_all')],
                ['Delete', __('app.tasks_kbd_delete')],
                ['?', __('app.tasks_kbd_shortcuts')],
            ] as [$key, $desc])
            <div style="display:flex;align-items:center;gap:12px">
                <kbd>{{ $key }}</kbd>
                <span style="color:var(--muted)">{{ $desc }}</span>
            </div>
            @endforeach
        </div>
        <div style="text-align:right;margin-top:20px">
            <button class="btn btn-ghost btn-sm" onclick="document.getElementById('kb-modal').style.display='none'">{{ __('app.close') }}</button>
        </div>
    </div>
</div>

@push('modals')
    @include('tasks._modal_form')
@endpush

@push('scripts')
<style>
@keyframes rowIn { from { opacity:0; transform:translateX(-8px); } to { opacity:1; transform:translateX(0); } }

/* Quick filters */
.quick-filters {
    display: flex; align-items: center; gap: 6px;
    margin-bottom: 14px; flex-wrap: wrap;
}
.qf {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 500;
    color: var(--muted); background: var(--surface); border: 1px solid var(--border);
    text-decoration: none; cursor: pointer; transition: all .15s;
    white-space: nowrap;
}
.qf:hover { color: var(--text); border-color: #3a3a46; }
.qf.active { background: var(--accent); color: #0f0f11; border-color: var(--accent); font-weight: 600; }
.qf-count { background: rgba(0,0,0,.2); border-radius: 10px; padding: 0 5px; font-family: 'DM Sans',monospace; font-size: 10px; }
.qf.active .qf-count { background: rgba(0,0,0,.15); }
.qf-danger { background: rgba(224,84,84,.3) !important; color: var(--danger) !important; }

/* Bulk bar */
.bulk-bar {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 16px; background: var(--surface2);
    border: 1px solid var(--accent); border-radius: 10px;
    margin-bottom: 10px;
    animation: pageEnter .15s ease both;
}
.bulk-picker {
    display: flex; align-items: center; gap: 6px;
    padding: 8px 12px; background: var(--surface);
    border: 1px solid var(--border); border-radius: 8px;
    margin-bottom: 10px; flex-wrap: wrap;
    animation: pageEnter .15s ease both;
}
.bulk-opt {
    padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 500;
    border: 1px solid var(--border); background: var(--surface2);
    color: var(--text); cursor: pointer; transition: all .15s;
}
.bulk-opt:hover { border-color: var(--accent); color: var(--accent); }

/* Checkboxes */
.row-checkbox {
    appearance: none; -webkit-appearance: none;
    width: 16px; height: 16px; flex-shrink: 0;
    border: 1.5px solid var(--border);
    border-radius: 5px;
    background: var(--surface2);
    cursor: pointer;
    position: relative;
    transition: border-color .15s, background .15s;
    display: inline-block; vertical-align: middle;
}
.row-checkbox:hover { border-color: var(--accent); }
.row-checkbox:checked {
    background: var(--accent);
    border-color: var(--accent);
}
.row-checkbox:checked::after {
    content: '';
    position: absolute;
    left: 4px; top: 1.5px;
    width: 5px; height: 9px;
    border: 2px solid #0f0f11;
    border-top: none; border-left: none;
    transform: rotate(45deg);
}
.row-checkbox:indeterminate {
    background: var(--accent); border-color: var(--accent);
}
.row-checkbox:indeterminate::after {
    content: '';
    position: absolute;
    left: 3px; top: 6px;
    width: 8px; height: 2px;
    background: #0f0f11;
    border-radius: 1px;
}
tr.selected td { background: rgba(255,145,77,.06) !important; }

/* Inline edit popup */
#inline-popup {
    position: fixed; z-index: 9100;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 10px;
    box-shadow: 0 8px 32px rgba(0,0,0,.5);
    padding: 6px;
    min-width: 160px;
    animation: popupIn .15s cubic-bezier(.34,1.4,.64,1) both;
}
@keyframes popupIn { from { opacity:0; transform:scale(.9) translateY(-4px); } to { opacity:1; transform:scale(1) translateY(0); } }
.inline-opt {
    display: flex; align-items: center; gap: 8px;
    padding: 7px 10px; border-radius: 7px;
    font-size: 12px; cursor: pointer; transition: background .1s;
    color: var(--text); border: none; background: none; width: 100%; text-align: left;
}
.inline-opt:hover { background: var(--surface2); }
.inline-opt.current { color: var(--accent); font-weight: 600; }
.inline-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }

/* Drag */
.drag-over { border-top: 2px solid var(--accent) !important; }
.dragging { opacity: .4; }
tr.drag-mode td { cursor: grab !important; }

/* Kbd shortcut */
kbd {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 28px; height: 22px; padding: 0 6px;
    background: var(--surface2); border: 1px solid var(--border);
    border-radius: 5px; font-family: 'DM Sans',monospace; font-size: 11px;
    color: var(--text); font-weight: 600; flex-shrink: 0;
}

/* Inline edit badge hover */
.inline-edit-trigger { cursor: pointer; transition: opacity .15s; }
.inline-edit-trigger:hover { opacity: .7; }
</style>

<script>
const CSRF = document.querySelector('meta[name=csrf-token]').content;

async function api(method, url, body) {
    const r = await fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: body ? JSON.stringify(body) : undefined,
    });
    return r;
}

// ── Quick complete ────────────────────────────────────────────────────────────
document.querySelectorAll('.quick-complete').forEach(btn => {
    btn.addEventListener('click', async function(e) {
        e.stopPropagation();
        const row = this.closest('tr');
        this.innerHTML = '<span class="spinner"></span>'; this.disabled = true;
        const res = await api('PATCH', `/api/v1/tasks/${this.dataset.id}/complete`);
        if (res.ok) {
            row.style.transition = 'opacity .3s, transform .3s';
            row.style.opacity = '0'; row.style.transform = 'translateX(12px)';
            toast('{{ __('app.tasks_completed_toast') }}', 'success');
            setTimeout(() => row.remove(), 300);
        } else { toast('{{ __('app.tasks_error_complete') }}', 'error'); this.innerHTML = '✓'; this.disabled = false; }
    });
});

// ── Inline edit popup ─────────────────────────────────────────────────────────
const STATUS_OPTS = [
    { val: 'pending',     label: 'Pendente',     color: 'var(--status-pending)' },
    { val: 'in_progress', label: 'Em progresso', color: 'var(--status-in_progress)' },
    { val: 'completed',   label: 'Concluída',    color: 'var(--status-completed)' },
    { val: 'cancelled',   label: 'Cancelada',    color: 'var(--status-cancelled)' },
];
const PRIORITY_OPTS = [
    { val: 'low',    label: 'Baixa',   color: 'var(--priority-low)' },
    { val: 'medium', label: 'Média',   color: 'var(--priority-medium)' },
    { val: 'high',   label: 'Alta',    color: 'var(--priority-high)' },
    { val: 'urgent', label: 'Urgente', color: 'var(--priority-urgent)' },
];

// Move popup to body to escape overflow:auto stacking context
const popup = document.getElementById('inline-popup');
document.body.appendChild(popup);
let popupOpen = false;

function openInlinePopup(trigger, field, taskId, currentVal) {
    const opts = field === 'status' ? STATUS_OPTS : PRIORITY_OPTS;
    const inner = document.getElementById('inline-popup-inner');
    inner.innerHTML = opts.map(o => `
        <button class="inline-opt ${o.val === currentVal ? 'current' : ''}"
                data-val="${o.val}" onclick="applyInline('${field}', ${taskId}, '${o.val}', this)">
            <span class="inline-dot" style="background:${o.color}"></span>
            ${o.label}
        </button>
    `).join('');

    // Render invisible first to measure real dimensions
    popup.style.visibility = 'hidden';
    popup.style.display    = 'block';
    popup.style.animation  = 'none';
    popup.offsetHeight; // force reflow

    const rect   = trigger.getBoundingClientRect();
    const popupW = popup.offsetWidth  || 180;
    const popupH = popup.offsetHeight || 160;

    let top  = rect.bottom + 6;
    let left = rect.left;

    // Flip upward if not enough space below
    if (top + popupH > window.innerHeight - 8) {
        top = rect.top - popupH - 6;
    }
    // Clamp horizontally within viewport
    if (left + popupW > window.innerWidth - 8) {
        left = window.innerWidth - popupW - 8;
    }
    if (left < 8) left = 8;

    popup.style.top        = top + 'px';
    popup.style.left       = left + 'px';
    popup.style.visibility = '';
    popup.style.animation  = '';
    popupOpen = true;
}

async function applyInline(field, taskId, val, btn) {
    closePopup();
    const row = document.querySelector(`tr[data-id="${taskId}"]`);
    const res = await api('PUT', `/api/v1/tasks/${taskId}`, { [field]: val });
    if (res.ok) {
        toast(field === 'status' ? '{{ __('app.tasks_inline_status') }}' : '{{ __('app.tasks_inline_priority') }}', 'success');
        setTimeout(() => location.reload(), 500);
    } else { toast('Erro ao atualizar.', 'error'); }
}

function closePopup() { popup.style.display = 'none'; popupOpen = false; }

document.querySelectorAll('.inline-edit-trigger').forEach(el => {
    el.addEventListener('click', function(e) {
        e.stopPropagation();
        if (popupOpen) { closePopup(); return; }
        openInlinePopup(this, this.dataset.field, this.dataset.id,
            this.closest('tr').dataset[this.dataset.field]);
    });
});
document.addEventListener('click', e => { if (popupOpen && !popup.contains(e.target)) closePopup(); });
document.addEventListener('keydown', e => { if (e.key === 'Escape' && popupOpen) closePopup(); });

// ── Bulk selection ────────────────────────────────────────────────────────────
const bulkBar  = document.getElementById('bulk-bar');
const bulkCount = document.getElementById('bulk-count');
let selected = new Set();

function updateBulkBar() {
    if (selected.size > 0) {
        bulkBar.style.display = 'block';
        bulkCount.textContent = `${selected.size} {{ __('app.tasks_selected') }}`;
    } else {
        bulkBar.style.display = 'none';
        document.getElementById('bulk-status-picker').style.display  = 'none';
        document.getElementById('bulk-priority-picker').style.display = 'none';
    }
}

document.querySelectorAll('.task-checkbox').forEach(cb => {
    cb.addEventListener('change', function() {
        const row = this.closest('tr');
        this.checked ? selected.add(this.dataset.id) : selected.delete(this.dataset.id);
        row.classList.toggle('selected', this.checked);
        updateBulkBar();
        document.getElementById('select-all').indeterminate =
            selected.size > 0 && selected.size < document.querySelectorAll('.task-checkbox').length;
        document.getElementById('select-all').checked =
            selected.size === document.querySelectorAll('.task-checkbox').length;
    });
});

document.getElementById('select-all')?.addEventListener('change', function() {
    document.querySelectorAll('.task-checkbox').forEach(cb => {
        cb.checked = this.checked;
        const row = cb.closest('tr');
        this.checked ? selected.add(cb.dataset.id) : selected.delete(cb.dataset.id);
        row.classList.toggle('selected', this.checked);
    });
    updateBulkBar();
});

document.getElementById('bulk-cancel')?.addEventListener('click', () => {
    selected.clear();
    document.querySelectorAll('.task-checkbox').forEach(cb => { cb.checked = false; cb.closest('tr').classList.remove('selected'); });
    document.getElementById('select-all').checked = false;
    updateBulkBar();
});

// Bulk complete
document.getElementById('bulk-complete')?.addEventListener('click', async () => {
    if (!selected.size) return;
    const ids = [...selected];
    await Promise.all(ids.map(id => api('PATCH', `/api/v1/tasks/${id}/complete`)));
    toast(`${ids.length} {{ __('app.tasks_completed_many') }}`, 'success');
    setTimeout(() => location.reload(), 500);
});

// Bulk delete
document.getElementById('bulk-delete')?.addEventListener('click', () => {
    if (!selected.size) return;
    confirmDialog(
        '{{ __('app.tasks_delete_title') }}',
        `{{ __('app.tasks_delete_confirm', ['n' => '']) }}`.replace(':n', selected.size),
        async () => {
            await Promise.all([...selected].map(id => api('DELETE', `/api/v1/tasks/${id}`)));
            toast(`${selected.size} {{ __('app.tasks_deleted_toast') }}`, 'info');
            setTimeout(() => location.reload(), 500);
        }
    );
});

// Bulk status
document.getElementById('bulk-status')?.addEventListener('click', () => {
    const p = document.getElementById('bulk-status-picker');
    const q = document.getElementById('bulk-priority-picker');
    q.style.display = 'none';
    p.style.display = p.style.display === 'none' ? 'flex' : 'none';
});
document.getElementById('bulk-priority')?.addEventListener('click', () => {
    const p = document.getElementById('bulk-priority-picker');
    const q = document.getElementById('bulk-status-picker');
    q.style.display = 'none';
    p.style.display = p.style.display === 'none' ? 'flex' : 'none';
});

document.querySelectorAll('#bulk-status-picker .bulk-opt').forEach(btn => {
    btn.addEventListener('click', async function() {
        const val = this.dataset.val;
        await Promise.all([...selected].map(id => api('PUT', `/api/v1/tasks/${id}`, { status: val })));
        toast('{{ __('app.tasks_status_updated') }}', 'success');
        setTimeout(() => location.reload(), 500);
    });
});
document.querySelectorAll('#bulk-priority-picker .bulk-opt').forEach(btn => {
    btn.addEventListener('click', async function() {
        const val = this.dataset.val;
        await Promise.all([...selected].map(id => api('PUT', `/api/v1/tasks/${id}`, { priority: val })));
        toast('{{ __('app.tasks_priority_updated') }}', 'success');
        setTimeout(() => location.reload(), 500);
    });
});

// ── Drag & drop reorder ───────────────────────────────────────────────────────
let dragMode = true;
let dragSrc  = null;

function setDragMode(active) {
    dragMode = active;
    const btn = document.getElementById('drag-toggle');
    if (btn) btn.classList.toggle('active', dragMode);
    const header = document.getElementById('drag-handle-header');
    if (header) header.style.display = dragMode ? '' : 'none';
    document.querySelectorAll('.drag-handle-cell').forEach(c => c.style.display = dragMode ? '' : 'none');
    document.querySelectorAll('.task-row').forEach(r => r.classList.toggle('drag-mode', dragMode));
}

// Enable drag mode on page load
document.addEventListener('DOMContentLoaded', () => setDragMode(true));

const tbody = document.getElementById('sortable-body');
if (tbody) {
    tbody.addEventListener('dragstart', e => {
        dragSrc = e.target.closest('tr');
        if (!dragMode || !dragSrc) return;
        dragSrc.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
    });
    tbody.addEventListener('dragover', e => {
        e.preventDefault();
        const row = e.target.closest('tr');
        if (!row || row === dragSrc) return;
        document.querySelectorAll('.drag-over').forEach(r => r.classList.remove('drag-over'));
        row.classList.add('drag-over');
    });
    tbody.addEventListener('drop', e => {
        e.preventDefault();
        const target = e.target.closest('tr');
        if (!target || target === dragSrc) return;
        target.classList.remove('drag-over');
        dragSrc.classList.remove('dragging');
        const rows = [...tbody.querySelectorAll('tr')];
        const fromIdx = rows.indexOf(dragSrc);
        const toIdx   = rows.indexOf(target);
        if (fromIdx < toIdx) target.after(dragSrc);
        else target.before(dragSrc);
        saveOrder();
    });
    tbody.addEventListener('dragend', () => {
        document.querySelectorAll('.dragging, .drag-over').forEach(r => {
            r.classList.remove('dragging', 'drag-over');
        });
    });
    // Make rows draggable
    document.querySelectorAll('.task-row').forEach(r => r.setAttribute('draggable', true));
}

async function saveOrder() {
    const order = [...tbody.querySelectorAll('tr[data-id]')].map(r => parseInt(r.dataset.id));
    await api('POST', '/tasks/sort', { order });
    toast('{{ __('app.tasks_order_saved') }}', 'success');
}

// ── Keyboard shortcuts ────────────────────────────────────────────────────────
document.addEventListener('keydown', e => {
    const tag = document.activeElement.tagName;
    if (['INPUT','TEXTAREA','SELECT'].includes(tag)) return;

    switch(e.key) {
        case 'n': case 'N':
            document.getElementById('modal-new-task').classList.add('open');
            break;
        case '/':
            e.preventDefault();
            document.getElementById('advanced-filters').style.display = 'block';
            setTimeout(() => document.querySelector('[name=search]')?.focus(), 50);
            break;
        case 'a': case 'A':
            document.getElementById('select-all')?.click();
            break;
        case 'Delete':
        case 'Backspace':
            if (selected.size > 0 && e.key === 'Delete') document.getElementById('bulk-delete')?.click();
            break;
        case '?':
            document.getElementById('kb-modal').style.display = 'flex';
            break;
        case 'Escape':
            document.getElementById('kb-modal').style.display = 'none';
            if (selected.size) document.getElementById('bulk-cancel')?.click();
            break;
    }
});

document.getElementById('kb-help-btn')?.addEventListener('click', () => {
    document.getElementById('kb-modal').style.display = 'flex';
});
document.getElementById('btn-new-task-top')?.addEventListener('click', () => {
    document.getElementById('modal-new-task').classList.add('open');
});
document.getElementById('btn-empty-new')?.addEventListener('click', () => {
    document.getElementById('modal-new-task').classList.add('open');
});

// Advanced filters toggle on search shortcut
document.addEventListener('keydown', e => {
    if (e.key === 'f' && (e.metaKey || e.ctrlKey)) {
        e.preventDefault();
        document.getElementById('advanced-filters').style.display = 'block';
        document.querySelector('[name=search]')?.focus();
    }
});

// Show advanced filters if any active
@if(request()->anyFilled(['search','status','priority','overdue']))
document.getElementById('advanced-filters').style.display = 'block';
@endif

</script>
@endpush

@endsection