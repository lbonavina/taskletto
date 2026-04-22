@php use App\Enums\Plan; @endphp
@extends('layouts.app')

@section('page-title', 'Assinatura')

@push('styles')
<style>
.billing-wrap { max-width: 560px; display: flex; flex-direction: column; gap: 14px; }

.plan-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
}
.plan-badge.free { background: var(--surface2); color: var(--muted); border: 1px solid var(--border); }
.plan-badge.pro  { background: rgba(255,145,77,.1); color: var(--accent); border: 1px solid rgba(255,145,77,.25); }

.usage-bar-wrap { margin-top: 6px; }
.usage-bar-label { display: flex; justify-content: space-between; font-size: 11px; color: var(--muted); margin-bottom: 4px; }
.usage-bar { height: 5px; border-radius: 3px; background: var(--border); overflow: hidden; }
.usage-bar-fill { height: 100%; border-radius: 3px; background: var(--accent); transition: width .3s; }
.usage-bar-fill.near { background: #f0a05a; }
.usage-bar-fill.full { background: var(--danger); }

.billing-toggle {
    padding: 6px 16px; border-radius: 7px; border: none; background: none;
    font-family: var(--font);
    font-size: 12px; font-weight: 600; color: var(--muted); cursor: pointer;
    transition: background .15s, color .15s; display: flex; align-items: center;
}
.billing-toggle.active { background: var(--surface); color: var(--text); box-shadow: 0 1px 4px rgba(0,0,0,.18); }

.checkout-feature { display:flex; align-items:center; gap:10px; font-size:12.5px; color:var(--text); }
.checkout-feature svg { flex-shrink:0; color:var(--success); }
</style>
@endpush

@section('content')
<div class="billing-wrap">

    @if(session('success'))
        <div class="alert-inline success">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 8l4 4 8-8"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert-inline danger">
            <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0"><path d="M8 2L1 14h14L8 2zM8 7v3M8 12v.5"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Current plan --}}
    <div class="card">
        <div class="section-title" style="margin-bottom:16px">Seu plano</div>

        <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px">
            <span class="plan-badge {{ $plan === Plan::Pro ? 'pro' : 'free' }}">
                {{ $plan->label() }}
            </span>
            @if($plan === Plan::Pro && $sub->current_period_ends_at)
                <span style="font-size:12px;color:var(--muted)">
                    {{ $sub->isCancelled() ? 'Acesso até' : 'Renova em' }}
                    {{ $sub->current_period_ends_at->format('d/m/Y') }}
                </span>
            @endif
        </div>

        {{-- Usage bars --}}
        @php
            $limits = $plan->limits();
            $resources = [
                'tasks'      => ['label' => 'Tarefas',    'icon' => '✓'],
                'notes'      => ['label' => 'Notas',      'icon' => '📝'],
                'categories' => ['label' => 'Categorias', 'icon' => '🏷'],
                'storage_mb' => ['label' => 'Armazenamento', 'icon' => '💾'],
            ];
        @endphp

        <div style="display:flex;flex-direction:column;gap:14px">
            @foreach($resources as $key => $meta)
                @php
                    $count = $usage[$key];
                    $limit = $limits[$key] ?? null;
                    $pct   = $limit ? min(100, round(($count / $limit) * 100)) : 0;
                    $cls   = $pct >= 100 ? 'full' : ($pct >= 80 ? 'near' : '');
                    
                    $displayCount = $key === 'storage_mb' ? $count . ' MB' : $count;
                    $displayLimit = $limit;
                    if ($key === 'storage_mb' && $limit) {
                        $displayLimit = $limit >= 1024 ? round($limit / 1024, 1) . ' GB' : $limit . ' MB';
                    }
                @endphp
                <div>
                    <div class="usage-bar-label">
                        <span>{{ $meta['label'] }}</span>
                        <span>
                            {{ $displayCount }}
                            @if($limit)
                                / {{ $displayLimit }}
                            @else
                                <span style="color:var(--success)">ilimitado</span>
                            @endif
                        </span>
                    </div>
                    @if($limit)
                        <div class="usage-bar">
                            <div class="usage-bar-fill {{ $cls }}" style="width:{{ $pct }}%"></div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Upgrade CTA --}}
    @if($plan === Plan::Free)
        <div class="card" style="border-color:rgba(255,145,77,.25);background:rgba(255,145,77,.03)">
            <div class="section-title" style="margin-bottom:12px;color:var(--accent)">Upgrade para Pro</div>

            {{-- Period toggle --}}
            <div style="display:flex;align-items:center;gap:0;background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:3px;width:fit-content;margin-bottom:18px">
                <button id="toggle-monthly" onclick="setBillingPeriod('monthly')" class="billing-toggle active" type="button">Mensal</button>
                <button id="toggle-annual"  onclick="setBillingPeriod('annual')"  class="billing-toggle"        type="button">
                    Anual
                    <span style="background:rgba(74,222,128,.15);color:#4ade80;font-size:10px;font-weight:700;padding:1px 6px;border-radius:20px;margin-left:4px">-33%</span>
                </button>
            </div>

            {{-- Price display --}}
            <div style="margin-bottom:16px">
                <div style="display:flex;align-items:baseline;gap:4px">
                    <span id="price-display" style="font-family:'Montserrat',sans-serif;font-size:28px;font-weight:800;color:var(--text)">R$ 14,99</span>
                    <span style="font-size:13px;color:var(--muted)">/mês</span>
                </div>
                <div id="price-sub" style="font-size:11.5px;color:var(--muted);margin-top:3px;min-height:16px">Cobrado mensalmente. Cancele quando quiser.</div>
            </div>

            {{-- Forms (hidden, submitted via popup) --}}
            <form id="form-monthly" method="POST" action="{{ route('billing.checkout', 'pro-monthly') }}" style="display:none">@csrf</form>
            <form id="form-annual"  method="POST" action="{{ route('billing.checkout', 'pro-annual') }}"  style="display:none">@csrf</form>

            <button type="button" onclick="openCheckoutPopup()" class="btn btn-primary" style="width:100%;justify-content:center;gap:8px">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M8 1l2 4h4l-3 3 1 5-4-3-4 3 1-5L2 5h4z"/></svg>
                Assinar Pro
            </button>
        </div>
    @endif

    {{-- Cancel subscription --}}
    @if($plan === Plan::Pro && ! $sub->isCancelled())
        <div class="card">
            <div class="section-title" style="margin-bottom:8px">Cancelar assinatura</div>
            <p class="action-row-desc" style="margin-bottom:16px;line-height:1.6">
                Após o cancelamento, você continua com acesso Pro até
                {{ $sub->current_period_ends_at?->format('d/m/Y') ?? 'o fim do período atual' }}.
                Depois disso, volta automaticamente para o plano Free.
            </p>
            <form method="POST" action="{{ route('billing.cancel') }}"
                  onsubmit="return confirm('Tem certeza que deseja cancelar sua assinatura Pro?')">
                @csrf
                <button type="submit" class="btn btn-ghost" style="color:var(--danger);border-color:rgba(224,84,84,.25)">
                    Cancelar assinatura
                </button>
            </form>
        </div>
    @endif

    @if($sub->isCancelled())
        <div class="alert-inline warning">
            <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0"><path d="M8 2L1 14h14L8 2zM8 7v3M8 12v.5"/></svg>
            Assinatura cancelada. Acesso Pro ativo até {{ $sub->current_period_ends_at?->format('d/m/Y') }}.
        </div>
    @endif

    <div style="font-size:12px;color:var(--muted);line-height:1.6">
        Dúvidas sobre cobrança? Fale pelo e-mail
        <a href="mailto:suporte@taskletto.com" style="color:var(--accent)">suporte@taskletto.com</a>
    </div>

