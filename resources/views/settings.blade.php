@extends('layouts.app')

@section('page-title', 'Configurações')

@section('content')

<div style="max-width:640px;display:flex;flex-direction:column;gap:16px">

    <div class="card">
        <div style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:var(--muted);margin-bottom:16px">Sobre o sistema</div>
        <div style="display:flex;flex-direction:column;gap:12px;font-size:13px">
            <div style="display:flex;justify-content:space-between;padding-bottom:10px;border-bottom:1px solid var(--border)">
                <span style="color:var(--muted)">Aplicação</span>
                <span style="font-family:'DM Sans',monospace">Taskletto</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding-bottom:10px;border-bottom:1px solid var(--border)">
                <span style="color:var(--muted)">Framework</span>
                <span style="font-family:'DM Sans',monospace">Laravel {{ app()->version() }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding-bottom:10px;border-bottom:1px solid var(--border)">
                <span style="color:var(--muted)">PHP</span>
                <span style="font-family:'DM Sans',monospace">{{ PHP_VERSION }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding-bottom:10px;border-bottom:1px solid var(--border)">
                <span style="color:var(--muted)">Banco de dados</span>
                <span style="font-family:'DM Sans',monospace">{{ $dbSize }}</span>
            </div>
            <div style="display:flex;justify-content:space-between">
                <span style="color:var(--muted)">Ambiente</span>
                <span class="badge {{ app()->environment('production') ? 'status-completed' : 'status-in_progress' }}">
                    {{ app()->environment() }}
                </span>
            </div>
        </div>
    </div>

</div>
@endsection