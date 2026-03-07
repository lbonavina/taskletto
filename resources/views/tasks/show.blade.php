@extends('layouts.app')
@section('title', $task->title)
@section('page-title', __('app.task_detail_title'))

@section('topbar-actions')
    <a href="/tasks" class="btn btn-ghost btn-sm">{{ __('app.task_back') }}</a>
@endsection

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.snow.min.css" rel="stylesheet">
<style>
/* ── Quill dark theme ─────────────────────────────────────────── */
#quill-editor-wrap .ql-toolbar {
    background: var(--surface2);
    border: 1px solid var(--border);
    border-bottom: none;
    border-radius: 10px 10px 0 0;
    padding: 8px 10px;
    display: flex;
    flex-wrap: wrap;
    gap: 2px;
}
#quill-editor-wrap .ql-container {
    background: var(--surface2);
    border: 1px solid var(--border);
    border-top: none;
    border-radius: 0 0 10px 10px;
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    min-height: 160px;
    color: var(--text);
}
#quill-editor-wrap .ql-editor {
    min-height: 160px;
    padding: 12px 14px;
    line-height: 1.7;
    color: var(--text);
}
#quill-editor-wrap .ql-editor.ql-blank::before {
    color: var(--muted);
    font-style: normal;
    font-size: 13.5px;
}
#quill-editor-wrap .ql-editor p { margin-bottom: 4px; }
#quill-editor-wrap .ql-editor h1,
#quill-editor-wrap .ql-editor h2,
#quill-editor-wrap .ql-editor h3 {
    font-family: 'Codec Pro', sans-serif;
    font-weight: 700;
    color: var(--text);
    line-height: 1.25;
    margin: 12px 0 4px;
    letter-spacing: -0.3px;
}
#quill-editor-wrap .ql-editor h1 { font-size: 22px; letter-spacing: -0.4px; }
#quill-editor-wrap .ql-editor h2 { font-size: 18px; }
#quill-editor-wrap .ql-editor h3 { font-size: 15px; letter-spacing: -0.1px; }
#quill-editor-wrap .ql-editor ul,
#quill-editor-wrap .ql-editor ol { padding-left: 20px; }
#quill-editor-wrap .ql-editor li { margin: 2px 0; }
#quill-editor-wrap .ql-editor blockquote {
    border-left: 3px solid var(--accent);
    padding-left: 12px;
    color: var(--muted);
    margin: 8px 0;
}
#quill-editor-wrap .ql-editor code,
#quill-editor-wrap .ql-editor pre {
    background: rgba(0,0,0,.3);
    border-radius: 6px;
    font-family: 'DM Sans', monospace;
    font-size: 12.5px;
    color: var(--accent);
}
#quill-editor-wrap .ql-editor pre { padding: 10px 14px; }
#quill-editor-wrap .ql-editor code { padding: 1px 5px; }
#quill-editor-wrap:focus-within .ql-toolbar,
#quill-editor-wrap:focus-within .ql-container {
    border-color: var(--accent);
}
#quill-editor-wrap:focus-within .ql-toolbar {
    box-shadow: 0 0 0 3px rgba(255,145,77,.1);
}
#quill-editor-wrap:focus-within .ql-container {
    box-shadow: 0 0 0 3px rgba(255,145,77,.1);
}

