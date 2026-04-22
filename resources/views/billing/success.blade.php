@extends('layouts.app')

@section('page-title', 'Pagamento confirmado')

@section('content')
<div style="max-width:480px;text-align:center;padding:40px 0">
    <div style="font-size:48px;margin-bottom:16px">🎉</div>
    <h1 style="font-size:20px;font-weight:700;color:var(--text);margin-bottom:8px">Seja bem-vindo ao Taskletto Pro!</h1>
    <p style="color:var(--muted);font-size:14px;line-height:1.6;margin-bottom:28px">
        Seu pagamento foi confirmado. Aproveite tarefas, notas e categorias ilimitadas.
    </p>
    <a href="{{ route('dashboard') }}" class="btn btn-primary">Ir para o Dashboard</a>
</div>
@endsection
