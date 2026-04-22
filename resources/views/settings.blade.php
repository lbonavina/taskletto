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

/* ── Autostart button states ────────────────────────────────────────── */
.btn-autostart-on  { background: rgba(74,222,128,.12); border-color: rgba(74,222,128,.35) !important; color: var(--success); }
.btn-autostart-on:hover { background: rgba(74,222,128,.2); }
.btn-autostart-off { background: var(--surface2); border-color: var(--border) !important; color: var(--muted); }
.btn-autostart-off:hover { background: rgba(255,145,77,.1); border-color: rgba(255,145,77,.3) !important; color: var(--accent); }

</style>
@endpush

@section('content')
<div class="settings-wrap">

    <div class="settings-tabs">
        <button class="settings-tab active" onclick="switchTab('geral',this)">
            <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="8" cy="8" r="2.5"/><path d="M8 1v2M8 13v2M1 8h2M13 8h2M3.05 3.05l1.41 1.41M11.54 11.54l1.41 1.41M3.05 12.95l1.41-1.41M11.54 4.46l1.41-1.41"/></svg>
            Geral
        </button>
        <button class="settings-tab" onclick="switchTab('sobre',this)">
            <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="8" cy="8" r="6.5"/><path d="M8 7v5M8 5v.5"/></svg>
            Sobre
        </button>
    </div>

    {{-- GERAL --}}
    <div class="settings-panel active" id="panel-geral">

        @if(config('app.env') !== 'production')
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
        @endif

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

    {{-- SOBRE --}}
    <div class="settings-panel" id="panel-sobre">

        {{-- Versão --}}
        <div class="card" style="display:flex;align-items:center;gap:14px">
            <div style="flex:1">
                <div style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:2px">Taskletto</div>
                <div style="font-size:12px;color:var(--muted)">Versão {{ config('app.version', '1.0.0') }}</div>
            </div>
            <span class="badge status-completed" style="font-size:10px">Atualizado</span>
        </div>

        {{-- Suporte --}}
        <div class="card">
            <div class="section-title" style="margin-bottom:14px">Suporte</div>
            <div style="display:flex;flex-direction:column;gap:8px">
                <a href="mailto:suporte@taskletto.com" class="action-row" style="text-decoration:none;cursor:pointer">
                    <div class="action-row-text">
                        <div class="action-row-title">Falar com o suporte</div>
                        <div class="action-row-desc">suporte@taskletto.com</div>
                    </div>
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" style="color:var(--muted);flex-shrink:0"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                </a>
                <a href="{{ route('open-external', ['url' => 'https://github.com/lbonavina/taskletto']) }}" class="action-row" style="text-decoration:none;cursor:pointer">
                    <div class="action-row-text">
                        <div class="action-row-title">Reportar um problema</div>
                        <div class="action-row-desc">Abrir uma issue no GitHub</div>
                    </div>
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" style="color:var(--muted);flex-shrink:0"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                </a>
            </div>
        </div>

        {{-- Comunidade --}}
        <div class="card">
            <div class="section-title" style="margin-bottom:14px">Comunidade</div>
            <div style="display:flex;gap:8px;flex-wrap:wrap">
                <a href="{{ route('open-external', ['url' => 'https://github.com/lbonavina/taskletto']) }}" class="btn btn-ghost btn-sm">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/></svg>
                    GitHub
                </a>
                <a href="{{ route('open-external', ['url' => 'https://ko-fi.com']) }}" class="btn btn-ghost btn-sm">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor" style="color:#ff5e5b"><path d="M23.881 8.948c-.773-4.085-4.859-4.591-4.859-4.591H.723c-.604 0-.679.798-.679.798s-.082 7.324-.022 11.822c.164 2.424 2.586 2.672 2.586 2.672s8.267-.023 11.966-.049c2.438-.426 2.683-2.566 2.658-3.734 4.352.24 7.422-2.831 6.649-6.918zm-11.062 3.511c-1.246 1.453-4.011 3.976-4.011 3.976s-.121.119-.31.023c-.076-.057-.108-.09-.108-.09-.443-.441-3.368-3.049-4.034-3.954-.709-.965-1.041-2.7-.091-3.71.951-1.01 3.005-1.086 4.363.407 0 0 1.565-1.782 3.468-.963 1.904.82 1.832 2.318.723 4.311zm6.173.478c-.928.116-1.682.028-1.682.028V7.284h1.77s1.971.551 1.971 2.638c0 1.913-.985 2.667-2.059 3.015z"/></svg>
                    Ko-fi
                </a>
            </div>
        </div>

        {{-- Legal --}}
        <div class="card">
            <div class="section-title" style="margin-bottom:14px">Legal</div>
            <div style="display:flex;flex-direction:column;gap:8px">
                <a href="/terms" class="action-row" style="text-decoration:none;cursor:pointer">
                    <div class="action-row-text">
                        <div class="action-row-title">Termos de uso</div>
                    </div>
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" style="color:var(--muted);flex-shrink:0"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                </a>
                <a href="/privacy" class="action-row" style="text-decoration:none;cursor:pointer">
                    <div class="action-row-text">
                        <div class="action-row-title">Política de privacidade</div>
                    </div>
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" style="color:var(--muted);flex-shrink:0"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                </a>
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