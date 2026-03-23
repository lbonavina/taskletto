@extends('layouts.app')

@section('page-title', __('app.settings'))

@push('styles')
<style>
/* ── Settings layout ───────────────────────────────────────────────── */
.settings-wrap { max-width: 640px; }

.settings-tabs {
    display: flex; gap: 2px;
    border-bottom: 1px solid var(--border);
    margin-bottom: 20px;
    overflow-x: auto; scrollbar-width: none;
}
.settings-tabs::-webkit-scrollbar { display: none; }

.settings-tab {
    display: flex; align-items: center; gap: 7px;
    padding: 9px 16px; border: none; background: none;
    color: var(--muted); font-size: 12.5px; font-weight: 500;
    font-family: inherit; cursor: pointer;
    border-bottom: 2px solid transparent; margin-bottom: -1px;
    white-space: nowrap; border-radius: var(--radius-sm) var(--radius-sm) 0 0;
    transition: color .15s, border-color .15s, background .15s;
}
.settings-tab:hover { color: var(--text); background: var(--surface2); }
.settings-tab.active { color: var(--accent); border-bottom-color: var(--accent); background: rgba(255,145,77,.05); }

.settings-panel { display: none; flex-direction: column; gap: 14px; }
.settings-panel.active { display: flex; }

/* ── Setting card internals ─────────────────────────────────────────── */
.card + .card { margin-top: 0; } /* gap handled by panel */

