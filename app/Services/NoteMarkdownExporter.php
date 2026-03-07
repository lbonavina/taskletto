<?php

namespace App\Services;

use App\Models\Note;

class NoteMarkdownExporter
{
    /**
     * Export a Note to a Markdown string.
     */
    public function toMarkdown(Note $note): string
    {
        $lines = [];

        // Title as H1
        $title = trim($note->title ?? '');
        if ($title && $title !== 'Sem título') {
            $lines[] = "# {$title}";
            $lines[] = '';
        }

        // Metadata block
        $lines[] = '---';
        $lines[] = 'categoria: ' . ($note->category ?? '—');
        $lines[] = 'criada: ' . $note->created_at->format('d/m/Y H:i');
        $lines[] = 'atualizada: ' . $note->updated_at->format('d/m/Y H:i');
        if ($note->pinned) {
            $lines[] = 'fixada: sim';
        }
        $lines[] = '---';
        $lines[] = '';

        // Body
        $html = $note->content ?? '';
        if ($html) {
            $lines[] = $this->htmlToMarkdown($html);
        }

        return implode("\n", $lines);
    }

    /**
     * Convert Tiptap-generated HTML to Markdown.
     * Handles: headings, paragraphs, bold, italic, underline, strikethrough,
     * highlight, code, pre/code blocks, blockquote, ul, ol, task lists,
     * links, images, horizontal rules, and tables.
     */
    private function htmlToMarkdown(string $html): string
    {
        // Normalize line endings
        $html = str_replace(["\r\n", "\r"], "\n", $html);

        // Use DOMDocument for reliable parsing
        $dom = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8"><div id="__root__">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $root = $dom->getElementById('__root__');
        if (!$root) {
            return strip_tags($html);
        }

        $md = $this->nodeToMarkdown($root);

        // Clean up excessive blank lines (max 2 consecutive)
        $md = preg_replace("/\n{3,}/", "\n\n", $md);

        return trim($md);
    }

    private function nodeToMarkdown(\DOMNode $node, int $listDepth = 0, string $listType = ''): string
    {
        if ($node->nodeType === XML_TEXT_NODE) {
            return $node->nodeValue;
        }

        if ($node->nodeType !== XML_ELEMENT_NODE) {
            return '';
        }

        /** @var \DOMElement $node */
        $tag      = strtolower($node->tagName);
        $children = $this->childrenToMarkdown($node, $listDepth, $listType);

        switch ($tag) {
            // ── Block elements ────────────────────────────────────────────────
            case 'div':
                // Root wrapper — just pass through
                return $children;

            case 'p':
                $text = trim($children);
                if ($text === '') return '';
                return $text . "\n\n";

            case 'h1': return '# ' . trim($children) . "\n\n";
            case 'h2': return '## ' . trim($children) . "\n\n";
            case 'h3': return '### ' . trim($children) . "\n\n";
            case 'h4': return '#### ' . trim($children) . "\n\n";
            case 'h5': return '##### ' . trim($children) . "\n\n";
            case 'h6': return '###### ' . trim($children) . "\n\n";

            case 'hr': return "---\n\n";

            case 'br': return "\n";

            case 'blockquote':
                $inner = trim($children);
                $quoted = implode("\n", array_map(
                    fn($line) => '> ' . $line,
                    explode("\n", $inner)
                ));
                return $quoted . "\n\n";

            case 'pre':
                // Code block — extract language from inner <code class="language-xxx">
                $codeEl = $node->getElementsByTagName('code')->item(0);
                $lang   = '';
                if ($codeEl) {
                    $class = $codeEl->getAttribute('class');
                    if (preg_match('/language-(\S+)/', $class, $m)) {
                        $lang = $m[1];
                    }
                    $code = $codeEl->nodeValue;
                } else {
                    $code = $node->nodeValue;
                }
                return "```{$lang}\n{$code}\n```\n\n";

            // ── Lists ─────────────────────────────────────────────────────────
            case 'ul':
                // Task list detection
                $isTaskList = $node->getAttribute('data-type') === 'taskList';
                return $this->renderList($node, $listDepth, $isTaskList ? 'task' : 'ul') . "\n";

            case 'ol':
                return $this->renderList($node, $listDepth, 'ol') . "\n";

            case 'li':
                // Handled inside renderList
                return $children;

            // ── Tables ────────────────────────────────────────────────────────
            case 'table':
                return $this->renderTable($node) . "\n\n";

            case 'thead':
            case 'tbody':
            case 'tr':
            case 'td':
            case 'th':
                return $children; // handled by renderTable

            // ── Inline elements ───────────────────────────────────────────────
            case 'strong':
            case 'b':
                $inner = trim($children);
                return $inner ? "**{$inner}**" : '';

            case 'em':
            case 'i':
                $inner = trim($children);
                return $inner ? "_{$inner}_" : '';

            case 'u':
                // Markdown doesn't have underline; use HTML passthrough
                $inner = trim($children);
                return $inner ? "<u>{$inner}</u>" : '';

            case 's':
            case 'del':
                $inner = trim($children);
                return $inner ? "~~{$inner}~~" : '';

            case 'mark':
                // Highlight — no standard MD, wrap with ==text== (extended MD)
                $inner = trim($children);
                return $inner ? "=={$inner}==" : '';

            case 'code':
                // Inline code (pre > code handled above)
                $parent = $node->parentNode;
                if ($parent && strtolower($parent->tagName) === 'pre') {
                    return $node->nodeValue;
                }
                return '`' . $node->nodeValue . '`';

            case 'a':
                $href  = $node->getAttribute('href');
                $label = trim($children) ?: $href;
                return "[{$label}]({$href})";

            case 'img':
                $src = $node->getAttribute('src');
                $alt = $node->getAttribute('alt') ?: 'imagem';
                return "![{$alt}]({$src})";

            // ── Wrappers / unknowns ───────────────────────────────────────────
            default:
                return $children;
        }
    }

