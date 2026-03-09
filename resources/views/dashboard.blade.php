@extends('layouts.app')
@section('page-title', __('app.nav_dashboard'))

@section('topbar-actions')
    <button class="btn btn-ghost btn-sm" onclick="createNote()">
        <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M8 2v12M2 8h12" />
        </svg>
        {{ __('app.dash_new_note') }}
    </button>
    <button class="btn btn-primary btn-sm" onclick="document.getElementById('modal-new-task').classList.add('open')">
        <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M8 2v12M2 8h12" />
        </svg>
        {{ __('app.new_task') }}
    </button>
@endsection

@push('styles')
    <style>
        .dash {
            display: flex;
            flex-direction: column;
        }

        /* ── Hero ─────────────────────────────────────────────────────────── */
        .dash-hero {
            padding: 32px 0 28px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 28px;
        }

        .dash-hero-eyebrow {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            color: var(--muted);
            font-family: 'DM Sans', monospace;
            margin-bottom: 8px;
        }

        .dash-hero-title {
            font-family: 'Codec Pro', sans-serif;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -.5px;
            color: var(--text);
            margin-bottom: 6px;
            line-height: 1.2;
        }

        .dash-hero-sub {
            font-size: 13px;
            color: var(--muted);
            line-height: 1.6;
            max-width: 480px;
        }

        .dash-hero-sub strong {
            color: var(--text);
            font-weight: 600;
        }

        /* ── Hero KPIs ────────────────────────────────────────────────────── */
        .hero-kpis {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 0;
            margin-top: 24px;
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
            background: var(--surface);
        }

        .hk {
            padding: 16px 18px;
            border-right: 1px solid var(--border);
            transition: background .15s;
            cursor: default;
            position: relative;
        }

        .hk:last-child {
            border-right: none;
        }

        .hk:hover {
            background: var(--surface2);
        }

        .hk-val {
            font-family: 'Codec Pro', sans-serif;
            font-size: 26px;
            font-weight: 700;
            line-height: 1;
            color: var(--text);
            margin-bottom: 4px;
        }

        .hk-label {
            font-size: 11px;
            color: var(--muted);
            font-weight: 500;
            white-space: nowrap;
        }

        .hk-accent {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            opacity: 0;
            transition: opacity .15s;
        }

        .hk:hover .hk-accent {
            opacity: 1;
        }

        /* ── Body grid ────────────────────────────────────────────────────── */
        .dash-body {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 24px;
            align-items: start;
        }

        /* ── Section head ─────────────────────────────────────────────────── */
        .sh {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .sh-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .7px;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .sh-badge {
            font-size: 10px;
            padding: 1px 7px;
            border-radius: 20px;
            font-family: 'DM Sans', monospace;
            font-weight: 700;
        }

        .sh-link {
            font-size: 11.5px;
            color: var(--accent);
            text-decoration: none;
            font-weight: 500;
            opacity: .8;
            transition: opacity .15s;
        }

        .sh-link:hover {
            opacity: 1;
        }

        /* ── Task rows ────────────────────────────────────────────────────── */
        .task-list {
            display: flex;
            flex-direction: column;
            gap: 0;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
        }

        .tr {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            text-decoration: none;
            color: var(--text);
            border-bottom: 1px solid var(--border);
            transition: background .12s;
            position: relative;
        }

        .tr:last-child {
            border-bottom: none;
        }

        .tr:hover {
            background: var(--surface2);
        }

        .tr-pri {
            width: 3px;
            height: 28px;
            border-radius: 99px;
            background: var(--tr-color, var(--muted));
            flex-shrink: 0;
        }

        .tr-body {
            flex: 1;
            min-width: 0;
        }

        .tr-name {
            font-size: 13px;
            font-weight: 500;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            margin-bottom: 2px;
        }

        .tr-sub {
            font-size: 11px;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .tr-meta {
            display: flex;
            align-items: center;
            gap: 5px;
            flex-shrink: 0;
        }

        .tr-tag {
            font-size: 10px;
            padding: 2px 7px;
            border-radius: 20px;
            font-weight: 600;
            font-family: 'DM Sans', monospace;
            background: var(--surface2);
            color: var(--muted);
        }

        .tr-tag.overdue {
            background: rgba(224, 84, 84, .12);
            color: var(--danger);
        }

        .tr-tag.today {
            background: rgba(255, 145, 77, .12);
            color: var(--accent);
        }

        .tr-arr {
            opacity: 0;
            transition: opacity .12s;
            color: var(--muted);
            flex-shrink: 0;
        }

        .tr:hover .tr-arr {
            opacity: 1;
        }

        .tr-time-bar {
            position: absolute;
            bottom: 0;
            left: 14px;
            right: 14px;
            height: 2px;
            background: var(--border);
            border-radius: 99px;
            overflow: hidden;
        }

        .tr-time-fill {
            height: 100%;
            border-radius: 99px;
            width: 0;
            transition: width 1s cubic-bezier(.34, 1.2, .64, 1);
        }

        /* ── Notes list ───────────────────────────────────────────────────── */
        .notes-list {
            display: flex;
            flex-direction: column;
            gap: 0;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
        }

        .nl {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            text-decoration: none;
            color: var(--text);
            border-bottom: 1px solid var(--border);
            transition: background .12s;
        }

        .nl:last-child {
            border-bottom: none;
        }

        .nl:hover {
            background: var(--surface2);
        }

        .nl-bar {
            width: 3px;
            height: 32px;
            border-radius: 99px;
            flex-shrink: 0;
        }

        .nl-body {
            flex: 1;
            min-width: 0;
        }

        .nl-title {
            font-size: 13px;
            font-weight: 500;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            margin-bottom: 2px;
        }

        .nl-excerpt {
            font-size: 11px;
            color: var(--muted);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .nl-time {
            font-size: 10px;
            color: var(--muted);
            font-family: 'DM Sans', monospace;
            flex-shrink: 0;
        }

        .nl-arr {
            opacity: 0;
            transition: opacity .12s;
            color: var(--muted);
        }

        .nl:hover .nl-arr {
            opacity: 1;
        }

        .nc-new {
            background: none;
            border: none;
            border-top: 1px dashed var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 14px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 500;
            text-decoration: none;
            transition: color .15s, background .15s;
        }

        .nc-new:hover {
            color: var(--accent);
            background: rgba(255, 145, 77, .04);
        }

        /* ── Sidebar ──────────────────────────────────────────────────────── */
        .dash-sidebar {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .sc {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 18px;
        }

        .sc-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .7px;
            color: var(--muted);
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sc-pct {
            font-family: 'DM Sans', monospace;
            font-size: 12px;
            color: var(--accent);
            font-weight: 600;
            text-transform: none;
            letter-spacing: 0;
        }

        .ring-row {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
        }

        .ring-svg {
            transform: rotate(-90deg);
            flex-shrink: 0;
        }

        .ring-track {
            fill: none;
            stroke: var(--border);
            stroke-width: 4;
        }

        .ring-fill {
            fill: none;
            stroke: var(--accent);
            stroke-width: 4;
            stroke-linecap: round;
            stroke-dasharray: 126;
            stroke-dashoffset: 126;
            transition: stroke-dashoffset 1.4s cubic-bezier(.34, 1.2, .64, 1);
        }

        .ring-big {
            font-family: 'DM Sans', monospace;
            font-size: 24px;
            font-weight: 600;
            color: var(--text);
            line-height: 1;
        }

        .ring-sub {
            font-size: 11px;
            color: var(--muted);
            margin-top: 4px;
        }

        .sbar {
            margin-bottom: 8px;
        }

        .sbar-head {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }

        .sbar-lbl {
            font-size: 11.5px;
            color: var(--muted);
        }

        .sbar-cnt {
            font-size: 11px;
            font-family: 'DM Sans', monospace;
            color: var(--muted);
        }

        .sbar-track {
            height: 4px;
            background: var(--surface2);
            border-radius: 99px;
            overflow: hidden;
        }

        .sbar-fill {
            height: 100%;
            border-radius: 99px;
            width: 0;
            transition: width 1.1s cubic-bezier(.34, 1.2, .64, 1);
        }

        .mini-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .ms {
            background: var(--surface2);
            border-radius: 10px;
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .ms-val {
            font-family: 'DM Sans', monospace;
            font-size: 20px;
            font-weight: 700;
            color: var(--text);
            line-height: 1;
        }

        .ms-label {
            font-size: 10.5px;
            color: var(--muted);
        }

        .chart-sub {
            font-size: 11px;
            color: var(--muted);
            margin-top: -8px;
            margin-bottom: 14px;
        }

        .chart-bars {
            display: flex;
            align-items: flex-end;
            gap: 4px;
            height: 64px;
        }

        .chart-col {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            cursor: pointer;
        }

        .chart-pair {
            width: 100%;
            display: flex;
            gap: 2px;
            align-items: flex-end;
            height: 52px;
        }

        .chart-b {
            flex: 1;
            border-radius: 3px 3px 0 0;
            height: 3px;
            transition: height .7s cubic-bezier(.34, 1.2, .64, 1), opacity .15s;
        }

        .chart-col:hover .chart-b {
            opacity: .75;
        }

        .chart-lbl {
            font-size: 9px;
            color: var(--muted);
            font-family: 'DM Sans', monospace;
        }

        .chart-leg {
            display: flex;
            gap: 12px;
            margin-top: 10px;
        }

        .cl-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 10.5px;
            color: var(--muted);
        }

        .cl-dot {
            width: 7px;
            height: 7px;
            border-radius: 2px;
        }

        .chart-tooltip {
            position: fixed;
            background: #1a1a22;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 7px 10px;
            font-size: 11px;
            color: var(--text);
            pointer-events: none;
            z-index: 9999;
            white-space: nowrap;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .4);
            opacity: 0;
            transition: opacity .1s;
        }

        .cat-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 7px 0;
            border-bottom: 1px solid var(--border);
        }

        .cat-row:last-child {
            border-bottom: none;
        }

        .cat-icon {
            width: 26px;
            height: 26px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
        }

        .cat-name {
            flex: 1;
            font-size: 12.5px;
            color: var(--text);
            font-weight: 500;
        }

        .cat-bar-wrap {
            width: 56px;
            height: 4px;
            background: var(--surface2);
            border-radius: 99px;
            overflow: hidden;
        }

        .cat-bar {
            height: 100%;
            border-radius: 99px;
            width: 0;
            transition: width 1s cubic-bezier(.34, 1.2, .64, 1);
        }

        .cat-cnt {
            font-size: 11px;
            font-family: 'DM Sans', monospace;
            color: var(--muted);
            width: 18px;
            text-align: right;
        }

        .dash-sect {
            margin-bottom: 24px;
        }

        .dash-empty {
            padding: 18px 0;
            text-align: center;
            color: var(--muted);
            font-size: 12.5px;
        }

        .dash-empty .dei {
            font-size: 22px;
            opacity: .3;
            margin-bottom: 6px;
        }

        @media(max-width:960px) {
            .dash-body {
                grid-template-columns: 1fr;
            }

            .dash-sidebar {
                display: grid;
                grid-template-columns: 1fr 1fr;
            }

            .hero-kpis {
                grid-template-columns: repeat(3, 1fr);
            }

            .hero-kpis .hk:nth-child(3) {
                border-right: none;
            }

            .hero-kpis .hk:nth-child(n+4) {
                border-top: 1px solid var(--border);
            }

            .hero-kpis .hk:nth-child(6) {
                border-right: none;
            }
        }

        @media(max-width:600px) {
            .hero-kpis {
                grid-template-columns: repeat(2, 1fr);
            }

            .hero-kpis .hk:nth-child(2n) {
                border-right: none;
            }

            .hero-kpis .hk:nth-child(n+3) {
                border-top: 1px solid var(--border);
            }

            .dash-sidebar {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <div class="dash">

        {{-- Hero --}}
        <div class="dash-hero">
            <div class="dash-hero-eyebrow" id="dash-date"></div>
            <div class="dash-hero-title" id="dash-greet">{{ __('app.dash_good_morning') }} 👋</div>
            <div class="dash-hero-sub">
                @if($overdue > 0)
                    {{ __('app.dash_overdue_msg', ['n' => $overdue]) }}
                @elseif($byStatus->get('in_progress', 0) > 0)
                    {{ __('app.dash_in_progress_msg', ['n' => $byStatus->get('in_progress', 0)]) }}
                @else
                    {{ __('app.dash_all_good') }}
                @endif
            </div>
            <div class="hero-kpis">
                <div class="hk">
                    <div class="hk-val count-up" data-target="{{ $byStatus->get('in_progress', 0) }}"
                        style="color:var(--status-in_progress)">0</div>
                    <div class="hk-label">{{ __('app.dash_in_progress_kpi') }}</div>
                    <div class="hk-accent" style="background:var(--status-in_progress)"></div>
                </div>
                <div class="hk">
                    <div class="hk-val count-up" data-target="{{ $overdue }}" {{ $overdue > 0 ? 'style=color:var(--danger)' : '' }}>0</div>
                    <div class="hk-label">{{ __('app.dash_overdue') }}</div>
                    <div class="hk-accent" style="background:var(--danger)"></div>
                </div>
                <div class="hk">
                    <div class="hk-val count-up" data-target="{{ $todayTasks->count() }}" style="color:var(--accent)">0
                    </div>
                    <div class="hk-label">{{ __('app.dash_due_today_kpi') }}</div>
                    <div class="hk-accent" style="background:var(--accent)"></div>
                </div>
                <div class="hk">
                    <div class="hk-val count-up" data-target="{{ $byStatus->get('completed', 0) }}"
                        style="color:var(--success)">0</div>
                    <div class="hk-label">Concluídas</div>
                    <div class="hk-accent" style="background:var(--success)"></div>
                </div>
                <div class="hk">
                    @php $tt = $trackedToday;
                    $ttStr = $tt < 60 ? $tt . 's' : ($tt < 3600 ? floor($tt / 60) . 'm' : floor($tt / 3600) . 'h ' . floor(($tt % 3600) / 60) . 'm'); @endphp
                    <div class="hk-val" style="color:var(--info);font-size:{{ strlen($ttStr) > 4 ? '18px' : '26px' }}">
                        {{ $ttStr ?: '0s' }}</div>
                    <div class="hk-label">{{ __('app.dash_tracked_today_kpi') }}</div>
                    <div class="hk-accent" style="background:var(--info)"></div>
                </div>
                <div class="hk">
                    <div class="hk-val count-up" data-target="{{ $totalNotes }}" style="color:#c084fc">0</div>
                    <div class="hk-label">{{ __('app.dash_notes') }}</div>
                    <div class="hk-accent" style="background:#c084fc"></div>
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="dash-body">
            <div>

                {{-- Hoje --}}
                @if($todayTasks->isNotEmpty())
                    <div class="dash-sect">
                        <div class="sh">
                            <div class="sh-title">
                                Hoje
                                <span class="sh-badge"
                                    style="background:rgba(255,145,77,.12);color:var(--accent)">{{ $todayTasks->count() }}</span>
                            </div>
                            <a href="/tasks" class="sh-link">{{ __('app.dash_see_all') }}</a>
                        </div>
                        <div class="task-list">
                            @foreach($todayTasks as $t)
                                @php
                                    $pc = ['urgent' => 'var(--danger)', 'high' => '#fb923c', 'medium' => 'var(--accent)', 'low' => 'var(--muted)'][$t->priority->value] ?? 'var(--muted)';
                                    $hasProg = $t->estimated_minutes && $t->tracked_seconds > 0;
                                    $prog = $hasProg ? min(100, round($t->tracked_seconds / ($t->estimated_minutes * 60) * 100)) : 0;
                                @endphp
                                <a href="/tasks/{{ $t->id }}" class="tr"
                                    style="--tr-color:{{ $pc }};{{ $hasProg ? 'padding-bottom:14px' : '' }}">
                                    <div class="tr-pri"></div>
                                    <div class="tr-body">
                                        <div class="tr-name">{{ $t->title }}</div>
                                        <div class="tr-sub">
                                            <span class="badge status-{{ $t->status->value }}"
                                                style="font-size:9.5px;padding:1px 6px">{{ $t->status->label() }}</span>
                                            @if($t->category) <span>{{ $t->category->icon }} {{ $t->category->name }}</span> @endif
                                            @if($hasProg) <span style="color:var(--info)">⏱ {{ $t->formattedTrackedTime() }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="tr-meta">
                                        <span class="tr-tag today">{{ __('app.dash_tag_today') }}</span>
                                        <span class="badge priority-{{ $t->priority->value }}"
                                            style="font-size:9.5px;padding:1px 6px">{{ $t->priority->label() }}</span>
                                    </div>
                                    <svg class="tr-arr" width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="currentColor"
                                        stroke-width="1.5">
                                        <path d="M3 8h10M9 4l4 4-4 4" />
                                    </svg>
                                    @if($hasProg)
                                        <div class="tr-time-bar">
                                            <div class="tr-time-fill" data-w="{{ $prog }}"
                                                style="background:{{ $prog >= 100 ? 'var(--danger)' : 'var(--info)' }}"></div>
                                        </div>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Urgentes & vencidas --}}
                <div class="dash-sect">
                    <div class="sh">
                        <div class="sh-title">
                            {{ __('app.dash_urgent_overdue') }}
                            @if($overdue > 0)
                                <span class="sh-badge"
                                    style="background:rgba(224,84,84,.12);color:var(--danger)">{{ $overdue }}</span>
                            @endif
                        </div>
                        <a href="/tasks" class="sh-link">{{ __('app.dash_see_all') }}</a>
                    </div>
                    @if($urgentTasks->isNotEmpty())
                        <div class="task-list">
                            @foreach($urgentTasks as $t)
                                @php
                                    $pc = ['urgent' => 'var(--danger)', 'high' => '#fb923c', 'medium' => 'var(--accent)', 'low' => 'var(--muted)'][$t->priority->value] ?? 'var(--muted)';
                                    $hasProg = $t->estimated_minutes && $t->tracked_seconds > 0;
                                    $prog = $hasProg ? min(100, round($t->tracked_seconds / ($t->estimated_minutes * 60) * 100)) : 0;
                                @endphp
                                <a href="/tasks/{{ $t->id }}" class="tr"
                                    style="--tr-color:{{ $pc }};{{ $hasProg ? 'padding-bottom:14px' : '' }}">
                                    <div class="tr-pri"></div>
                                    <div class="tr-body">
                                        <div class="tr-name">{{ $t->title }}</div>
                                        <div class="tr-sub">
                                            @if($t->category) <span>{{ $t->category->icon }} {{ $t->category->name }}</span> @endif
                                        </div>
                                    </div>
                                    <div class="tr-meta">
                                        @if($t->isOverdue())
                                            <span class="tr-tag overdue">{{ __('app.dash_tag_overdue') }}</span>
                                        @elseif($t->due_date && \Carbon\Carbon::parse($t->due_date)->isToday())
                                            <span class="tr-tag today">{{ __('app.dash_tag_today') }}</span>
                                        @elseif($t->due_date)
                                            <span class="tr-tag">{{ \Carbon\Carbon::parse($t->due_date)->format('d/m') }}</span>
                                        @endif
                                        <span class="badge status-{{ $t->status->value }}"
                                            style="font-size:9.5px;padding:1px 6px">{{ $t->status->label() }}</span>
                                    </div>
                                    <svg class="tr-arr" width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="currentColor"
                                        stroke-width="1.5">
                                        <path d="M3 8h10M9 4l4 4-4 4" />
                                    </svg>
                                    @if($hasProg)
                                        <div class="tr-time-bar">
                                            <div class="tr-time-fill" data-w="{{ $prog }}"
                                                style="background:{{ $prog >= 100 ? 'var(--danger)' : 'var(--info)' }}"></div>
                                        </div>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="dash-empty">
                            <div class="dei">🎉</div>
                            <p>{{ __('app.dash_no_urgent') }}</p>
                        </div>
                    @endif
                </div>

                {{-- Em andamento --}}
                @if($inProgressTasks->isNotEmpty())
                    <div class="dash-sect">
                        <div class="sh">
                            <div class="sh-title">{{ __('app.dash_in_progress_title') }}</div>
                            <a href="/tasks?status=in_progress" class="sh-link">{{ __('app.dash_see_all') }}</a>
                        </div>
                        <div class="task-list">
                            @foreach($inProgressTasks as $t)
                                @php
                                    $hasProg = $t->estimated_minutes && $t->tracked_seconds > 0;
                                    $prog = $hasProg ? min(100, round($t->tracked_seconds / ($t->estimated_minutes * 60) * 100)) : 0;
                                @endphp
                                <a href="/tasks/{{ $t->id }}" class="tr"
                                    style="--tr-color:var(--status-in_progress);{{ $hasProg ? 'padding-bottom:14px' : '' }}">
                                    <div class="tr-pri"></div>
                                    <div class="tr-body">
                                        <div class="tr-name">{{ $t->title }}</div>
                                        <div class="tr-sub">
                                            @if($t->due_date) <span>{{ \Carbon\Carbon::parse($t->due_date)->format('d/m') }}</span>
                                            @endif
                                            @if($hasProg) <span style="color:var(--info)">⏱ {{ $t->formattedTrackedTime() }} /
                                            {{ $t->estimated_minutes }}min</span> @endif
                                        </div>
                                    </div>
                                    <svg class="tr-arr" width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="currentColor"
                                        stroke-width="1.5">
                                        <path d="M3 8h10M9 4l4 4-4 4" />
                                    </svg>
                                    @if($hasProg)
                                        <div class="tr-time-bar">
                                            <div class="tr-time-fill" data-w="{{ $prog }}"
                                                style="background:{{ $prog >= 100 ? 'var(--danger)' : 'var(--info)' }}"></div>
                                        </div>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Notas recentes --}}
                <div class="dash-sect">
                    <div class="sh">
                        <div class="sh-title">{{ __('app.dash_recent_notes') }}</div>
                        <a href="/notes" class="sh-link">{{ __('app.dash_see_all') }}</a>
                    </div>
                    @if($recentNotes->isNotEmpty())
                        <div class="notes-list">
                            @foreach($recentNotes as $note)
                                <a href="/notes/{{ $note->id }}" class="nl">
                                    <div class="nl-bar" style="background:{{ $note->color }}"></div>
                                    <div class="nl-body">
                                        <div class="nl-title">{{ $note->title ?: __('app.notes_untitled') }}</div>
                                        <div class="nl-excerpt">{{ $note->excerpt(60) ?: __('app.notes_blank') }}</div>
                                    </div>
                                    @if($note->pinned)<span style="font-size:11px;opacity:.5;flex-shrink:0">📌</span>@endif
                                    <div class="nl-time">{{ $note->updated_at->diffForHumans(null, true) }}</div>
                                    <svg class="nl-arr" width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="currentColor"
                                        stroke-width="1.5">
                                        <path d="M3 8h10M9 4l4 4-4 4" />
                                    </svg>
                                </a>
                            @endforeach
                            <a href="#" class="nc-new" onclick="event.preventDefault();createNote()">
                                <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M8 2v12M2 8h12" />
                                </svg>
                                {{ __('app.dash_new_note') }}
                            </a>
                        </div>
                    @else
                        <div class="dash-empty">
                            <div class="dei">📝</div>
                            <p>{{ __('app.dash_no_notes') }} <a href="#" style="color:var(--accent)"
                                    onclick="event.preventDefault();createNote()">{{ __('app.dash_create_first_note') }}</a></p>
                        </div>
                    @endif
                </div>

            </div>

            {{-- Sidebar --}}
            <div class="dash-sidebar">

                {{-- Progresso --}}
                <div class="sc">
                    <div class="sc-title">
                        {{ __('app.dash_completion') }}
                        <span class="sc-pct">{{ $completionRate }}%</span>
                    </div>
                    <div class="ring-row">
                        <svg class="ring-svg" width="52" height="52" viewBox="0 0 54 54">
                            <circle class="ring-track" cx="27" cy="27" r="20" />
                            <circle class="ring-fill" id="ring-fill" cx="27" cy="27" r="20" />
                        </svg>
                        <div>
                            <div class="ring-big" id="ring-pct">0%</div>
                            <div class="ring-sub">{{ $total }} {{ __('app.dash_tasks_total') }}</div>
                        </div>
                    </div>
                    @php
                        $statusItems = [
                            ['label' => __('app.dash_pending'), 'key' => 'pending', 'color' => 'var(--status-pending)'],
                            ['label' => __('app.dash_in_progress_label'), 'key' => 'in_progress', 'color' => 'var(--status-in_progress)'],
                            ['label' => __('app.dash_completed'), 'key' => 'completed', 'color' => 'var(--status-completed)'],
                            ['label' => __('app.dash_cancelled'), 'key' => 'cancelled', 'color' => 'var(--status-cancelled)'],
                        ];
                    @endphp
                    @foreach($statusItems as $s)
                        @php $count = $byStatus->get($s['key'], 0);
                        $pct = $total > 0 ? round($count / $total * 100) : 0; @endphp
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

                {{-- Produtividade --}}
                <div class="sc">
                    <div class="sc-title">Produtividade</div>
                    <div class="mini-stats">
                        <div class="ms">
                            <div class="ms-val count-up" data-target="{{ $streak }}">0</div>
                            <div class="ms-label">🔥 {{ $streak === 1 ? __('app.dash_streak_day') : __('app.dash_streak_days') }}</div>
                        </div>
                        <div class="ms">
                            <div class="ms-val count-up" data-target="{{ $recurringCount }}">0</div>
                            <div class="ms-label">🔁 Recorrentes</div>
                        </div>
                        <div class="ms" style="grid-column:span 2">
                            @php $ttStr2 = $trackedToday < 60 ? $trackedToday . 's' : ($trackedToday < 3600 ? floor($trackedToday / 60) . 'm ' . ($trackedToday % 60) . 's' : floor($trackedToday / 3600) . 'h ' . floor(($trackedToday % 3600) / 60) . 'm'); @endphp
                            <div class="ms-val" style="color:var(--info);font-size:18px">{{ $ttStr2 ?: '0s' }}</div>
                            <div class="ms-label">⏱ {{ __('app.dash_tracked_label') }}</div>
                        </div>
                    </div>
                </div>

                {{-- Atividade --}}
                <div class="sc">
                    <div class="sc-title" style="margin-bottom:4px">{{ __('app.dash_activity') }}</div>
                    <div class="chart-sub">{{ __('app.dash_last_7_days') }}</div>
                    <div class="chart-bars" id="chart-wrap">
                        @php $max = collect($days)->flatMap(fn($d) => [$d['created'], $d['completed']])->max() ?: 1; @endphp
                        @foreach($days as $i => $day)
                            <div class="chart-col" data-delay="{{ $i * 55 }}" data-created="{{ $day['created'] }}"
                                data-completed="{{ $day['completed'] }}" data-date="{{ $day['date'] }}">
                                <div class="chart-pair">
                                    <div class="chart-b" data-h="{{ max(3, round($day['created'] / $max * 48)) }}"
                                        style="background:rgba(96,165,250,.5)"></div>
                                    <div class="chart-b" data-h="{{ max(3, round($day['completed'] / $max * 48)) }}"
                                        style="background:rgba(74,222,128,.55)"></div>
                                </div>
                                <span class="chart-lbl">{{ $day['date'] }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="chart-leg">
                        <div class="cl-item">
                            <div class="cl-dot" style="background:rgba(96,165,250,.6)"></div>{{ __('app.dash_created') }}
                        </div>
                        <div class="cl-item">
                            <div class="cl-dot" style="background:rgba(74,222,128,.65)"></div>
                            {{ __('app.dash_completed_legend') }}
                        </div>
                    </div>
                </div>

                {{-- Categorias --}}
                @if($categories->isNotEmpty())
                    <div class="sc">
                        <div class="sh" style="margin-bottom:10px">
                            <div class="sh-title">{{ __('app.dash_categories') }}</div>
                            <a href="/categories" class="sh-link">{{ __('app.dash_manage') }}</a>
                        </div>
                        @php $maxCat = $categories->max('tasks_count') ?: 1; @endphp
                        @foreach($categories as $cat)
                            <div class="cat-row">
                                <div class="cat-icon" style="background:{{ $cat->color }}22">{{ $cat->icon ?: '📁' }}</div>
                                <div class="cat-name">{{ $cat->name }}</div>
                                <div class="cat-bar-wrap">
                                    <div class="cat-bar" data-w="{{ round($cat->tasks_count / $maxCat * 100) }}"
                                        style="background:{{ $cat->color }}"></div>
                                </div>
                                <span class="cat-cnt">{{ $cat->tasks_count }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </div>
    </div>

    <div class="chart-tooltip" id="chart-tooltip"></div>

    @push('modals')
        @include('tasks._modal_form')
    @endpush

    @push('scripts')
        <script>
            function createNote() {
                fetch('/notes', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                    body: JSON.stringify({})
                }).then(r => r.json()).then(d => location.href = '/notes/' + d.id);
            }

            (function () {
                const h = new Date().getHours();
                document.getElementById('dash-greet').textContent = h < 12 ? '{{ __('app.dash_good_morning') }} ☀️' : h < 18 ? '{{ __('app.dash_good_afternoon') }} 🌤️' : '{{ __('app.dash_good_evening') }} 🌙';
                const d = new Date();
                const ds = d.toLocaleDateString('{{ str_replace('_', '-', app()->getLocale()) }}', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
                document.getElementById('dash-date').textContent = ds.charAt(0).toUpperCase() + ds.slice(1);
            })();

            document.querySelectorAll('.count-up').forEach(el => {
                const t = parseInt(el.dataset.target) || 0;
                if (!t) { el.textContent = 0; return; }
                let n = 0, s = Math.max(1, Math.ceil(t / 30));
                const i = setInterval(() => { n = Math.min(n + s, t); el.textContent = n; if (n >= t) clearInterval(i); }, 30);
            });

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
                document.querySelectorAll('.cat-bar[data-w]').forEach(el => el.style.width = el.dataset.w + '%');
                document.querySelectorAll('.tr-time-fill[data-w]').forEach(el => el.style.width = el.dataset.w + '%');
            }, 350);

            document.querySelectorAll('#chart-wrap .chart-col').forEach(col => {
                setTimeout(() => { col.querySelectorAll('.chart-b[data-h]').forEach(b => b.style.height = b.dataset.h + 'px'); }, 350 + parseInt(col.dataset.delay || 0));
            });

            const tooltip = document.getElementById('chart-tooltip');
            document.querySelectorAll('#chart-wrap .chart-col').forEach(col => {
                col.addEventListener('mouseenter', () => {
                    tooltip.innerHTML = `<strong>${col.dataset.date}</strong><br>Criadas: ${col.dataset.created} &nbsp; Concluídas: ${col.dataset.completed}`;
                    tooltip.style.opacity = '1';
                });
                col.addEventListener('mousemove', e => { tooltip.style.left = (e.clientX + 12) + 'px'; tooltip.style.top = (e.clientY - 40) + 'px'; });
                col.addEventListener('mouseleave', () => { tooltip.style.opacity = '0'; });
            });
        </script>
    @endpush
@endsection