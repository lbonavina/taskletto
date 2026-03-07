<?php

namespace Tests\Feature;

use App\Models\Note;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_returns_markdown_file(): void
    {
        $note = Note::create([
            'title'   => 'Minha Nota de Teste',
            'content' => '<p>Olá <strong>mundo</strong>!</p>',
            'color'   => '#ff914d',
            'pinned'  => false,
        ]);

        $response = $this->get(route('notes.export', $note));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/markdown; charset=UTF-8');
        $this->assertStringContainsString(
            'attachment',
            $response->headers->get('Content-Disposition')
        );
        $this->assertStringContainsString('.md', $response->headers->get('Content-Disposition'));

        $body = $response->getContent();
        $this->assertStringContainsString('# Minha Nota de Teste', $body);
        $this->assertStringContainsString('**mundo**', $body);
    }

    public function test_export_filename_is_slugified(): void
    {
        $note = Note::create([
            'title'   => 'Reunião de Planejamento',
            'content' => '<p>Pauta</p>',
            'color'   => '#ff914d',
            'pinned'  => false,
        ]);

        $response = $this->get(route('notes.export', $note));
        $response->assertOk();

        $disposition = $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('Reuni', $disposition); // partial match is fine
        $this->assertStringContainsString('.md', $disposition);
    }

    public function test_export_untitled_note_uses_fallback_filename(): void
    {
        $note = Note::create([
            'title'   => 'Sem título',
            'content' => '<p>Vazio</p>',
            'color'   => '#ff914d',
            'pinned'  => false,
        ]);

        $response = $this->get(route('notes.export', $note));
        $response->assertOk();

        $disposition = $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('nota-', $disposition);
    }

    public function test_export_returns_404_for_missing_note(): void
    {
        $response = $this->get('/notes/99999/export');
        $response->assertNotFound();
    }
}
