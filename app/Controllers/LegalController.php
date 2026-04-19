<?php
namespace App\Controllers;

use App\Core\Database;
use App\Core\View;

class LegalController
{
    private function getPage(string $slug): array
    {
        return Database::selectOne(
            "SELECT * FROM paginas_legais WHERE slug = ?",
            [$slug]
        ) ?? ['slug' => $slug, 'titulo' => '', 'conteudo' => '', 'updated_at' => null];
    }

    public function privacidade(): void
    {
        $pagina = $this->getPage('privacidade');
        $title  = $pagina['titulo'] ?: 'Política de Privacidade';
        View::render('legal/page', compact('pagina', 'title'), 'public');
    }

    public function termos(): void
    {
        $pagina = $this->getPage('termos');
        $title  = $pagina['titulo'] ?: 'Termos de Uso';
        View::render('legal/page', compact('pagina', 'title'), 'public');
    }
}
