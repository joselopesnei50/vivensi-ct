<?php
namespace App\Controllers;

use App\Core\AuditLog;
use App\Core\Auth;
use App\Core\Database;
use App\Core\Flash;
use App\Core\Request;
use App\Core\View;
use App\Services\AIService;
use App\Services\PDFService;

class AtendimentoController
{
    public function index(): void
    {
        Auth::requireAuth();
        $tenantId = Auth::tenantId();

        $status   = Request::get('status', '');
        $busca    = Request::get('busca', '');
        $prioridade = Request::get('prioridade', '');

        $where  = "WHERE a.tenant_id = ?";
        $params = [$tenantId];

        if ($status)    { $where .= " AND a.status = ?";      $params[] = $status; }
        if ($prioridade){ $where .= " AND a.prioridade = ?";  $params[] = $prioridade; }
        if ($busca)     { $where .= " AND (a.numero_protocolo LIKE ? OR a.tipo_demanda LIKE ?)";
                          $params[] = "%{$busca}%"; $params[] = "%{$busca}%"; }

        $atendimentos = Database::select(
            "SELECT a.*, u.nome as conselheiro
             FROM atendimentos a
             JOIN users u ON u.id = a.user_id
             {$where}
             ORDER BY FIELD(a.prioridade,'urgente','alta','media','baixa'), a.created_at DESC",
            $params
        );

        View::render('atendimentos/index', compact('atendimentos', 'status', 'busca', 'prioridade'));
    }

    public function create(): void
    {
        Auth::requireAuth();
        $tenantId   = Auth::tenantId();
        $redeServicos = Database::select(
            "SELECT * FROM rede_servicos WHERE tenant_id = ? AND ativo = 1 ORDER BY nome_servico",
            [$tenantId]
        );
        View::render('atendimentos/create', compact('redeServicos'));
    }

    public function store(): void
    {
        Auth::requireAuth();
        if (!Request::verifyCsrf()) {
            Flash::error('Token inválido.');
            Request::back();
        }

        $tenantId = Auth::tenantId();
        $userId   = Auth::id();

        // Gera protocolo único
        $protocolo = 'CT-' . date('Y') . '-' . str_pad(
            (Database::selectOne("SELECT COUNT(*)+1 as n FROM atendimentos WHERE tenant_id=?", [$tenantId])['n'] ?? 1),
            5, '0', STR_PAD_LEFT
        );

        $data = [
            'tenant_id'             => $tenantId,
            'user_id'               => $userId,
            'numero_protocolo'      => $protocolo,
            'status'                => 'aberto',
            'tipo_demanda'          => Request::sanitize('tipo_demanda'),
            'prioridade'            => Request::post('prioridade', 'media'),
            'data_ocorrencia'       => Request::post('data_ocorrencia') ?: null,
            'data_atendimento'      => date('Y-m-d'),
            'relato_visita'         => Request::post('relato_visita', ''),
            'levantamento_preliminar'=> Request::post('levantamento_preliminar', ''),
            // Dados sensíveis criptografados
            'nome_crianca'          => $this->encrypt(Request::post('nome_crianca', '')),
            'filiacao'              => $this->encrypt(Request::post('filiacao', '')),
            'endereco_enc'          => $this->encrypt(Request::post('endereco', '')),
            'escola'                => Request::sanitize('escola'),
            'genero'                => Request::sanitize('genero'),
            // SIPIA CT Web
            'sipia_natureza'        => Request::sanitize('sipia_natureza') ?: null,
            'sipia_abrangencia'     => Request::post('sipia_abrangencia', 'individual'),
            'sipia_protocolo'       => $this->encrypt(Request::post('sipia_protocolo', '')),
        ];

        $id = Database::insert('atendimentos', $data);
        AuditLog::record('atendimento.create', 'atendimentos', (int)$id, ['protocolo' => $protocolo, 'tipo' => $data['tipo_demanda']]);

        // Analisar com IA se solicitado
        if (Request::post('analisar_ia') === '1') {
            $tenant = Database::selectOne("SELECT * FROM tenants WHERE id=?", [$tenantId]);
            $redeServicos = Database::select("SELECT nome_servico, tipo_servico FROM rede_servicos WHERE tenant_id=? AND ativo=1", [$tenantId]);
            $redeStr = implode(', ', array_column($redeServicos, 'nome_servico'));

            $aiService = new AIService();
            $analise   = $aiService->analisarCaso([
                'municipio'              => $tenant['municipio'] ?? '',
                'tipo_demanda'           => $data['tipo_demanda'],
                'relato_visita'          => $data['relato_visita'],
                'levantamento_preliminar'=> $data['levantamento_preliminar'],
                'rede_servicos'          => $redeStr,
            ]);

            Database::update('atendimentos', [
                'analise_ia'            => json_encode($analise, JSON_UNESCAPED_UNICODE),
                'fluxo_encaminhamento'  => json_encode($analise['fluxo_encaminhamento'] ?? [], JSON_UNESCAPED_UNICODE),
                'mapa_mental_mermaid'   => $analise['mapa_mental_mermaid'] ?? '',
                'minutas_geradas'       => json_encode($analise['minutas'] ?? [], JSON_UNESCAPED_UNICODE),
                'prioridade'            => $analise['prioridade'] ?? $data['prioridade'],
                'status'                => 'em_andamento',
            ], 'id = ?', [$id]);

            Flash::success("Atendimento #{$protocolo} criado e analisado pela IA com sucesso!");
        } else {
            Flash::success("Atendimento #{$protocolo} criado com sucesso!");
        }

        Request::redirect(url("/atendimentos/{$id}"));
    }