/* Toolbar buttons */
#quill-editor-wrap .ql-toolbar .ql-stroke { stroke: var(--muted); transition: stroke .15s; }
#quill-editor-wrap .ql-toolbar .ql-fill   { fill:   var(--muted); transition: fill .15s; }
#quill-editor-wrap .ql-toolbar button:hover .ql-stroke,
#quill-editor-wrap .ql-toolbar .ql-picker-label:hover .ql-stroke { stroke: var(--text); }
#quill-editor-wrap .ql-toolbar button:hover .ql-fill,
#quill-editor-wrap .ql-toolbar .ql-picker-label:hover .ql-fill   { fill: var(--text); }
#quill-editor-wrap .ql-toolbar button.ql-active .ql-stroke,
#quill-editor-wrap .ql-toolbar .ql-picker-label.ql-active .ql-stroke { stroke: var(--accent) !important; }
#quill-editor-wrap .ql-toolbar button.ql-active .ql-fill { fill: var(--accent) !important; }
#quill-editor-wrap .ql-toolbar button {
    border-radius: 5px;
    padding: 3px 5px;
    transition: background .12s;
}
#quill-editor-wrap .ql-toolbar button:hover { background: rgba(255,145,77,.1); }
#quill-editor-wrap .ql-toolbar button.ql-active { background: rgba(255,145,77,.15); }

/* Picker dropdowns */
#quill-editor-wrap .ql-toolbar .ql-picker-label { color: var(--muted); border-color: var(--border) !important; border-radius: 5px; }
#quill-editor-wrap .ql-toolbar .ql-picker-label:hover { color: var(--text); }
#quill-editor-wrap .ql-toolbar .ql-picker-options {
    background: #1e1e26;
    border: 1px solid var(--border) !important;
    border-radius: 8px;
    box-shadow: 0 8px 24px rgba(0,0,0,.5);
    padding: 4px;
}
#quill-editor-wrap .ql-toolbar .ql-picker-item { color: var(--muted); border-radius: 5px; padding: 4px 8px; }
#quill-editor-wrap .ql-toolbar .ql-picker-item:hover { color: var(--text); background: rgba(255,145,77,.08); }
#quill-editor-wrap .ql-toolbar .ql-picker-item.ql-selected { color: var(--accent); }

/* Tooltip */
#quill-editor-wrap .ql-tooltip {
    background: #1e1e26;
    border: 1px solid var(--border);
    border-radius: 8px;
    color: var(--text);
    box-shadow: 0 8px 24px rgba(0,0,0,.4);
}
#quill-editor-wrap .ql-tooltip input { background: var(--surface2); border-color: var(--border); color: var(--text); border-radius: 6px; }

/* Light theme */
html[data-theme=light] #quill-editor-wrap .ql-toolbar { background: #f4f4f6; }
html[data-theme=light] #quill-editor-wrap .ql-container { background: #ffffff; color: #18181c; }
html[data-theme=light] #quill-editor-wrap .ql-editor { color: #18181c; }
html[data-theme=light] #quill-editor-wrap .ql-toolbar .ql-stroke { stroke: #8888a0; }
html[data-theme=light] #quill-editor-wrap .ql-toolbar .ql-fill   { fill:   #8888a0; }
html[data-theme=light] #quill-editor-wrap .ql-toolbar .ql-picker-options { background: #ffffff; }

/* Description display area */
.desc-display { font-size: 14px; line-height: 1.7; color: var(--text); }
.desc-display h1, .desc-display h2, .desc-display h3 { font-family: 'Codec Pro', sans-serif; font-weight: 700; letter-spacing: -0.3px; margin: 10px 0 4px; }
.desc-display h1 { font-size: 22px; }
.desc-display h2 { font-size: 18px; }
.desc-display h3 { font-size: 15px; }
.desc-display p  { margin-bottom: 4px; }
.desc-display ul, .desc-display ol { padding-left: 20px; }
.desc-display li { margin: 2px 0; }
.desc-display blockquote { border-left: 3px solid var(--accent); padding-left: 12px; color: var(--muted); margin: 8px 0; }
.desc-display code { background: rgba(0,0,0,.3); border-radius: 4px; font-family: 'DM Sans',monospace; font-size: 12.5px; color: var(--accent); padding: 1px 5px; }
.desc-display pre  { background: rgba(0,0,0,.3); border-radius: 8px; padding: 10px 14px; font-family: 'DM Sans',monospace; font-size: 12.5px; color: var(--accent); margin: 6px 0; }
.desc-display strong { font-weight: 600; }
.desc-display em     { font-style: italic; }
</style>
@endpush

