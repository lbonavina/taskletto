@extends('layouts.app')

@section('page-title', __('app.settings'))

@section('content')

<div style="max-width:640px;display:flex;flex-direction:column;gap:16px">

    {{-- Backup & Restore --}}
    <div class="card">
        <div style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:var(--muted);margin-bottom:6px">
            Backup & Restauração
        </div>
        <p style="font-size:13px;color:var(--muted);margin-bottom:20px;line-height:1.6">
            Exporte todos os seus dados (tasks, notas, categorias, comentários e histórico de tempo) num arquivo <code style="background:var(--surface2);padding:1px 5px;border-radius:4px;font-family:'DM Sans',monospace;color:var(--accent)">.json</code>. Para restaurar em outro computador, basta importar o arquivo.
        </p>

        {{-- Alert --}}
        <div id="portability-alert" style="display:none;margin-bottom:14px"></div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">

            {{-- Export --}}
            <div style="background:var(--surface2);border:1px solid var(--border);border-radius:12px;padding:16px;display:flex;flex-direction:column;gap:12px">
                <div style="display:flex;align-items:center;gap:10px">
                    <div style="width:34px;height:34px;border-radius:9px;background:rgba(74,222,128,.1);border:1px solid rgba(74,222,128,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="rgba(74,222,128,.9)" stroke-width="1.8"><path d="M8 2v8M5 7l3 3 3-3M2 12v1a1 1 0 001 1h10a1 1 0 001-1v-1"/></svg>
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:600;color:var(--text)">Exportar dados</div>
                        <div style="font-size:11px;color:var(--muted);margin-top:1px">Baixa um arquivo .json completo</div>
                    </div>
                </div>
                <a href="{{ route('settings.export') }}" class="btn btn-ghost btn-sm" style="justify-content:center;gap:7px;color:var(--success);border-color:rgba(74,222,128,.25)" id="btn-export">
                    <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 2v8M5 7l3 3 3-3M2 12v1a1 1 0 001 1h10a1 1 0 001-1v-1"/></svg>
                    Exportar agora
                </a>
            </div>

            {{-- Import --}}
            <div style="background:var(--surface2);border:1px solid var(--border);border-radius:12px;padding:16px;display:flex;flex-direction:column;gap:12px">
                <div style="display:flex;align-items:center;gap:10px">
                    <div style="width:34px;height:34px;border-radius:9px;background:rgba(255,145,77,.1);border:1px solid rgba(255,145,77,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="rgba(255,145,77,.9)" stroke-width="1.8"><path d="M8 11V3M5 6l3-3 3 3M2 12v1a1 1 0 001 1h10a1 1 0 001-1v-1"/></svg>
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:600;color:var(--text)">Importar dados</div>
                        <div style="font-size:11px;color:var(--muted);margin-top:1px">Restaura a partir de um backup</div>
                    </div>
                </div>
                <label style="cursor:pointer">
                    <input type="file" id="import-file" accept=".json" style="display:none" onchange="handleImport(this)">
                    <div class="btn btn-ghost btn-sm" style="justify-content:center;gap:7px;color:var(--accent);border-color:rgba(255,145,77,.25);pointer-events:none">
                        <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 11V3M5 6l3-3 3 3M2 12v1a1 1 0 001 1h10a1 1 0 001-1v-1"/></svg>
                        <span id="import-label">Selecionar arquivo</span>
                    </div>
                </label>
            </div>

        </div>

        {{-- Import warning --}}
        <div style="display:flex;align-items:flex-start;gap:8px;margin-top:12px;padding:10px 12px;border-radius:8px;background:rgba(224,84,84,.06);border:1px solid rgba(224,84,84,.15)">
            <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="var(--danger)" stroke-width="1.8" style="flex-shrink:0;margin-top:1px"><path d="M8 2L1 14h14L8 2zM8 7v3M8 12v.5"/></svg>
            <span style="font-size:11.5px;color:var(--muted);line-height:1.5">A importação <strong style="color:var(--text)">adiciona</strong> os dados ao banco atual — não apaga os existentes. Para uma restauração limpa, exclua os dados manualmente antes de importar.</span>
        </div>
    </div>
    <div class="card">
        <div style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:var(--muted);margin-bottom:16px">{{ __('app.settings_language') }}</div>
        <p style="font-size:13px;color:var(--muted);margin-bottom:16px;line-height:1.6">{{ __('app.settings_lang_desc') }}</p>

        @if(session('locale_saved'))
            <div style="display:flex;align-items:center;gap:8px;padding:10px 14px;border-radius:10px;background:rgba(74,222,128,.08);border:1px solid rgba(74,222,128,.2);color:var(--success);font-size:13px;margin-bottom:16px">
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

    {{-- About --}}
    <div class="card">
        <div style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:var(--muted);margin-bottom:16px">🕐 {{ __('app.settings_timezone') }}</div>
        <p style="font-size:13px;color:var(--muted);margin-bottom:16px;line-height:1.6">
            {{ __('app.settings_tz_desc') }}
        </p>

        @if(session('timezone_saved'))
            <div style="display:flex;align-items:center;gap:8px;padding:10px 14px;border-radius:10px;background:rgba(74,222,128,.08);border:1px solid rgba(74,222,128,.2);color:var(--success);font-size:13px;margin-bottom:16px">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 8l4 4 8-8"/></svg>
                {{ __('app.settings_tz_saved') }}
            </div>
        @endif

        <form method="POST" action="{{ route('settings.timezone') }}" id="timezone-form">
            @csrf
            @php $currentTz = \App\Models\AppSetting::get('timezone', config('app.timezone')); @endphp
            <div class="select-wrap" style="width:320px">
                <select name="timezone" onchange="document.getElementById('timezone-form').submit()">
                    @foreach([
                        'UTC'                         => 'UTC',
                        'America/Sao_Paulo'           => 'América/São Paulo (BRT, UTC-3)',
                        'America/Manaus'              => 'América/Manaus (AMT, UTC-4)',
                        'America/Belem'               => 'América/Belém (BRT, UTC-3)',
                        'America/Fortaleza'           => 'América/Fortaleza (BRT, UTC-3)',
                        'America/Recife'              => 'América/Recife (BRT, UTC-3)',
                        'America/Noronha'             => 'América/Noronha (FNT, UTC-2)',
                        'America/New_York'            => 'América/New York (EST, UTC-5)',
                        'America/Chicago'             => 'América/Chicago (CST, UTC-6)',
                        'America/Denver'              => 'América/Denver (MST, UTC-7)',
                        'America/Los_Angeles'         => 'América/Los Angeles (PST, UTC-8)',
                        'America/Buenos_Aires'        => 'América/Buenos Aires (ART, UTC-3)',
                        'America/Santiago'            => 'América/Santiago (CLT, UTC-4)',
                        'America/Bogota'              => 'América/Bogotá (COT, UTC-5)',
                        'America/Lima'                => 'América/Lima (PET, UTC-5)',
                        'America/Mexico_City'         => 'América/Cidade do México (CST, UTC-6)',
                        'Europe/Lisbon'               => 'Europa/Lisboa (WET, UTC+0)',
                        'Europe/London'               => 'Europa/Londres (GMT, UTC+0)',
                        'Europe/Madrid'               => 'Europa/Madrid (CET, UTC+1)',
                        'Europe/Paris'                => 'Europa/Paris (CET, UTC+1)',
                        'Europe/Berlin'               => 'Europa/Berlim (CET, UTC+1)',
                        'Europe/Moscow'               => 'Europa/Moscou (MSK, UTC+3)',
                        'Asia/Tokyo'                  => 'Ásia/Tóquio (JST, UTC+9)',
                        'Asia/Shanghai'               => 'Ásia/Xangai (CST, UTC+8)',
                        'Asia/Kolkata'                => 'Ásia/Calcutá (IST, UTC+5:30)',
                        'Asia/Dubai'                  => 'Ásia/Dubai (GST, UTC+4)',
                        'Australia/Sydney'            => 'Austrália/Sydney (AEDT, UTC+11)',
                    ] as $tzKey => $tzLabel)
                        <option value="{{ $tzKey }}" {{ $currentTz === $tzKey ? 'selected' : '' }}>
                            {{ $tzLabel }}
                        </option>
                    @endforeach
                </select>
            </div>
            <p style="font-size:11px;color:var(--muted);margin-top:8px">
                {{ __('app.settings_tz_current') }}: <strong style="font-family:'DM Sans',monospace">{{ now()->format('d/m/Y H:i:s') }}</strong>
            </p>
        </form>
    </div>

    {{-- About --}}
    <div class="card">
        <div style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:var(--muted);margin-bottom:16px">{{ __('app.settings_about') }}</div>
        <div style="display:flex;flex-direction:column;gap:12px;font-size:13px">
            <div style="display:flex;justify-content:space-between;padding-bottom:10px;border-bottom:1px solid var(--border)">
                <span style="color:var(--muted)">{{ __('app.settings_app') }}</span>
                <span style="font-family:'DM Sans',monospace">Taskletto</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding-bottom:10px;border-bottom:1px solid var(--border)">
                <span style="color:var(--muted)">{{ __('app.settings_framework') }}</span>
                <span style="font-family:'DM Sans',monospace">Laravel {{ app()->version() }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding-bottom:10px;border-bottom:1px solid var(--border)">
                <span style="color:var(--muted)">{{ __('app.settings_php') }}</span>
                <span style="font-family:'DM Sans',monospace">{{ PHP_VERSION }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding-bottom:10px;border-bottom:1px solid var(--border)">
                <span style="color:var(--muted)">{{ __('app.settings_db') }}</span>
                <span style="font-family:'DM Sans',monospace">{{ $dbSize }}</span>
            </div>
            <div style="display:flex;justify-content:space-between">
                <span style="color:var(--muted)">{{ __('app.settings_env') }}</span>
                <span class="badge {{ app()->environment('production') ? 'status-completed' : 'status-in_progress' }}">
                    {{ app()->environment() }}
                </span>
            </div>
        </div>
    </div>

    {{-- API --}}
    <div class="card">
        <div style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:var(--muted);margin-bottom:16px">{{ __('app.settings_api') }}</div>
        <p style="font-size:13px;color:var(--muted);margin-bottom:16px;line-height:1.6">
            {{ __('app.settings_api_desc') }} <code style="background:var(--surface2);padding:2px 6px;border-radius:4px;font-family:'DM Sans',monospace;color:var(--accent)">/api/v1/tasks</code>.
        </p>
        <a href="/api/documentation" target="_blank" class="btn btn-ghost">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 3L2 8l3 5M11 3l3 5-3 5M9 2l-2 12"/></svg>
            {{ __('app.settings_api_open') }}
        </a>
    </div>

    {{-- API Endpoints --}}
    <div class="card">
        <div style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:var(--muted);margin-bottom:16px">{{ __('app.settings_endpoints') }}</div>
        <div style="display:flex;flex-direction:column;gap:6px;font-size:12px;font-family:'DM Sans',monospace">
            @foreach([
                ['GET',    '/api/v1/tasks',              'List tasks'],
                ['POST',   '/api/v1/tasks',              'Create task'],
                ['GET',    '/api/v1/tasks/{id}',         'Get task'],
                ['PUT',    '/api/v1/tasks/{id}',         'Update task'],
                ['DELETE', '/api/v1/tasks/{id}',         'Delete task'],
                ['PATCH',  '/api/v1/tasks/{id}/complete','Complete task'],
                ['PATCH',  '/api/v1/tasks/{id}/reopen',  'Reopen task'],
                ['GET',    '/api/v1/tasks-stats',        'Statistics'],
            ] as [$method, $path, $desc])
            <div style="display:flex;align-items:center;gap:10px;padding:6px 10px;border-radius:6px;background:var(--surface2)">
                <span style="
                    width:54px;text-align:center;border-radius:4px;padding:2px 0;font-size:10px;font-weight:600;
                    color:{{ match($method) { 'GET' => '#60a5fa', 'POST' => '#4ade80', 'PUT' => '#f0a05a', 'PATCH' => '#c084fc', 'DELETE' => '#e05454', default => 'var(--muted)' } }};
                    background:{{ match($method) { 'GET' => 'rgba(96,165,250,.1)', 'POST' => 'rgba(74,222,128,.1)', 'PUT' => 'rgba(240,160,90,.1)', 'PATCH' => 'rgba(192,132,252,.1)', 'DELETE' => 'rgba(224,84,84,.1)', default => 'transparent' } }};
                ">{{ $method }}</span>
                <span style="color:var(--text);flex:1">{{ $path }}</span>
                <span style="color:var(--muted);font-family:'DM Sans',sans-serif;font-size:11px">{{ $desc }}</span>
            </div>
            @endforeach
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
async function handleImport(input) {
    const file = input.files[0];
    if (!file) return;

    const label    = document.getElementById('import-label');
    const alertEl  = document.getElementById('portability-alert');

    label.textContent = '⏳ Importando...';
    alertEl.style.display = 'none';

    const formData = new FormData();
    formData.append('file', file);
    formData.append('_token', document.querySelector('meta[name=csrf-token]').content);

    try {
        const res  = await fetch('{{ route('settings.import') }}', { method: 'POST', body: formData });
        const data = await res.json();

        if (res.ok) {
            alertEl.innerHTML = `
                <div style="display:flex;align-items:center;gap:8px;padding:10px 14px;border-radius:10px;background:rgba(74,222,128,.08);border:1px solid rgba(74,222,128,.2);color:var(--success);font-size:13px">
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 8l4 4 8-8"/></svg>
                    ${data.message}
                </div>`;
        } else {
            alertEl.innerHTML = `
                <div style="display:flex;align-items:center;gap:8px;padding:10px 14px;border-radius:10px;background:rgba(224,84,84,.08);border:1px solid rgba(224,84,84,.2);color:var(--danger);font-size:13px">
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 2l12 12M14 2L2 14"/></svg>
                    ${data.message || 'Erro ao importar.'}
                </div>`;
        }
        alertEl.style.display = 'block';
    } catch (e) {
        alertEl.innerHTML = `
            <div style="padding:10px 14px;border-radius:10px;background:rgba(224,84,84,.08);border:1px solid rgba(224,84,84,.2);color:var(--danger);font-size:13px">
                Erro de conexão ao importar.
            </div>`;
        alertEl.style.display = 'block';
    } finally {
        label.textContent = 'Selecionar arquivo';
        input.value = '';
    }
}
</script>
@endpush