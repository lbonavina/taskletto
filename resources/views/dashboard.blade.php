@extends('layouts.app')
@section('page-title', __('app.nav_dashboard'))

@section('topbar-actions')
    <button class="btn btn-ghost" onclick="createNote()">
        <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M8 2v12M2 8h12" />
        </svg>
        {{ __('app.dash_new_note') }}
    </button>
    <button class="btn btn-primary" onclick="document.getElementById('modal-new-task').classList.add('open')">
        <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M8 2v12M2 8h12" />
        </svg>
        {{ __('app.new_task') }}
    </button>
@endsection

@push('styles')
<style>
/* ─── Page padding override ─────────────────────────────────── */
.page-content { padding: 14px 18px !important; }

/* ─── Masonry via JS ────────────────────────────────────────── */
.dash-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    grid-auto-rows: 1px;
    gap: 10px;
    align-items: start;
    box-sizing: border-box;
}
.dash-grid > * {
    min-width: 0;
    box-sizing: border-box;
}

/* ─── Card specifics ───────────────────────────────────────── */
.dc-greeting { min-height: 120px; display:flex; align-items:flex-end; overflow:hidden; }
.dc-tasks    { padding: 0; }
.dc-notes    { padding: 0; }


/* ─── Card base ────────────────────────────────────────────── */
.dc {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
    position: relative;
    transition: border-color .2s, box-shadow .2s;
    animation: dcIn .28s cubic-bezier(.25,.46,.45,.94) both;
    min-width: 0;
    max-width: 100%;
    box-sizing: border-box;
}
.dc:hover { border-color: rgba(255,145,77,.22); box-shadow: inset 0 0 0 1px rgba(255,145,77,.1); }

@keyframes dcIn {
    from { opacity:0; transform:translateY(6px) scale(.988); }
    to   { opacity:1; transform:translateY(0) scale(1); }
}
.dc:nth-child(1){animation-delay:0ms}.dc:nth-child(2){animation-delay:35ms}
.dc:nth-child(3){animation-delay:65ms}.dc:nth-child(4){animation-delay:95ms}
.dc:nth-child(5){animation-delay:120ms}.dc:nth-child(6){animation-delay:145ms}
.dc:nth-child(n+7){animation-delay:170ms}

.dc-pad { padding: 14px 16px; }
.dc-label {
    font-size: 9px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 1.2px; color: var(--muted);
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 8px;
}
.dc-label a { font-size: 10px; color: var(--accent); text-decoration: none; opacity: .8; letter-spacing: 0; text-transform: none; font-weight: 500; transition: opacity .15s; }
.dc-label a:hover { opacity: 1; }

/* ─── Grid areas ───────────────────────────────────────────── */










