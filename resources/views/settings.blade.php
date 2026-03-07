@extends('layouts.app')

@section('page-title', __('app.settings'))

@section('content')

<div style="max-width:640px;display:flex;flex-direction:column;gap:16px">

    {{-- Language --}}
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