</div>
@endsection

@push('modals')
<div id="checkout-backdrop" style="display:none;position:fixed;inset:0;z-index:900;background:rgba(0,0,0,.5)" onclick="closeCheckoutPopup()"></div>
<div id="checkout-modal" style="display:none;position:fixed;top:50vh;left:50vw;transform:translate(-50%,-54%) scale(.96);z-index:901;width:min(420px,calc(100vw - 32px));background:var(--surface);border:1px solid var(--border);border-radius:18px;padding:28px;box-shadow:0 24px 64px rgba(0,0,0,.45);opacity:0;transition:transform .2s cubic-bezier(.34,1.2,.64,1),opacity .18s ease">

    {{-- Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:20px">
        <div style="display:flex;align-items:center;gap:12px">
            <div style="width:42px;height:42px;border-radius:12px;background:rgba(255,145,77,.12);border:1px solid rgba(255,145,77,.22);display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0">⭐</div>
            <div>
                <div style="font-family:'Montserrat',sans-serif;font-weight:800;font-size:15px;color:var(--text)">Taskletto Pro</div>
                <div id="popup-period-label" style="font-size:11.5px;color:var(--muted);margin-top:1px">Plano Mensal</div>
            </div>
        </div>
        <button onclick="closeCheckoutPopup()" style="width:28px;height:28px;border-radius:7px;border:1px solid var(--border);background:var(--surface2);color:var(--muted);cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:background .12s" onmouseenter="this.style.background='var(--surface)';this.style.color='var(--text)'" onmouseleave="this.style.background='var(--surface2)';this.style.color='var(--muted)'">
            <svg width="11" height="11" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M1 1l10 10M11 1L1 11"/></svg>
        </button>
    </div>

    {{-- Price --}}
    <div style="background:var(--surface2);border:1px solid var(--border);border-radius:12px;padding:16px 18px;margin-bottom:18px;display:flex;align-items:center;justify-content:space-between">
        <div>
            <div id="popup-price" style="font-family:'Montserrat',sans-serif;font-size:24px;font-weight:800;color:var(--text)">R$ 14,99<span style="font-size:13px;font-weight:500;color:var(--muted)">/mês</span></div>
            <div id="popup-price-note" style="font-size:11px;color:var(--muted);margin-top:2px">Cobrado mensalmente</div>
        </div>
        <div id="popup-savings" style="display:none;background:rgba(74,222,128,.12);color:#4ade80;font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;border:1px solid rgba(74,222,128,.2)">Economize R$ 60/ano</div>
    </div>

    {{-- Features --}}
    <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:20px">
        @foreach(Plan::Pro->features() as $feature)
        <div class="checkout-feature">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M2 8l4 4 8-8"/></svg>
            {{ $feature }}
        </div>
        @endforeach
    </div>

    {{-- Payment methods --}}
    <div style="display:flex;align-items:center;gap:8px;padding:10px 14px;background:var(--surface2);border:1px solid var(--border);border-radius:10px;margin-bottom:18px">
        <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="var(--muted)" stroke-width="1.5" stroke-linecap="round"><rect x="1" y="3" width="14" height="10" rx="2"/><path d="M1 7h14"/></svg>
        <span style="font-size:11.5px;color:var(--muted)">Pagamento via <strong style="color:var(--text)">PIX</strong> ou <strong style="color:var(--text)">Cartão de crédito</strong> — processado pelo AbacatePay 🔒</span>
    </div>

    {{-- CTA --}}
    <button id="popup-submit-btn" onclick="submitCheckout()" class="btn btn-primary" style="width:100%;justify-content:center;font-size:13px;padding:10px">
        Continuar para o pagamento
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M2 6h8M6 2l4 4-4 4"/></svg>
    </button>
    <p style="font-size:10.5px;color:var(--muted);text-align:center;margin-top:10px;line-height:1.5">
        Você será redirecionado para o AbacatePay. Cancele quando quiser, sem multa.
    </p>
</div>
@endpush

@push('scripts')
<script>
let _billingPeriod = 'monthly';

function setBillingPeriod(period) {
    _billingPeriod = period;
    document.getElementById('toggle-monthly').classList.toggle('active', period === 'monthly');
    document.getElementById('toggle-annual').classList.toggle('active',  period === 'annual');

    if (period === 'annual') {
        document.getElementById('price-display').textContent = 'R$ 9,99';
        document.getElementById('price-sub').textContent = 'Cobrado R$ 119,88/ano. Economize R$ 60 em relação ao mensal.';
    } else {
        document.getElementById('price-display').textContent = 'R$ 14,99';
        document.getElementById('price-sub').textContent = 'Cobrado mensalmente. Cancele quando quiser.';
    }
}

function openCheckoutPopup() {
    const isAnnual = _billingPeriod === 'annual';
    document.getElementById('popup-period-label').textContent = isAnnual ? 'Plano Anual' : 'Plano Mensal';
    document.getElementById('popup-price').innerHTML = isAnnual
        ? 'R$ 9,99<span style="font-size:13px;font-weight:500;color:var(--muted)">/mês</span>'
        : 'R$ 14,99<span style="font-size:13px;font-weight:500;color:var(--muted)">/mês</span>';
    document.getElementById('popup-price-note').textContent = isAnnual ? 'Cobrado R$ 119,88/ano' : 'Cobrado mensalmente';
    document.getElementById('popup-savings').style.display = isAnnual ? 'block' : 'none';

    const backdrop = document.getElementById('checkout-backdrop');
    const modal    = document.getElementById('checkout-modal');
    backdrop.style.display = 'block';
    modal.style.display    = 'block';
    requestAnimationFrame(() => {
        modal.style.transform = 'translate(-50%,-50%) scale(1)';
        modal.style.opacity   = '1';
    });
}

function closeCheckoutPopup() {
    const modal    = document.getElementById('checkout-modal');
    const backdrop = document.getElementById('checkout-backdrop');
    modal.style.transform = 'translate(-50%,-54%) scale(.96)';
    modal.style.opacity   = '0';
    setTimeout(() => { modal.style.display = backdrop.style.display = 'none'; modal.style.opacity = ''; }, 180);
}

function submitCheckout() {
    document.getElementById('popup-submit-btn').textContent = 'Aguarde…';
    document.getElementById(_billingPeriod === 'annual' ? 'form-annual' : 'form-monthly').submit();
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeCheckoutPopup(); });
</script>
@endpush