@section('content')

<div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start">

    <div>
        {{-- Header card --}}
        <div class="card" style="margin-bottom:16px">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:20px">
                <div>
                    <h2 style="font-family:'Codec Pro',sans-serif;font-size:22px;font-weight:700;letter-spacing:-.4px;line-height:1.2;margin-bottom:8px">{{ $task->title }}</h2>
                    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
                        <span class="badge status-{{ $task->status->value }}">
                            <span class="badge-dot" style="background:var(--status-{{ $task->status->value }})"></span>
                            {{ $task->status->label() }}
                        </span>
                        <span class="badge priority-{{ $task->priority->value }}">{{ $task->priority->label() }}</span>
                        @if($task->isOverdue())
                            <span class="badge" style="background:rgba(224,84,84,.12);color:var(--danger)">⚠ Atrasada</span>
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
            <div style="border-top:1px solid var(--border);padding-top:16px">
                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:var(--muted);margin-bottom:8px">{{ __('app.task_section_desc') }}</div>
                @if($task->description)
                    <div class="desc-display ql-editor" style="padding:0">
                        {!! $task->description !!}
                    </div>
                @else
                    <p style="color:var(--muted);font-size:14px">{{ __('app.task_no_desc') }}</p>
                @endif
            </div>
        </div>

        {{-- Edit card --}}
        <div class="card" style="margin-bottom:16px">
            <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:var(--muted);margin-bottom:18px">{{ __('app.task_section_edit') }}</div>
            <div id="edit-alert" style="display:none" class="alert"></div>

            <div class="form-group">
                <label>{{ __('app.task_label_title') }}</label>
                <input type="text" id="edit-title" value="{{ $task->title }}">
            </div>
            <div class="form-group">
                <label>{{ __('app.task_label_description') }}</label>
                <div id="quill-editor-wrap">
                    <div id="quill-editor"></div>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="form-group">
                    <label>{{ __('app.task_label_status') }}</label>
                    <div class="select-wrap">
                        <select id="edit-status">
                            <option value="pending"     {{ $task->status->value === 'pending'     ? 'selected' : '' }}>{{ __('app.status_pending') }}</option>
                            <option value="in_progress" {{ $task->status->value === 'in_progress' ? 'selected' : '' }}>{{ __('app.status_in_progress') }}</option>
                            <option value="completed"   {{ $task->status->value === 'completed'   ? 'selected' : '' }}>{{ __('app.status_completed') }}</option>
                            <option value="cancelled"   {{ $task->status->value === 'cancelled'   ? 'selected' : '' }}>{{ __('app.status_cancelled') }}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>{{ __('app.task_label_priority') }}</label>
                    <div class="select-wrap">
                        <select id="edit-priority">
                            <option value="low"    {{ $task->priority->value === 'low'    ? 'selected' : '' }}>{{ __('app.priority_low') }}</option>
                            <option value="medium" {{ $task->priority->value === 'medium' ? 'selected' : '' }}>{{ __('app.priority_medium') }}</option>
                            <option value="high"   {{ $task->priority->value === 'high'   ? 'selected' : '' }}>{{ __('app.priority_high') }}</option>
                            <option value="urgent" {{ $task->priority->value === 'urgent' ? 'selected' : '' }}>{{ __('app.priority_urgent') }}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="form-group">
                    <label>{{ __('app.task_label_due') }}</label>
                    <input type="date" id="edit-due-date" value="{{ $task->due_date?->format('Y-m-d') }}">
                </div>
                <div class="form-group">
                    <label>{{ __('app.task_label_category') }}</label>
                    <div class="select-wrap">
                        <select id="edit-category">
                            <option value="">— Sem categoria —</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->name }}" data-color="{{ $cat->color }}" data-icon="{{ $cat->icon }}"
                                    {{ $task->category === $cat->name ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div style="text-align:right">
                <button class="btn btn-primary" id="btn-save-edit">{{ __('app.task_save_changes') }}</button>
            </div>
        </div>

        {{-- History --}}
        @if($task->histories && $task->histories->count())
        <div class="card">
            <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:var(--muted);margin-bottom:16px">{{ __('app.task_section_history') }}</div>
            @foreach($task->histories->sortByDesc('created_at') as $h)
            <div style="display:flex;gap:10px;padding:8px 0;border-bottom:1px solid var(--border)">
                <div style="width:6px;height:6px;border-radius:50%;background:var(--accent);margin-top:5px;flex-shrink:0"></div>
                <div>
                    <span style="color:var(--text);font-size:13px">{{ $h->label }}</span>
                    <span style="color:var(--muted);font-size:11px;font-family:'DM Sans',monospace;margin-left:8px">{{ $h->created_at?->format('d/m/Y H:i') }}</span>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Info sidebar --}}
    <div class="card" style="font-size:13px">
        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:var(--muted);margin-bottom:16px">{{ __('app.task_section_info') }}</div>
        <div style="display:flex;flex-direction:column;gap:12px">
            <div>
                <div style="font-size:11px;color:var(--muted);margin-bottom:2px">ID</div>
                <div style="font-family:'DM Sans',monospace">#{{ $task->id }}</div>
            </div>
            <div>
                <div style="font-size:11px;color:var(--muted);margin-bottom:2px">{{ __('app.task_info_created') }}</div>
                <div style="font-family:'DM Sans',monospace;font-size:12px">{{ $task->created_at->format('d/m/Y H:i') }}</div>
            </div>
            <div>
                <div style="font-size:11px;color:var(--muted);margin-bottom:2px">{{ __('app.task_info_updated') }}</div>
                <div style="font-family:'DM Sans',monospace;font-size:12px">{{ $task->updated_at->format('d/m/Y H:i') }}</div>
            </div>
            @if($task->completed_at)
            <div>
                <div style="font-size:11px;color:var(--muted);margin-bottom:2px">Concluída em</div>
                <div style="font-family:'DM Sans',monospace;font-size:12px;color:var(--success)">{{ $task->completed_at->format('d/m/Y H:i') }}</div>
            </div>
            @endif
            @if($task->due_date)
            <div>
                <div style="font-size:11px;color:var(--muted);margin-bottom:2px">{{ __('app.task_info_due') }}</div>
                <div style="font-family:'DM Sans',monospace;font-size:12px;color:{{ $task->isOverdue() ? 'var(--danger)' : 'var(--text)' }}">
                    {{ $task->due_date->format('d/m/Y') }}
                    @if($task->isOverdue()) <span style="font-size:10px">(atrasada)</span> @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.min.js"></script>
