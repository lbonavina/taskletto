@php use App\Enums\Plan; @endphp
@extends('layouts.app')

@section('page-title', 'Planos')

@push('styles')
<style>
.pricing-wrap { max-width: 720px; }

.pricing-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 24px;
}
@media (max-width: 600px) { .pricing-grid { grid-template-columns: 1fr; } }

.pricing-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    padding: 24px;
    display: flex;
    flex-direction: column;
    gap: 0;
}
.pricing-card.featured {
    border-color: var(--accent);
    background: rgba(255,145,77,.04);
}

.pricing-plan-name {
    font-size: 13px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .06em;
    color: var(--muted); margin-bottom: 10px;
}
.pricing-card.featured .pricing-plan-name { color: var(--accent); }

.pricing-price {
    font-size: 28px; font-weight: 800; color: var(--text);
    margin-bottom: 4px; line-height: 1;
}
.pricing-price-sub {
    font-size: 12px; color: var(--muted); margin-bottom: 20px;
}

.pricing-features {
    list-style: none; display: flex; flex-direction: column; gap: 9px;
    margin-bottom: 24px; flex: 1;
}
.pricing-features li {
    display: flex; align-items: flex-start; gap: 8px;
    font-size: 13px; color: var(--text); line-height: 1.4;
}
.pricing-features li svg { flex-shrink: 0; margin-top: 1px; color: var(--success); }
.pricing-card.featured .pricing-features li svg { color: var(--accent); }

.pricing-cta {
    display: flex; align-items: center; justify-content: center;
    padding: 11px 20px; border-radius: var(--radius-md);
    font-size: 13px; font-weight: 600; font-family: inherit;
    cursor: pointer; text-decoration: none; border: none;
    transition: opacity .15s, transform .1s;
}
.pricing-cta:hover { opacity: .85; transform: translateY(-1px); }
.pricing-cta.primary { background: var(--accent); color: #1a1a1a; }
.pricing-cta.ghost   { background: var(--surface2); border: 1px solid var(--border); color: var(--text); }

.pricing-badge {
    display: inline-flex; align-items: center;
    padding: 3px 10px; border-radius: 20px;
    background: rgba(255,145,77,.1); border: 1px solid rgba(255,145,77,.2);
    color: var(--accent); font-size: 10px; font-weight: 700;
    letter-spacing: .06em; text-transform: uppercase;
    margin-bottom: 10px; width: fit-content;
}

.current-plan-banner {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 16px; border-radius: var(--radius-md);
    background: rgba(74,222,128,.06); border: 1px solid rgba(74,222,128,.15);
    margin-bottom: 20px; font-size: 13px; color: var(--text);
}
.current-plan-banner svg { color: var(--success); flex-shrink: 0; }
</style>
@endpush

@section('content')
<div class="pricing-wrap">

    @auth
        @php $currentPlan = Auth::user()->plan(); @endphp
        <div class="current-plan-banner">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 8l4 4 8-8"/></svg>
            Seu plano atual: <strong>{{ $currentPlan->label() }}</strong>
            @if($currentPlan === Plan::Pro)
                — acesso ilimitado ativo
            @endif
        </div>
    @endauth

    <div class="pricing-grid">
        {{-- Free --}}
        <div class="pricing-card">
            <div class="pricing-plan-name">Free</div>
            <div class="pricing-price">Grátis</div>
            <div class="pricing-price-sub">para sempre</div>
            <ul class="pricing-features">
                @foreach(Plan::Free->features() as $feature)
                    <li>
                        <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M2 8l4 4 8-8"/></svg>
                        {{ $feature }}
                    </li>
                @endforeach
            </ul>
            @auth
                @if(Auth::user()->plan() === Plan::Free)
                    <span class="pricing-cta ghost" style="cursor:default;opacity:.6">Plano atual</span>
                @else
                    <span class="pricing-cta ghost" style="cursor:default;opacity:.6">Disponível ao cancelar</span>
                @endif
            @else
                <a href="{{ route('register') }}" class="pricing-cta ghost">Começar grátis</a>
            @endauth
        </div>

        {{-- Pro --}}
        <div class="pricing-card featured">
            <div class="pricing-badge">Mais popular</div>
            <div class="pricing-plan-name">Pro</div>
            <div class="pricing-price">R$ 14,90</div>
            <div class="pricing-price-sub">por mês · cancele quando quiser</div>
            <ul class="pricing-features">
                @foreach(Plan::Pro->features() as $feature)
                    <li>
                        <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M2 8l4 4 8-8"/></svg>
                        {{ $feature }}
                    </li>
                @endforeach
            </ul>
            @auth
                @if(Auth::user()->plan() === Plan::Pro)
                    <span class="pricing-cta primary" style="cursor:default">Plano atual ✓</span>
                @else
                    <form method="POST" action="{{ route('billing.checkout', 'pro') }}">
                        @csrf
                        <button type="submit" class="pricing-cta primary" style="width:100%">
                            Assinar Pro — PIX, Boleto ou Cartão
                        </button>
                    </form>
                @endif
            @else
                <a href="{{ route('register') }}" class="pricing-cta primary">Criar conta e assinar Pro</a>
            @endauth
        </div>
    </div>

    <div class="card" style="font-size:12.5px;color:var(--muted);line-height:1.7">
        Pagamentos processados com segurança via <strong style="color:var(--text)">AbacatePay</strong> — PIX, Boleto ou Cartão de Crédito.
        Cancele a qualquer momento sem multa. Após o cancelamento, você mantém o acesso Pro até o fim do período pago.
    </div>

</div>
@endsection