    private function childrenToMarkdown(\DOMNode $node, int $listDepth = 0, string $listType = ''): string
    {
        $result = '';
        foreach ($node->childNodes as $child) {
            $result .= $this->nodeToMarkdown($child, $listDepth, $listType);
        }
        return $result;
    }

    private function renderList(\DOMElement $node, int $depth, string $type): string
    {
        $result  = '';
        $counter = 1;
        $indent  = str_repeat('  ', $depth);

        foreach ($node->childNodes as $child) {
            if ($child->nodeType !== XML_ELEMENT_NODE) continue;
            $tag = strtolower($child->tagName);
            if ($tag !== 'li') continue;

            /** @var \DOMElement $child */

            // Task list item?
            if ($type === 'task') {
                $checked = $child->getAttribute('data-checked') === 'true';
                $box     = $checked ? '[x]' : '[ ]';
                $text    = $this->extractListItemText($child, $depth);
                $result .= "{$indent}- {$box} {$text}\n";
            } elseif ($type === 'ol') {
                $text    = $this->extractListItemText($child, $depth);
                $result .= "{$indent}{$counter}. {$text}\n";
                $counter++;
            } else {
                $text    = $this->extractListItemText($child, $depth);
                $result .= "{$indent}- {$text}\n";
            }
        }

        return $result;
    }

    private function extractListItemText(\DOMElement $li, int $depth): string
    {
        $text     = '';
        $subLists = '';

        foreach ($li->childNodes as $child) {
            if ($child->nodeType === XML_TEXT_NODE) {
                $text .= $child->nodeValue;
                continue;
            }
            if ($child->nodeType !== XML_ELEMENT_NODE) continue;

            $ctag = strtolower($child->tagName);
            if ($ctag === 'ul') {
                $isTask   = $child->getAttribute('data-type') === 'taskList';
                $subLists .= "\n" . $this->renderList($child, $depth + 1, $isTask ? 'task' : 'ul');
            } elseif ($ctag === 'ol') {
                $subLists .= "\n" . $this->renderList($child, $depth + 1, 'ol');
            } elseif ($ctag === 'p') {
                $text .= $this->childrenToMarkdown($child);
            } elseif ($ctag === 'label') {
                // Skip task-list checkbox label wrapper
                continue;
            } elseif ($ctag === 'div') {
                $text .= $this->childrenToMarkdown($child);
            } else {
                $text .= $this->nodeToMarkdown($child, $depth);
            }
        }

        return trim($text) . $subLists;
    }

    private function renderTable(\DOMElement $table): string
    {
        $rows = [];

        foreach ($table->childNodes as $section) {
            if ($section->nodeType !== XML_ELEMENT_NODE) continue;
            $stag = strtolower($section->tagName);
            if (!in_array($stag, ['thead', 'tbody', 'tr'])) continue;

            $trNodes = $stag === 'tr' ? [$section] : iterator_to_array($section->childNodes);

            foreach ($trNodes as $tr) {
                if ($tr->nodeType !== XML_ELEMENT_NODE || strtolower($tr->tagName) !== 'tr') continue;
                $cells = [];
                foreach ($tr->childNodes as $cell) {
                    if ($cell->nodeType !== XML_ELEMENT_NODE) continue;
                    $ctag = strtolower($cell->tagName);
                    if (!in_array($ctag, ['td', 'th'])) continue;
                    $cells[] = trim($this->childrenToMarkdown($cell));
                }
                $rows[] = ['cells' => $cells, 'isHeader' => $stag === 'thead'];
            }
        }

        if (empty($rows)) return '';

        $colCount = max(array_map(fn($r) => count($r['cells']), $rows));
        $lines    = [];

        foreach ($rows as $i => $row) {
            $padded  = array_pad($row['cells'], $colCount, '');
            $lines[] = '| ' . implode(' | ', $padded) . ' |';
            // Add separator after first row (header) if no explicit thead
            if ($i === 0) {
                $lines[] = '| ' . implode(' | ', array_fill(0, $colCount, '---')) . ' |';
            }
        }

        return implode("\n", $lines);
    }
}