/* KPIs inline dentro do progress ─────────────────────────── */
.progress-kpis { display:grid; grid-template-columns:repeat(4,1fr); gap:6px; margin-bottom:12px; }
.kpi-card { background:var(--surface2); border:1px solid var(--border); border-radius:10px; padding:8px 10px; position:relative; overflow:hidden; }
.kpi-stripe { position:absolute; top:0; left:0; right:0; height:2px; opacity:.7; }
.kpi-val { font-size:22px; font-weight:800; letter-spacing:-.5px; line-height:1; margin-bottom:3px; }
.kpi-label { font-size:9px; color:var(--muted); font-weight:500; line-height:1.3; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

/* ─── Greeting ─────────────────────────────────────────────── */
.greet-bg { position:absolute; inset:0; z-index:0; background-size:cover; background-position:center; }
.greet-bg::after { content:''; position:absolute; inset:0; background:linear-gradient(to top, rgba(12,12,14,.92) 25%, rgba(12,12,14,.3) 65%, transparent 100%); }
/* Light: pastel gradients are used — just a light scrim at the bottom for text legibility */
html[data-theme=light] .greet-bg::after { background:linear-gradient(to top, rgba(255,255,255,.70) 0%, rgba(255,255,255,.25) 50%, transparent 100%); }
html[data-theme=light] .greet-date  { color: rgba(30,30,60,.65); }
html[data-theme=light] .greet-title { color: #1a1a2e; text-shadow: 0 1px 3px rgba(255,255,255,.4); }
html[data-theme=light] .greet-sub   { color: rgba(30,30,60,.60); }
.greet-content { position:relative; z-index:1; padding:14px 16px; width:100%; }
.greet-date { font-size:8.5px; font-weight:700; letter-spacing:1.4px; text-transform:uppercase; color:var(--muted); margin-bottom:4px; }
.greet-title { font-size:18px; font-weight:800; letter-spacing:-.4px; color:var(--text); line-height:1.15; margin-bottom:3px; }
.greet-sub { font-size:11px; color:var(--muted); line-height:1.5; }

.period-badge { position:absolute; top:10px; right:12px; z-index:2; font-size:8.5px; font-weight:700; letter-spacing:.9px; text-transform:uppercase; padding:3px 9px; border-radius:20px; background:rgba(255,145,77,.15); color:var(--accent); border:1px solid rgba(255,145,77,.2); display:flex; align-items:center; gap:4px; }
.period-dot { width:5px; height:5px; border-radius:50%; background:var(--accent); animation:pulse-dot 2s ease-in-out infinite; }
@keyframes pulse-dot { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(.7)} }

/* ─── Weather widget (compacto) ────────────────────────────── */
.wx-main { display:flex; align-items:center; gap:10px; margin-bottom:6px; }
.wx-icon { font-size:32px; line-height:1; }
.wx-temp { font-size:26px; font-weight:800; letter-spacing:-1px; color:var(--text); line-height:1; }
.wx-feels { font-size:9.5px; color:var(--muted); margin-top:1px; }
.wx-desc { font-size:10.5px; color:var(--text); font-weight:600; margin-bottom:6px; }
.wx-location { font-size:9px; color:var(--muted); display:flex; align-items:center; gap:3px; margin-bottom:8px; }
.wx-pills { display:flex; gap:5px; flex-wrap:wrap; }
.wx-pill { font-size:9px; padding:2px 8px; border-radius:20px; background:var(--surface2); border:1px solid var(--border); color:var(--muted); }
.wx-skeleton { height:11px; background:var(--surface2); border-radius:5px; animation:shimmer 1.4s ease-in-out infinite; margin-bottom:7px; }
.wx-error { font-size:11px; color:var(--muted); text-align:center; padding:16px 0; }

/* ─── Quote widget (compacto) ──────────────────────────────── */
.quote-body { display:flex; flex-direction:column; justify-content:center; gap:8px; height:100%; }
.quote-mark { font-size:28px; line-height:1; color:var(--accent); opacity:.3; font-family:Georgia,serif; margin-bottom:-6px; }
.quote-text { font-size:11.5px; font-weight:500; color:var(--text); line-height:1.6; font-style:italic; }
.quote-author { font-size:9.5px; color:var(--muted); font-weight:600; letter-spacing:.4px; }
.quote-skeleton { height:12px; background:var(--surface2); border-radius:5px; animation:shimmer 1.4s ease-in-out infinite; }
@keyframes shimmer { 0%,100%{opacity:.4} 50%{opacity:1} }

/* ─── Task rows ────────────────────────────────────────────── */
.dc-tasks .dc-label, .dc-notes .dc-label { padding: 12px 16px 0; margin-bottom: 6px; }
.tr { display:flex; align-items:center; gap:9px; padding:8px 16px; text-decoration:none; color:var(--text); border-bottom:1px solid var(--border); transition:background .12s; position:relative; }
.tr:last-child { border-bottom:none; }
.tr:hover { background:var(--surface2); }
.tr-pri { width:2.5px; height:24px; border-radius:99px; background:var(--tr-color,var(--muted)); flex-shrink:0; }
.tr-body { flex:1; min-width:0; }
.tr-name { font-size:11.5px; font-weight:500; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; margin-bottom:1px; }
.tr-sub { font-size:9.5px; color:var(--muted); display:flex; align-items:center; gap:4px; }
.tr-meta { display:flex; align-items:center; gap:4px; flex-shrink:0; }
.tr-tag { font-size:9px; padding:1px 6px; border-radius:4px; font-weight:600; background:var(--surface2); color:var(--muted); }
.tr-tag.overdue { background:rgba(224,84,84,.12); color:var(--danger); }
.tr-tag.today   { background:rgba(255,145,77,.12); color:var(--accent); }
.tr-arr { opacity:0; transition:opacity .12s; color:var(--muted); flex-shrink:0; }
.tr:hover .tr-arr { opacity:1; }
.tr-time-bar { position:absolute; bottom:0; left:16px; right:16px; height:2px; background:var(--border); border-radius:99px; overflow:hidden; }
.tr-time-fill { height:100%; border-radius:99px; width:0; transition:width 1s cubic-bezier(.34,1.2,.64,1); }
.dash-empty { padding:12px 16px; color:var(--muted); font-size:11px; display:flex; align-items:center; gap:7px; }
.dash-sect { font-size:8px; font-weight:700; text-transform:uppercase; letter-spacing:1.2px; color:var(--muted); padding:6px 16px 3px; opacity:.5; }

/* ─── Note rows ────────────────────────────────────────────── */
.nl { display:flex; align-items:center; gap:9px; padding:8px 16px; text-decoration:none; color:var(--text); border-bottom:1px solid var(--border); transition:background .12s; }
.nl:last-child { border-bottom:none; }
.nl:hover { background:var(--surface2); }
.nl-bar { width:2.5px; height:24px; border-radius:99px; flex-shrink:0; }
.nl-body { flex:1; min-width:0; }
.nl-title { font-size:11.5px; font-weight:500; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; margin-bottom:1px; }
.nl-excerpt { font-size:9.5px; color:var(--muted); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.nl-time { font-size:9px; color:var(--muted); flex-shrink:0; }
.nl-arr { opacity:0; transition:opacity .12s; color:var(--muted); }
.nl:hover .nl-arr { opacity:1; }
.nc-new { border:none; border-top:1px dashed var(--border); display:flex; align-items:center; justify-content:center; gap:6px; padding:9px 16px; color:var(--muted); font-size:11px; font-weight:500; text-decoration:none; transition:color .15s, background .15s; cursor:pointer; background:none; font-family:inherit; width:100%; }
.nc-new:hover { color:var(--accent); background:rgba(255,145,77,.04); }

/* ─── Progress ─────────────────────────────────────────────── */
.ring-row { display:flex; align-items:center; gap:8px; margin-bottom:8px; }
.ring-svg { transform:rotate(-90deg); flex-shrink:0; }
.ring-track { fill:none; stroke:var(--border); stroke-width:4; }
.ring-fill { fill:none; stroke:var(--accent); stroke-width:4; stroke-linecap:round; stroke-dasharray:126; stroke-dashoffset:126; transition:stroke-dashoffset 1.4s cubic-bezier(.34,1.2,.64,1); }
.ring-big { font-size:20px; font-weight:800; color:var(--text); line-height:1; letter-spacing:-.5px; }
.ring-sub { font-size:10px; color:var(--muted); margin-top:2px; }
.sbar { margin-bottom:6px; }
.sbar-head { display:flex; justify-content:space-between; margin-bottom:3px; }
.sbar-lbl { font-size:10.5px; color:var(--muted); }
.sbar-cnt { font-size:10.5px; color:var(--muted); }
.sbar-track { height:2px; background:var(--surface2); border-radius:99px; overflow:hidden; }
.sbar-fill { height:100%; border-radius:99px; width:0; transition:width 1.1s cubic-bezier(.34,1.2,.64,1); }

/* ─── Mini Calendar ────────────────────────────────────────── */
.cal-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:6px; }
.cal-month-label { font-size:12px; font-weight:700; color:var(--text); }
.cal-nav { display:flex; gap:3px; }
.cal-nav-btn { width:20px; height:20px; border-radius:5px; border:1px solid var(--border); background:var(--surface2); color:var(--muted); cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background .12s; font-size:10px; }
.cal-nav-btn:hover { background:var(--surface); color:var(--text); }
.cal-grid { display:grid; grid-template-columns:repeat(7,1fr); gap:1px 2px; }
.cal-dow { text-align:center; font-size:9px; font-weight:700; letter-spacing:.5px; text-transform:uppercase; color:var(--muted); padding-bottom:3px; }
.cal-day { height:26px; display:flex; align-items:center; justify-content:center; border-radius:5px; font-size:11px; font-weight:500; color:var(--muted); cursor:pointer; transition:background .12s, color .12s; position:relative; }
.cal-day:hover { background:var(--surface2); color:var(--text); }
.cal-day.other-month { opacity:.2; pointer-events:none; }
.cal-day.today { background:var(--accent); color:#fff; font-weight:700; }
.cal-day.today:hover { background:var(--accent2); }
.cal-day.weekend { color: color-mix(in srgb, var(--muted) 80%, var(--accent)); }
.cal-day.today.weekend { color:#fff; }
/* task dot */
.cal-day.has-task::after { content:''; position:absolute; bottom:2px; left:50%; transform:translateX(-50%); width:3px; height:3px; border-radius:50%; background:var(--accent); opacity:.9; }
.cal-day.today.has-task::after { background:#fff; }
/* holiday */
.cal-day.holiday { color:#f87171; }
.cal-day.holiday:hover { background:rgba(248,113,113,.1); }
.cal-day.today.holiday { background:var(--accent); color:#fff; }
.cal-day.holiday::before { content:''; position:absolute; top:2px; right:3px; width:3px; height:3px; border-radius:50%; background:#f87171; }
.cal-day.today.holiday::before { background:rgba(255,255,255,.7); }
/* legend */
.cal-legend { display:flex; gap:10px; margin-top:6px; flex-wrap:wrap; }
.cal-leg-item { display:flex; align-items:center; gap:4px; font-size:8px; color:var(--muted); }
.cal-leg-dot { width:6px; height:6px; border-radius:50%; flex-shrink:0; }
/* tooltip */
.cal-tooltip { position:fixed; background:var(--surface2); border:1px solid var(--border); border-radius:9px; padding:8px 11px; font-size:10.5px; color:var(--text); pointer-events:none; z-index:9999; box-shadow:0 6px 20px rgba(0,0,0,.55); opacity:0; transition:opacity .12s; max-width:200px; white-space:normal; }
.cal-tooltip-title { font-weight:700; font-size:9px; text-transform:uppercase; letter-spacing:.8px; color:var(--muted); margin-bottom:5px; }
.cal-tooltip-item { display:flex; align-items:center; gap:6px; margin-bottom:3px; font-size:10px; }
.cal-tooltip-item:last-child { margin-bottom:0; }
.cal-tooltip-dot { width:5px; height:5px; border-radius:50%; flex-shrink:0; }

/* ─── Pomodoro ─────────────────────────────────────────────── */
.pom-mode-tabs { display:flex; gap:4px; margin-bottom:8px; }
.pom-tab { padding:2px 8px; border-radius:6px; border:1px solid var(--border); background:transparent; color:var(--muted); font-size:9px; font-weight:600; font-family:inherit; cursor:pointer; transition:all .15s; }
.pom-tab.active { background:rgba(255,145,77,.12); border-color:rgba(255,145,77,.3); color:var(--accent); }
/* horizontal layout: ring left, controls right */
.pom-body { display:flex; align-items:center; gap:12px; }
.pom-ring-wrap { position:relative; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.pom-svg { transform:rotate(-90deg); }
.pom-track { fill:none; stroke:var(--border); stroke-width:6; }
.pom-fill { fill:none; stroke:var(--accent); stroke-width:6; stroke-linecap:round; stroke-dasharray:352; stroke-dashoffset:0; transition:stroke-dashoffset .5s linear, stroke .3s; }
.pom-center { position:absolute; display:flex; flex-direction:column; align-items:center; }
.pom-time { font-size:18px; font-weight:800; letter-spacing:-.8px; color:var(--text); line-height:1; }
.pom-rounds { font-size:8px; color:var(--muted); margin-top:2px; }
/* right side */
.pom-side { display:flex; flex-direction:column; gap:8px; flex:1; }
.pom-progress-bars { display:flex; gap:4px; }
.pom-dot { flex:1; height:4px; border-radius:99px; background:var(--border); transition:background .3s; }
.pom-dot.done { background:var(--accent); }
.pom-controls { display:flex; align-items:center; gap:6px; }
.pom-btn { width:28px; height:28px; border-radius:7px; border:1px solid var(--border); background:var(--surface2); color:var(--muted); cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all .15s; }
.pom-btn:hover { background:var(--surface); color:var(--text); }
.pom-btn-main { width:36px; height:36px; border-radius:10px; background:var(--accent); border:none; color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background .15s, transform .1s; box-shadow:0 3px 12px rgba(255,145,77,.3); }
.pom-btn-main:hover { background:var(--accent2); }
.pom-btn-main:active { transform:scale(.97); }
.pom-session-label { font-size:8px; font-weight:700; text-transform:uppercase; letter-spacing:.8px; color:var(--muted); }
.pom-next-label { font-size:8.5px; color:var(--muted); }
/* ─── Activity ─────────────────────────────────────────────── */
.act-summary { display:flex; align-items:center; gap:0; margin-bottom:0; background:var(--surface2); border-radius:8px; border:1px solid var(--border); overflow:hidden; }
.act-sum-item { flex:1; padding:10px 4px; text-align:center; }
.act-sum-val { font-size:20px; font-weight:800; letter-spacing:-.5px; line-height:1; }
.act-sum-lbl { font-size:9px; color:var(--muted); margin-top:3px; text-transform:uppercase; letter-spacing:.5px; }
.act-sum-sep { width:1px; background:var(--border); align-self:stretch; }

/* ─── Activity chart ───────────────────────────────────────── */
.chart-sub { font-size:10px; color:var(--muted); margin-bottom:8px; }
.chart-bars { display:flex; align-items:flex-end; gap:3px; height:50px; margin-top:20px; margin-bottom:4px; }
.chart-col { flex:1; display:flex; flex-direction:column; align-items:center; gap:6px; cursor:pointer; }
.chart-pair { width:100%; display:flex; gap:1px; align-items:flex-end; height:40px; }
.chart-b { flex:1; border-radius:2px 2px 0 0; height:3px; transition:height .7s cubic-bezier(.34,1.2,.64,1), opacity .15s; }
.chart-col:hover .chart-b { opacity:.65; }
.chart-lbl { font-size:9.5px; color:var(--muted); }
.chart-leg { display:flex; gap:8px; margin-top:10px; }
.cl-item { display:flex; align-items:center; gap:4px; font-size:9.5px; color:var(--muted); }
.cl-dot { width:6px; height:6px; border-radius:2px; }
.chart-tooltip { position:fixed; background:var(--surface2); border:1px solid var(--border); border-radius:8px; padding:6px 10px; font-size:11px; color:var(--text); pointer-events:none; z-index:9999; white-space:nowrap; box-shadow:0 4px 16px rgba(0,0,0,.4); opacity:0; transition:opacity .1s; }

/* ─── Streak + Notes inline ────────────────────────────────── */
.stat-mini-row { display:flex; gap:6px; margin-top:8px; }
.stat-mini { flex:1; background:var(--surface2); border-radius:8px; padding:9px 12px; border:1px solid var(--border); }
.stat-mini-val { font-size:18px; font-weight:800; letter-spacing:-.4px; line-height:1; }
.stat-mini-lbl { font-size:9px; color:var(--muted); margin-top:2px; }

/* ─── Responsive ───────────────────────────────────────────── */
@media (max-width: 900px) {
    .dash-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 600px) {
    .dash-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
<div class="dash-grid">
    {{-- ── Greeting Card ──────────────────────────────────────── --}}
    <div class="dc dc-greeting">
        <div class="greet-bg" id="greet-bg"></div>
        <div class="period-badge" id="period-badge">
            <div class="period-dot"></div>
            <span id="period-label">Dia</span>
        </div>
        <div class="greet-content">
            <div class="greet-date" id="dash-date"></div>
            <div class="greet-title" id="dash-greet">{{ __('app.dash_good_morning') }} ☀️</div>
            <div class="greet-sub">
                @if($overdue > 0)
                    {{ __('app.dash_overdue_msg', ['n' => $overdue]) }}
                @elseif($byStatus->get('in_progress', 0) > 0)
                    {{ __('app.dash_in_progress_msg', ['n' => $byStatus->get('in_progress', 0)]) }}
                @else
                    {{ __('app.dash_all_good') }}
                @endif
            </div>
        </div>
    </div>

    {{-- ── Tasks ───────────────────────────────────────────────── --}}
    <div class="dc dc-tasks">
        <div class="dc-label">
            <span>Tarefas @if($todayTasks->isNotEmpty() || $urgentTasks->isNotEmpty())<span style="background:rgba(255,145,77,.12);color:var(--accent);padding:1px 8px;border-radius:20px;font-size:9px;font-weight:700">{{ $todayTasks->count() + $urgentTasks->count() }}</span>@endif</span>
            <a href="/tasks">ver todas →</a>
        </div>
        @if($todayTasks->isNotEmpty())
            <div class="dash-sect">Hoje</div>
            @foreach($todayTasks as $t)
                @php $pc=['urgent'=>'var(--danger)','high'=>'#fb923c','medium'=>'var(--accent)','low'=>'var(--muted)'][$t->priority->value]??'var(--muted)'; $hasProg=$t->estimated_minutes&&$t->tracked_seconds>0; $prog=$hasProg?min(100,round($t->tracked_seconds/($t->estimated_minutes*60)*100)):0; @endphp
                <a href="/tasks/{{ $t->id }}" class="tr" style="--tr-color:{{ $pc }};{{ $hasProg?'padding-bottom:14px':'' }}">
                    <div class="tr-pri"></div>
                    <div class="tr-body"><div class="tr-name">{{ $t->title }}</div><div class="tr-sub"><span class="badge status-{{ $t->status->value }}" style="font-size:9px;padding:1px 5px">{{ $t->status->label() }}</span>@if($t->category)<span>{{ $t->category->icon }} {{ $t->category->name }}</span>@endif</div></div>
                    <div class="tr-meta"><span class="tr-tag today">Hoje</span></div>
                    <svg class="tr-arr" width="10" height="10" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                    @if($hasProg)<div class="tr-time-bar"><div class="tr-time-fill" data-w="{{ $prog }}" style="background:{{ $prog>=100?'var(--danger)':'var(--info)' }}"></div></div>@endif
                </a>
            @endforeach
        @endif
        @if($urgentTasks->isNotEmpty())
            <div class="dash-sect">Urgentes & vencidas</div>
            @foreach($urgentTasks as $t)
                @php $pc=['urgent'=>'var(--danger)','high'=>'#fb923c','medium'=>'var(--accent)','low'=>'var(--muted)'][$t->priority->value]??'var(--muted)'; @endphp
                <a href="/tasks/{{ $t->id }}" class="tr" style="--tr-color:{{ $pc }}">
                    <div class="tr-pri"></div>
                    <div class="tr-body"><div class="tr-name">{{ $t->title }}</div><div class="tr-sub">@if($t->category)<span>{{ $t->category->icon }} {{ $t->category->name }}</span>@endif</div></div>
                    <div class="tr-meta">@if($t->isOverdue())<span class="tr-tag overdue">Vencida</span>@elseif($t->due_date)<span class="tr-tag">{{ \Carbon\Carbon::parse($t->due_date)->format('d/m') }}</span>@endif</div>
                    <svg class="tr-arr" width="10" height="10" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                </a>
            @endforeach
        @endif
        @if($inProgressTasks->isNotEmpty())
            <div class="dash-sect">Em andamento</div>
            @foreach($inProgressTasks as $t)
                @php $hasProg=$t->estimated_minutes&&$t->tracked_seconds>0; $prog=$hasProg?min(100,round($t->tracked_seconds/($t->estimated_minutes*60)*100)):0; @endphp
                <a href="/tasks/{{ $t->id }}" class="tr" style="--tr-color:var(--status-in_progress);{{ $hasProg?'padding-bottom:14px':'' }}">
                    <div class="tr-pri"></div>
                    <div class="tr-body"><div class="tr-name">{{ $t->title }}</div><div class="tr-sub">@if($t->due_date)<span>{{ \Carbon\Carbon::parse($t->due_date)->format('d/m') }}</span>@endif</div></div>
                    <svg class="tr-arr" width="10" height="10" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                    @if($hasProg)<div class="tr-time-bar"><div class="tr-time-fill" data-w="{{ $prog }}" style="background:{{ $prog>=100?'var(--danger)':'var(--info)' }}"></div></div>@endif
                </a>
            @endforeach
        @endif
        @if($todayTasks->isEmpty() && $urgentTasks->isEmpty() && $inProgressTasks->isEmpty())
            <div class="dash-empty">🎉 Tudo em dia!</div>
        @endif
    </div>

    {{-- ── Mini Calendar ──────────────────────────────────────── --}}
    {{-- Pass task due dates to JS --}}
    @php
        $calTasksRaw = collect(array_merge(
            $todayTasks->toArray(),
            $urgentTasks->toArray(),
            $inProgressTasks->toArray()
        ));
        $calTasksJson = $calTasksRaw
            ->filter(function($t) { return !empty($t['due_date']); })
            ->map(function($t) {
                return [
                    'date'   => substr($t['due_date'], 0, 10),
                    'title'  => $t['title'],
                    'status' => is_string($t['status']) ? $t['status'] : $t['status']->value,
                ];
            })
            ->values();
    @endphp
    <script>
    window._calTasks = @json($calTasksJson);
    </script>
    <div class="dc dc-calendar dc-pad">
        <div class="dc-label">
            <span>Calendário</span>
            <span id="cal-holiday-name" style="font-size:8.5px;color:var(--accent);text-transform:none;letter-spacing:0;font-weight:500;max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"></span>
        </div>
        <div class="cal-header">
            <div class="cal-month-label" id="cal-month-label">—</div>
            <div class="cal-nav">
                <button class="cal-nav-btn" id="cal-prev">‹</button>
                <button class="cal-nav-btn" id="cal-next">›</button>
            </div>
        </div>
        <div class="cal-grid" id="cal-grid">
            <div class="cal-dow">D</div>
            <div class="cal-dow">S</div>
            <div class="cal-dow">T</div>
            <div class="cal-dow">Q</div>
            <div class="cal-dow">Q</div>
            <div class="cal-dow">S</div>
            <div class="cal-dow">S</div>
        </div>
        <div class="cal-legend">
            <div class="cal-leg-item"><div class="cal-leg-dot" style="background:var(--accent)"></div>Tarefa</div>
            <div class="cal-leg-item"><div class="cal-leg-dot" style="background:#f87171"></div>Feriado</div>
        </div>
    </div>

    {{-- ── Clima ───────────────────────────────────────────────── --}}
    <div class="dc dc-weather dc-pad">
        <div class="dc-label"><span>Clima</span></div>
        <div id="wx-body">
            <div class="wx-skeleton" style="width:60%"></div>
            <div class="wx-skeleton" style="width:40%;margin-top:6px"></div>
            <div class="wx-skeleton" style="width:80%;margin-top:10px"></div>
        </div>
    </div>

    {{-- ── Notes ───────────────────────────────────────────────── --}}
    <div class="dc dc-notes">
        <div class="dc-label"><span>{{ __('app.dash_recent_notes') }}</span><a href="/notes">ver todas →</a></div>
        @if($recentNotes->isNotEmpty())
            @foreach($recentNotes as $note)
                <a href="/notes/{{ $note->id }}" class="nl">
                    <div class="nl-bar" style="background:{{ $note->color }}"></div>
                    <div class="nl-body"><div class="nl-title">{{ $note->title ?: __('app.notes_untitled') }}</div><div class="nl-excerpt">{{ $note->excerpt(55) ?: __('app.notes_blank') }}</div></div>
                    @if($note->pinned)<span style="font-size:10px;opacity:.4;flex-shrink:0">📌</span>@endif
                    <div class="nl-time">{{ $note->updated_at->diffForHumans(null, true) }}</div>
                    <svg class="nl-arr" width="10" height="10" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                </a>
            @endforeach
            <button class="nc-new" onclick="createNote()">
                <svg width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 2v12M2 8h12"/></svg>
                {{ __('app.dash_new_note') }}
            </button>
        @else
            <div class="dash-empty">📝 <a href="#" style="color:var(--accent)" onclick="event.preventDefault();createNote()">Criar primeira nota</a></div>
        @endif
    </div>

    {{-- ── Pomodoro Timer ─────────────────────────────────────── --}}
    <div class="dc dc-pomodoro dc-pad">
        <div class="dc-label"><span>Pomodoro</span><span class="pom-session-label" id="pom-session-label">Sessão de foco</span></div>
        {{-- Tabs --}}
        <div class="pom-mode-tabs">
            <button class="pom-tab active" data-mode="focus" data-dur="1500">Foco</button>
            <button class="pom-tab" data-mode="short" data-dur="300">Pausa curta</button>
            <button class="pom-tab" data-mode="long" data-dur="900">Pausa longa</button>
        </div>
        {{-- Ring + controls side by side --}}
        <div class="pom-body">
            <div class="pom-ring-wrap">
                <svg class="pom-svg" width="90" height="90" viewBox="0 0 130 130">
                    <circle class="pom-track" cx="65" cy="65" r="56"/>
                    <circle class="pom-fill" id="pom-fill" cx="65" cy="65" r="56"/>
                </svg>
                <div class="pom-center">
                    <div class="pom-time" id="pom-time">25:00</div>
                    <div class="pom-rounds" id="pom-rounds">0 / 4</div>
                </div>
            </div>
            <div class="pom-side">
                <div class="pom-progress-bars" id="pom-session-dots">
                    <div class="pom-dot"></div><div class="pom-dot"></div>
                    <div class="pom-dot"></div><div class="pom-dot"></div>
                </div>
                <div class="pom-controls">
                    <button class="pom-btn" id="pom-reset" title="Resetar">
                        <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 8a5 5 0 1 0 1.5-3.5"/><path d="M1 5l2 2 2-2"/></svg>
                    </button>
                    <button class="pom-btn-main" id="pom-play">
                        <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor" id="pom-icon"><path d="M5 3.5l8 4.5-8 4.5z"/></svg>
                    </button>
                    <button class="pom-btn" id="pom-skip" title="Pular">
                        <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 3l7 5-7 5zM13 3v10"/></svg>
                    </button>
                </div>
                <div class="pom-next-label" id="pom-next-label">Próxima: pausa curta</div>
            </div>
        </div>
    </div>

    {{-- ── Quote do dia ────────────────────────────────────────── --}}
    <div class="dc dc-quote dc-pad">
        <div class="dc-label"><span>Frase do dia</span></div>
        <div class="quote-body" id="quote-body">
            <div class="quote-skeleton" style="width:90%"></div>
            <div class="quote-skeleton" style="width:70%;margin-top:4px"></div>
            <div class="quote-skeleton" style="width:30%;margin-top:8px"></div>
        </div>
    </div>

    {{-- ── Progress + KPIs ──────────────────────────────────────── --}}
    <div class="dc dc-progress dc-pad">
        <div class="dc-label"><span>Resumo</span><span style="color:var(--accent);font-size:11px;font-weight:700;text-transform:none;letter-spacing:0">{{ $completionRate }}% concluído</span></div>
        {{-- KPIs --}}
        <div class="progress-kpis">
            <div class="kpi-card">
                <div class="kpi-stripe" style="background:var(--status-in_progress)"></div>
                <div class="kpi-val count-up" data-target="{{ $byStatus->get('in_progress', 0) }}" style="color:var(--status-in_progress)">0</div>
                <div class="kpi-label">Em andamento</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-stripe" style="background:var(--danger)"></div>
                <div class="kpi-val count-up" data-target="{{ $overdue }}" {{ $overdue > 0 ? 'style=color:var(--danger)' : '' }}>0</div>
                <div class="kpi-label">Vencidas</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-stripe" style="background:var(--accent)"></div>
                <div class="kpi-val count-up" data-target="{{ $todayTasks->count() }}" style="color:var(--accent)">0</div>
                <div class="kpi-label">Vencem hoje</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-stripe" style="background:var(--success)"></div>
                <div class="kpi-val count-up" data-target="{{ $byStatus->get('completed', 0) }}" style="color:var(--success)">0</div>
                <div class="kpi-label">Concluídas</div>
            </div>
        </div>
        {{-- Ring + bars --}}
        <div style="display:flex;gap:14px;align-items:center;margin-bottom:4px">
            <div class="ring-row" style="margin-bottom:0;flex-shrink:0">
                <svg class="ring-svg" width="48" height="48" viewBox="0 0 54 54"><circle class="ring-track" cx="27" cy="27" r="20"/><circle class="ring-fill" id="ring-fill" cx="27" cy="27" r="20"/></svg>
                <div><div class="ring-big" id="ring-pct">0%</div><div class="ring-sub">{{ $total }} tarefas</div></div>
            </div>
            <div style="flex:1">
                @php $statusItems=[['label'=>__('app.dash_pending'),'key'=>'pending','color'=>'var(--status-pending)'],['label'=>__('app.dash_in_progress_label'),'key'=>'in_progress','color'=>'var(--status-in_progress)'],['label'=>__('app.dash_completed'),'key'=>'completed','color'=>'var(--status-completed)'],['label'=>__('app.dash_cancelled'),'key'=>'cancelled','color'=>'var(--status-cancelled)']]; @endphp
                @foreach($statusItems as $s)
                    @php $count=$byStatus->get($s['key'],0); $pct=$total>0?round($count/$total*100):0; @endphp
                    <div class="sbar"><div class="sbar-head"><span class="sbar-lbl">{{ $s['label'] }}</span><span class="sbar-cnt">{{ $count }}</span></div><div class="sbar-track"><div class="sbar-fill" data-w="{{ $pct }}" style="background:{{ $s['color'] }}"></div></div></div>
                @endforeach
            </div>
        </div>
        {{-- Streak + Notes --}}
        <div class="stat-mini-row">
            <div class="stat-mini"><div class="stat-mini-val" style="color:#fb923c">{{ $streak }} 🔥</div><div class="stat-mini-lbl">{{ $streak === 1 ? __('app.dash_streak_day') : __('app.dash_streak_days') }}</div></div>
            <div class="stat-mini"><div class="stat-mini-val" style="color:#c084fc">{{ $totalNotes }}</div><div class="stat-mini-lbl">{{ __('app.dash_notes') }}</div></div>
        </div>
    </div>

    {{-- ── Activity Chart ─────────────────────────────────────── --}}
    @php
        $totalCreated   = collect($days)->sum('created');
        $totalCompleted = collect($days)->sum('completed');
        $maxBar = collect($days)->flatMap(fn($d)=>[$d['created'],$d['completed']])->max() ?: 1;
    @endphp
    <div class="dc dc-activity dc-pad">
        <div class="dc-label"><span>{{ __('app.dash_activity') }}</span><span style="font-size:8.5px;color:var(--muted);text-transform:none;letter-spacing:0;font-weight:400">Últimos 7 dias</span></div>
        {{-- Summary row --}}
        <div class="act-summary">
            <div class="act-sum-item">
                <div class="act-sum-val" style="color:rgba(96,165,250,.9)">{{ $totalCreated }}</div>
                <div class="act-sum-lbl">{{ __('app.dash_created') }}</div>
            </div>
            <div class="act-sum-sep"></div>
            <div class="act-sum-item">
                <div class="act-sum-val" style="color:rgba(74,222,128,.9)">{{ $totalCompleted }}</div>
                <div class="act-sum-lbl">{{ __('app.dash_completed_legend') }}</div>
            </div>
            <div class="act-sum-sep"></div>
            <div class="act-sum-item">
                <div class="act-sum-val" style="color:var(--accent)">{{ $totalCreated > 0 ? round($totalCompleted/$totalCreated*100) : 0 }}%</div>
                <div class="act-sum-lbl">Taxa</div>
            </div>
        </div>
        {{-- Bar chart --}}
        <div class="chart-bars" id="chart-wrap">
            @foreach($days as $i => $day)
                <div class="chart-col" data-delay="{{ $i*55 }}" data-created="{{ $day['created'] }}" data-completed="{{ $day['completed'] }}" data-date="{{ $day['date'] }}">
                    <div class="chart-pair">
                        <div class="chart-b" data-h="{{ max(3,round($day['created']/$maxBar*38)) }}" style="background:rgba(96,165,250,.35)"></div>
                        <div class="chart-b" data-h="{{ max(3,round($day['completed']/$maxBar*38)) }}" style="background:rgba(74,222,128,.4)"></div>
                    </div>
                    <span class="chart-lbl">{{ $day['date'] }}</span>
                </div>
            @endforeach
        </div>
        <div class="chart-leg">
            <div class="cl-item"><div class="cl-dot" style="background:rgba(96,165,250,.5)"></div>{{ __('app.dash_created') }}</div>
            <div class="cl-item"><div class="cl-dot" style="background:rgba(74,222,128,.55)"></div>{{ __('app.dash_completed_legend') }}</div>
        </div>
    </div>
</div>

<div class="chart-tooltip" id="chart-tooltip"></div>

@endsection

@push('modals')
    @include('tasks._modal_form')
@endpush

@push('scripts')
<script>
/* ── Greeting & background ─────────────────────────────────── */
(function () {
    const h = new Date().getHours();
    const greetEl = document.getElementById('dash-greet');
    const bgEl = document.getElementById('greet-bg');
    const labelEl = document.getElementById('period-label');
    const badgeEl = document.getElementById('period-badge');

    // Period detection
    let period, emoji, greet;
    if (h >= 5 && h < 12)      { period = 'morning'; emoji = '☀️'; greet = '{{ __('app.dash_good_morning') }}'; labelEl.textContent = 'Manhã'; }
    else if (h >= 12 && h < 18){ period = 'afternoon'; emoji = '🌤️'; greet = '{{ __('app.dash_good_afternoon') }}'; labelEl.textContent = 'Tarde'; }
    else if (h >= 18 && h < 21){ period = 'evening'; emoji = '🌆'; greet = '{{ __('app.dash_good_evening') }}'; labelEl.textContent = 'Tarde'; }
    else                        { period = 'night'; emoji = '🌙'; greet = '{{ __('app.dash_good_evening') }}'; labelEl.textContent = 'Noite';
        badgeEl.style.background = 'rgba(147,100,255,.15)';
        badgeEl.style.borderColor = 'rgba(147,100,255,.2)';
        badgeEl.style.color = '#9364ff';
        badgeEl.querySelector('.period-dot').style.background = '#9364ff';
    }

    greetEl.textContent = greet + ' ' + emoji;

    // Gradient backgrounds matching time of day
    const bgs = {
        morning:   'linear-gradient(135deg, #1a2a4a 0%, #2d4a7a 30%, #e8a84a 70%, #f0c060 100%)',
        afternoon: 'linear-gradient(160deg, #1a3a6a 0%, #2a5090 20%, #60a0d0 60%, #87ceeb 100%)',
        evening:   'linear-gradient(145deg, #1a1030 0%, #4a2060 30%, #d06030 65%, #e07840 100%)',
        night:     'linear-gradient(160deg, #050510 0%, #0a0520 30%, #1a0a40 60%, #0d1a3a 100%)',
    };
    const bgsLight = {
        morning:   'linear-gradient(135deg, #fde68a 0%, #fbbf24 40%, #fed7aa 75%, #fff7ed 100%)',
        afternoon: 'linear-gradient(160deg, #bae6fd 0%, #7dd3fc 30%, #e0f2fe 70%, #f0f9ff 100%)',
        evening:   'linear-gradient(145deg, #fcd34d 0%, #f97316 35%, #fb923c 65%, #fed7aa 100%)',
        night:     'linear-gradient(160deg, #818cf8 0%, #6366f1 30%, #a78bfa 65%, #c4b5fd 100%)',
    };
    const applyGreetBg = () => {
        const light = document.documentElement.dataset.theme === 'light';
        bgEl.style.background = light ? bgsLight[period] : bgs[period];
    };
    applyGreetBg();

    // Re-apply when theme toggles
    new MutationObserver(applyGreetBg).observe(
        document.documentElement,
        { attributes: true, attributeFilter: ['data-theme'] }
    );

    // Date
    const d = new Date();
    const ds = d.toLocaleDateString('{{ str_replace('_', '-', app()->getLocale()) }}', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    document.getElementById('dash-date').textContent = ds.charAt(0).toUpperCase() + ds.slice(1);
})();

/* ── Count-up animations ───────────────────────────────────── */
document.querySelectorAll('.count-up').forEach(el => {
    const t = parseInt(el.dataset.target) || 0;
    if (!t) { el.textContent = 0; return; }
    let n = 0, s = Math.max(1, Math.ceil(t / 30));
    const i = setInterval(() => { n = Math.min(n + s, t); el.textContent = n; if (n >= t) clearInterval(i); }, 30);
});

/* ── Progress ring ─────────────────────────────────────────── */
const rate = {{ $completionRate }};
const fill = document.getElementById('ring-fill');
const pctEl = document.getElementById('ring-pct');
const circ = 2 * Math.PI * 20;
fill.style.strokeDasharray = circ;
fill.style.strokeDashoffset = circ;
setTimeout(() => {
    fill.style.strokeDashoffset = circ - (rate / 100 * circ);
    let n = 0; const i = setInterval(() => { n = Math.min(n + 1, rate); pctEl.textContent = n + '%'; if (n >= rate) clearInterval(i); }, 16);
}, 300);

setTimeout(() => {
    document.querySelectorAll('.sbar-fill[data-w]').forEach(el => el.style.width = el.dataset.w + '%');
    document.querySelectorAll('.tr-time-fill[data-w]').forEach(el => el.style.width = el.dataset.w + '%');
}, 350);

/* ── Activity chart ────────────────────────────────────────── */
document.querySelectorAll('#chart-wrap .chart-col').forEach(col => {
    setTimeout(() => { col.querySelectorAll('.chart-b[data-h]').forEach(b => b.style.height = b.dataset.h + 'px'); }, 350 + parseInt(col.dataset.delay || 0));
});
const tooltip = document.getElementById('chart-tooltip');
document.querySelectorAll('#chart-wrap .chart-col').forEach(col => {
    col.addEventListener('mouseenter', e => {
        tooltip.innerHTML = `<strong>${col.dataset.date}</strong><br>Criadas: ${col.dataset.created} &nbsp; Concluídas: ${col.dataset.completed}`;
        tooltip.style.opacity = '1';
        tooltip.style.left = (e.clientX - tooltip.offsetWidth / 2) + 'px';
        tooltip.style.top  = (e.clientY - tooltip.offsetHeight - 12) + 'px';
    });
    col.addEventListener('mousemove', e => {
        tooltip.style.left = (e.clientX - tooltip.offsetWidth / 2) + 'px';
        tooltip.style.top  = (e.clientY - tooltip.offsetHeight - 12) + 'px';
    });
    col.addEventListener('mouseleave', () => { tooltip.style.opacity = '0'; });
});

/* ── Mini Calendar ─────────────────────────────────────────── */
(function () {
    const grid      = document.getElementById('cal-grid');
    const label     = document.getElementById('cal-month-label');
    // Tooltip must live on <body> — .dc has overflow:hidden + transform (dcIn animation)
    // which creates a containing block for fixed, clipping the tooltip inside .dc.
    const calTip = (() => {
        const el = document.createElement('div');
        el.className = 'cal-tooltip';
        document.body.appendChild(el);
        return el;
    })();
    const hlName    = document.getElementById('cal-holiday-name');

    // ── Detect if user is likely in Brazil via timezone ──────
    const tz = Intl.DateTimeFormat().resolvedOptions().timeZone || '';
    const isBrazil = tz.startsWith('America/Sao_Paulo') ||
                     tz.startsWith('America/Manaus')    ||
                     tz.startsWith('America/Belem')     ||
                     tz.startsWith('America/Fortaleza')  ||
                     tz.startsWith('America/Recife')     ||
                     tz.startsWith('America/Maceio')     ||
                     tz.startsWith('America/Bahia')      ||
                     tz.startsWith('America/Cuiaba')     ||
                     tz.startsWith('America/Porto_Velho')||
                     tz.startsWith('America/Boa_Vista')  ||
                     tz.startsWith('America/Rio_Branco') ||
                     tz.startsWith('America/Noronha');

    // ── Easter (Gauss algorithm) ─────────────────────────────
    function easter(y) {
        const a = y % 19, b = Math.floor(y / 100), c = y % 100;
        const d = Math.floor(b / 4), e = b % 4;
        const f = Math.floor((b + 8) / 25);
        const g = Math.floor((b - f + 1) / 3);
        const h = (19 * a + b - d - g + 15) % 30;
        const i = Math.floor(c / 4), k = c % 4;
        const l = (32 + 2 * e + 2 * i - h - k) % 7;
        const m = Math.floor((a + 11 * h + 22 * l) / 451);
        const month = Math.floor((h + l - 7 * m + 114) / 31) - 1;
        const day   = ((h + l - 7 * m + 114) % 31) + 1;
        return new Date(y, month, day);
    }

    function addDays(date, n) {
        const d = new Date(date); d.setDate(d.getDate() + n); return d;
    }

    function fmt(d) {
        return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
    }

    // ── Brazilian holidays for a given year ──────────────────
    function getHolidays(y) {
        if (!isBrazil) return {};
        const e  = easter(y);
        const h  = {};
        const add = (date, name) => { h[fmt(date)] = name; };

        // Fixed national
        add(new Date(y,  0,  1), 'Confraternização Universal');
        add(new Date(y,  3, 21), 'Tiradentes');
        add(new Date(y,  4,  1), 'Dia do Trabalho');
        add(new Date(y,  8,  7), 'Independência do Brasil');
        add(new Date(y,  9, 12), 'Nossa Sra. Aparecida');
        add(new Date(y,  9,  2), 'Finados');
        add(new Date(y, 10, 15), 'Proclamação da República');
        add(new Date(y, 11, 25), 'Natal');

        // Mobile (Easter-based)
        add(addDays(e, -48), 'Carnaval');          // Seg
        add(addDays(e, -47), 'Carnaval');          // Ter
        add(addDays(e,  -2), 'Sexta-feira Santa');
        add(e,               'Páscoa');
        add(addDays(e,  60), 'Corpus Christi');

        return h;
    }

    // ── Build task map: "YYYY-MM-DD" → [tasks] ───────────────
    const taskMap = {};
    (window._calTasks || []).forEach(t => {
        if (!taskMap[t.date]) taskMap[t.date] = [];
        taskMap[t.date].push(t);
    });

    const statusColors = {
        pending:     'var(--status-pending)',
        in_progress: 'var(--status-in_progress)',
        completed:   'var(--status-completed)',
        cancelled:   'var(--status-cancelled)',
    };

    let cur = new Date(); cur.setDate(1);
    let cachedHolidays = {};

    // ── Tooltip: fixed positioning from cell rect ────────────
    function buildTipContent(holiday, tasks) {
        const lines = [];
        if (holiday) {
            lines.push(`<div class="cal-tooltip-title">🎉 Feriado</div>`);
            lines.push(`<div class="cal-tooltip-item"><span>${holiday}</span></div>`);
        }
        if (tasks && tasks.length) {
            lines.push(`<div class="cal-tooltip-title" style="margin-top:${holiday?6:0}px">📌 Tarefas</div>`);
            tasks.forEach(t => {
                const color = statusColors[t.status] || 'var(--muted)';
                lines.push(`<div class="cal-tooltip-item"><div class="cal-tooltip-dot" style="background:${color}"></div><span>${t.title}</span></div>`);
            });
        }
        return lines.join('');
    }
    function showTip(el, holiday, tasks) {
        calTip.innerHTML = buildTipContent(holiday, tasks);

        // Force off-screen + visible so browser computes real layout dimensions
        calTip.style.transition  = 'none';
        calTip.style.visibility  = 'hidden';
        calTip.style.opacity     = '0';
        calTip.style.left        = '0px';
        calTip.style.top         = '0px';

        // Use rAF to ensure reflow has happened before reading offsetWidth
        requestAnimationFrame(() => {
            const tw = calTip.offsetWidth  || 160;
            const th = calTip.offsetHeight || 60;

            const r  = el.getBoundingClientRect();
            const vw = window.innerWidth;
            const vh = window.innerHeight;

            // Prefer right of cell; flip left if it would overflow
            let left = r.right + 10;
            let top  = r.top + (r.height / 2) - (th / 2);
            if (left + tw > vw - 8) left = r.left - tw - 10;
            if (top  + th > vh - 8) top  = vh - th - 8;
            if (left < 8)           left = 8;
            if (top  < 8)           top  = 8;

            calTip.style.left       = left + 'px';
            calTip.style.top        = top  + 'px';
            calTip.style.transition = '';
            calTip.style.visibility = 'visible';
            calTip.style.opacity    = '1';
        });
    }
    function hideTip() { calTip.style.opacity = '0'; calTip.style.visibility = 'hidden'; }

    // ── Render ────────────────────────────────────────────────
    function render() {
        while (grid.children.length > 7) grid.removeChild(grid.lastChild);

        const now = new Date();
        const y = cur.getFullYear(), m = cur.getMonth();
        const lbl = new Date(y, m, 1).toLocaleDateString('pt-BR', { month: 'long', year: 'numeric' });
        label.textContent = lbl.charAt(0).toUpperCase() + lbl.slice(1);

        if (!cachedHolidays[y]) cachedHolidays[y] = getHolidays(y);
        const holidays = cachedHolidays[y];

        const first       = new Date(y, m,     1).getDay();
        const daysInMonth = new Date(y, m + 1, 0).getDate();
        const daysInPrev  = new Date(y, m,     0).getDate();

        const makeDay = (num, cls, dateStr) => {
            const d = document.createElement('div');
            d.className = 'cal-day ' + cls;
            d.textContent = num;

            const holiday = dateStr ? holidays[dateStr] : null;
            const tasks   = dateStr ? (taskMap[dateStr] || []) : [];
            const dow     = dateStr ? new Date(dateStr + 'T12:00:00').getDay() : -1;

            if (holiday) d.classList.add('holiday');
            if (tasks.length) d.classList.add('has-task');
            if (dow === 0 || dow === 6) d.classList.add('weekend');

            if (dateStr && (holiday || tasks.length)) {
                d.addEventListener('mouseenter', () => {
                    showTip(d, holiday, tasks);
                    if (holiday) hlName.textContent = holiday;
                });
                d.addEventListener('mouseleave', () => {
                    hideTip();
                    if (holiday) hlName.textContent = '';
                });
            }

            return d;
        };

        // Prev month padding
        for (let i = 0; i < first; i++) {
            grid.appendChild(makeDay(daysInPrev - first + 1 + i, 'other-month', null));
        }

        // Current month
        for (let i = 1; i <= daysInMonth; i++) {
            const dateStr = `${y}-${String(m+1).padStart(2,'0')}-${String(i).padStart(2,'0')}`;
            const cls = (y === now.getFullYear() && m === now.getMonth() && i === now.getDate()) ? 'today' : '';
            grid.appendChild(makeDay(i, cls, dateStr));
        }

        // Next month padding
        const total = first + daysInMonth;
        const remaining = total % 7 === 0 ? 0 : 7 - (total % 7);
        for (let i = 1; i <= remaining; i++) {
            grid.appendChild(makeDay(i, 'other-month', null));
        }
    }

    render();
    document.getElementById('cal-prev').onclick = () => { cur.setMonth(cur.getMonth() - 1); render(); };
    document.getElementById('cal-next').onclick = () => { cur.setMonth(cur.getMonth() + 1); render(); };
})();

/* ── Pomodoro Timer ────────────────────────────────────────── */
(function () {
    const CIRCUMFERENCE = 2 * Math.PI * 56; // 351.858...
    const pomFill = document.getElementById('pom-fill');
    const pomTime = document.getElementById('pom-time');
    const pomRounds = document.getElementById('pom-rounds');
    const pomSessionLabel = document.getElementById('pom-session-label');
    const pomIcon = document.getElementById('pom-icon');
    pomFill.style.strokeDasharray = CIRCUMFERENCE;
    pomFill.style.strokeDashoffset = 0;

    let duration = 1500;
    let remaining = 1500;
    let running = false;
    let interval = null;
    let rounds = 0;
    let mode = 'focus';
    const modeLabels  = { focus: 'Sessão de foco', short: 'Pausa curta',  long: 'Pausa longa' };
    const nextLabels  = { focus: 'Próxima: pausa curta', short: 'Próxima: foco', long: 'Próxima: foco' };
    const dots = document.querySelectorAll('.pom-dot');
    const nextLbl = document.getElementById('pom-next-label');

    function updateDots() {
        dots.forEach((d, i) => d.classList.toggle('done', i < (rounds % 4) || (rounds > 0 && rounds % 4 === 0 && i < 4)));
        // reset dots display after full cycle
        if (rounds % 4 === 0 && rounds > 0) dots.forEach(d => d.classList.add('done'));
        else dots.forEach((d, i) => d.classList.toggle('done', i < rounds % 4));
    }
    function updateNextLabel() {
        if (mode === 'focus') {
            const nextBreak = rounds % 4 === 3 ? 'Próxima: pausa longa' : 'Próxima: pausa curta';
            if (nextLbl) nextLbl.textContent = nextBreak;
        } else {
            if (nextLbl) nextLbl.textContent = 'Próxima: foco';
        }
    }

    function fmt(s) {
        const m = Math.floor(s / 60), sec = s % 60;
        return `${String(m).padStart(2,'0')}:${String(sec).padStart(2,'0')}`;
    }
    function updateRing() {
        const pct = remaining / duration;
        pomFill.style.strokeDashoffset = CIRCUMFERENCE * (1 - pct);
    }
    function updateDisplay() {
        pomTime.textContent = fmt(remaining);
        pomRounds.textContent = `${rounds % 4 === 0 && rounds > 0 ? 4 : rounds % 4} / 4`;
        updateRing();
        updateDots();
        updateNextLabel();
    }
    function setMode(m, dur) {
        if (running) stop();
        mode = m;
        duration = dur;
        remaining = dur;
        pomSessionLabel.textContent = modeLabels[m] || m;
        updateDisplay();
    }
    function stop() {
        clearInterval(interval);
        interval = null;
        running = false;
        pomIcon.innerHTML = '<path d="M5 3.5l8 4.5-8 4.5z"/>';
    }
    function onComplete() {
        const wasFocus = mode === 'focus';
        if (wasFocus) rounds++;
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification('Taskletto ⏱', { body: wasFocus ? 'Sessão concluída! Hora de pausar.' : 'Pausa finalizada. Bora focar!' });
        }
        // Auto-advance to next mode
        let nextMode, nextDur;
        if (wasFocus) {
            nextMode = rounds % 4 === 0 ? 'long' : 'short';
            nextDur  = rounds % 4 === 0 ? 900 : 300;
        } else {
            nextMode = 'focus'; nextDur = 1500;
        }
        document.querySelectorAll('.pom-tab').forEach(t => t.classList.toggle('active', t.dataset.mode === nextMode));
        setMode(nextMode, nextDur);
    }
    function start() {
        running = true;
        pomIcon.innerHTML = '<rect x="3" y="3" width="4" height="10" rx="1"/><rect x="9" y="3" width="4" height="10" rx="1"/>';
        interval = setInterval(() => {
            if (remaining <= 0) { stop(); onComplete(); return; }
            remaining--;
            updateDisplay();
        }, 1000);
    }

    document.getElementById('pom-play').onclick = async () => {
        if ('Notification' in window && Notification.permission === 'default') {
            await Notification.requestPermission();
        }
        if (running) stop(); else start();
    };
    document.getElementById('pom-reset').onclick = () => {
        stop();
        remaining = duration;
        updateDisplay();
    };
    document.getElementById('pom-skip').onclick = () => {
        stop();
        remaining = duration;
        updateDisplay();
    };

    document.querySelectorAll('.pom-tab').forEach(tab => {
        tab.onclick = () => {
            document.querySelectorAll('.pom-tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            setMode(tab.dataset.mode, parseInt(tab.dataset.dur));
        };
    });

    updateDisplay();
})();

/* ── Create note ───────────────────────────────────────────── */
/* ── Quote do dia (ZenQuotes API) ──────────────────────────── */
(function () {
    const el = document.getElementById('quote-body');
    const cacheKey = 'taskletto_quote_' + new Date().toISOString().slice(0, 10);
    const cached = localStorage.getItem(cacheKey);

    function render(q, a) {
        el.innerHTML = `
            <div class="quote-mark">“</div>
            <div class="quote-text">${q}</div>
            <div class="quote-author">— ${a}</div>`;
    }

    if (cached) {
        try { const d = JSON.parse(cached); render(d.q, d.a); return; } catch(e) {}
    }

    // ZenQuotes via allorigins proxy (evita CORS)
    fetch('https://api.allorigins.win/get?url=' + encodeURIComponent('https://zenquotes.io/api/today'))
        .then(r => r.json())
        .then(data => {
            const arr = JSON.parse(data.contents);
            const { q, a } = arr[0];
            localStorage.setItem(cacheKey, JSON.stringify({ q, a }));
            render(q, a);
        })
        .catch(() => {
            // Fallback quotes em português
            const fallbacks = [
                { q: 'A disciplina é a ponte entre metas e conquistas.', a: 'Jim Rohn' },
                { q: 'Não espere. O momento nunca será perfeito.', a: 'Napoleon Hill' },
                { q: 'O sucesso é a soma de pequenos esforços repetidos dia após dia.', a: 'Robert Collier' },
                { q: 'Comece onde você está. Use o que você tem. Faça o que você pode.', a: 'Arthur Ashe' },
                { q: 'A jornada de mil milhas começa com um único passo.', a: 'Lao-Tsé' },
            ];
            const today = new Date().getDay();
            const { q, a } = fallbacks[today % fallbacks.length];
            render(q, a);
        });
})();

/* ── Clima (Open-Meteo, sem API key) ───────────────────────── */
(function () {
    const el = document.getElementById('wx-body');

    const WX_CODES = {
        0:  { icon: '☀️',  desc: 'Céu limpo' },
        1:  { icon: '🌤️', desc: 'Predominantemente limpo' },
        2:  { icon: '⛅',  desc: 'Parcialmente nublado' },
        3:  { icon: '☁️',  desc: 'Nublado' },
        45: { icon: '🌫️', desc: 'Neblina' },
        48: { icon: '🌫️', desc: 'Neblina com geada' },
        51: { icon: '🌦️', desc: 'Garoa leve' },
        53: { icon: '🌦️', desc: 'Garoa moderada' },
        55: { icon: '🌧️', desc: 'Garoa intensa' },
        61: { icon: '🌧️', desc: 'Chuva leve' },
        63: { icon: '🌧️', desc: 'Chuva moderada' },
        65: { icon: '🌧️', desc: 'Chuva forte' },
        71: { icon: '❄️',  desc: 'Neve leve' },
        73: { icon: '❄️',  desc: 'Neve moderada' },
        75: { icon: '❄️',  desc: 'Neve intensa' },
        80: { icon: '🌦️', desc: 'Pancadas leves' },
        81: { icon: '🌧️', desc: 'Pancadas moderadas' },
        82: { icon: '⛈️', desc: 'Pancadas fortes' },
        95: { icon: '⛈️', desc: 'Tempestade' },
        96: { icon: '⛈️', desc: 'Tempestade com granizo' },
        99: { icon: '⛈️', desc: 'Tempestade severa' },
    };

    function getWxInfo(code) {
        return WX_CODES[code] || { icon: '🌡️', desc: 'Condição variável' };
    }

    function renderWx(data, cityName) {
        const cur = data.current;
        const code = cur.weather_code;
        const wx = getWxInfo(code);
        const temp = Math.round(cur.temperature_2m);
        const feels = Math.round(cur.apparent_temperature);
        const humidity = cur.relative_humidity_2m;
        const wind = Math.round(cur.wind_speed_10m);
        const isDay = cur.is_day;

        // Ajusta ícone para noite
        let icon = wx.icon;
        if (!isDay && code === 0) icon = '🌙';
        if (!isDay && code === 1) icon = '🌙';

        el.innerHTML = `
            <div class="wx-location">
                <svg width="10" height="10" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 2a4 4 0 0 1 4 4c0 3-4 8-4 8S4 9 4 6a4 4 0 0 1 4-4z"/><circle cx="8" cy="6" r="1.5"/></svg>
                ${cityName}
            </div>
            <div class="wx-main">
                <div class="wx-icon">${icon}</div>
                <div>
                    <div class="wx-temp">${temp}°C</div>
                    <div class="wx-feels">Sensação ${feels}°C</div>
                </div>
            </div>
            <div class="wx-desc">${wx.desc}</div>
            <div class="wx-pills">
                <div class="wx-pill">💧 ${humidity}%</div>
                <div class="wx-pill">💨 ${wind} km/h</div>
            </div>`;
    }

    const WX_TTL = 60 * 60 * 1000; // 1 hora

    function fetchWx(lat, lon, cityName) {
        const cacheKey = `wx_cache_${lat.toFixed(2)}_${lon.toFixed(2)}`;
        try {
            const cached = JSON.parse(localStorage.getItem(cacheKey));
            if (cached && (Date.now() - cached.ts) < WX_TTL) {
                renderWx(cached.data, cached.city);
                return;
            }
        } catch (_) {}

        const url = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current=temperature_2m,apparent_temperature,relative_humidity_2m,weather_code,wind_speed_10m,is_day&wind_speed_unit=kmh&timezone=auto`;
        fetch(url)
            .then(r => r.json())
            .then(data => {
                try { localStorage.setItem(cacheKey, JSON.stringify({ ts: Date.now(), data, city: cityName })); } catch (_) {}
                renderWx(data, cityName);
            })
            .catch(() => {
                el.innerHTML = '<div class="wx-error">🌡️<span>Não foi possível<br>carregar o clima</span></div>';
            });
    }

    if ('geolocation' in navigator) {
        navigator.geolocation.getCurrentPosition(
            pos => {
                const { latitude: lat, longitude: lon } = pos.coords;
                // Reverse geocode via open-meteo nominatim
                fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json`)
                    .then(r => r.json())
                    .then(geo => {
                        const city = geo.address.city || geo.address.town || geo.address.village || 'Sua cidade';
                        fetchWx(lat, lon, city);
                    })
                    .catch(() => fetchWx(lat, lon, 'Localização atual'));
            },
            () => {
                // Sem permissão — usa São Paulo como fallback
                fetchWx(-23.5505, -46.6333, 'São Paulo');
            },
            { timeout: 8000 }
        );
    } else {
        fetchWx(-23.5505, -46.6333, 'São Paulo');
    }
})();

/* ── Masonry: ajusta grid-row-span de cada card ────────────── */
function masonryLayout() {
    const grid = document.querySelector('.dash-grid');
    if (!grid) return;
    const gap = parseInt(getComputedStyle(grid).gap) || 10;
    const rowH = 1; // grid-auto-rows: 1px

    grid.querySelectorAll(':scope > *').forEach(card => {
        // Reset first so we can measure natural height
        card.style.gridRowEnd = 'auto';
    });

    grid.querySelectorAll(':scope > *').forEach(card => {
        const h = card.getBoundingClientRect().height;
        const span = Math.ceil((h + gap) / (rowH + gap));
        card.style.gridRowEnd = `span ${span}`;
    });
}

// Run on load and after images/fonts settle
window.addEventListener('DOMContentLoaded', () => {
    masonryLayout();
    // Re-run after a tick to catch late-rendering content
    setTimeout(masonryLayout, 100);
    setTimeout(masonryLayout, 500);
});
window.addEventListener('resize', masonryLayout);

// Observe dynamic content changes (weather, quote load in async)
const _masonryObs = new MutationObserver(() => masonryLayout());
document.addEventListener('DOMContentLoaded', () => {
    const grid = document.querySelector('.dash-grid');
    if (grid) _masonryObs.observe(grid, { childList: true, subtree: true, characterData: true, attributes: false });
});

function createNote() {
    fetch('/notes', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
        body: JSON.stringify({})
    }).then(r => r.json()).then(d => location.href = '/notes/' + d.id);
}
</script>
@endpush