/* ── Portability grid ───────────────────────────────────────────────── */
.portability-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 12px;
}
.portability-card {
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.portability-card-header {
    display: flex;
    align-items: center;
    gap: 10px;
}
.portability-icon {
    width: 34px; height: 34px;
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.portability-icon.success {
    background: rgba(74,222,128,.1);
    border: 1px solid rgba(74,222,128,.2);
}
.portability-icon.accent {
    background: rgba(255,145,77,.1);
    border: 1px solid rgba(255,145,77,.2);
}
.portability-card-title { font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 2px; }
.portability-card-desc  { font-size: 11px; color: var(--muted); }

/* ── API endpoint list ──────────────────────────────────────────────── */
.endpoint-row {
    display: flex; align-items: center; gap: 10px;
    padding: 6px 10px; border-radius: var(--radius-sm);
    background: var(--surface2);
    font-size: 12px;
}
.endpoint-method {
    width: 54px; text-align: center;
    border-radius: var(--radius-xs); padding: 2px 0;
    font-size: 10px; font-weight: 700;
    flex-shrink: 0;
}
.method-GET    { color: var(--color-get);    background: rgba(96,165,250,.1); }
.method-POST   { color: var(--color-post);   background: rgba(74,222,128,.1); }
.method-PUT    { color: var(--color-put);    background: rgba(240,160,90,.1); }
.method-PATCH  { color: var(--color-patch);  background: rgba(192,132,252,.1); }
.method-DELETE { color: var(--color-delete); background: rgba(224,84,84,.1); }
.endpoint-path { color: var(--text); flex: 1; }
.endpoint-desc { color: var(--muted); font-size: 11px; }

/* ── Sync badge in tab ──────────────────────────────────────────────── */
.sync-tab-badge {
    font-size: 10px; font-weight: 600;
    padding: 1px 6px; border-radius: var(--radius-xs);
    display: none;
}

/* ── Autostart button states ────────────────────────────────────────── */
.btn-autostart-on  { background: rgba(74,222,128,.12); border-color: rgba(74,222,128,.35) !important; color: var(--success); }
.btn-autostart-on:hover { background: rgba(74,222,128,.2); }
.btn-autostart-off { background: var(--surface2); border-color: var(--border) !important; color: var(--muted); }
.btn-autostart-off:hover { background: rgba(255,145,77,.1); border-color: rgba(255,145,77,.3) !important; color: var(--accent); }

/* ── Gist connected row ─────────────────────────────────────────────── */
.gist-connected-row {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 14px; border-radius: var(--radius-md);
    background: rgba(74,222,128,.07);
    border: 1px solid rgba(74,222,128,.18);
    margin-bottom: 14px;
}
.gist-connected-row .gist-info { flex: 1; font-size: 12px; color: var(--text); }
.gist-connected-row .gist-disconnect {
    background: none; border: none; color: var(--muted);
    font-size: 11px; cursor: pointer; font-family: inherit;
    padding: 2px 6px; border-radius: var(--radius-xs);
    transition: color .12s;
}
.gist-connected-row .gist-disconnect:hover { color: var(--danger); }

.gist-meta { font-size: 11px; color: var(--muted); margin-bottom: 12px; }
.gist-sync-error { color: var(--danger); margin-left: 8px; display: none; }

.gist-picker-item {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 14px; border-radius: 9px; cursor: pointer;
    border: 1px solid var(--border); background: var(--surface2);
    transition: border-color .15s, background .15s;
    user-select: none;
}
.gist-picker-item:hover { border-color: rgba(255,145,77,.3); background: rgba(255,145,77,.04); }
.gist-picker-item.selected { border-color: var(--accent); background: rgba(255,145,77,.08); }
.gist-picker-item.taskletto-badge { border-color: rgba(74,222,128,.3); }
.gist-picker-item.taskletto-badge.selected { border-color: var(--accent); }
.gist-pick-radio { width:16px; height:16px; border-radius:50%; border:2px solid var(--border); flex-shrink:0; transition: border-color .15s, background .15s; }
.gist-picker-item.selected .gist-pick-radio { border-color: var(--accent); background: var(--accent); }
.gist-pick-info { flex:1; min-width:0; }
.gist-pick-desc { font-size:12.5px; font-weight:500; color:var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.gist-pick-meta { font-size:10.5px; color:var(--muted); margin-top:2px; }
.gist-pick-badge { font-size:9.5px; font-weight:700; padding:1px 7px; border-radius:20px; background:rgba(74,222,128,.1); color:var(--success); border:1px solid rgba(74,222,128,.2); flex-shrink:0; }
.gist-pick-new { border-style: dashed; }
.gist-pick-new:hover { border-color: rgba(255,145,77,.4); }

.gist-sync-row {
    display: flex; align-items: center; gap: 8px;
    flex-wrap: wrap;
    font-size: 12px; color: var(--muted);
}
</style>
@endpush

@section('content')
<div class="settings-wrap">

    <div class="settings-tabs">
        <button class="settings-tab active" onclick="switchTab('geral',this)">
            <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="8" cy="8" r="2.5"/><path d="M8 1v2M8 13v2M1 8h2M13 8h2M3.05 3.05l1.41 1.41M11.54 11.54l1.41 1.41M3.05 12.95l1.41-1.41M11.54 4.46l1.41-1.41"/></svg>
            Geral
        </button>
        <button class="settings-tab" onclick="switchTab('sync',this)">
            <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M1 8a7 7 0 0114 0M15 8a7 7 0 01-14 0"/><path d="M4 4l2 2-2 2M12 4l-2 2 2 2"/></svg>
            Sync
            <span id="sync-status-badge" class="sync-tab-badge"></span>
        </button>
        <button class="settings-tab" onclick="switchTab('dados',this)">
            <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6"><ellipse cx="8" cy="4" rx="6" ry="2.5"/><path d="M2 4v4c0 1.38 2.69 2.5 6 2.5S14 9.38 14 8V4M2 8v4c0 1.38 2.69 2.5 6 2.5S14 13.38 14 12V8"/></svg>
            Dados
        </button>
        <button class="settings-tab" onclick="switchTab('sobre',this)">
            <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="8" cy="8" r="6.5"/><path d="M8 7v5M8 5v.5"/></svg>
            Sobre
        </button>
    </div>

    {{-- GERAL --}}
    <div class="settings-panel active" id="panel-geral">

        <div class="card">
            <div class="section-title">⚙️ Sistema</div>
            <div class="action-row">
                <div class="action-row-text">
                    <div class="action-row-title">Iniciar com o Windows</div>
                    <div class="action-row-desc">Abre o Taskletto automaticamente quando o Windows iniciar.</div>
                </div>
                <button id="btn-autostart" onclick="toggleAutostart()"
                    class="btn {{ \App\Providers\NativeAppServiceProvider::isAutoStartEnabled() ? 'btn-autostart-on' : 'btn-autostart-off' }}">
                    {{ \App\Providers\NativeAppServiceProvider::isAutoStartEnabled() ? '✓ Ativo' : 'Ativar' }}
                </button>
            </div>
        </div>

        <div class="card">
            <div class="section-title">🌐 {{ __('app.settings_language') }}</div>
            <p class="action-row-desc" style="margin-bottom:14px;line-height:1.6">{{ __('app.settings_lang_desc') }}</p>
            @if(session('locale_saved'))
                <div class="alert-inline success" style="margin-bottom:14px">
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 8l4 4 8-8"/></svg>
                    {{ __('app.settings_lang_saved') }}
                </div>
            @endif
            <form method="POST" action="{{ route('settings.locale') }}" id="locale-form">
                @csrf
                <div class="select-wrap" style="width:220px">
                    <select name="locale" onchange="document.getElementById('locale-form').submit()">
                        <option value="pt" {{ app()->getLocale() === 'pt' ? 'selected' : '' }}>{{ __('app.lang_pt') }}</option>
                        <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>{{ __('app.lang_en') }}</option>
                        <option value="es" {{ app()->getLocale() === 'es' ? 'selected' : '' }}>{{ __('app.lang_es') }}</option>
                    </select>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="section-title">🕐 {{ __('app.settings_timezone') }}</div>
            <p class="action-row-desc" style="margin-bottom:14px;line-height:1.6">{{ __('app.settings_tz_desc') }}</p>
            @if(session('timezone_saved'))
                <div class="alert-inline success" style="margin-bottom:14px">
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 8l4 4 8-8"/></svg>
                    {{ __('app.settings_tz_saved') }}
                </div>
            @endif
            <form method="POST" action="{{ route('settings.timezone') }}" id="timezone-form">
                @csrf
                @php $currentTz = \App\Models\AppSetting::get('timezone', config('app.timezone')); @endphp
                <div class="select-wrap" style="width:320px">
                    <select name="timezone" onchange="document.getElementById('timezone-form').submit()">
                        @foreach(['UTC'=>'UTC','America/Sao_Paulo'=>'América/São Paulo (BRT, UTC-3)','America/Manaus'=>'América/Manaus (AMT, UTC-4)','America/Belem'=>'América/Belém (BRT, UTC-3)','America/Fortaleza'=>'América/Fortaleza (BRT, UTC-3)','America/Recife'=>'América/Recife (BRT, UTC-3)','America/Noronha'=>'América/Noronha (FNT, UTC-2)','America/New_York'=>'América/New York (EST, UTC-5)','America/Chicago'=>'América/Chicago (CST, UTC-6)','America/Denver'=>'América/Denver (MST, UTC-7)','America/Los_Angeles'=>'América/Los Angeles (PST, UTC-8)','America/Buenos_Aires'=>'América/Buenos Aires (ART, UTC-3)','America/Santiago'=>'América/Santiago (CLT, UTC-4)','America/Bogota'=>'América/Bogotá (COT, UTC-5)','America/Lima'=>'América/Lima (PET, UTC-5)','America/Mexico_City'=>'América/Cidade do México (CST, UTC-6)','Europe/Lisbon'=>'Europa/Lisboa (WET, UTC+0)','Europe/London'=>'Europa/Londres (GMT, UTC+0)','Europe/Madrid'=>'Europa/Madrid (CET, UTC+1)','Europe/Paris'=>'Europa/Paris (CET, UTC+1)','Europe/Berlin'=>'Europa/Berlim (CET, UTC+1)','Europe/Moscow'=>'Europa/Moscou (MSK, UTC+3)','Asia/Tokyo'=>'Ásia/Tóquio (JST, UTC+9)','Asia/Shanghai'=>'Ásia/Xangai (CST, UTC+8)','Asia/Kolkata'=>'Ásia/Calcutá (IST, UTC+5:30)','Asia/Dubai'=>'Ásia/Dubai (GST, UTC+4)','Australia/Sydney'=>'Austrália/Sydney (AEDT, UTC+11)'] as $tzKey => $tzLabel)
                            <option value="{{ $tzKey }}" {{ $currentTz === $tzKey ? 'selected' : '' }}>{{ $tzLabel }}</option>
                        @endforeach
                    </select>
                </div>
                <p class="action-row-desc" style="margin-top:8px">{{ __('app.settings_tz_current') }}: <strong style="color:var(--text)">{{ now()->format('d/m/Y H:i:s') }}</strong></p>
            </form>
        </div>

    </div>

    {{-- SYNC --}}
    <div class="settings-panel" id="panel-sync">
        <div class="card" id="gist-sync-card">
            <div class="section-title">☁️ Sync — GitHub Gist</div>
            <p class="action-row-desc" style="margin-bottom:16px;line-height:1.6">
                Sincronize seus dados via <strong style="color:var(--text)">GitHub Gist</strong> (gratuito). Crie um token em
                <a href="#" onclick="event.preventDefault();fetch('/open-external?url=https://github.com/settings/tokens/new?scopes=gist%26description=Taskletto+Sync')" style="color:var(--accent)">github.com/settings/tokens</a>
                com escopo <code>gist</code> e cole abaixo.
            </p>

            <div id="gist-config-section">
                {{-- Step 1: token input --}}
                <div id="gist-step-token">
                    <div style="display:grid;grid-template-columns:1fr auto;gap:8px;align-items:end">
                        <div class="form-group" style="margin:0">
                            <label>Token GitHub</label>
                            <input type="password" id="gist-token-input" placeholder="ghp_xxxxxxxxxxxxxxxxxxxx" autocomplete="off"
                                onkeydown="if(event.key==='Enter') verifyToken()">
                        </div>
                        <button class="btn btn-primary" onclick="verifyToken()" id="btn-verify" style="height:42px;white-space:nowrap">Verificar</button>
                    </div>
                    <div id="gist-token-error" style="display:none;margin-top:8px"></div>
                </div>

                {{-- Step 2: gist picker (shown after token verified) --}}
                <div id="gist-step-picker" style="display:none;margin-top:14px">
                    <div style="font-size:12px;color:var(--muted);margin-bottom:8px">
                        Selecione o Gist do Taskletto ou crie um novo:
                    </div>
                    <div id="gist-picker-list" style="display:flex;flex-direction:column;gap:6px;max-height:220px;overflow-y:auto;margin-bottom:10px"></div>
                    <div style="display:flex;gap:8px;justify-content:flex-end">
                        <button class="btn btn-ghost btn-sm" onclick="resetGistStep()">← Voltar</button>
                        <button class="btn btn-primary btn-sm" id="btn-confirm-gist" onclick="confirmGistSelection()" disabled>Usar este Gist</button>
                    </div>
                </div>
            </div>

            <div id="gist-connected-section" style="display:none">
                <div class="gist-connected-row">
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="var(--success)" stroke-width="2"><path d="M2 8l4 4 8-8"/></svg>
                    <div class="gist-info">Conectado · Gist: <code id="gist-id-display" style="color:var(--muted)"></code></div>
                    <button class="gist-disconnect" onclick="disconnectGist()">Desconectar</button>
                </div>
                <div class="gist-meta">
                    Último sync: <span id="gist-last-sync">—</span>
                    <span id="gist-sync-error" class="gist-sync-error"></span>
                </div>
                <div class="gist-sync-row">
                    <span>Auto-sync a cada</span>
                    <div class="select-wrap" style="width:110px">
                        <select id="gist-interval-select" onchange="setGistInterval(this.value)">
                            <option value="5">5 min</option><option value="10">10 min</option>
                            <option value="15" selected>15 min</option><option value="30">30 min</option>
                            <option value="60">1 hora</option>
                        </select>
                    </div>
                    <div style="flex:1"></div>
                    <button class="btn btn-ghost btn-sm" onclick="manualPull()" id="btn-pull">
                        <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 3v8M4 8l4 4 4-4"/><path d="M2 14h12"/></svg> Pull
                    </button>
                    <button class="btn btn-primary btn-sm" onclick="manualPush()" id="btn-push">
                        <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 13V5M4 8l4-4 4 4"/><path d="M2 2h12"/></svg> Push
                    </button>
                </div>
            </div>
            <div id="gist-alert" style="display:none;margin-top:12px"></div>
        </div>
    </div>

    {{-- DADOS --}}
    <div class="settings-panel" id="panel-dados">
        <div class="card">
            <div class="section-title">💾 Backup & Restauração</div>
            <p class="action-row-desc" style="margin-bottom:18px;line-height:1.6">
                Exporte todos os seus dados num arquivo <code>.json</code>. Para restaurar em outro dispositivo, basta importar o arquivo.
            </p>
            <div id="portability-alert" style="display:none;margin-bottom:14px"></div>
            <div class="portability-grid">
                <div class="portability-card">
                    <div class="portability-card-header">
                        <div class="portability-icon success">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="rgba(74,222,128,.9)" stroke-width="1.8"><path d="M8 2v8M5 7l3 3 3-3M2 12v1a1 1 0 001 1h10a1 1 0 001-1v-1"/></svg>
                        </div>
                        <div>
                            <div class="portability-card-title">Exportar dados</div>
                            <div class="portability-card-desc">Baixa um arquivo .json completo</div>
                        </div>
                    </div>
                    <a href="{{ route('settings.export') }}" class="btn btn-ghost btn-sm" style="justify-content:center;color:var(--success);border-color:rgba(74,222,128,.25)">
                        <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 2v8M5 7l3 3 3-3M2 12v1a1 1 0 001 1h10a1 1 0 001-1v-1"/></svg>
                        Exportar agora
                    </a>
                </div>
                <div class="portability-card">
                    <div class="portability-card-header">
                        <div class="portability-icon accent">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="rgba(255,145,77,.9)" stroke-width="1.8"><path d="M8 11V3M5 6l3-3 3 3M2 12v1a1 1 0 001 1h10a1 1 0 001-1v-1"/></svg>
                        </div>
                        <div>
                            <div class="portability-card-title">Importar dados</div>
                            <div class="portability-card-desc">Restaura a partir de um backup</div>
                        </div>
                    </div>
                    <label style="cursor:pointer">
                        <input type="file" id="import-file" accept=".json" style="display:none" onchange="handleImport(this)">
                        <div class="btn btn-ghost btn-sm" style="justify-content:center;color:var(--accent);border-color:rgba(255,145,77,.25);pointer-events:none">
                            <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 11V3M5 6l3-3 3 3M2 12v1a1 1 0 001 1h10a1 1 0 001-1v-1"/></svg>
                            <span id="import-label">Selecionar arquivo</span>
                        </div>
                    </label>
                </div>
            </div>
            <div class="alert-inline warning">
                <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0"><path d="M8 2L1 14h14L8 2zM8 7v3M8 12v.5"/></svg>
                A importação <strong>adiciona</strong> os dados ao banco atual — não apaga os existentes.
            </div>
        </div>
    </div>

    {{-- SOBRE --}}
    <div class="settings-panel" id="panel-sobre">
        <div class="card">
            <div class="section-title">ℹ️ {{ __('app.settings_about') }}</div>
            <div class="info-row">
                <span class="info-row-label">{{ __('app.settings_app') }}</span>
                <span class="info-row-value">Taskletto</span>
            </div>
            <div class="info-row">
                <span class="info-row-label">{{ __('app.settings_framework') }}</span>
                <span class="info-row-value">Laravel {{ app()->version() }}</span>
            </div>
            <div class="info-row">
                <span class="info-row-label">{{ __('app.settings_php') }}</span>
                <span class="info-row-value">{{ PHP_VERSION }}</span>
            </div>
            <div class="info-row">
                <span class="info-row-label">{{ __('app.settings_db') }}</span>
                <span class="info-row-value">{{ $dbSize }}</span>
            </div>
            <div class="info-row">
                <span class="info-row-label">{{ __('app.settings_env') }}</span>
                <span class="badge {{ app()->environment('production') ? 'status-completed' : 'status-in_progress' }}">{{ app()->environment() }}</span>
            </div>
        </div>

        <div class="card">
            <div class="section-title">🔌 {{ __('app.settings_api') }}</div>
            <p class="action-row-desc" style="margin-bottom:14px;line-height:1.6">{{ __('app.settings_api_desc') }} <code>/api/v1/tasks</code>.</p>
            <a href="/api/documentation" target="_blank" class="btn btn-ghost">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 3L2 8l3 5M11 3l3 5-3 5M9 2l-2 12"/></svg>
                {{ __('app.settings_api_open') }}
            </a>
        </div>

        <div class="card">
            <div class="section-title">📋 {{ __('app.settings_endpoints') }}</div>
            <div style="display:flex;flex-direction:column;gap:5px">
                @foreach([['GET','/api/v1/tasks','List tasks'],['POST','/api/v1/tasks','Create task'],['GET','/api/v1/tasks/{id}','Get task'],['PUT','/api/v1/tasks/{id}','Update task'],['DELETE','/api/v1/tasks/{id}','Delete task'],['PATCH','/api/v1/tasks/{id}/complete','Complete task'],['PATCH','/api/v1/tasks/{id}/reopen','Reopen task'],['GET','/api/v1/tasks-stats','Statistics']] as [$method,$path,$desc])
                <div class="endpoint-row">
                    <span class="endpoint-method method-{{ $method }}">{{ $method }}</span>
                    <span class="endpoint-path">{{ $path }}</span>
                    <span class="endpoint-desc">{{ $desc }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function switchTab(name, btn) {
    document.querySelectorAll('.settings-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.settings-tab').forEach(b => b.classList.remove('active'));
    document.getElementById('panel-' + name).classList.add('active');
    btn.classList.add('active');
    localStorage.setItem('settings-tab', name);
}
(function() {
    const last = localStorage.getItem('settings-tab');
    if (last) {
        const btn = document.querySelector('.settings-tab[onclick*="' + last + '"]');
        if (btn) switchTab(last, btn);
    }
    @if(session('locale_saved') || session('timezone_saved'))
        switchTab('geral', document.querySelector('.settings-tab'));
    @endif
})();

async function handleImport(input) {
    const file = input.files[0]; if (!file) return;
    const label = document.getElementById('import-label');
    const alertEl = document.getElementById('portability-alert');
    label.textContent = '⏳ Importando...'; alertEl.style.display = 'none';
    const fd = new FormData(); fd.append('file', file); fd.append('_token', document.querySelector('meta[name=csrf-token]').content);
    try {
        const res = await fetch('{{ route('settings.import') }}', { method: 'POST', body: fd });
        const data = await res.json();
        const type = res.ok ? 'success' : 'danger';
        alertEl.innerHTML = `<div class="alert-inline ${type}"><span>${data.message || (res.ok ? 'Importado!' : 'Erro ao importar.')}</span></div>`;
        alertEl.style.display = 'block';
    } catch(e) {
        alertEl.innerHTML = `<div class="alert-inline danger"><span>Erro de conexão.</span></div>`;
        alertEl.style.display = 'block';
    } finally { label.textContent = 'Selecionar arquivo'; input.value = ''; }
}

const CSRF = document.querySelector('meta[name=csrf-token]').content;
async function apiPost(url, body={}) {
    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), 30000);
    try {
        const r = await fetch(url, {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
            body: JSON.stringify(body),
            signal: controller.signal
        });
        clearTimeout(timeout);
        return r.json();
    } catch(e) {
        clearTimeout(timeout);
        if (e.name === 'AbortError') return { ok: false, message: 'Tempo limite esgotado. Tente novamente.' };
        return { ok: false, message: 'Erro de conexão: ' + e.message };
    }
}
function showGistAlert(msg, type='success') {
    const el = document.getElementById('gist-alert');
    el.innerHTML = `<div class="alert-inline ${type}">${msg}</div>`;
    el.style.display='block'; setTimeout(()=>{el.style.display='none'},5000);
}
function setBtnLoading(id, loading) {
    const btn=document.getElementById(id); if(!btn) return;
    btn.disabled=loading; if(loading) btn.dataset.orig=btn.innerHTML;
    btn.innerHTML=loading?'<span class="spinner"></span>':btn.dataset.orig;
}
async function loadGistStatus() {
    try {
        const d = await fetch('/settings/gist/status').then(r=>r.json());
        const badge=document.getElementById('sync-status-badge');
        const config=document.getElementById('gist-config-section');
        const connected=document.getElementById('gist-connected-section');
        if (d.configured) {
            config.style.display='none'; connected.style.display='block';
            document.getElementById('gist-id-display').textContent = d.gist_id||'(novo)';
            document.getElementById('gist-last-sync').textContent = d.last_sync_at ? new Date(d.last_sync_at).toLocaleString('pt-BR') : 'Nunca';
            const errEl=document.getElementById('gist-sync-error');
            if(d.last_status==='error'&&d.last_error){errEl.textContent='⚠ '+d.last_error;errEl.style.display='inline';}else errEl.style.display='none';
            badge.textContent=d.last_status==='ok'?'✓ Sincronizado':d.last_status==='error'?'⚠ Erro':'Aguardando';
            badge.style.background=d.last_status==='ok'?'rgba(74,222,128,.1)':d.last_status==='error'?'rgba(224,84,84,.1)':'rgba(96,165,250,.1)';
            badge.style.color=d.last_status==='ok'?'var(--success)':d.last_status==='error'?'var(--danger)':'var(--info)';
            badge.style.display='block';
            const sel=document.getElementById('gist-interval-select'); if(sel) sel.value=String(d.interval_min);
        } else { config.style.display='block'; connected.style.display='none'; badge.style.display='none'; }
    } catch(e){}
}
// ── Gist picker flow ─────────────────────────────────────────────────────────
let _pickerToken = null;
let _selectedGistId = null; // null = criar novo

async function verifyToken() {
    const token = document.getElementById('gist-token-input').value.trim();
    if (!token) { showTokenError('Informe o token.'); return; }

    setBtnLoading('btn-verify', true);
    let d;
    try {
        d = await apiPost('/settings/gist/list-gists', { token });
    } catch(e) {
        showTokenError('Erro inesperado: ' + e.message);
        return;
    } finally {
        setBtnLoading('btn-verify', false);
    }

    if (!d || !d.ok) { showTokenError(d ? d.message : 'Erro desconhecido.'); return; }

    _pickerToken = token;
    _selectedGistId = null;
    hideTokenError();
    renderGistPicker(d.gists || []);
    document.getElementById('gist-step-token').style.display = 'none';
    document.getElementById('gist-step-picker').style.display = 'block';
}

function renderGistPicker(gists) {
    const list = document.getElementById('gist-picker-list');

    // "Criar novo Gist" always first option
    list.innerHTML = `
        <div class="gist-picker-item gist-pick-new selected" data-id="__new__" onclick="selectGist(this,'__new__')">
            <div class="gist-pick-radio"></div>
            <div class="gist-pick-info">
                <div class="gist-pick-desc">➕ Criar novo Gist</div>
                <div class="gist-pick-meta">Um novo arquivo de sync será criado na sua conta</div>
            </div>
        </div>`;

    gists.forEach(g => {
        const date = new Date(g.updated_at).toLocaleDateString('pt-BR');
        const fileList = g.files.slice(0,3).join(', ') + (g.files.length > 3 ? '...' : '');
        const badge = g.is_taskletto ? '<span class="gist-pick-badge">✓ Taskletto</span>' : '';
        const cls = g.is_taskletto ? 'taskletto-badge' : '';
        list.innerHTML += `
            <div class="gist-picker-item ${cls}" data-id="${g.id}" onclick="selectGist(this,'${g.id}')">
                <div class="gist-pick-radio"></div>
                <div class="gist-pick-info">
                    <div class="gist-pick-desc">${escHtml(g.description)}</div>
                    <div class="gist-pick-meta">${fileList} · atualizado ${date}</div>
                </div>
                ${badge}
            </div>`;
    });

    // Auto-select the Taskletto gist if found
    const taskletto = gists.find(g => g.is_taskletto);
    if (taskletto) {
        const newItem = list.querySelector('[data-id="__new__"]');
        const taskItem = list.querySelector('[data-id="' + taskletto.id + '"]');
        if (newItem) newItem.classList.remove('selected');
        if (taskItem) { taskItem.classList.add('selected'); _selectedGistId = taskletto.id; }
    }

    document.getElementById('btn-confirm-gist').disabled = false;
}

function selectGist(el, id) {
    document.querySelectorAll('.gist-picker-item').forEach(i => {
        i.classList.remove('selected');
    });
    el.classList.add('selected');
    _selectedGistId = id === '__new__' ? null : id;
}

async function confirmGistSelection() {
    const body = { token: _pickerToken };
    if (_selectedGistId) body.gist_id = _selectedGistId;

    setBtnLoading('btn-confirm-gist', true);
    const d = await apiPost('/settings/gist/config', body);
    setBtnLoading('btn-confirm-gist', false);

    if (d.ok) {
        const msg = _selectedGistId
            ? 'Gist vinculado! Clique em Pull para restaurar seus dados.'
            : 'Conectado! Clique em Push para criar o Gist.';
        showGistAlert(msg);
        loadGistStatus();
    } else {
        showGistAlert(d.message, 'danger');
    }
}

function resetGistStep() {
    document.getElementById('gist-step-picker').style.display = 'none';
    document.getElementById('gist-step-token').style.display = 'block';
    document.getElementById('gist-token-error').style.display = 'none';
    _pickerToken = null;
    _selectedGistId = null;
}

function showTokenError(msg) {
    const el = document.getElementById('gist-token-error');
    el.innerHTML = `<div class="alert-inline danger">${msg}</div>`;
    el.style.display = 'block';
}
function hideTokenError() {
    const el = document.getElementById('gist-token-error');
    if (el) el.style.display = 'none';
}
function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
async function disconnectGist() {
    if(!confirm('Desconectar o sync?')) return;
    await apiPost('/settings/gist/disconnect'); loadGistStatus(); showGistAlert('Sync desconectado.');
}
async function manualPush() {
    setBtnLoading('btn-push', true);
    try {
        const d = await apiPost('/settings/gist/push');
        showGistAlert(d.message, d.ok ? 'success' : 'danger');
        if (d.ok) loadGistStatus();
    } finally {
        setBtnLoading('btn-push', false);
    }
}
async function manualPull() {
    setBtnLoading('btn-pull', true);
    try {
        const d = await apiPost('/settings/gist/pull');
        showGistAlert(d.message, d.ok ? 'success' : 'danger');
        if (d.ok) loadGistStatus();
    } finally {
        setBtnLoading('btn-pull', false);
    }
}
async function setGistInterval(val) {
    await apiPost('/settings/gist/interval',{interval:parseInt(val)});
    showGistAlert(`Auto-sync configurado para ${val} min.`);
}
loadGistStatus(); setInterval(loadGistStatus,60000);

async function toggleAutostart() {
    const btn=document.getElementById('btn-autostart'); btn.disabled=true; btn.textContent='...';
    try {
        const d=await fetch('/native/autostart/toggle').then(r=>r.json());
        btn.textContent=d.enabled?'✓ Ativo':'Ativar';
        btn.className='btn '+(d.enabled?'btn-autostart-on':'btn-autostart-off');
    } catch(e){btn.textContent='Erro';}
    btn.disabled=false;
}
</script>
@endpush