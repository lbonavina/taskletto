@extends('layouts.app')
@section('page-title', 'Início')

@section('topbar-actions')
    <button class="btn btn-ghost btn-sm" onclick="fetch('/notes',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},body:JSON.stringify({})}).then(r=>r.json()).then(d=>location.href='/notes/'+d.id)">
        <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 2v12M2 8h12"/></svg>
        Nova nota
    </button>
    <button class="btn btn-primary btn-sm" onclick="document.getElementById('modal-new-task').classList.add('open')">
        <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 2v12M2 8h12"/></svg>
        Nova tarefa
    </button>
@endsection

@push('styles')
<style>
.dash { display:flex; flex-direction:column; }

/* ── Hero ─────────────────────────────────────────────────────────── */
.dash-hero {
    padding:36px 0 30px;
    border-bottom:1px solid var(--border);
    margin-bottom:32px;
}
.dash-hero-date {
    font-size:11px; font-weight:600; letter-spacing:1.4px;
    text-transform:uppercase; color:var(--muted);
    font-family:'DM Mono',monospace; margin-bottom:10px;
}
.dash-hero-title {
    font-family:'Codec Pro',sans-serif;
    font-size:30px; font-weight:700; letter-spacing:-.6px;
    color:var(--text); margin-bottom:9px; line-height:1.15;
}
.dash-hero-sub {
    font-size:13.5px; color:var(--muted); line-height:1.6; max-width:500px;
}
.dash-hero-sub strong { color:var(--text); font-weight:600; }
.dash-hero-stats {
    display:flex; gap:10px; margin-top:24px; flex-wrap:wrap;
}
.dhs {
    display:flex; align-items:center; gap:9px;
    padding:8px 14px; border-radius:10px;
    border:1px solid var(--border); background:var(--surface2);
    transition:border-color .15s, background .15s; cursor:default;
}
.dhs:hover { border-color:var(--dhs-color,var(--accent)); background:var(--surface); }
.dhs-dot { width:7px; height:7px; border-radius:50%; background:var(--dhs-color,var(--accent)); flex-shrink:0; }
.dhs-val { font-family:'DM Mono',monospace; font-size:16px; font-weight:600; color:var(--text); line-height:1; }
.dhs-label { font-size:11.5px; color:var(--muted); font-weight:500; }

/* ── Body grid ────────────────────────────────────────────────────── */
.dash-body {
    display:grid;
    grid-template-columns:1fr 310px;
    gap:28px; align-items:start;
}

/* ── Section head ─────────────────────────────────────────────────── */
.sh { display:flex; align-items:baseline; justify-content:space-between; margin-bottom:14px; }
.sh-title { font-family:'Codec Pro',sans-serif; font-size:13px; font-weight:700; color:var(--text); letter-spacing:-.1px; }
.sh-link { font-size:11.5px; color:var(--accent); text-decoration:none; font-weight:500; opacity:.8; transition:opacity .15s; }
.sh-link:hover { opacity:1; }

/* ── Notes grid ───────────────────────────────────────────────────── */
.notes-sect { margin-bottom:32px; }
.notes-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:10px; }

.nc {
    display:flex; flex-direction:column;
    border-radius:12px; overflow:hidden;
    border:1px solid var(--border); background:var(--surface);
    text-decoration:none; min-height:130px; position:relative;
    transition:border-color .18s, transform .18s, box-shadow .18s;
}
.nc:hover {
    border-color:color-mix(in srgb,var(--nc-color,var(--accent)) 55%,var(--border));
    transform:translateY(-3px);
    box-shadow:0 10px 32px rgba(0,0,0,.22);
}
.nc-bar { height:3px; background:var(--nc-color,var(--accent)); flex-shrink:0; }
.nc-body { padding:14px 16px; flex:1; display:flex; flex-direction:column; gap:6px; }
.nc-title {
    font-family:'Codec Pro',sans-serif; font-size:13.5px; font-weight:700;
    color:var(--text); letter-spacing:-.2px;
    overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
}
.nc-excerpt {
    font-size:12px; color:var(--muted); line-height:1.55; flex:1;
    display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden;
}
.nc-foot { display:flex; align-items:center; justify-content:space-between; margin-top:4px; }
.nc-time { font-size:10px; font-family:'DM Mono',monospace; color:var(--muted); opacity:.65; }

