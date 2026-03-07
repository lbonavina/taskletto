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

    <div class="card">
        <div style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:var(--muted);margin-bottom:16px">API</div>
        <p style="font-size:13px;color:var(--muted);margin-bottom:16px;line-height:1.6">
            A API RESTful está disponível em <code style="background:var(--surface2);padding:2px 6px;border-radius:4px;font-family:'DM Sans',monospace;color:var(--accent)">/api/v1/tasks</code>. Acesse a documentação interativa abaixo.
        </p>
        <a href="/api/documentation" target="_blank" class="btn btn-ghost">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 3L2 8l3 5M11 3l3 5-3 5M9 2l-2 12"/></svg>
            Abrir Swagger UI
        </a>
    </div>

    <div class="card">
        <div style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:var(--muted);margin-bottom:16px">Endpoints da API</div>
        <div style="display:flex;flex-direction:column;gap:6px;font-size:12px;font-family:'DM Sans',monospace">
            @foreach([
                ['GET',    '/api/v1/tasks',              'Listar tarefas'],
                ['POST',   '/api/v1/tasks',              'Criar tarefa'],
                ['GET',    '/api/v1/tasks/{id}',         'Buscar tarefa'],
                ['PUT',    '/api/v1/tasks/{id}',         'Atualizar tarefa'],
                ['DELETE', '/api/v1/tasks/{id}',         'Excluir tarefa'],
                ['PATCH',  '/api/v1/tasks/{id}/complete','Concluir tarefa'],
                ['PATCH',  '/api/v1/tasks/{id}/reopen',  'Reabrir tarefa'],
                ['GET',    '/api/v1/tasks-stats',        'Estatísticas'],
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