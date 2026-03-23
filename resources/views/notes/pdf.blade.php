<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $note->title ?: 'Nota' }} — Taskletto</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Montserrat', 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.75;
            color: #1a1a2e;
            background: #fff;
            padding: 0;
        }

        /* ── Print header ────────────────────────────────────────────── */
        .print-header {
            border-bottom: 3px solid {{ $note->color ?? '#ff914d' }};
            padding: 28px 40px 20px;
            margin-bottom: 32px;
        }
        .print-header-app {
            font-size: 9pt;
            color: #888;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 10px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
        }
        .print-title {
            font-size: 24pt;
            font-weight: 800;
            line-height: 1.2;
            color: #0f0f1a;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
            font-family: 'Montserrat', sans-serif;
        }
        .print-meta {
            display: flex;
            gap: 18px;
            flex-wrap: wrap;
            font-size: 9pt;
            color: #666;
            font-family: 'Montserrat', sans-serif;
        }
        .print-meta-item { display: flex; align-items: center; gap: 4px; }
        .print-tag {
            display: inline-block;
            background: rgba(255,145,77,.1);
            border: 1px solid rgba(255,145,77,.25);
            border-radius: 20px;
            padding: 2px 9px;
            font-size: 8pt;
            font-family: 'Montserrat', sans-serif;
            font-weight: 500;
            color: #c2410c;
        }
        .print-tags { display: flex; gap: 5px; flex-wrap: wrap; margin-top: 8px; }

        /* ── Content area ─────────────────────────────────────────────── */
        .print-body {
            padding: 0 40px 40px;
            max-width: 780px;
            margin: 0 auto;
        }

        /* Tiptap HTML reset for print */
        .print-body h1 { font-family: 'Montserrat', sans-serif; font-size: 19pt; font-weight: 800; margin: 24px 0 10px; color: #0f0f1a; letter-spacing: -0.4px; padding-bottom: 8px; border-bottom: 2px solid #e8e8ee; }
        .print-body h2 { font-family: 'Montserrat', sans-serif; font-size: 15pt; font-weight: 700; margin: 20px 0 8px; color: #1a1a2e; letter-spacing: -0.2px; }
        .print-body h3 { font-family: 'Montserrat', sans-serif; font-size: 12pt; font-weight: 700; margin: 16px 0 6px; color: #1a1a2e; }
        .print-body p  { margin: 0 0 9px; }
        .print-body ul, .print-body ol { padding-left: 22px; margin: 0 0 10px; }
        .print-body li { margin-bottom: 4px; }
        .print-body blockquote {
            border-left: 4px solid {{ $note->color ?? '#ff914d' }};
            padding: 8px 16px;
            margin: 14px 0;
            color: #555;
            font-style: italic;
            background: rgba(255,145,77,.04);
            border-radius: 0 8px 8px 0;
        }
        .print-body code {
            background: rgba(59,130,246,.08);
            border: 1px solid rgba(59,130,246,.15);
            border-radius: 4px;
            padding: 1px 6px;
            font-family: 'Courier New', monospace;
            font-size: 10pt;
            color: #2563eb;
        }
        .print-body pre {
            background: #1e1e2e;
            border-radius: 10px;
            padding: 14px 18px;
            overflow: auto;
            margin: 12px 0;
            font-family: 'Courier New', monospace;
            font-size: 9.5pt;
            line-height: 1.6;
        }
        .print-body pre code { background: none; border: none; padding: 0; color: #e2e8f0; }
        .print-body hr { border: none; border-top: 1px solid #ddd; margin: 24px 0; }
        .print-body a { color: #ff914d; text-decoration: underline; text-underline-offset: 2px; }
        .print-body img { max-width: 100%; height: auto; border-radius: 8px; margin: 8px 0; }
        .print-body mark { background: rgba(250,204,21,.3); padding: 1px 4px; border-radius: 3px; }
        .print-body strong { font-weight: 700; }
        .print-body em { font-style: italic; }
        .print-body u  { text-decoration: underline; text-underline-offset: 3px; }
        .print-body s  { text-decoration: line-through; color: #888; }

        /* Task list (checklist) */
        .print-body ul[data-type="taskList"] { list-style: none; padding-left: 0; }
        .print-body ul[data-type="taskList"] li { display: flex; align-items: flex-start; gap: 8px; margin-bottom: 5px; }
        .print-body ul[data-type="taskList"] li input[type="checkbox"] { margin-top: 3px; flex-shrink: 0; }

        /* Tables */
        .print-body table { width: 100%; border-collapse: collapse; margin: 14px 0; font-size: 10pt; }
        .print-body th, .print-body td { border: 1px solid #ddd; padding: 7px 10px; text-align: left; }
        .print-body th { background: #f4f4f8; font-weight: 700; font-family: 'Montserrat', sans-serif; }
        .print-body tr:nth-child(even) td { background: #fafafa; }

        /* ── Footer ───────────────────────────────────────────────────── */
        .print-footer {
            border-top: 1px solid #e8e8ee;
            padding: 14px 40px;
            margin-top: 40px;
            font-size: 9pt;
            color: #aaa;
            font-family: 'Montserrat', sans-serif;
            display: flex;
            justify-content: space-between;
        }

        /* ── Print-only controls (hidden on print) ───────────────────── */
        .no-print {
            position: fixed;
            bottom: 28px;
            right: 28px;
            display: flex;
            gap: 10px;
            z-index: 999;
        }
        .no-print button {
            padding: 11px 22px;
            border-radius: 10px;
            border: none;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Montserrat', sans-serif;
            letter-spacing: .2px;
        }
        .btn-print { background: {{ $note->color ?? '#ff914d' }}; color: #fff; box-shadow: 0 4px 14px rgba(255,145,77,.35); }
        .btn-close  { background: #f0f0f6; color: #444; }

        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
            .print-header { padding: 18px 28px 14px; }
            .print-body { padding: 0 28px 28px; }
            .print-footer { padding: 10px 28px; }
            @page { margin: 12mm 10mm; }
        }
    </style>
</head>
<body>

<div class="print-header">
    <div class="print-header-app">Taskletto</div>
    <div class="print-title">{{ $note->title ?: 'Sem título' }}</div>
    <div class="print-meta">
        <span class="print-meta-item">📅 Criada em {{ $note->created_at->format('d/m/Y') }}</span>
        <span class="print-meta-item">✏️ Editada em {{ $note->updated_at->format('d/m/Y H:i') }}</span>
        @if($note->category)
            <span class="print-meta-item">📁 {{ $note->category }}</span>
        @endif
    </div>
    @if(count($note->tags_array) > 0)
        <div class="print-tags">
            @foreach($note->tags_array as $tag)
                <span class="print-tag">#{{ $tag }}</span>
            @endforeach
        </div>
    @endif
</div>

<div class="print-body">
    {!! $note->content !!}
</div>

<div class="print-footer">
    <span>Taskletto — {{ now()->format('d/m/Y H:i') }}</span>
    <span>{{ $note->title ?: 'Nota #' . $note->id }}</span>
</div>

<div class="no-print">
    <button class="btn-close" onclick="window.close()">✕ Fechar</button>
    <button class="btn-print" onclick="window.print()">🖨 Imprimir / Salvar PDF</button>
</div>

<script>
    // Auto-trigger print dialog after page loads
    window.addEventListener('load', () => {
        setTimeout(() => window.print(), 400);
    });
</script>

</body>
</html>