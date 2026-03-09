@extends('layouts.app')
@section('title', $task->title)
@section('page-title', __('app.task_detail_title'))

@section('topbar-actions')
    <a href="/tasks" class="btn btn-ghost btn-sm">{{ __('app.task_back') }}</a>
@endsection

@push('styles')
    <style>
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

            .ttb-task-btn:hover {
                background: rgba(255, 145, 77, .1);
                color: var(--text);
            }

            .ttb-task-btn.active {
                background: rgba(255, 145, 77, .15);
                color: var(--accent);
            }

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
                font-family: 'DM Sans', sans-serif;
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
                font-family: 'Codec Pro', sans-serif;
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
                background: rgba(0, 0, 0, .3);
                border-radius: 4px;
                font-family: 'DM Sans', monospace;
                font-size: 12.5px;
                color: var(--accent);
                padding: 1px 5px;
            }

            #task-tiptap-editor .tiptap pre {
                background: rgba(0, 0, 0, .3);
                border-radius: 8px;
                padding: 10px 14px;
                font-family: 'DM Sans', monospace;
                font-size: 12.5px;
                color: var(--accent);
                margin: 6px 0;
            }

            #task-tiptap-editor .tiptap pre code { background: none; padding: 0; font-size: inherit; }
            #task-tiptap-editor .tiptap strong { font-weight: 600; }
            #task-tiptap-editor .tiptap em { font-style: italic; }
            #task-tiptap-editor .tiptap s { text-decoration: line-through; }

            #task-tiptap-editor .tiptap a {
                color: var(--accent);
                text-decoration: underline;
                text-underline-offset: 2px;
            }

            html[data-theme=light] #task-tiptap-editor {
                background: #ffffff;
                color: #18181c;
            }

            html[data-theme=light] #task-tiptap-editor .tiptap code,
            html[data-theme=light] #task-tiptap-editor .tiptap pre {
                background: rgba(0, 0, 0, .06);
            }

            /* Description display area */
            .desc-display {
                font-size: 14px;
                line-height: 1.7;
                color: var(--text);
            }

            .desc-display h1,
            .desc-display h2,
            .desc-display h3 {
                font-family: 'Codec Pro', sans-serif;
                font-weight: 700;
                letter-spacing: -0.3px;
                margin: 10px 0 4px;
            }

            .desc-display h1 {
                font-size: 22px;
            }

            .desc-display h2 {
                font-size: 18px;
            }

            .desc-display h3 {
                font-size: 15px;
            }

            .desc-display p {
                margin-bottom: 4px;
            }

            .desc-display ul,
            .desc-display ol {
                padding-left: 20px;
            }

            .desc-display li {
                margin: 2px 0;
            }

            .desc-display blockquote {
                border-left: 3px solid var(--accent);
                padding-left: 12px;
                color: var(--muted);
                margin: 8px 0;
            }

            .desc-display code {
                background: rgba(0, 0, 0, .3);
                border-radius: 4px;
                font-family: 'DM Sans', monospace;
                font-size: 12.5px;
                color: var(--accent);
                padding: 1px 5px;
            }

            .desc-display pre {
                background: rgba(0, 0, 0, .3);
                border-radius: 8px;
                padding: 10px 14px;
                font-family: 'DM Sans', monospace;
                font-size: 12.5px;
                color: var(--accent);
                margin: 6px 0;
            }

            .desc-display strong {
                font-weight: 600;
            }

            .desc-display em {
                font-style: italic;
            }
        </style>
@endpush

