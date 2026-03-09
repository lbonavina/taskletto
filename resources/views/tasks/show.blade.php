@extends('layouts.app')
@section('title', $task->title)
@section('page-title', __('app.task_detail_title'))

@section('topbar-actions')
    <a href="/tasks" class="btn btn-ghost btn-sm">{{ __('app.task_back') }}</a>
@endsection

@push('styles')
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
            background: rgba(0,0,0,.3);
            border-radius: 4px;
            font-family: 'DM Sans', monospace;
            font-size: 12.5px;
            color: var(--accent);
            padding: 1px 5px;
        }
        #task-tiptap-editor .tiptap pre {
            background: rgba(0,0,0,.3);
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
        #task-tiptap-editor .tiptap a { color: var(--accent); text-decoration: underline; text-underline-offset: 2px; }
        html[data-theme=light] #task-tiptap-editor { background: #ffffff; color: #18181c; }
        html[data-theme=light] #task-tiptap-editor .tiptap code,
        html[data-theme=light] #task-tiptap-editor .tiptap pre { background: rgba(0,0,0,.06); }

        /* ── Markdown body ─────────────────────────────────────────────── */
        .md-body { font-size: 13px; line-height: 1.65; color: var(--text); word-break: break-word; }
        .md-body p { margin: 0 0 6px; }
        .md-body p:last-child { margin-bottom: 0; }
        .md-body h1, .md-body h2, .md-body h3 { font-family: 'Codec Pro', sans-serif; font-weight: 700; margin: 10px 0 4px; letter-spacing: -.2px; }
        .md-body h1 { font-size: 18px; }
        .md-body h2 { font-size: 15px; }
        .md-body h3 { font-size: 13.5px; }
        .md-body ul, .md-body ol { padding-left: 20px; margin: 4px 0; }
        .md-body li { margin: 2px 0; }
        .md-body code { background: rgba(0,0,0,.25); border-radius: 4px; font-family: 'DM Sans', monospace; font-size: 12px; color: var(--accent); padding: 1px 5px; }
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
        }
        .prop-date-input:focus { border-color: var(--accent); }
    </style>
@endpush