<script>
const taskId = {{ $task->id }};
const csrf   = document.querySelector('meta[name=csrf-token]').content;

async function apiCall(method, path, body = null) {
    const opts = { method, headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf } };
    if (body) opts.body = JSON.stringify(body);
    return fetch(path, opts);
}

// ── Quill init ───────────────────────────────────────────────────────────────
const quill = new Quill('#quill-editor', {
    theme: 'snow',
    placeholder: '{{ __('app.task_desc_ph') }}',
    modules: {
        toolbar: [
            [{ header: [1, 2, 3, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ list: 'ordered' }, { list: 'bullet' }],
            ['blockquote', 'code-block'],
            ['link'],
            ['clean'],
        ]
    }
});

// Load existing content
const existingContent = @json($task->description);
if (existingContent) {
    quill.clipboard.dangerouslyPasteHTML(existingContent);
}

// ── Actions ──────────────────────────────────────────────────────────────────
function launchConfetti() {
    const colors = ['#ff914d','#4ade80','#60a5fa','#f0a05a','#c084fc'];
    const style  = document.createElement('style');
    style.textContent = '@keyframes confettiFall { 0% { transform:translateY(-10px) rotate(0deg); opacity:1; } 100% { transform:translateY(100vh) rotate(720deg); opacity:0; } }';
    document.head.appendChild(style);
    for (let i = 0; i < 60; i++) {
        const el = document.createElement('div');
        const size = Math.random() * 8 + 4;
        el.style.cssText = `position:fixed;top:0;left:${Math.random()*100}vw;width:${size}px;height:${size}px;background:${colors[i%colors.length]};border-radius:${Math.random()>.5?'50%':'2px'};pointer-events:none;z-index:9999;animation:confettiFall ${1.2+Math.random()*1.5}s ease-in forwards;animation-delay:${Math.random()*0.4}s`;
        document.body.appendChild(el);
        el.addEventListener('animationend', () => el.remove());
    }
}

const btnComplete = document.getElementById('btn-complete');
if (btnComplete) {
    btnComplete.addEventListener('click', async function() {
        this.innerHTML = '<span class="spinner"></span>';
        this.disabled = true;
        const res = await apiCall('PATCH', `/api/v1/tasks/${taskId}/complete`);
        if (res.ok) { launchConfetti(); toast('{{ __('app.task_toast_completed') }}', 'success'); setTimeout(() => location.reload(), 1200); }
        else { toast('{{ __('app.task_toast_err_complete') }}', 'error'); this.innerHTML = '{{ __('app.task_complete_btn') }}'; this.disabled = false; }
    });
}

const btnReopen = document.getElementById('btn-reopen');
if (btnReopen) {
    btnReopen.addEventListener('click', async function() {
        this.innerHTML = '<span class="spinner"></span>';
        this.disabled = true;
        const res = await apiCall('PATCH', `/api/v1/tasks/${taskId}/reopen`);
        if (res.ok) { toast('{{ __('app.task_toast_reopened') }}', 'info'); setTimeout(() => location.reload(), 600); }
        else { toast('{{ __('app.task_toast_err_reopen') }}', 'error'); this.innerHTML = '{{ __('app.task_reopen_btn') }}'; this.disabled = false; }
    });
}

document.getElementById('btn-delete').addEventListener('click', function() {
    confirmDialog('{{ __('app.task_delete_title') }}', '{{ __('app.task_delete_msg') }}', async () => {
        const res = await apiCall('DELETE', `/api/v1/tasks/${taskId}`);
        if (res.ok) { toast('{{ __('app.task_toast_deleted') }}', 'info'); setTimeout(() => window.location.href = '/tasks', 600); }
        else toast('{{ __('app.task_toast_err_delete') }}', 'error');
    });
});

document.getElementById('btn-save-edit').addEventListener('click', async function() {
    const btn = this; const alertEl = document.getElementById('edit-alert');
    alertEl.style.display = 'none';
    btn.innerHTML = '<span class="spinner"></span> Salvando...'; btn.disabled = true;

    // Get HTML from Quill — empty editor returns <p><br></p>
    const rawHtml    = quill.root.innerHTML;
    const descValue  = quill.getText().trim() ? rawHtml : null;

    const payload = {
        title:       document.getElementById('edit-title').value,
        description: descValue,
        status:      document.getElementById('edit-status').value,
        priority:    document.getElementById('edit-priority').value,
        due_date:    document.getElementById('edit-due-date').value || null,
        category:    document.getElementById('edit-category').value || null,
    };
    try {
        const res = await apiCall('PUT', `/api/v1/tasks/${taskId}`, payload);
        const data = await res.json();
        if (res.ok) { toast('{{ __('app.task_toast_saved') }}', 'success'); setTimeout(() => location.reload(), 700); }
        else {
            const msgs = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Erro.');
            alertEl.className = 'alert alert-error'; alertEl.textContent = msgs; alertEl.style.display = 'block';
        }
    } catch(e) { toast('{{ __('app.task_toast_err_save') }}', 'error'); }
    finally { btn.innerHTML = '{{ __('app.task_save_changes') }}'; btn.disabled = false; }
});
</script>
@endpush

@endsection