    public function show(string $id): void
    {
        Auth::requireAuth();
        $tenantId = Auth::tenantId();

        $atendimento = Database::selectOne(
            "SELECT a.*, u.nome as conselheiro, u.registro_funcional, t.municipio, t.estado
             FROM atendimentos a
             JOIN users u ON u.id = a.user_id
             JOIN tenants t ON t.id = a.tenant_id
             WHERE a.id = ? AND a.tenant_id = ?",
            [$id, $tenantId]
        );

        if (!$atendimento) {
            Flash::error('Atendimento não encontrado.');
            Request::redirect(url('/atendimentos'));
        }

        // Decrypt sensitive data for display
        $atendimento['nome_crianca_dec'] = $this->decrypt($atendimento['nome_crianca'] ?? '');
        $atendimento['filiacao_dec']     = $this->decrypt($atendimento['filiacao'] ?? '');
        $atendimento['endereco_dec']     = $this->decrypt($atendimento['endereco_enc'] ?? '');

        $analiseIA    = json_decode($atendimento['analise_ia'] ?? '{}', true) ?? [];
        $fluxo        = json_decode($atendimento['fluxo_encaminhamento'] ?? '[]', true) ?? [];
        $minutas      = json_decode($atendimento['minutas_geradas'] ?? '{}', true) ?? [];
        $mapaMermaid  = $atendimento['mapa_mental_mermaid'] ?? '';

        $medidas      = Database::select("SELECT * FROM medidas_protecao WHERE atendimento_id = ? ORDER BY created_at DESC", [$id]);
        $encaminhamentos = Database::select("SELECT * FROM encaminhamentos WHERE atendimento_id = ? ORDER BY created_at DESC", [$id]);
        $documentos   = Database::select("SELECT * FROM documentos WHERE atendimento_id = ? AND excluido = 0 ORDER BY created_at DESC", [$id]);

        View::render('atendimentos/show', compact(
            'atendimento', 'analiseIA', 'fluxo', 'minutas',
            'mapaMermaid', 'medidas', 'encaminhamentos', 'documentos'
        ));
    }

    public function analisarIA(string $id): void
    {
        Auth::requireAuth();
        if (!Request::verifyCsrf()) {
            View::json(['error' => 'Token inválido'], 403);
        }
        $tenantId = Auth::tenantId();

        $atendimento = Database::selectOne(
            "SELECT * FROM atendimentos WHERE id = ? AND tenant_id = ?",
            [$id, $tenantId]
        );

        if (!$atendimento) {
            View::json(['error' => 'Atendimento não encontrado'], 404);
        }

        $tenant       = Database::selectOne("SELECT * FROM tenants WHERE id=?", [$tenantId]);
        $redeServicos = Database::select("SELECT nome_servico, tipo_servico FROM rede_servicos WHERE tenant_id=? AND ativo=1", [$tenantId]);
        $redeStr      = implode(', ', array_column($redeServicos, 'nome_servico'));

        $aiService = new AIService();
        $analise   = $aiService->analisarCaso([
            'municipio'              => $tenant['municipio'] ?? '',
            'tipo_demanda'           => $atendimento['tipo_demanda'],
            'relato_visita'          => $atendimento['relato_visita'],
            'levantamento_preliminar'=> $atendimento['levantamento_preliminar'],
            'rede_servicos'          => $redeStr,
        ]);

        Database::update('atendimentos', [
            'analise_ia'           => json_encode($analise, JSON_UNESCAPED_UNICODE),
            'fluxo_encaminhamento' => json_encode($analise['fluxo_encaminhamento'] ?? [], JSON_UNESCAPED_UNICODE),
            'mapa_mental_mermaid'  => $analise['mapa_mental_mermaid'] ?? '',
            'minutas_geradas'      => json_encode($analise['minutas'] ?? [], JSON_UNESCAPED_UNICODE),
            'prioridade'           => $analise['prioridade'] ?? $atendimento['prioridade'],
            'status'               => 'em_andamento',
        ], 'id = ?', [$id]);

        View::json(['success' => true, 'data' => $analise]);
    }