@section('content')

    {{-- ── Main grid: left column + sidebar ─────────────────────────── --}}
    <div id="task-detail-grid">

        {{-- Left column --}}
        <div>

            {{-- Header card --}}
            <div class="card" style="margin-bottom:16px">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px">
                    <div style="flex:1;min-width:0">
                        <h2 style="font-family:'Codec Pro',sans-serif;font-size:22px;font-weight:700;letter-spacing:-.4px;line-height:1.2;margin-bottom:8px">{{ $task->title }}</h2>
                        <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap">
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
            </div>{{-- /header card --}}

            {{-- Edit card --}}
            <div class="card" style="margin-bottom:16px">
                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:var(--muted);margin-bottom:18px">
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

                <div style="text-align:right;margin-top:4px">
                    <button class="btn btn-primary" id="btn-save-edit">{{ __('app.task_save_changes') }}</button>
                </div>
            </div>{{-- /edit card --}}

            {{-- Comments card --}}
            <div class="card" id="comments-card">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
                    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:var(--muted)">
                        Comentários
                        <span id="comment-count" style="margin-left:6px;background:var(--surface2);color:var(--muted);border-radius:20px;padding:1px 8px;font-size:10px;font-weight:700">{{ $task->comments()->count() }}</span>
                    </div>
                    <span style="font-size:11px;color:var(--muted)">Suporta Markdown</span>
                </div>
                <div id="comment-list" style="display:flex;flex-direction:column;gap:0"></div>
                <div id="comments-load-more-wrap" style="display:none;text-align:center;padding:10px 0">
                    <button id="btn-load-more" class="btn btn-ghost btn-sm">Carregar mais</button>
                </div>
                <div style="margin-top:14px;display:flex;flex-direction:column;gap:8px">
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
            </div>{{-- /comments card --}}

            {{-- History trigger --}}
            @if($task->histories && $task->histories->count())
            <div style="text-align:center;padding:8px 0">
                <button id="btn-open-history" style="background:none;border:none;cursor:pointer;font-size:11px;color:var(--muted);font-family:inherit;display:inline-flex;align-items:center;gap:5px;padding:6px 10px;border-radius:6px;transition:color .15s,background .15s"
                    onmouseover="this.style.color='var(--text)';this.style.background='var(--surface2)'"
                    onmouseout="this.style.color='var(--muted)';this.style.background='none'">
                    <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="8" cy="8" r="6.5"/><path d="M8 4.5V8l2.5 2"/></svg>
                    Ver histórico de alterações ({{ $task->histories->count() }})
                </button>
            </div>
            @endif

        </div>{{-- /left column --}}

        {{-- Right sidebar --}}
        <div id="task-sidebar">

            {{-- Properties card --}}
            <div class="card" style="font-size:13px">
                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:var(--muted);margin-bottom:14px">Propriedades</div>
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
                        <input type="date" id="sidebar-due-date" value="{{ $task->due_date?->format('Y-m-d') }}" class="prop-date-input">
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
                        <div style="font-size:11px;color:var(--muted);margin-bottom:4px">Recorrência</div>
                        <div class="select-wrap">
                            <select id="sidebar-recurrence" style="font-size:12px">
                                <option value="none"    {{ $task->recurrence->value === 'none'    ? 'selected' : '' }}>Sem recorrência</option>
                                <option value="daily"   {{ $task->recurrence->value === 'daily'   ? 'selected' : '' }}>Diária</option>
                                <option value="weekly"  {{ $task->recurrence->value === 'weekly'  ? 'selected' : '' }}>Semanal</option>
                                <option value="monthly" {{ $task->recurrence->value === 'monthly' ? 'selected' : '' }}>Mensal</option>
                            </select>
                        </div>
                    </div>

                    <div id="sidebar-recurrence-ends-wrap" style="{{ $task->recurrence->value === 'none' ? 'display:none' : '' }}">
                        <div style="font-size:11px;color:var(--muted);margin-bottom:4px">Termina em</div>
                        <input type="date" id="sidebar-recurrence-ends" value="{{ $task->recurrence_ends_at?->format('Y-m-d') }}" class="prop-date-input">
                    </div>

                    <div id="props-saved" style="display:none;font-size:11px;color:var(--success);text-align:right">✓ Salvo</div>

                </div>
            </div>{{-- /properties card --}}

            {{-- Info card --}}
            <div class="card" style="font-size:13px">
                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:var(--muted);margin-bottom:16px">
                    {{ __('app.task_section_info') }}
                </div>
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
                    @if($task->isRecurring())
                    <div>
                        <div style="font-size:11px;color:var(--muted);margin-bottom:2px">Recorrência</div>
                        <div style="font-size:12px;display:flex;align-items:center;gap:5px">
                            <span style="font-size:14px">🔁</span>
                            <span>{{ $task->recurrence->label() }}</span>
                            @if($task->recurrence_ends_at)
                                <span style="font-size:11px;color:var(--muted)">até {{ $task->recurrence_ends_at->format('d/m/Y') }}</span>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>{{-- /info card --}}

            {{-- Time tracking card --}}
            <div class="card" id="time-card">
                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:var(--muted);margin-bottom:14px">Tempo</div>

                <div style="margin-bottom:12px">
                    <div style="font-size:11px;color:var(--muted);margin-bottom:4px">Registrado</div>
                    <div id="tracked-display" style="font-size:24px;font-family:'DM Sans',monospace;font-weight:700;color:var(--text);letter-spacing:-.5px;line-height:1">{{ $task->formattedTrackedTime() }}</div>
                </div>

                @php
                    $pct      = $task->estimated_minutes ? min(100, round(($task->tracked_seconds / ($task->estimated_minutes * 60)) * 100)) : 0;
                    $barColor = $pct >= 100 ? 'var(--danger)' : 'var(--accent)';
                    $estH     = $task->estimated_minutes ? intdiv($task->estimated_minutes, 60) : 0;
                    $estM     = $task->estimated_minutes ? $task->estimated_minutes % 60 : 0;
                @endphp
                <div style="background:var(--surface2);border-radius:99px;height:4px;overflow:hidden;margin-bottom:14px">
                    <div id="time-progress-bar" style="height:100%;width:{{ $pct }}%;background:{{ $barColor }};border-radius:99px;transition:width .4s"></div>
                </div>

                <div style="margin-bottom:12px">
                    <div style="font-size:11px;color:var(--muted);margin-bottom:6px">Estimativa</div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px">
                        <div id="est-h-wrap" style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:8px 10px;display:flex;flex-direction:column;gap:3px;transition:border-color .15s;cursor:text" onclick="document.getElementById('est-h').focus()">
                            <div style="font-size:9px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.6px">Horas</div>
                            <input id="est-h" type="number" min="0" max="99" step="1" value="{{ $estH ?: '' }}" placeholder="0"
                                style="background:transparent;border:none;outline:none;font-size:20px;font-family:'DM Sans',monospace;font-weight:700;color:var(--text);width:100%;padding:0;line-height:1;-moz-appearance:textfield">
                        </div>
                        <div id="est-m-wrap" style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:8px 10px;display:flex;flex-direction:column;gap:3px;transition:border-color .15s;cursor:text" onclick="document.getElementById('est-m').focus()">
                            <div style="font-size:9px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.6px">Minutos</div>
                            <input id="est-m" type="number" min="0" max="59" step="5" value="{{ $estM ?: '' }}" placeholder="0"
                                style="background:transparent;border:none;outline:none;font-size:20px;font-family:'DM Sans',monospace;font-weight:700;color:var(--text);width:100%;padding:0;line-height:1;-moz-appearance:textfield">
                        </div>
                    </div>
                    <button id="btn-save-estimate" class="btn btn-ghost btn-sm" style="width:100%;justify-content:center;margin-top:6px;gap:6px">
                        <svg width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 8l4 4 8-8"/></svg>
                        Salvar estimativa
                    </button>
                    <div id="est-summary" style="font-size:11px;color:var(--muted);margin-top:6px;text-align:center;min-height:14px">
                        @if($task->estimated_minutes)
                            {{ $estH > 0 ? $estH.'h ' : '' }}{{ $estM > 0 ? $estM.'min' : '' }} estimados
                            @if($pct > 0) · <span style="color:{{ $pct >= 100 ? 'var(--danger)' : 'var(--accent)' }}">{{ $pct }}% concluído</span> @endif
                        @endif
                    </div>
                </div>

                @if(!$task->isCompleted())
                    <button id="btn-timer" class="btn btn-ghost btn-sm" style="width:100%;justify-content:center;gap:8px;padding:8px">
                        <span id="timer-icon" style="font-size:12px">▶</span>
                        <span id="timer-label">Iniciar timer</span>
                    </button>
                    <div id="timer-elapsed" style="display:none;font-size:11px;color:var(--accent);text-align:center;font-family:'DM Sans',monospace;margin-top:6px;font-weight:500"></div>
                @endif
            </div>{{-- /time-card --}}

        </div>{{-- /right sidebar --}}

    </div>{{-- /grid --}}

    {{-- History modal --}}
    @if($task->histories && $task->histories->count())
    <div id="history-modal" style="display:none;position:fixed;inset:0;z-index:1000;align-items:center;justify-content:center">
        <div id="history-backdrop" style="position:absolute;inset:0;background:rgba(0,0,0,.55);backdrop-filter:blur(2px)"></div>
        <div style="position:relative;z-index:1;background:var(--surface);border:1px solid var(--border);border-radius:14px;width:100%;max-width:480px;max-height:70vh;display:flex;flex-direction:column;box-shadow:0 24px 60px rgba(0,0,0,.4);margin:0 16px">
            <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 20px 14px;border-bottom:1px solid var(--border);flex-shrink:0">
                <div style="display:flex;align-items:center;gap:8px">
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="var(--accent)" stroke-width="1.8"><circle cx="8" cy="8" r="6.5"/><path d="M8 4.5V8l2.5 2"/></svg>
                    <span style="font-size:13px;font-weight:600;color:var(--text)">Histórico de alterações</span>
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
                        <div style="font-size:11px;color:var(--muted);font-family:'DM Sans',monospace;margin-top:2px">{{ $h->created_at?->format('d/m/Y \à\s H:i') }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @push('scripts')
        <script type="module">
            import { Editor } from 'https://esm.sh/@tiptap/core@3';
            import StarterKit from 'https://esm.sh/@tiptap/starter-kit@3';
            import Underline from 'https://esm.sh/@tiptap/extension-underline@3';
            import Placeholder from 'https://esm.sh/@tiptap/extension-placeholder@3';
            import { marked } from 'https://esm.sh/marked@12';

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
                        toast('Erro ao salvar.', 'error');
                    }
                } catch { toast('Erro ao salvar.', 'error'); }
            }

            document.getElementById('sidebar-status')?.addEventListener('change', function () { saveProp('status', this.value); });
            document.getElementById('sidebar-priority')?.addEventListener('change', function () { saveProp('priority', this.value); });
            document.getElementById('sidebar-category')?.addEventListener('change', function () { saveProp('category_id', this.value ? parseInt(this.value) : null); });
            document.getElementById('sidebar-due-date')?.addEventListener('change', function () { saveProp('due_date', this.value || null); });
            document.getElementById('sidebar-recurrence')?.addEventListener('change', function () {
                const wrap = document.getElementById('sidebar-recurrence-ends-wrap');
                wrap.style.display = this.value === 'none' ? 'none' : '';
                if (this.value === 'none') document.getElementById('sidebar-recurrence-ends').value = '';
                saveProp('recurrence', this.value);
            });
            document.getElementById('sidebar-recurrence-ends')?.addEventListener('change', function () { saveProp('recurrence_ends_at', this.value || null); });

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
                    if (res.ok) { toast('{{ __('app.task_toast_deleted') }}', 'info'); setTimeout(()=>window.location.href='/tasks', 600); }
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
                    title: document.getElementById('edit-title').value,
                    description: editor.getText().trim() ? html : null,
                };
                try {
                    const res = await apiCall('PUT', `/api/v1/tasks/${taskId}`, payload);
                    const data = await res.json();
                    if (res.ok) {
                        toast('{{ __('app.task_toast_saved') }}', 'success');
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
                    icon.textContent='⏹'; lbl.textContent='Parar timer';
                    elapsed.style.display='block';
                    clearInterval(timerInterval);
                    timerInterval=setInterval(()=>{ elapsed.textContent='Sessão atual: '+fmtSeconds(Math.floor((Date.now()-timerStartedAt)/1000)); },1000);
                } else {
                    btn.style.borderColor=''; btn.style.color='';
                    icon.textContent='▶'; lbl.textContent='Iniciar timer';
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
                } catch { toast('Erro ao controlar timer','error'); }
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
                        ['est-h-wrap','est-m-wrap'].forEach(id=>{ const el=document.getElementById(id); if(el){el.style.borderColor='var(--success)';setTimeout(()=>{el.style.borderColor='var(--border)';},1000);} });
                    } else { toast('Erro ao salvar estimativa.','error'); }
                } catch { toast('Erro ao salvar estimativa.','error'); }
            }

            const estH=document.getElementById('est-h'), estM=document.getElementById('est-m');
            const estHWrap=document.getElementById('est-h-wrap'), estMWrap=document.getElementById('est-m-wrap');
            estH?.addEventListener('focus',()=>{if(estHWrap)estHWrap.style.borderColor='var(--accent)';});
            estH?.addEventListener('blur', ()=>{if(estHWrap)estHWrap.style.borderColor='var(--border)';});
            estM?.addEventListener('focus',()=>{if(estMWrap)estMWrap.style.borderColor='var(--accent)';});
            estM?.addEventListener('blur', ()=>{if(estMWrap)estMWrap.style.borderColor='var(--border)';});
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
            function showEmpty(){ if(document.getElementById('comments-empty'))return; const el=document.createElement('div'); el.id='comments-empty'; el.style.cssText='text-align:center;padding:20px 0;color:var(--muted);font-size:13px'; el.textContent='Nenhum comentário ainda.'; commentList.appendChild(el); }

            function setTab(tab){
                const accent='var(--accent)',muted='var(--muted)',none='transparent';
                if(tab==='write'){commentBody.style.display='';commentPreview.style.display='none';tabWrite.style.color=accent;tabWrite.style.borderBottomColor=accent;tabPreview.style.color=muted;tabPreview.style.borderBottomColor=none;}
                else{commentPreview.innerHTML=renderMd(commentBody.value)||'<em style="color:var(--muted)">Nada para pré-visualizar.</em>';commentBody.style.display='none';commentPreview.style.display='';tabPreview.style.color=accent;tabPreview.style.borderBottomColor=accent;tabWrite.style.color=muted;tabWrite.style.borderBottomColor=none;}
            }
            tabWrite?.addEventListener('click',()=>setTab('write'));
            tabPreview?.addEventListener('click',()=>setTab('preview'));
            commentBody?.addEventListener('input',()=>{ const len=commentBody.value.length; commentCharCount.textContent=`${len} / 2000`; commentCharCount.style.color=len>1800?'var(--danger)':'var(--muted)'; });
            commentBody?.addEventListener('keydown',e=>{ if((e.ctrlKey||e.metaKey)&&e.key==='Enter'){e.preventDefault();btnAddComment.click();} });

            function buildCommentEl(comment){
                const div=document.createElement('div'); div.className='comment-item'; div.dataset.id=comment.id; div.dataset.body=comment.body;
                div.style.cssText='display:flex;gap:10px;padding:10px 0;border-bottom:1px solid var(--border)';
                const editedLabel=comment.edited?`<span style="color:var(--muted);font-size:10px;margin-left:6px">(editado)</span>`:'';
                div.innerHTML=`<div style="width:28px;height:28px;border-radius:50%;background:rgba(255,145,77,.15);display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;margin-top:1px">💬</div><div style="flex:1;min-width:0"><div class="comment-body-display md-body">${renderMd(comment.body)}</div><div style="display:flex;align-items:center;justify-content:space-between;margin-top:5px;flex-wrap:wrap;gap:4px"><span style="color:var(--muted);font-size:11px;font-family:'DM Sans',monospace">${comment.created_at}${editedLabel}</span><div style="display:flex;gap:4px"><button class="btn-edit-comment" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:11px;padding:2px 6px;border-radius:4px;transition:color .15s,background .15s">Editar</button><button class="btn-delete-comment" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:11px;padding:2px 6px;border-radius:4px;transition:color .15s,background .15s">Excluir</button></div></div></div>`;
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
                const btnCancel=document.createElement('button'); btnCancel.textContent='Cancelar'; btnCancel.className='btn btn-ghost btn-sm';
                const btnSave=document.createElement('button'); btnSave.textContent='Salvar'; btnSave.className='btn btn-primary btn-sm';
                btnCancel.addEventListener('click',()=>{ wrap.remove(); display.style.display=''; });
                btnSave.addEventListener('click',()=>saveEdit(el,textarea,display,wrap,btnSave));
                textarea.addEventListener('keydown',e=>{ if((e.ctrlKey||e.metaKey)&&e.key==='Enter'){e.preventDefault();btnSave.click();} if(e.key==='Escape'){wrap.remove();display.style.display='';} });
                actions.append(btnCancel,btnSave); wrap.append(textarea,counter,actions); display.after(wrap); textarea.focus();
            }

            async function saveEdit(el,textarea,display,wrap,btnSave){
                const body=textarea.value.trim(); if(!body){textarea.focus();return;}
                btnSave.innerHTML='<span class="spinner"></span>'; btnSave.disabled=true;
                const res=await apiCall('PATCH',`/api/v1/tasks/${taskId}/comments/${el.dataset.id}`,{body});
                if(res.ok){ const updated=await res.json(); el.dataset.body=updated.body; display.innerHTML=renderMd(updated.body); const ts=el.querySelector('[style*="monospace"]'); if(ts&&!ts.querySelector('[data-edited]'))ts.insertAdjacentHTML('beforeend','<span style="color:var(--muted);font-size:10px;margin-left:6px" data-edited>(editado)</span>'); wrap.remove(); display.style.display=''; toast('Comentário atualizado.','success'); }
                else { const d=await res.json().catch(()=>({})); toast(d.errors?Object.values(d.errors).flat().join(' '):(d.message||'Erro.'),'error'); btnSave.innerHTML='Salvar'; btnSave.disabled=false; }
            }

            async function handleDelete(el){
                confirmDialog('Excluir comentário','Esta ação não pode ser desfeita.',async()=>{
                    const res=await apiCall('DELETE',`/api/v1/tasks/${taskId}/comments/${el.dataset.id}`);
                    if(res.ok){el.remove();deltaCount(-1);if(!commentList.querySelector('.comment-item'))showEmpty();toast('Comentário excluído.','info');}
                    else toast('Erro ao excluir.','error');
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
                } catch { toast('Erro ao carregar comentários.','error'); }
                finally { if(btnLoadMore){btnLoadMore.innerHTML='Carregar mais';btnLoadMore.disabled=false;} }
            }

            btnLoadMore?.addEventListener('click',()=>loadComments(currentPage+1));

            btnAddComment?.addEventListener('click', async function(){
                const body=commentBody.value.trim(); if(!body){setTab('write');commentBody.focus();return;}
                this.innerHTML='<span class="spinner"></span>'; this.disabled=true;
                try {
                    const res=await apiCall('POST',`/api/v1/tasks/${taskId}/comments`,{body});
                    if(res.ok){ const comment=await res.json(); document.getElementById('comments-empty')?.remove(); commentList.insertBefore(buildCommentEl(comment),commentList.firstChild); commentBody.value=''; commentCharCount.textContent='0 / 2000'; setTab('write'); deltaCount(+1); toast('Comentário adicionado.','success'); }
                    else { const d=await res.json(); toast(d.errors?Object.values(d.errors).flat().join(' '):(d.message||'Erro.'),'error'); }
                } catch { toast('Erro de conexão.','error'); }
                finally { this.innerHTML='Comentar'; this.disabled=false; }
            });

            loadComments(1);
        </script>
    @endpush

@endsection