@section('content')

    <div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start">

        <div>
            {{-- Header card --}}
            <div class="card" style="margin-bottom:16px">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:20px">
                    <div>
                        <h2
                            style="font-family:'Codec Pro',sans-serif;font-size:22px;font-weight:700;letter-spacing:-.4px;line-height:1.2;margin-bottom:8px">
                            {{ $task->title }}</h2>
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
                    <div
                        style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:var(--muted);margin-bottom:8px">
                        {{ __('app.task_section_desc') }}</div>
                    @if($task->description)
                        <div class="desc-display">
                            {!! $task->description !!}
                        </div>
                    @else
                        <p style="color:var(--muted);font-size:14px">{{ __('app.task_no_desc') }}</p>
                    @endif
                </div>
            </div>

            {{-- Edit card --}}
            <div class="card" style="margin-bottom:16px">
                <div
                    style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:var(--muted);margin-bottom:18px">
                    {{ __('app.task_section_edit') }}</div>
                <div id="edit-alert" style="display:none" class="alert"></div>

                <div class="form-group">
                    <label>{{ __('app.task_label_title') }}</label>
                    <input type="text" id="edit-title" value="{{ $task->title }}">
                </div>
                <div class="form-group">
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
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                    <div class="form-group">
                        <label>{{ __('app.task_label_status') }}</label>
                        <div class="select-wrap">
                            <select id="edit-status">
                                <option value="pending" {{ $task->status->value === 'pending' ? 'selected' : '' }}>
                                    {{ __('app.status_pending') }}</option>
                                <option value="in_progress" {{ $task->status->value === 'in_progress' ? 'selected' : '' }}>
                                    {{ __('app.status_in_progress') }}</option>
                                <option value="completed" {{ $task->status->value === 'completed' ? 'selected' : '' }}>
                                    {{ __('app.status_completed') }}</option>
                                <option value="cancelled" {{ $task->status->value === 'cancelled' ? 'selected' : '' }}>
                                    {{ __('app.status_cancelled') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>{{ __('app.task_label_priority') }}</label>
                        <div class="select-wrap">
                            <select id="edit-priority">
                                <option value="low" {{ $task->priority->value === 'low' ? 'selected' : '' }}>
                                    {{ __('app.priority_low') }}</option>
                                <option value="medium" {{ $task->priority->value === 'medium' ? 'selected' : '' }}>
                                    {{ __('app.priority_medium') }}</option>
                                <option value="high" {{ $task->priority->value === 'high' ? 'selected' : '' }}>
                                    {{ __('app.priority_high') }}</option>
                                <option value="urgent" {{ $task->priority->value === 'urgent' ? 'selected' : '' }}>
                                    {{ __('app.priority_urgent') }}</option>
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
                                    <option value="{{ $cat->id }}" data-color="{{ $cat->color }}" data-icon="{{ $cat->icon }}"
                                        {{ $task->category_id === $cat->id ? 'selected' : '' }}>
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
                    <div
                        style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:var(--muted);margin-bottom:16px">
                        {{ __('app.task_section_history') }}</div>
                    @foreach($task->histories->sortByDesc('created_at') as $h)
                        <div style="display:flex;gap:10px;padding:8px 0;border-bottom:1px solid var(--border)">
                            <div
                                style="width:6px;height:6px;border-radius:50%;background:var(--accent);margin-top:5px;flex-shrink:0">
                            </div>
                            <div>
                                <span style="color:var(--text);font-size:13px">{{ $h->label }}</span>
                                <span
                                    style="color:var(--muted);font-size:11px;font-family:'DM Sans',monospace;margin-left:8px">{{ $h->created_at?->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Info sidebar --}}
        <div class="card" style="font-size:13px">
            <div
                style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:var(--muted);margin-bottom:16px">
                {{ __('app.task_section_info') }}</div>
            <div style="display:flex;flex-direction:column;gap:12px">
                <div>
                    <div style="font-size:11px;color:var(--muted);margin-bottom:2px">ID</div>
                    <div style="font-family:'DM Sans',monospace">#{{ $task->id }}</div>
                </div>
                <div>
                    <div style="font-size:11px;color:var(--muted);margin-bottom:2px">{{ __('app.task_info_created') }}</div>
                    <div style="font-family:'DM Sans',monospace;font-size:12px">{{ $task->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>
                <div>
                    <div style="font-size:11px;color:var(--muted);margin-bottom:2px">{{ __('app.task_info_updated') }}</div>
                    <div style="font-family:'DM Sans',monospace;font-size:12px">{{ $task->updated_at->format('d/m/Y H:i') }}
                    </div>
                </div>
                @if($task->completed_at)
                    <div>
                        <div style="font-size:11px;color:var(--muted);margin-bottom:2px">Concluída em</div>
                        <div style="font-family:'DM Sans',monospace;font-size:12px;color:var(--success)">
                            {{ $task->completed_at->format('d/m/Y H:i') }}</div>
                    </div>
                @endif
                @if($task->due_date)
                    <div>
                        <div style="font-size:11px;color:var(--muted);margin-bottom:2px">{{ __('app.task_info_due') }}</div>
                        <div
                            style="font-family:'DM Sans',monospace;font-size:12px;color:{{ $task->isOverdue() ? 'var(--danger)' : 'var(--text)' }}">
                            {{ $task->due_date->format('d/m/Y') }}
                            @if($task->isOverdue()) <span style="font-size:10px">(atrasada)</span> @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script type="module">
            import { Editor } from 'https://esm.sh/@tiptap/core@3';
            import StarterKit from 'https://esm.sh/@tiptap/starter-kit@3';
            import Underline from 'https://esm.sh/@tiptap/extension-underline@3';
            import Placeholder from 'https://esm.sh/@tiptap/extension-placeholder@3';

            const taskId = {{ $task->id }};
            const csrf = document.querySelector('meta[name=csrf-token]').content;
            const existingContent = @json($task->description);

            async function apiCall(method, path, body = null) {
                const opts = { method, headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf } };
                if (body) opts.body = JSON.stringify(body);
                return fetch(path, opts);
            }

            // ── Tiptap init ──────────────────────────────────────────────────────────────
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

            // ── Toolbar ───────────────────────────────────────────────────────────────────
            function updateToolbar() {
                document.querySelectorAll('.ttb-task-btn[data-cmd]').forEach(btn => {
                    const isActive = ({
                        bold: editor.isActive('bold'),
                        italic: editor.isActive('italic'),
                        underline: editor.isActive('underline'),
                        strike: editor.isActive('strike'),
                        bulletList: editor.isActive('bulletList'),
                        orderedList: editor.isActive('orderedList'),
                        blockquote: editor.isActive('blockquote'),
                        codeBlock: editor.isActive('codeBlock'),
                        h1: editor.isActive('heading', { level: 1 }),
                        h2: editor.isActive('heading', { level: 2 }),
                        h3: editor.isActive('heading', { level: 3 }),
                    })[btn.dataset.cmd];
                    btn.classList.toggle('active', !!isActive);
                });
            }

            document.querySelectorAll('.ttb-task-btn[data-cmd]').forEach(btn => {
                btn.addEventListener('mousedown', e => {
                    e.preventDefault();
                    ({
                        bold: () => editor.chain().focus().toggleBold().run(),
                        italic: () => editor.chain().focus().toggleItalic().run(),
                        underline: () => editor.chain().focus().toggleUnderline().run(),
                        strike: () => editor.chain().focus().toggleStrike().run(),
                        h1: () => editor.chain().focus().toggleHeading({ level: 1 }).run(),
                        h2: () => editor.chain().focus().toggleHeading({ level: 2 }).run(),
                        h3: () => editor.chain().focus().toggleHeading({ level: 3 }).run(),
                        bulletList: () => editor.chain().focus().toggleBulletList().run(),
                        orderedList: () => editor.chain().focus().toggleOrderedList().run(),
                        blockquote: () => editor.chain().focus().toggleBlockquote().run(),
                        codeBlock: () => editor.chain().focus().toggleCodeBlock().run(),
                        undo: () => editor.chain().focus().undo().run(),
                        redo: () => editor.chain().focus().redo().run(),
                    })[btn.dataset.cmd]?.();
                    updateToolbar();
                });
            });

            // ── Actions ──────────────────────────────────────────────────────────────────
            function launchConfetti() {
                const colors = ['#ff914d', '#4ade80', '#60a5fa', '#f0a05a', '#c084fc'];
                const style = document.createElement('style');
                style.textContent = '@keyframes confettiFall { 0% { transform:translateY(-10px) rotate(0deg); opacity:1; } 100% { transform:translateY(100vh) rotate(720deg); opacity:0; } }';
                document.head.appendChild(style);
                for (let i = 0; i < 60; i++) {
                    const el = document.createElement('div');
                    const size = Math.random() * 8 + 4;
                    el.style.cssText = `position:fixed;top:0;left:${Math.random() * 100}vw;width:${size}px;height:${size}px;background:${colors[i % colors.length]};border-radius:${Math.random() > .5 ? '50%' : '2px'};pointer-events:none;z-index:9999;animation:confettiFall ${1.2 + Math.random() * 1.5}s ease-in forwards;animation-delay:${Math.random() * 0.4}s`;
                    document.body.appendChild(el);
                    el.addEventListener('animationend', () => el.remove());
                }
            }

            const btnComplete = document.getElementById('btn-complete');
            if (btnComplete) {
                btnComplete.addEventListener('click', async function () {
                    this.innerHTML = '<span class="spinner"></span>';
                    this.disabled = true;
                    const res = await apiCall('PATCH', `/api/v1/tasks/${taskId}/complete`);
                    if (res.ok) { launchConfetti(); toast('{{ __('app.task_toast_completed') }}', 'success'); setTimeout(() => location.reload(), 1200); }
                    else { toast('{{ __('app.task_toast_err_complete') }}', 'error'); this.innerHTML = '{{ __('app.task_complete_btn') }}'; this.disabled = false; }
                });
            }

            const btnReopen = document.getElementById('btn-reopen');
            if (btnReopen) {
                btnReopen.addEventListener('click', async function () {
                    this.innerHTML = '<span class="spinner"></span>';
                    this.disabled = true;
                    const res = await apiCall('PATCH', `/api/v1/tasks/${taskId}/reopen`);
                    if (res.ok) { toast('{{ __('app.task_toast_reopened') }}', 'info'); setTimeout(() => location.reload(), 600); }
                    else { toast('{{ __('app.task_toast_err_reopen') }}', 'error'); this.innerHTML = '{{ __('app.task_reopen_btn') }}'; this.disabled = false; }
                });
            }

            document.getElementById('btn-delete').addEventListener('click', function () {
                confirmDialog('{{ __('app.task_delete_title') }}', '{{ __('app.task_delete_msg') }}', async () => {
                    const res = await apiCall('DELETE', `/api/v1/tasks/${taskId}`);
                    if (res.ok) { toast('{{ __('app.task_toast_deleted') }}', 'info'); setTimeout(() => window.location.href = '/tasks', 600); }
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
                const descValue = editor.getText().trim() ? html : null;

                const categoryVal = document.getElementById('edit-category').value;
                const payload = {
                    title: document.getElementById('edit-title').value,
                    description: descValue,
                    status: document.getElementById('edit-status').value,
                    priority: document.getElementById('edit-priority').value,
                    due_date: document.getElementById('edit-due-date').value || null,
                    category_id: categoryVal ? parseInt(categoryVal) : null,
                };

                try {
                    const res = await apiCall('PUT', `/api/v1/tasks/${taskId}`, payload);
                    const data = await res.json();
                    if (res.ok) {
                        toast('{{ __('app.task_toast_saved') }}', 'success');
                        setTimeout(() => location.reload(), 700);
                    } else {
                        const msgs = data.errors
                            ? Object.values(data.errors).flat().join(' ')
                            : (data.message || 'Erro.');
                        alertEl.className = 'alert alert-error';
                        alertEl.textContent = msgs;
                        alertEl.style.display = 'block';
                    }
                } catch (e) {
                    toast('{{ __('app.task_toast_err_save') }}', 'error');
                } finally {
                    btn.innerHTML = '{{ __('app.task_save_changes') }}';
                    btn.disabled = false;
                }
            });
        </script>
    @endpush

@endsection