    public function gerarDocumento(string $id): void
    {
        Auth::requireAuth();
        $tenantId = Auth::tenantId();
        $userId   = Auth::id();

        $atendimento = Database::selectOne(
            "SELECT * FROM atendimentos WHERE id = ? AND tenant_id = ?",
            [$id, $tenantId]
        );

        if (!$atendimento) {
            View::json(['error' => 'Não encontrado'], 404);
        }

        $tipoDoc    = Request::post('tipo_documento', 'Relatório de Atendimento');
        $conteudo   = Request::post('conteudo', '');
        $assinatura = Request::post('assinatura_data', '');
        $nomeAss    = Request::sanitize('assinante_nome');
        $cargoAss   = Request::sanitize('assinante_cargo');

        $pdfService = new PDFService();

        $docData = [
            'tipo_documento'  => $tipoDoc,
            'conteudo'        => $conteudo,
            'assinante_nome'  => $nomeAss,
            'assinante_cargo' => $cargoAss,
        ];

        $filename = $pdfService->gerarDocumentoAssinado($atendimento, $docData, $assinatura);

        // Salva no banco com expiração de 3 dias
        $docId = Database::insert('documentos', [
            'atendimento_id'  => $id,
            'user_id'         => $userId,
            'tipo_documento'  => $tipoDoc,
            'nome_arquivo'    => $filename,
            'caminho_arquivo' => $filename,
            'assinatura_data' => $assinatura,
            'assinante_nome'  => $nomeAss,
            'assinante_cargo' => $cargoAss,
            'assinado'        => !empty($assinatura) ? 1 : 0,
            'data_assinatura' => !empty($assinatura) ? date('Y-m-d H:i:s') : null,
            'expira_em'       => date('Y-m-d H:i:s', strtotime('+3 days')),
        ]);

        View::json(['success' => true, 'doc_id' => $docId, 'filename' => $filename]);
    }

    public function downloadDocumento(string $id, string $docId): void
    {
        Auth::requireAuth();
        $tenantId = Auth::tenantId();

        $doc = Database::selectOne(
            "SELECT d.* FROM documentos d
             JOIN atendimentos a ON a.id = d.atendimento_id
             WHERE d.id = ? AND a.tenant_id = ? AND d.excluido = 0",
            [$docId, $tenantId]
        );

        if (!$doc) {
            Flash::error('Documento não encontrado ou já foi expirado.');
            Request::redirect(url("/atendimentos/{$id}"));
        }

        $pdfService = new PDFService();
        $pdfService->download($doc['caminho_arquivo']);
    }

    public function updateStatus(string $id): void
    {
        Auth::requireAuth();
        if (!Request::verifyCsrf()) {
            View::json(['error' => 'Token inválido'], 403);
        }
        $tenantId = Auth::tenantId();

        $allowed = ['aberto', 'em_andamento', 'encerrado', 'arquivado'];
        $status  = Request::post('status', 'em_andamento');
        if (!in_array($status, $allowed, true)) {
            View::json(['error' => 'Status inválido'], 422);
        }

        Database::update('atendimentos', ['status' => $status], 'id = ? AND tenant_id = ?', [$id, $tenantId]);
        AuditLog::record('atendimento.status_change', 'atendimentos', (int)$id, ['status' => $status]);

        View::json(['success' => true]);
    }

    // Criptografia AES-256-CBC com chave derivada de APP_KEY (LGPD)
    private function encryptionKey(): string
    {
        $raw = env('APP_KEY', 'VCT_2024_s3cur3K3y_LGPD_ECA_Gu4rdi40Digital!!');
        // Strip optional 'base64:' prefix
        if (str_starts_with($raw, 'base64:')) {
            $raw = base64_decode(substr($raw, 7));
        }
        // Derive 32 bytes for AES-256-CBC via SHA-256
        return hash('sha256', $raw, true);
    }

    private function encrypt(string $data): string
    {
        if (empty($data)) return '';
        $key = $this->encryptionKey();
        $iv  = random_bytes(16);
        $enc = openssl_encrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($iv . $enc);
    }

    private function decrypt(string $data): string
    {
        if (empty($data)) return '';
        try {
            $key     = $this->encryptionKey();
            $decoded = base64_decode($data);
            $iv      = substr($decoded, 0, 16);
            $enc     = substr($decoded, 16);
            $result  = openssl_decrypt($enc, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
            return $result !== false ? $result : '';
        } catch (\Exception $e) {
            return '';
        }
    }
}
