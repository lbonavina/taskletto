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

        #task-tiptap-editor .tiptap {
            outline: none;
            min-height: 136px;
        }

        #task-tiptap-editor .tiptap p.is-editor-empty:first-child::before {
            content: attr(data-placeholder);
            color: var(--muted);
            font-size: 13.5px;
            pointer-events: none;
            float: left;
            height: 0;
        }

        #task-tiptap-editor .tiptap>*+* {
            margin-top: 4px;
        }

        #task-tiptap-editor .tiptap p {
            margin-bottom: 4px;
        }

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

        #task-tiptap-editor .tiptap h1 {
            font-size: 22px;
            letter-spacing: -0.4px;
        }

        #task-tiptap-editor .tiptap h2 {
            font-size: 18px;
        }

        #task-tiptap-editor .tiptap h3 {
            font-size: 15px;
        }

        #task-tiptap-editor .tiptap ul,
        #task-tiptap-editor .tiptap ol {
            padding-left: 20px;
        }

        #task-tiptap-editor .tiptap li {
            margin: 2px 0;
        }

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

        #task-tiptap-editor .tiptap pre code {
            background: none;
            padding: 0;
            font-size: inherit;
        }

        #task-tiptap-editor .tiptap strong {
            font-weight: 600;
        }

        #task-tiptap-editor .tiptap em {
            font-style: italic;
        }

        #task-tiptap-editor .tiptap s {
            text-decoration: line-through;
        }

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
        .md-body {
            font-size: 13px;
            line-height: 1.65;
            color: var(--text);
            word-break: break-word;
        }

        .md-body p {
            margin: 0 0 6px;
        }

        .md-body p:last-child {
            margin-bottom: 0;
        }

        .md-body h1,
        .md-body h2,
        .md-body h3 {
            font-family: 'Codec Pro', sans-serif;
            font-weight: 700;
            margin: 10px 0 4px;
            letter-spacing: -.2px;
        }

        .md-body h1 {
            font-size: 18px;
        }

        .md-body h2 {
            font-size: 15px;
        }

        .md-body h3 {
            font-size: 13.5px;
        }

        .md-body ul,
        .md-body ol {
            padding-left: 20px;
            margin: 4px 0;
        }

        .md-body li {
            margin: 2px 0;
        }

        .md-body code {
            background: rgba(0, 0, 0, .25);
            border-radius: 4px;
            font-family: 'DM Sans', monospace;
            font-size: 12px;
            color: var(--accent);
            padding: 1px 5px;
        }

        .md-body pre {
            background: rgba(0, 0, 0, .25);
            border-radius: 8px;
            padding: 10px 14px;
            margin: 6px 0;
            overflow-x: auto;
        }

        .md-body pre code {
            background: none;
            padding: 0;
        }

        .md-body blockquote {
            border-left: 3px solid var(--accent);
            padding-left: 10px;
            color: var(--muted);
            margin: 6px 0;
        }

        .md-body strong {
            font-weight: 600;
        }

        .md-body em {
            font-style: italic;
        }

        .md-body a {
            color: var(--accent);
            text-decoration: underline;
            text-underline-offset: 2px;
        }

        html[data-theme=light] .md-body code,
        html[data-theme=light] .md-body pre {
            background: rgba(0, 0, 0, .07);
        }

        /* Inline edit mode */
        .comment-edit-textarea {
            width: 100%;
            resize: vertical;
            min-height: 60px;
            background: var(--surface2);
            border: 1px solid var(--accent);
            border-radius: 8px;
            padding: 8px 10px;
            font-size: 13px;
            font-family: inherit;
            color: var(--text);
            line-height: 1.55;
            outline: none;
            box-sizing: border-box;
            box-shadow: 0 0 0 3px rgba(255, 145, 77, .1);
        }

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
                            {{ $task->title }}
                        </h2>
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
                        {{ __('app.task_section_desc') }}
                    </div>
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
                    {{ __('app.task_section_edit') }}
                </div>
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
                            <button class="ttb-task-btn" data-cmd="underline" title="Sublinhado"
                                style="text-decoration:underline">U</button>
                            <button class="ttb-task-btn" data-cmd="strike" title="Tachado"
                                style="text-decoration:line-through">S</button>
                            <div class="ttb-task-sep"></div>
                            <button class="ttb-task-btn" data-cmd="h1" title="Título 1" style="font-size:11px">H1</button>
                            <button class="ttb-task-btn" data-cmd="h2" title="Título 2" style="font-size:11px">H2</button>
                            <button class="ttb-task-btn" data-cmd="h3" title="Título 3" style="font-size:11px">H3</button>
                            <div class="ttb-task-sep"></div>
                            <button class="ttb-task-btn" data-cmd="bulletList" title="Lista com marcadores">•≡</button>
                            <button class="ttb-task-btn" data-cmd="orderedList" title="Lista numerada">1≡</button>
                            <div class="ttb-task-sep"></div>
                            <button class="ttb-task-btn" data-cmd="blockquote" title="Citação">"</button>
                            <button class="ttb-task-btn" data-cmd="codeBlock" title="Bloco de código"
                                style="font-family:monospace;font-size:11px">&lt;/&gt;</button>
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
                                    {{ __('app.status_pending') }}
                                </option>
                                <option value="in_progress" {{ $task->status->value === 'in_progress' ? 'selected' : '' }}>
                                    {{ __('app.status_in_progress') }}
                                </option>
                                <option value="completed" {{ $task->status->value === 'completed' ? 'selected' : '' }}>
                                    {{ __('app.status_completed') }}
                                </option>
                                <option value="cancelled" {{ $task->status->value === 'cancelled' ? 'selected' : '' }}>
                                    {{ __('app.status_cancelled') }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>{{ __('app.task_label_priority') }}</label>
                        <div class="select-wrap">
                            <select id="edit-priority">
                                <option value="low" {{ $task->priority->value === 'low' ? 'selected' : '' }}>
                                    {{ __('app.priority_low') }}
                                </option>
                                <option value="medium" {{ $task->priority->value === 'medium' ? 'selected' : '' }}>
                                    {{ __('app.priority_medium') }}
                                </option>
                                <option value="high" {{ $task->priority->value === 'high' ? 'selected' : '' }}>
                                    {{ __('app.priority_high') }}
                                </option>
                                <option value="urgent" {{ $task->priority->value === 'urgent' ? 'selected' : '' }}>
                                    {{ __('app.priority_urgent') }}
                                </option>
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
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                    <div class="form-group">
                        <label>Recorrência</label>
                        <div class="select-wrap">
                            <select id="edit-recurrence">
                                <option value="none" {{ $task->recurrence->value === 'none' ? 'selected' : '' }}>Sem
                                    recorrência</option>
                                <option value="daily" {{ $task->recurrence->value === 'daily' ? 'selected' : '' }}>Diária
                                </option>
                                <option value="weekly" {{ $task->recurrence->value === 'weekly' ? 'selected' : '' }}>Semanal
                                </option>
                                <option value="monthly" {{ $task->recurrence->value === 'monthly' ? 'selected' : '' }}>Mensal
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" id="recurrence-ends-wrap"
                        style="{{ $task->recurrence->value === 'none' ? 'opacity:.4;pointer-events:none' : '' }}">
                        <label>Termina em</label>
                        <input type="date" id="edit-recurrence-ends"
                            value="{{ $task->recurrence_ends_at?->format('Y-m-d') }}" placeholder="Sem fim">
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
                        {{ __('app.task_section_history') }}
                    </div>
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

        {{-- Right sidebar --}}
        <div style="display:flex;flex-direction:column;gap:16px">

            {{-- Info card --}}
            <div class="card" style="font-size:13px">
                <div
                    style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:var(--muted);margin-bottom:16px">
                    {{ __('app.task_section_info') }}
                </div>
                <div style="display:flex;flex-direction:column;gap:12px">
                    <div>
                        <div style="font-size:11px;color:var(--muted);margin-bottom:2px">ID</div>
                        <div style="font-family:'DM Sans',monospace">#{{ $task->id }}</div>
                    </div>
                    <div>
                        <div style="font-size:11px;color:var(--muted);margin-bottom:2px">{{ __('app.task_info_created') }}
                        </div>
                        <div style="font-family:'DM Sans',monospace;font-size:12px">
                            {{ $task->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div>
                        <div style="font-size:11px;color:var(--muted);margin-bottom:2px">{{ __('app.task_info_updated') }}
                        </div>
                        <div style="font-family:'DM Sans',monospace;font-size:12px">
                            {{ $task->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                    @if($task->completed_at)
                        <div>
                            <div style="font-size:11px;color:var(--muted);margin-bottom:2px">Concluída em</div>
                            <div style="font-family:'DM Sans',monospace;font-size:12px;color:var(--success)">
                                {{ $task->completed_at->format('d/m/Y H:i') }}
                            </div>
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
                    @if($task->isRecurring())
                        <div>
                            <div style="font-size:11px;color:var(--muted);margin-bottom:2px">Recorrência</div>
                            <div style="font-size:12px;display:flex;align-items:center;gap:5px">
                                <span style="font-size:14px">🔁</span>
                                <span>{{ $task->recurrence->label() }}</span>
                                @if($task->recurrence_ends_at)
                                    <span style="font-size:11px;color:var(--muted)">até
                                        {{ $task->recurrence_ends_at->format('d/m/Y') }}</span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Time tracking card --}}
            <div class="card" id="time-card">
                <div
                    style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:var(--muted);margin-bottom:14px">
                    Tempo
                </div>
                {{-- Tracked display --}}
                    <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:10px">
                        <div>
                            <div style="font-size:11px;color:var(--muted);margin-bottom:2px">Registrado</div>
                            <div id="tracked-display" style="font-size:22px;font-family:'DM Sans',monospace;font-weight:700;color:var(--text);letter-spacing:-.5px;line-height:1">{{ $task->formattedTrackedTime() }}</div>
                        </div>
                        <div style="text-align:right">
                            <div style="font-size:11px;color:var(--muted);margin-bottom:2px">Estimativa</div>
                            <div style="display:flex;align-items:center;gap:3px;justify-content:flex-end">
                                <input id="estimated-minutes" type="number" min="0" step="5"
                                    value="{{ $task->estimated_minutes }}"
                                    placeholder="—"
                                    style="width:48px;background:transparent;border:none;border-bottom:1px solid var(--border);border-radius:0;padding:2px 4px;font-size:13px;font-family:'DM Sans',monospace;font-weight:600;color:var(--text);text-align:right;outline:none;transition:border-color .15s"
                                    onfocus="this.style.borderBottomColor='var(--accent)'"
                                    onblur="this.style.borderBottomColor='var(--border)';saveEstimate(this.value)">
                                <span style="font-size:11px;color:var(--muted)">min</span>
                            </div>
                        </div>
                    </div>

                    {{-- Progress bar (always visible, empty if no estimate) --}}
                    <div style="background:var(--surface2);border-radius:99px;height:4px;overflow:hidden;margin-bottom:12px">
                        @php
                            $pct = $task->estimated_minutes
                                ? min(100, round(($task->tracked_seconds / ($task->estimated_minutes * 60)) * 100))
                                : 0;
                            $barColor = $pct >= 100 ? 'var(--danger)' : 'var(--accent)';
                        @endphp
                        <div id="time-progress-bar" style="height:100%;width:{{ $pct }}%;background:{{ $barColor }};border-radius:99px;transition:width .4s"></div>
                    </div>

                    {{-- Timer button --}}
                    @if(!$task->isCompleted())
                        <button id="btn-timer" class="btn btn-ghost btn-sm" style="width:100%;justify-content:center;gap:8px;padding:8px">
                            <span id="timer-icon" style="font-size:12px">▶</span>
                            <span id="timer-label">Iniciar timer</span>
                        </button>
                        <div id="timer-elapsed" style="display:none;font-size:11px;color:var(--accent);text-align:center;font-family:'DM Sans',monospace;margin-top:6px;font-weight:500"></div>
                    @endif
                </div>

                {{-- Comments card --}}
                <div class="card" id="comments-card">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
                        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:var(--muted)">
                            Comentários
                            <span id="comment-count" style="margin-left:6px;background:var(--surface2);color:var(--muted);border-radius:20px;padding:1px 8px;font-size:10px;font-weight:700">{{ $task->comments()->count() }}</span>
                        </div>
                        <span style="font-size:11px;color:var(--muted)">Suporta Markdown</span>
                    </div>

                    {{-- Comment list (populated by JS for pagination) --}}
                    <div id="comment-list" style="display:flex;flex-direction:column;gap:0"></div>

                    {{-- Load more --}}
                    <div id="comments-load-more-wrap" style="display:none;text-align:center;padding:10px 0">
                        <button id="btn-load-more" class="btn btn-ghost btn-sm">Carregar mais</button>
                    </div>

                    {{-- New comment form --}}
                    <div style="margin-top:14px;display:flex;flex-direction:column;gap:8px">
                        {{-- Tab bar: Write / Preview --}}
                        <div style="display:flex;gap:0;border-bottom:1px solid var(--border);margin-bottom:4px">
                            <button id="tab-write" style="background:none;border:none;padding:5px 14px;font-size:12px;font-weight:600;color:var(--accent);border-bottom:2px solid var(--accent);cursor:pointer;margin-bottom:-1px;font-family:inherit">Escrever</button>
                            <button id="tab-preview" style="background:none;border:none;padding:5px 14px;font-size:12px;font-weight:600;color:var(--muted);border-bottom:2px solid transparent;cursor:pointer;margin-bottom:-1px;font-family:inherit">Pré-visualizar</button>
                        </div>
                        <textarea id="comment-body" rows="3" placeholder="Escreva um comentário… Suporta **negrito**, _itálico_, `código`, listas, etc." style="width:100%;resize:vertical;min-height:72px;background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:10px 12px;font-size:13.5px;font-family:inherit;color:var(--text);line-height:1.55;transition:border-color .15s,box-shadow .15s;outline:none;box-sizing:border-box" onfocus="this.style.borderColor='var(--accent)';this.style.boxShadow='0 0 0 3px rgba(255,145,77,.1)'" onblur="this.style.borderColor='var(--border)';this.style.boxShadow='none'"></textarea>
                        <div id="comment-preview" style="display:none;min-height:72px;background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:10px 12px;font-size:13.5px;color:var(--text);line-height:1.55" class="md-body"></div>
                        <div style="display:flex;align-items:center;justify-content:space-between">
                            <span id="comment-char-count" style="font-size:11px;color:var(--muted);font-family:'DM Sans',monospace">0 / 2000</span>
                            <button id="btn-add-comment" class="btn btn-primary btn-sm">Comentar</button>
                        </div>
                    </div>
                </div>

            </div>{{-- /right sidebar --}}

        </div>{{-- /grid --}}

        @push('scripts')
            <script type="module">
                import { Editor } from 'https://esm.sh/@tiptap/core@3';
                import StarterKit from 'https://esm.sh/@tiptap/starter-kit@3';
                import Underline from 'https://esm.sh/@tiptap/extension-underline@3';
                import Placeholder from 'https://esm.sh/@tiptap/extension-placeholder@3';
                import { marked } from 'https://esm.sh/marked@12';

                marked.setOptions({ breaks: true, gfm: true });

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
                    const recurrenceVal = document.getElementById('edit-recurrence').value;
                    const payload = {
                        title: document.getElementById('edit-title').value,
                        description: descValue,
                        status: document.getElementById('edit-status').value,
                        priority: document.getElementById('edit-priority').value,
                        due_date: document.getElementById('edit-due-date').value || null,
                        category_id: categoryVal ? parseInt(categoryVal) : null,
                        recurrence: recurrenceVal,
                        recurrence_ends_at: recurrenceVal !== 'none'
                            ? (document.getElementById('edit-recurrence-ends').value || null)
                            : null,
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

                // Enable/disable "Termina em" based on recurrence select
                document.getElementById('edit-recurrence').addEventListener('change', function () {
                    const endsWrap = document.getElementById('recurrence-ends-wrap');
                    const isNone = this.value === 'none';
                    endsWrap.style.opacity = isNone ? '.4' : '1';
                    endsWrap.style.pointerEvents = isNone ? 'none' : '';
                    if (isNone) document.getElementById('edit-recurrence-ends').value = '';
                });

                // ── Time tracking ─────────────────────────────────────────────────────────────
                let timerInterval = null;
                let timerRunning = false;
                let timerStartedAt = null;

                function fmtSeconds(s) {
                    const h = Math.floor(s / 3600);
                    const m = Math.floor((s % 3600) / 60);
                    const sec = s % 60;
                    return h > 0
                        ? `${h}h ${String(m).padStart(2,'0')}m ${String(sec).padStart(2,'0')}s`
                        : `${String(m).padStart(2,'0')}m ${String(sec).padStart(2,'0')}s`;
                }

                function fmtTracked(s) {
                    if (s < 60) return `${s}s`;
                    const m = Math.floor(s / 60);
                    const h = Math.floor(m / 60);
                    return h > 0 ? `${h}h ${m % 60}m` : `${m}m`;
                }

                function setTimerUI(running, startedAt, trackedSeconds) {
                    timerRunning = running;
                    timerStartedAt = startedAt ? new Date(startedAt) : null;
                    const btn = document.getElementById('btn-timer');
                    const icon = document.getElementById('timer-icon');
                    const lbl = document.getElementById('timer-label');
                    const elapsed = document.getElementById('timer-elapsed');
                    if (!btn) return;
                    if (running) {
                        btn.style.borderColor = 'var(--accent)';
                        btn.style.color = 'var(--accent)';
                        icon.textContent = '⏹';
                        lbl.textContent = 'Parar timer';
                        elapsed.style.display = 'block';
                        clearInterval(timerInterval);
                        timerInterval = setInterval(() => {
                            const secs = Math.floor((Date.now() - timerStartedAt) / 1000);
                            elapsed.textContent = 'Sessão atual: ' + fmtSeconds(secs);
                        }, 1000);
                    } else {
                        btn.style.borderColor = '';
                        btn.style.color = '';
                        icon.textContent = '▶';
                        lbl.textContent = 'Iniciar timer';
                        elapsed.style.display = 'none';
                        clearInterval(timerInterval);
                    }
                    if (trackedSeconds !== undefined) {
                        const td = document.getElementById('tracked-display');
                        if (td) td.textContent = fmtTracked(trackedSeconds);
                    }
                }

                // Load initial timer status
                (async () => {
                    try {
                        const r = await apiCall('GET', `/api/v1/tasks/${taskId}/time/status`);
                        const d = await r.json();
                        setTimerUI(d.running, d.started_at, d.tracked_seconds);
                    } catch {}
                })();

                document.getElementById('btn-timer')?.addEventListener('click', async function () {
                    this.disabled = true;
                    try {
                        if (timerRunning) {
                            const r = await apiCall('POST', `/api/v1/tasks/${taskId}/time/stop`);
                            const d = await r.json();
                            setTimerUI(false, null, d.tracked_seconds);
                            toast('Timer parado — ' + fmtTracked(d.elapsed_seconds) + ' registrado', 'success');
                        } else {
                            const r = await apiCall('POST', `/api/v1/tasks/${taskId}/time/start`);
                            const d = await r.json();
                            setTimerUI(true, d.started_at, d.tracked_seconds);
                        }
                    } catch {
                        toast('Erro ao controlar timer', 'error');
                    } finally {
                        this.disabled = false;
                    }
                });

                async function saveEstimate(val) {
                    const minutes = parseInt(val) || null;
                    try {
                        await apiCall('PUT', `/api/v1/tasks/${taskId}`, { estimated_minutes: minutes });
                    } catch {}
                }

                // ── Comments ─────────────────────────────────────────────────────────────────
                const commentBody      = document.getElementById('comment-body');
                const commentPreview   = document.getElementById('comment-preview');
                const commentCharCount = document.getElementById('comment-char-count');
                const btnAddComment    = document.getElementById('btn-add-comment');
                const commentList      = document.getElementById('comment-list');
                const countBadge       = document.getElementById('comment-count');
                const loadMoreWrap     = document.getElementById('comments-load-more-wrap');
                const btnLoadMore      = document.getElementById('btn-load-more');
                const tabWrite         = document.getElementById('tab-write');
                const tabPreview       = document.getElementById('tab-preview');

                let currentPage = 1;
                let lastPage    = 1;
                let totalCount  = parseInt(countBadge?.textContent) || 0;

                function renderMd(text) { return marked.parse(text || ''); }

                function setCount(n) { totalCount = n; if (countBadge) countBadge.textContent = n; }
                function deltaCount(d) { setCount(Math.max(0, totalCount + d)); }

                function showEmpty() {
                    if (document.getElementById('comments-empty')) return;
                    const el = document.createElement('div');
                    el.id = 'comments-empty';
                    el.style.cssText = 'text-align:center;padding:20px 0;color:var(--muted);font-size:13px';
                    el.textContent = 'Nenhum comentário ainda.';
                    commentList.appendChild(el);
                }

                function setTab(tab) {
                    const accent = 'var(--accent)', muted = 'var(--muted)', none = 'transparent';
                    if (tab === 'write') {
                        commentBody.style.display = ''; commentPreview.style.display = 'none';
                        tabWrite.style.color = accent; tabWrite.style.borderBottomColor = accent;
                        tabPreview.style.color = muted; tabPreview.style.borderBottomColor = none;
                    } else {
                        commentPreview.innerHTML = renderMd(commentBody.value) || '<em style="color:var(--muted)">Nada para pré-visualizar.</em>';
                        commentBody.style.display = 'none'; commentPreview.style.display = '';
                        tabPreview.style.color = accent; tabPreview.style.borderBottomColor = accent;
                        tabWrite.style.color = muted; tabWrite.style.borderBottomColor = none;
                    }
                }
                tabWrite?.addEventListener('click', () => setTab('write'));
                tabPreview?.addEventListener('click', () => setTab('preview'));
                commentBody?.addEventListener('input', () => {
                    const len = commentBody.value.length;
                    commentCharCount.textContent = `${len} / 2000`;
                    commentCharCount.style.color = len > 1800 ? 'var(--danger)' : 'var(--muted)';
                });
                commentBody?.addEventListener('keydown', e => {
                    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') { e.preventDefault(); btnAddComment.click(); }
                });

                function buildCommentEl(comment) {
                    const div = document.createElement('div');
                    div.className = 'comment-item';
                    div.dataset.id = comment.id; div.dataset.body = comment.body;
                    div.style.cssText = 'display:flex;gap:10px;padding:10px 0;border-bottom:1px solid var(--border)';
                    const editedLabel = comment.edited ? `<span style="color:var(--muted);font-size:10px;margin-left:6px">(editado)</span>` : '';
                    div.innerHTML = `
                        <div style="width:28px;height:28px;border-radius:50%;background:rgba(255,145,77,.15);display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;margin-top:1px">💬</div>
                        <div style="flex:1;min-width:0">
                            <div class="comment-body-display md-body">${renderMd(comment.body)}</div>
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-top:5px;flex-wrap:wrap;gap:4px">
                                <span style="color:var(--muted);font-size:11px;font-family:'DM Sans',monospace">${comment.created_at}${editedLabel}</span>
                                <div style="display:flex;gap:4px">
                                    <button class="btn-edit-comment" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:11px;padding:2px 6px;border-radius:4px;transition:color .15s,background .15s">Editar</button>
                                    <button class="btn-delete-comment" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:11px;padding:2px 6px;border-radius:4px;transition:color .15s,background .15s">Excluir</button>
                                </div>
                            </div>
                        </div>`;
                    div.querySelectorAll('button').forEach(btn => {
                        btn.addEventListener('mouseenter', () => { const d = btn.classList.contains('btn-delete-comment'); btn.style.color = d ? 'var(--danger)' : 'var(--accent)'; btn.style.background = d ? 'rgba(224,84,84,.1)' : 'rgba(255,145,77,.1)'; });
                        btn.addEventListener('mouseleave', () => { btn.style.color = 'var(--muted)'; btn.style.background = 'none'; });
                    });
                    div.querySelector('.btn-edit-comment').addEventListener('click', () => startEdit(div));
                    div.querySelector('.btn-delete-comment').addEventListener('click', () => handleDelete(div));
                    return div;
                }

                function startEdit(el) {
                    if (el.querySelector('.comment-edit-textarea')) return;
                    const display = el.querySelector('.comment-body-display');
                    display.style.display = 'none';
                    const wrap = document.createElement('div');
                    const textarea = document.createElement('textarea');
                    textarea.className = 'comment-edit-textarea';
                    textarea.value = el.dataset.body;
                    textarea.style.cssText = 'width:100%;resize:vertical;min-height:72px;background:var(--surface2);border:1px solid var(--accent);border-radius:8px;padding:10px 12px;font-size:13px;font-family:inherit;color:var(--text);line-height:1.55;outline:none;box-sizing:border-box;box-shadow:0 0 0 3px rgba(255,145,77,.1)';
                    const counter = document.createElement('div');
                    counter.style.cssText = 'font-size:11px;color:var(--muted);text-align:right;margin-top:3px';
                    counter.textContent = `${textarea.value.length} / 2000`;
                    textarea.addEventListener('input', () => { counter.textContent = `${textarea.value.length} / 2000`; counter.style.color = textarea.value.length > 1800 ? 'var(--danger)' : 'var(--muted)'; });
                    const actions = document.createElement('div');
                    actions.style.cssText = 'display:flex;gap:6px;justify-content:flex-end;margin-top:6px';
                    const btnCancel = document.createElement('button'); btnCancel.textContent = 'Cancelar'; btnCancel.className = 'btn btn-ghost btn-sm';
                    const btnSave = document.createElement('button'); btnSave.textContent = 'Salvar'; btnSave.className = 'btn btn-primary btn-sm';
                    btnCancel.addEventListener('click', () => { wrap.remove(); display.style.display = ''; });
                    btnSave.addEventListener('click', () => saveEdit(el, textarea, display, wrap, btnSave));
                    textarea.addEventListener('keydown', e => { if ((e.ctrlKey||e.metaKey) && e.key==='Enter') { e.preventDefault(); btnSave.click(); } if (e.key==='Escape') { wrap.remove(); display.style.display=''; } });
                    actions.append(btnCancel, btnSave);
                    wrap.append(textarea, counter, actions);
                    display.after(wrap);
                    textarea.focus();
                }

                async function saveEdit(el, textarea, display, wrap, btnSave) {
                    const body = textarea.value.trim();
                    if (!body) { textarea.focus(); return; }
                    btnSave.innerHTML = '<span class="spinner"></span>'; btnSave.disabled = true;
                    const res = await apiCall('PATCH', `/api/v1/tasks/${taskId}/comments/${el.dataset.id}`, { body });
                    if (res.ok) {
                        const updated = await res.json();
                        el.dataset.body = updated.body;
                        display.innerHTML = renderMd(updated.body);
                        const ts = el.querySelector('[style*="monospace"]');
                        if (ts && !ts.querySelector('[data-edited]')) ts.insertAdjacentHTML('beforeend', '<span style="color:var(--muted);font-size:10px;margin-left:6px" data-edited>(editado)</span>');
                        wrap.remove(); display.style.display = '';
                        toast('Comentário atualizado.', 'success');
                    } else {
                        const d = await res.json().catch(()=>({}));
                        toast(d.errors ? Object.values(d.errors).flat().join(' ') : (d.message||'Erro.'), 'error');
                        btnSave.innerHTML = 'Salvar'; btnSave.disabled = false;
                    }
                }

                async function handleDelete(el) {
                    confirmDialog('Excluir comentário', 'Esta ação não pode ser desfeita.', async () => {
                        const res = await apiCall('DELETE', `/api/v1/tasks/${taskId}/comments/${el.dataset.id}`);
                        if (res.ok) { el.remove(); deltaCount(-1); if (!commentList.querySelector('.comment-item')) showEmpty(); toast('Comentário excluído.', 'info'); }
                        else toast('Erro ao excluir.', 'error');
                    });
                }

                async function loadComments(page = 1) {
                    if (btnLoadMore) { btnLoadMore.innerHTML = '<span class="spinner"></span>'; btnLoadMore.disabled = true; }
                    try {
                        const res = await apiCall('GET', `/api/v1/tasks/${taskId}/comments?page=${page}`);
                        const data = await res.json();
                        if (page === 1) { commentList.innerHTML = ''; document.getElementById('comments-empty')?.remove(); }
                        data.data.length === 0 && page === 1 ? showEmpty() : data.data.forEach(c => commentList.appendChild(buildCommentEl(c)));
                        currentPage = data.current_page; lastPage = data.last_page; setCount(data.total);
                        if (loadMoreWrap) loadMoreWrap.style.display = currentPage < lastPage ? '' : 'none';
                    } catch { toast('Erro ao carregar comentários.', 'error'); }
                    finally { if (btnLoadMore) { btnLoadMore.innerHTML = 'Carregar mais'; btnLoadMore.disabled = false; } }
                }

                btnLoadMore?.addEventListener('click', () => loadComments(currentPage + 1));

                btnAddComment?.addEventListener('click', async function () {
                    const body = commentBody.value.trim();
                    if (!body) { setTab('write'); commentBody.focus(); return; }
                    this.innerHTML = '<span class="spinner"></span>'; this.disabled = true;
                    try {
                        const res = await apiCall('POST', `/api/v1/tasks/${taskId}/comments`, { body });
                        if (res.ok) {
                            const comment = await res.json();
                            document.getElementById('comments-empty')?.remove();
                            commentList.insertBefore(buildCommentEl(comment), commentList.firstChild);
                            commentBody.value = ''; commentCharCount.textContent = '0 / 2000';
                            setTab('write'); deltaCount(+1);
                            toast('Comentário adicionado.', 'success');
                        } else { const d = await res.json(); toast(d.errors ? Object.values(d.errors).flat().join(' ') : (d.message||'Erro.'), 'error'); }
                    } catch { toast('Erro de conexão.', 'error'); }
                    finally { this.innerHTML = 'Comentar'; this.disabled = false; }
                });

                loadComments(1);
            </script>
        @endpush

@endsection