<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $note->title ?: 'Nota' }} — Taskletto</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-size: 13pt;
            line-height: 1.7;
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
            font-size: 10pt;
            color: #888;
            letter-spacing: .5px;
            text-transform: uppercase;
            margin-bottom: 10px;
            font-family: 'Arial', sans-serif;
        }
        .print-title {
            font-size: 26pt;
            font-weight: 700;
            line-height: 1.2;
            color: #0f0f1a;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }
        .print-meta {
            display: flex;
            gap: 18px;
            flex-wrap: wrap;
            font-size: 9pt;
            color: #666;
            font-family: 'Arial', sans-serif;
        }
        .print-meta-item { display: flex; align-items: center; gap: 4px; }
        .print-tag {
            display: inline-block;
            background: #f0f0f6;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 1px 6px;
            font-size: 8pt;
            font-family: 'Arial', sans-serif;
            color: #444;
        }
        .print-tags { display: flex; gap: 5px; flex-wrap: wrap; margin-top: 8px; }

        /* ── Content area ─────────────────────────────────────────────── */
        .print-body {
            padding: 0 40px 40px;
            max-width: 780px;
            margin: 0 auto;
        }

        /* Tiptap HTML reset for print */
        .print-body h1 { font-size: 20pt; margin: 24px 0 10px; color: #0f0f1a; }
        .print-body h2 { font-size: 16pt; margin: 20px 0 8px; color: #1a1a2e; }
        .print-body h3 { font-size: 13pt; margin: 16px 0 6px; color: #1a1a2e; }
        .print-body p  { margin: 0 0 10px; }
        .print-body ul, .print-body ol { padding-left: 22px; margin: 0 0 10px; }
        .print-body li { margin-bottom: 4px; }
        .print-body blockquote {
            border-left: 4px solid {{ $note->color ?? '#ff914d' }};
            padding: 6px 16px;
            margin: 12px 0;
            color: #555;
            font-style: italic;
        }
        .print-body code {
            background: #f4f4f8;
            border-radius: 4px;
            padding: 1px 5px;
            font-family: 'Courier New', monospace;
            font-size: 11pt;
        }
        .print-body pre {
            background: #f4f4f8;
            border-radius: 8px;
            padding: 14px 18px;
            overflow: auto;
            margin: 12px 0;
            font-family: 'Courier New', monospace;
            font-size: 10pt;
            line-height: 1.5;
        }
        .print-body pre code { background: none; padding: 0; }
        .print-body hr { border: none; border-top: 1px solid #ddd; margin: 20px 0; }
        .print-body a { color: #0066cc; }
        .print-body img { max-width: 100%; height: auto; border-radius: 6px; margin: 8px 0; }
        .print-body mark { background: #fff3b0; padding: 0 2px; border-radius: 2px; }
        .print-body strong { font-weight: 700; }
        .print-body em { font-style: italic; }
        .print-body u  { text-decoration: underline; }
        .print-body s  { text-decoration: line-through; }

        /* Task list (checklist) */
        .print-body ul[data-type="taskList"] { list-style: none; padding-left: 0; }
        .print-body ul[data-type="taskList"] li { display: flex; align-items: flex-start; gap: 8px; margin-bottom: 5px; }
        .print-body ul[data-type="taskList"] li input[type="checkbox"] { margin-top: 3px; flex-shrink: 0; }

        /* Tables */
        .print-body table { width: 100%; border-collapse: collapse; margin: 14px 0; font-size: 11pt; }
        .print-body th, .print-body td { border: 1px solid #ccc; padding: 7px 10px; text-align: left; }
        .print-body th { background: #f0f0f6; font-weight: 600; }
        .print-body tr:nth-child(even) td { background: #fafafa; }

        /* ── Footer ───────────────────────────────────────────────────── */
        .print-footer {
            border-top: 1px solid #ddd;
            padding: 14px 40px;
            margin-top: 40px;
            font-size: 9pt;
            color: #aaa;
            font-family: 'Arial', sans-serif;
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
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Arial', sans-serif;
        }
        .btn-print { background: {{ $note->color ?? '#ff914d' }}; color: #fff; }
        .btn-close  { background: #eee; color: #333; }

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