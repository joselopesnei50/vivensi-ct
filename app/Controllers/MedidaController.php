<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Flash;
use App\Core\Request;
use App\Core\View;

class MedidaController
{
    private array $medidasECA = [
        'Encaminhamento aos pais ou responsável'                              => 'Art. 101, I',
        'Orientação, apoio e acompanhamento temporários'                      => 'Art. 101, II',
        'Matrícula e frequência obrigatórias em estabelecimento de ensino'    => 'Art. 101, III',
        'Inclusão em programa de auxílio à família'                           => 'Art. 101, IV',
        'Requisição de tratamento médico, psicológico ou psiquiátrico'        => 'Art. 101, V',
        'Inclusão em programa oficial ou comunitário de proteção à família'   => 'Art. 101, VI',
        'Acolhimento institucional'                                           => 'Art. 101, VII',
        'Inclusão em programa de acolhimento familiar'                        => 'Art. 101, VIII',
        'Colocação em família substituta'                                     => 'Art. 101, IX',
    ];

    public function index(): void
    {
        Auth::requireAuth();
        $tenantId = Auth::tenantId();

        $medidas = Database::select(
            "SELECT mp.*, a.numero_protocolo, u.nome as conselheiro
             FROM medidas_protecao mp
             JOIN atendimentos a ON a.id = mp.atendimento_id
             JOIN users u ON u.id = mp.user_id
             WHERE a.tenant_id = ?
             ORDER BY mp.created_at DESC",
            [$tenantId]
        );

        View::render('medidas/index', compact('medidas'));
    }

    public function store(string $atendimentoId): void
    {
        Auth::requireAuth();
        if (!Request::verifyCsrf()) {
            View::json(['error' => 'Token inválido'], 403);
        }

        $tenantId = Auth::tenantId();
        $userId   = Auth::id();

        // Verify access
        $atendimento = Database::selectOne(
            "SELECT id FROM atendimentos WHERE id = ? AND tenant_id = ?",
            [$atendimentoId, $tenantId]
        );
        if (!$atendimento) {
            View::json(['error' => 'Atendimento não encontrado'], 404);
        }

        $tipoMedida = Request::sanitize('tipo_medida');
        $artigo     = $this->medidasECA[$tipoMedida] ?? Request::sanitize('artigo_eca');

        $id = Database::insert('medidas_protecao', [
            'atendimento_id'    => $atendimentoId,
            'user_id'           => $userId,
            'tipo_medida'       => $tipoMedida,
            'artigo_eca'        => $artigo,
            'descricao'         => Request::post('descricao', ''),
            'fundamentacao_legal'=> Request::post('fundamentacao_legal', ''),
            'prazo_cumprimento' => Request::post('prazo_cumprimento') ?: null,
            'status'            => 'aplicada',
            'observacoes'       => Request::post('observacoes', ''),
        ]);

        View::json(['success' => true, 'id' => $id, 'tipo' => $tipoMedida, 'artigo' => $artigo]);
    }

    public function updateStatus(string $id): void
    {
        Auth::requireAuth();
        if (!Request::verifyCsrf()) {
            View::json(['error' => 'Token inválido'], 403);
        }

        $tenantId = Auth::tenantId();

        // Verify the measure belongs to this tenant (IDOR prevention)
        $medida = Database::selectOne(
            "SELECT mp.id FROM medidas_protecao mp
             JOIN atendimentos a ON a.id = mp.atendimento_id
             WHERE mp.id = ? AND a.tenant_id = ?",
            [$id, $tenantId]
        );
        if (!$medida) {
            View::json(['error' => 'Medida não encontrada'], 404);
        }

        $allowed = ['aplicada', 'cumprida', 'cancelada', 'pendente'];
        $status  = Request::post('status', 'aplicada');
        if (!in_array($status, $allowed, true)) {
            View::json(['error' => 'Status inválido'], 422);
        }

        Database::update('medidas_protecao', ['status' => $status], 'id = ?', [$id]);
        View::json(['success' => true]);
    }

    public function getMedidasECA(): void
    {
        View::json($this->medidasECA);
    }
}
