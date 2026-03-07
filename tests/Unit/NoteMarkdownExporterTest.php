<?php

namespace Tests\Unit;

use App\Models\Note;
use App\Services\NoteMarkdownExporter;
use Tests\TestCase;

class NoteMarkdownExporterTest extends TestCase
{
    private NoteMarkdownExporter $exporter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->exporter = new NoteMarkdownExporter();
    }

    private function makeNote(string $title, string $content, array $attrs = []): Note
    {
        $note = new Note(array_merge([
            'title'    => $title,
            'content'  => $content,
            'color'    => '#ff914d',
            'pinned'   => false,
            'category' => null,
        ], $attrs));
        $note->id         = 1;
        $note->created_at = now();
        $note->updated_at = now();
        return $note;
    }

    public function test_title_becomes_h1(): void
    {
        $note = $this->makeNote('Minha nota', '<p>Conteúdo</p>');
        $md   = $this->exporter->toMarkdown($note);

        $this->assertStringContainsString('# Minha nota', $md);
    }

    public function test_metadata_block_is_included(): void
    {
        $note = $this->makeNote('Teste', '<p>Olá</p>', ['category' => 'Trabalho']);
        $md   = $this->exporter->toMarkdown($note);

        $this->assertStringContainsString('---', $md);
        $this->assertStringContainsString('categoria: Trabalho', $md);
    }

    public function test_heading_tags_convert_correctly(): void
    {
        $html = '<h1>Título 1</h1><h2>Título 2</h2><h3>Título 3</h3>';
        $note = $this->makeNote('X', $html);
        $md   = $this->exporter->toMarkdown($note);

        $this->assertStringContainsString('# Título 1', $md);
        $this->assertStringContainsString('## Título 2', $md);
        $this->assertStringContainsString('### Título 3', $md);
    }

    public function test_bold_and_italic(): void
    {
        $html = '<p><strong>negrito</strong> e <em>itálico</em></p>';
        $note = $this->makeNote('X', $html);
        $md   = $this->exporter->toMarkdown($note);

        $this->assertStringContainsString('**negrito**', $md);
        $this->assertStringContainsString('_itálico_', $md);
    }

    public function test_strikethrough(): void
    {
        $html = '<p><s>riscado</s></p>';
        $note = $this->makeNote('X', $html);
        $md   = $this->exporter->toMarkdown($note);

        $this->assertStringContainsString('~~riscado~~', $md);
    }

    public function test_inline_code(): void
    {
        $html = '<p><code>$var</code></p>';
        $note = $this->makeNote('X', $html);
        $md   = $this->exporter->toMarkdown($note);

        $this->assertStringContainsString('`$var`', $md);
    }

    public function test_code_block(): void
    {
        $html = '<pre><code class="language-php">echo "hello";</code></pre>';
        $note = $this->makeNote('X', $html);
        $md   = $this->exporter->toMarkdown($note);

        $this->assertStringContainsString('```php', $md);
        $this->assertStringContainsString('echo "hello";', $md);
        $this->assertStringContainsString('```', $md);
    }

    public function test_unordered_list(): void
    {
        $html = '<ul><li><p>Item A</p></li><li><p>Item B</p></li></ul>';
        $note = $this->makeNote('X', $html);
        $md   = $this->exporter->toMarkdown($note);

        $this->assertStringContainsString('- Item A', $md);
        $this->assertStringContainsString('- Item B', $md);
    }

    public function test_ordered_list(): void
    {
        $html = '<ol><li><p>Primeiro</p></li><li><p>Segundo</p></li></ol>';
        $note = $this->makeNote('X', $html);
        $md   = $this->exporter->toMarkdown($note);

        $this->assertStringContainsString('1. Primeiro', $md);
        $this->assertStringContainsString('2. Segundo', $md);
    }

    public function test_task_list_checked_and_unchecked(): void
    {
        $html = '<ul data-type="taskList">'
              . '<li data-checked="true"><label><input type="checkbox" checked></label><div><p>Feito</p></div></li>'
              . '<li data-checked="false"><label><input type="checkbox"></label><div><p>Pendente</p></div></li>'
              . '</ul>';
        $note = $this->makeNote('X', $html);
        $md   = $this->exporter->toMarkdown($note);

        $this->assertStringContainsString('- [x] Feito', $md);
        $this->assertStringContainsString('- [ ] Pendente', $md);
    }

    public function test_blockquote(): void
    {
        $html = '<blockquote><p>Uma citação</p></blockquote>';
        $note = $this->makeNote('X', $html);
        $md   = $this->exporter->toMarkdown($note);

        $this->assertStringContainsString('> ', $md);
        $this->assertStringContainsString('Uma citação', $md);
    }

    public function test_link(): void
    {
        $html = '<p><a href="https://example.com">Exemplo</a></p>';
        $note = $this->makeNote('X', $html);
        $md   = $this->exporter->toMarkdown($note);

        $this->assertStringContainsString('[Exemplo](https://example.com)', $md);
    }

    public function test_image(): void
    {
        $html = '<p><img src="https://example.com/img.png" alt="foto"></p>';
        $note = $this->makeNote('X', $html);
        $md   = $this->exporter->toMarkdown($note);

        $this->assertStringContainsString('![foto](https://example.com/img.png)', $md);
    }

    public function test_horizontal_rule(): void
    {
        $html = '<p>Antes</p><hr><p>Depois</p>';
        $note = $this->makeNote('X', $html);
        $md   = $this->exporter->toMarkdown($note);

        $this->assertStringContainsString('---', $md);
    }

    public function test_table(): void
    {
        $html = '<table><thead><tr><th>Col A</th><th>Col B</th></tr></thead>'
              . '<tbody><tr><td>1</td><td>2</td></tr></tbody></table>';
        $note = $this->makeNote('X', $html);
        $md   = $this->exporter->toMarkdown($note);

        $this->assertStringContainsString('| Col A | Col B |', $md);
        $this->assertStringContainsString('| --- | --- |', $md);
        $this->assertStringContainsString('| 1 | 2 |', $md);
    }

    public function test_empty_content_does_not_crash(): void
    {
        $note = $this->makeNote('Vazia', '');
        $md   = $this->exporter->toMarkdown($note);

        $this->assertIsString($md);
        $this->assertStringContainsString('# Vazia', $md);
    }

    public function test_pinned_flag_appears_in_metadata(): void
    {
        $note = $this->makeNote('Fixada', '<p>texto</p>', ['pinned' => true]);
        $md   = $this->exporter->toMarkdown($note);

        $this->assertStringContainsString('fixada: sim', $md);
    }
}