/* Featured note */
.nc.feat { grid-column:span 2; flex-direction:row; min-height:108px; }
.nc.feat .nc-bar { width:3px; height:auto; }
.nc.feat .nc-body { flex-direction:row; align-items:flex-start; gap:16px; padding:18px 20px; }
.nc.feat .nc-content { flex:1; min-width:0; }
.nc.feat .nc-title { font-size:15px; white-space:normal; margin-bottom:4px; }
.nc.feat .nc-excerpt { -webkit-line-clamp:2; }
.nc.feat .nc-foot { flex-direction:column; align-items:flex-end; gap:4px; justify-content:center; flex-shrink:0; }

/* New note card */
.nc-new {
    border:1.5px dashed var(--border); background:none;
    display:flex; align-items:center; justify-content:center;
    gap:8px; border-radius:12px; min-height:80px;
    color:var(--muted); font-size:12.5px; font-weight:500;
    text-decoration:none;
    transition:border-color .15s, color .15s, background .15s;
}
.nc-new:hover { border-color:var(--accent); color:var(--accent); background:rgba(255,145,77,.04); }

/* ── Task rows ────────────────────────────────────────────────────── */
.tasks-sect { margin-bottom:28px; }
.task-list { display:flex; flex-direction:column; gap:3px; }
.tr {
    display:flex; align-items:center; gap:12px;
    padding:9px 13px; border-radius:10px;
    text-decoration:none; color:var(--text);
    border:1px solid transparent;
    transition:background .12s, border-color .12s;
}
.tr:hover { background:var(--surface2); border-color:var(--border); }
.tr-dot {
    width:7px; height:7px; border-radius:50%;
    background:var(--tr-color,var(--muted)); flex-shrink:0;
}
.tr-name { flex:1; font-size:13px; font-weight:500; min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.tr-meta { display:flex; align-items:center; gap:5px; flex-shrink:0; }
.tr-tag {
    font-size:10px; padding:2px 7px; border-radius:20px;
    font-weight:600; font-family:'DM Mono',monospace;
    background:var(--surface2); color:var(--muted);
}
.tr-tag.overdue { background:rgba(224,84,84,.12); color:var(--danger); }
.tr-tag.today   { background:rgba(255,145,77,.12); color:var(--accent); }
.tr-arr { opacity:0; transition:opacity .12s; color:var(--muted); }
.tr:hover .tr-arr { opacity:1; }

/* ── Sidebar cards ────────────────────────────────────────────────── */
.dash-sidebar { display:flex; flex-direction:column; gap:20px; }

.sc {
    background:var(--surface); border:1px solid var(--border);
    border-radius:14px; padding:20px;
}
.sc-title {
    font-family:'Codec Pro',sans-serif; font-size:13px; font-weight:700;
    color:var(--text); margin-bottom:16px;
    display:flex; align-items:center; justify-content:space-between;
}
.sc-pct { font-family:'DM Mono',monospace; font-size:12px; color:var(--accent); font-weight:600; }

/* Ring */
.ring-row { display:flex; align-items:center; gap:16px; margin-bottom:18px; }
.ring-svg  { transform:rotate(-90deg); flex-shrink:0; }
.ring-track { fill:none; stroke:var(--border); stroke-width:4; }
.ring-fill  {
    fill:none; stroke:var(--accent); stroke-width:4; stroke-linecap:round;
    stroke-dasharray:126; stroke-dashoffset:126;
    transition:stroke-dashoffset 1.4s cubic-bezier(.34,1.2,.64,1);
}
.ring-big  { font-family:'DM Mono',monospace; font-size:26px; font-weight:600; color:var(--text); line-height:1; }
.ring-sub  { font-size:11px; color:var(--muted); margin-top:4px; }

/* Status bars */
.sbar { margin-bottom:9px; }
.sbar-head { display:flex; justify-content:space-between; margin-bottom:5px; }
.sbar-lbl { font-size:11.5px; color:var(--muted); }
.sbar-cnt { font-size:11px; font-family:'DM Mono',monospace; color:var(--muted); }
.sbar-track { height:5px; background:var(--surface2); border-radius:99px; overflow:hidden; }
.sbar-fill  { height:100%; border-radius:99px; width:0; transition:width 1.1s cubic-bezier(.34,1.2,.64,1); }

/* Chart */
.chart-sub { font-size:11px; color:var(--muted); margin-top:-10px; margin-bottom:16px; }
.chart-bars { display:flex; align-items:flex-end; gap:5px; height:64px; }
.chart-col  { flex:1; display:flex; flex-direction:column; align-items:center; gap:6px; }
.chart-pair { width:100%; display:flex; gap:2px; align-items:flex-end; height:52px; }
.chart-b    { flex:1; border-radius:3px 3px 0 0; height:3px; transition:height .7s cubic-bezier(.34,1.2,.64,1); }
.chart-lbl  { font-size:9px; color:var(--muted); font-family:'DM Mono',monospace; }
.chart-leg  { display:flex; gap:12px; margin-top:10px; }
.cl-item    { display:flex; align-items:center; gap:5px; font-size:10.5px; color:var(--muted); }
.cl-dot     { width:7px; height:7px; border-radius:2px; }

/* Categories */
.cat-row { display:flex; align-items:center; gap:10px; padding:7px 0; border-bottom:1px solid var(--border); }
.cat-row:last-child { border-bottom:none; }
.cat-icon { width:26px; height:26px; border-radius:7px; display:flex; align-items:center; justify-content:center; font-size:13px; flex-shrink:0; }
.cat-name { flex:1; font-size:12.5px; color:var(--text); font-weight:500; }
.cat-bar-wrap { width:60px; height:4px; background:var(--surface2); border-radius:99px; overflow:hidden; }
.cat-bar { height:100%; border-radius:99px; width:0; transition:width 1s cubic-bezier(.34,1.2,.64,1); }
.cat-cnt { font-size:11px; font-family:'DM Mono',monospace; color:var(--muted); width:18px; text-align:right; }

/* Empty */
.dash-empty { padding:22px 0; text-align:center; color:var(--muted); font-size:12.5px; }
.dash-empty .dei { font-size:24px; opacity:.3; margin-bottom:6px; }

@media(max-width:960px){
    .dash-body { grid-template-columns:1fr; }
    .dash-sidebar { display:grid; grid-template-columns:1fr 1fr; }
}
@media(max-width:600px){
    .notes-grid { grid-template-columns:1fr; }
    .nc.feat { flex-direction:column; }
    .nc.feat .nc-bar { width:auto; height:3px; }
    .dash-sidebar { grid-template-columns:1fr; }
}
</style>
@endpush

@section('content')
<div class="dash">

{{-- Hero --}}
<div class="dash-hero">
    <div class="dash-hero-date" id="dash-date"></div>
    <div class="dash-hero-title" id="dash-greet">Olá 👋</div>
    <div class="dash-hero-sub">
        @if($overdue > 0)
            Você tem <strong>{{ $overdue }} tarefa{{ $overdue>1?'s':'' }} vencida{{ $overdue>1?'s':'' }}</strong> aguardando atenção.
        @elseif($byStatus->get('in_progress',0) > 0)
            <strong>{{ $byStatus->get('in_progress',0) }} tarefa{{ $byStatus->get('in_progress',0)>1?'s':'' }}</strong> em andamento. Continue assim.
        @else
            Tudo em dia. Que tal criar uma nova nota ou tarefa?
        @endif
    </div>
    <div class="dash-hero-stats">
        <div class="dhs" style="--dhs-color:var(--status-in_progress)">
            <div class="dhs-dot"></div>
            <div class="dhs-val count-up" data-target="{{ $byStatus->get('in_progress',0) }}">0</div>
            <div class="dhs-label">em andamento</div>
        </div>
        <div class="dhs" style="--dhs-color:var(--danger)">
            <div class="dhs-dot"></div>
            <div class="dhs-val count-up" data-target="{{ $overdue }}">0</div>
            <div class="dhs-label">vencidas</div>
        </div>
        <div class="dhs" style="--dhs-color:var(--status-completed)">
            <div class="dhs-dot"></div>
            <div class="dhs-val count-up" data-target="{{ $byStatus->get('completed',0) }}">0</div>
            <div class="dhs-label">concluídas</div>
        </div>
        <div class="dhs" style="--dhs-color:#c084fc">
            <div class="dhs-dot"></div>
            <div class="dhs-val count-up" data-target="{{ $totalNotes }}">0</div>
            <div class="dhs-label">notas</div>
        </div>
    </div>
</div>

{{-- Body --}}
<div class="dash-body">

    {{-- Left --}}
    <div>

        {{-- Notas recentes --}}
        <div class="notes-sect">
            <div class="sh">
                <div class="sh-title">Notas recentes</div>
                <a href="/notes" class="sh-link">ver todas →</a>
            </div>
            @if($recentNotes->isNotEmpty())
            <div class="notes-grid">
                @php $first = $recentNotes->first(); @endphp
                <a href="/notes/{{ $first->id }}" class="nc feat" style="--nc-color:{{ $first->color }}">
                    <div class="nc-bar"></div>
                    <div class="nc-body">
                        <div class="nc-content">
                            <div class="nc-title">{{ $first->title ?: 'Sem título' }}</div>
                            <div class="nc-excerpt">{{ $first->excerpt(120) ?: 'Nota em branco…' }}</div>
                        </div>
                        <div class="nc-foot">
                            @if($first->pinned)<span style="font-size:11px;opacity:.6">📌</span>@endif
                            <span class="nc-time">{{ $first->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </a>
                @foreach($recentNotes->skip(1) as $note)
                <a href="/notes/{{ $note->id }}" class="nc" style="--nc-color:{{ $note->color }}">
                    <div class="nc-bar"></div>
                    <div class="nc-body">
                        <div class="nc-title">{{ $note->title ?: 'Sem título' }}</div>
                        <div class="nc-excerpt">{{ $note->excerpt(80) ?: 'Nota em branco…' }}</div>
                        <div class="nc-foot">
                            @if($note->pinned)<span style="font-size:11px;opacity:.6">📌</span>@endif
                            <span class="nc-time">{{ $note->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </a>
                @endforeach
                <a href="#" class="nc-new" onclick="event.preventDefault();fetch('/notes',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},body:JSON.stringify({})}).then(r=>r.json()).then(d=>location.href='/notes/'+d.id)">
                    <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 2v12M2 8h12"/></svg>
                    Nova nota
                </a>
            </div>
            @else
            <div class="dash-empty">
                <div class="dei">📝</div>
                <p>Nenhuma nota ainda. <a href="#" style="color:var(--accent)" onclick="event.preventDefault();fetch('/notes',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},body:JSON.stringify({})}).then(r=>r.json()).then(d=>location.href='/notes/'+d.id)">Criar primeira nota →</a></p>
            </div>
            @endif
        </div>

        {{-- Urgentes & vencidas --}}
        <div class="tasks-sect">
            <div class="sh">
                <div class="sh-title">
                    Urgentes &amp; vencidas
                    @if($overdue > 0)
                        <span style="margin-left:6px;background:rgba(224,84,84,.12);color:var(--danger);border-radius:20px;padding:1px 8px;font-size:10px;font-family:'DM Mono',monospace;font-weight:700">{{ $overdue }}</span>
                    @endif
                </div>
                <a href="/tasks" class="sh-link">ver todas →</a>
            </div>
            @if($urgentTasks->isNotEmpty())
            <div class="task-list">
                @foreach($urgentTasks as $t)
                <a href="/tasks/{{ $t->id }}" class="tr" style="--tr-color:var(--priority-{{ $t->priority->value }})">
                    <div class="tr-dot"></div>
                    <div class="tr-name">{{ $t->title }}</div>
                    <div class="tr-meta">
                        @if($t->isOverdue())
                            <span class="tr-tag overdue">vencida</span>
                        @elseif($t->due_date && \Carbon\Carbon::parse($t->due_date)->isToday())
                            <span class="tr-tag today">hoje</span>
                        @elseif($t->due_date)
                            <span class="tr-tag">{{ \Carbon\Carbon::parse($t->due_date)->format('d/m') }}</span>
                        @endif
                        <span class="badge status-{{ $t->status->value }}" style="font-size:9.5px;padding:1px 6px">{{ $t->status->label() }}</span>
                    </div>
                    <svg class="tr-arr" width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                </a>
                @endforeach
            </div>
            @else
            <div class="dash-empty">
                <div class="dei">🎉</div>
                <p>Nenhuma tarefa urgente. Boa!</p>
            </div>
            @endif
        </div>

        {{-- Em andamento --}}
        @if($inProgressTasks->isNotEmpty())
        <div class="tasks-sect">
            <div class="sh">
                <div class="sh-title">Em andamento</div>
                <a href="/tasks?status=in_progress" class="sh-link">ver todas →</a>
            </div>
            <div class="task-list">
                @foreach($inProgressTasks as $t)
                <a href="/tasks/{{ $t->id }}" class="tr" style="--tr-color:var(--status-in_progress)">
                    <div class="tr-dot"></div>
                    <div class="tr-name">{{ $t->title }}</div>
                    <div class="tr-meta">
                        @if($t->due_date)
                            <span class="tr-tag">{{ \Carbon\Carbon::parse($t->due_date)->format('d/m') }}</span>
                        @endif
                    </div>
                    <svg class="tr-arr" width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                </a>
                @endforeach
            </div>
        </div>
        @endif

    </div>

    {{-- Right sidebar --}}
    <div class="dash-sidebar">

        {{-- Progresso --}}
        <div class="sc">
            <div class="sc-title">
                Conclusão
                <span class="sc-pct">{{ $completionRate }}%</span>
            </div>
            <div class="ring-row">
                <svg class="ring-svg" width="54" height="54" viewBox="0 0 54 54">
                    <circle class="ring-track" cx="27" cy="27" r="20"/>
                    <circle class="ring-fill" id="ring-fill" cx="27" cy="27" r="20"/>
                </svg>
                <div>
                    <div class="ring-big" id="ring-pct">0%</div>
                    <div class="ring-sub">{{ $total }} tarefa{{ $total!=1?'s':'' }} no total</div>
                </div>
            </div>
            @php
                $statusItems = [
                    ['label'=>'Pendentes',   'key'=>'pending',     'color'=>'var(--status-pending)'],
                    ['label'=>'Em progresso','key'=>'in_progress', 'color'=>'var(--status-in_progress)'],
                    ['label'=>'Concluídas',  'key'=>'completed',   'color'=>'var(--status-completed)'],
                    ['label'=>'Canceladas',  'key'=>'cancelled',   'color'=>'var(--status-cancelled)'],
                ];
            @endphp
            @foreach($statusItems as $s)
                @php $count=$byStatus->get($s['key'],0); $pct=$total>0?round($count/$total*100):0; @endphp
                <div class="sbar">
                    <div class="sbar-head">
                        <span class="sbar-lbl">{{ $s['label'] }}</span>
                        <span class="sbar-cnt">{{ $count }}</span>
                    </div>
                    <div class="sbar-track">
                        <div class="sbar-fill" data-w="{{ $pct }}" style="background:{{ $s['color'] }}"></div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Atividade --}}
        <div class="sc">
            <div class="sc-title" style="margin-bottom:4px">Atividade</div>
            <div class="chart-sub">Últimos 7 dias</div>
            <div class="chart-bars" id="chart-wrap">
                @php $max=collect($days)->flatMap(fn($d)=>[$d['created'],$d['completed']])->max()?:1; @endphp
                @foreach($days as $i => $day)
                <div class="chart-col" data-delay="{{ $i*55 }}">
                    <div class="chart-pair">
                        <div class="chart-b" data-h="{{ max(3,round($day['created']/$max*48)) }}" style="background:rgba(96,165,250,.5)" title="Criadas:{{ $day['created'] }}"></div>
                        <div class="chart-b" data-h="{{ max(3,round($day['completed']/$max*48)) }}" style="background:rgba(74,222,128,.55)" title="Concluídas:{{ $day['completed'] }}"></div>
                    </div>
                    <span class="chart-lbl">{{ $day['date'] }}</span>
                </div>
                @endforeach
            </div>
            <div class="chart-leg">
                <div class="cl-item"><div class="cl-dot" style="background:rgba(96,165,250,.6)"></div>Criadas</div>
                <div class="cl-item"><div class="cl-dot" style="background:rgba(74,222,128,.65)"></div>Concluídas</div>
            </div>
        </div>

        {{-- Categorias --}}
        @if($categories->isNotEmpty())
        <div class="sc">
            <div class="sh" style="margin-bottom:12px">
                <div class="sh-title">Categorias</div>
                <a href="/categories" class="sh-link">gerenciar →</a>
            </div>
            @php $maxCat=$categories->max('tasks_count')?:1; @endphp
            @foreach($categories as $cat)
            <div class="cat-row">
                <div class="cat-icon" style="background:{{ $cat->color }}22">{{ $cat->icon?:'📁' }}</div>
                <div class="cat-name">{{ $cat->name }}</div>
                <div class="cat-bar-wrap">
                    <div class="cat-bar" data-w="{{ round($cat->tasks_count/$maxCat*100) }}" style="background:{{ $cat->color }}"></div>
                </div>
                <span class="cat-cnt">{{ $cat->tasks_count }}</span>
            </div>
            @endforeach
        </div>
        @endif

    </div>
</div>
</div>

@push('modals')
    @include('tasks._modal_form')
@endpush

@push('scripts')
<script>
(function(){
    const h=new Date().getHours();
    document.getElementById('dash-greet').textContent=h<12?'Bom dia ☀️':h<18?'Boa tarde 🌤️':'Boa noite 🌙';
    const d=new Date();
    const ds=d.toLocaleDateString('pt-BR',{weekday:'long',day:'numeric',month:'long',year:'numeric'});
    document.getElementById('dash-date').textContent=ds.charAt(0).toUpperCase()+ds.slice(1);
})();

document.querySelectorAll('.count-up').forEach(el=>{
    const t=parseInt(el.dataset.target)||0;
    if(!t){el.textContent=0;return;}
    let n=0,s=Math.max(1,Math.ceil(t/30));
    const i=setInterval(()=>{n=Math.min(n+s,t);el.textContent=n;if(n>=t)clearInterval(i);},30);
});

const rate={{ $completionRate }};
const fill=document.getElementById('ring-fill');
const pctEl=document.getElementById('ring-pct');
const circ=2*Math.PI*20;
fill.style.strokeDasharray=circ;
fill.style.strokeDashoffset=circ;
setTimeout(()=>{
    fill.style.strokeDashoffset=circ-(rate/100*circ);
    let n=0;const i=setInterval(()=>{n=Math.min(n+1,rate);pctEl.textContent=n+'%';if(n>=rate)clearInterval(i);},16);
},300);

setTimeout(()=>{
    document.querySelectorAll('.sbar-fill[data-w]').forEach(el=>el.style.width=el.dataset.w+'%');
    document.querySelectorAll('.cat-bar[data-w]').forEach(el=>el.style.width=el.dataset.w+'%');
},350);

document.querySelectorAll('#chart-wrap .chart-col').forEach(col=>{
    setTimeout(()=>{
        col.querySelectorAll('.chart-b[data-h]').forEach(b=>b.style.height=b.dataset.h+'px');
    },350+parseInt(col.dataset.delay||0));
});
</script>
@endpush
@endsection