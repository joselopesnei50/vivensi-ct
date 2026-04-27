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

class MedidaCampoController
{
    // ──────────────────────────────────────────────────────────────────────────
    // GET /medidas-campo
    // ──────────────────────────────────────────────────────────────────────────
    public function index(): void
    {
        Auth::requireAuth();
        $tenantId = Auth::tenantId();

        $status = Request::get('status', '');
        $where  = "WHERE mc.tenant_id = ? AND mc.excluido = 0";
        $params = [$tenantId];

        if ($status) {
            $where  .= " AND mc.status = ?";
            $params[] = $status;
        }

        $medidas = Database::select(
            "SELECT mc.*, u.nome as conselheiro
             FROM medidas_campo mc
             JOIN users u ON u.id = mc.user_id
             {$where}
             ORDER BY mc.created_at DESC",
            $params
        );

        // Descriptografar nome da criança para exibição na listagem
        foreach ($medidas as &$m) {
            $m['nome_crianca_dec'] = $this->decrypt($m['nome_crianca'] ?? '');
        }
        unset($m);

        View::render('medidas_campo/index', compact('medidas', 'status'));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /medidas-campo/nova
    // ──────────────────────────────────────────────────────────────────────────
    public function nova(): void
    {
        Auth::requireAuth();
        $user = Auth::user();
        View::render('medidas_campo/nova', compact('user'));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // POST /medidas-campo
    // ──────────────────────────────────────────────────────────────────────────
    public function store(): void
    {
        Auth::requireAuth();
        if (!Request::verifyCsrf()) {
            Flash::error('Token inválido.');
            Request::back();
        }

        $tenantId = Auth::tenantId();
        $userId   = Auth::id();

        // Número sequencial do documento
        $seq    = Database::selectOne(
            "SELECT COUNT(*)+1 AS n FROM medidas_campo WHERE tenant_id = ?",
            [$tenantId]
        )['n'] ?? 1;
        $numDoc = 'MC-' . date('Y') . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);

        $data = [
            'tenant_id'          => $tenantId,
            'user_id'            => $userId,
            'numero_doc'         => $numDoc,
            'nome_crianca'       => $this->encrypt(Request::post('nome_crianca', '')),
            'data_nascimento_enc'=> $this->encrypt(Request::post('data_nascimento', '')),
            'genero'             => Request::sanitize('genero'),
            'nome_responsavel'   => $this->encrypt(Request::post('nome_responsavel', '')),
            'endereco_enc'       => $this->encrypt(Request::post('endereco', '')),
            'tipo_medida'        => Request::sanitize('tipo_medida'),
            'artigo_eca'         => Request::sanitize('artigo_eca'),
            'situacao_relatada'  => Request::post('situacao_relatada', ''),
            'texto_medida'       => Request::post('texto_medida', ''),
            'assinante_nome'     => Request::sanitize('assinante_nome'),
            'assinante_cargo'    => Request::sanitize('assinante_cargo'),
            'status'             => 'rascunho',
            'expira_em'          => date('Y-m-d H:i:s', strtotime('+30 days')),
        ];

        $id = Database::insert('medidas_campo', $data);
        AuditLog::record('medida_campo.create', 'medidas_campo', $id, [
            'numero_doc'  => $numDoc,
            'tipo_medida' => $data['tipo_medida'],
        ]);

        Flash::success("Medida {$numDoc} criada com sucesso!");
        Request::redirect(url("/medidas-campo/{$id}"));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /medidas-campo/{id}
    // ──────────────────────────────────────────────────────────────────────────
    public function show(string $id): void
    {
        Auth::requireAuth();
        $tenantId = Auth::tenantId();

        $medida = Database::selectOne(
            "SELECT mc.*, u.nome as conselheiro, u.registro_funcional, t.municipio
             FROM medidas_campo mc
             JOIN users u ON u.id = mc.user_id
             JOIN tenants t ON t.id = mc.tenant_id
             WHERE mc.id = ? AND mc.tenant_id = ? AND mc.excluido = 0",
            [$id, $tenantId]
        );

        if (!$medida) {
            Flash::error('Documento não encontrado.');
            Request::redirect(url('/medidas-campo'));
        }

        // Descriptografar
        $medida['nome_crianca_dec']    = $this->decrypt($medida['nome_crianca'] ?? '');
        $medida['nascimento_dec']      = $this->decrypt($medida['data_nascimento_enc'] ?? '');
        $medida['nome_responsavel_dec']= $this->decrypt($medida['nome_responsavel'] ?? '');
        $medida['endereco_dec']        = $this->decrypt($medida['endereco_enc'] ?? '');

        View::render('medidas_campo/show', compact('medida'));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // POST /medidas-campo/{id}/gerar-texto  (AJAX)
    // ──────────────────────────────────────────────────────────────────────────
    public function gerarTexto(string $id): void
    {
        Auth::requireAuth();
        if (!Request::verifyCsrf()) {
            View::json(['error' => 'Token inválido'], 403);
        }
        $tenantId = Auth::tenantId();

        $medida = Database::selectOne(
            "SELECT mc.*, u.nome as conselheiro, u.registro_funcional, t.municipio
             FROM medidas_campo mc
             JOIN users u ON u.id = mc.user_id
             JOIN tenants t ON t.id = mc.tenant_id
             WHERE mc.id = ? AND mc.tenant_id = ? AND mc.excluido = 0",
            [$id, $tenantId]
        );

        if (!$medida) {
            View::json(['error' => 'Não encontrado'], 404);
        }

        $ai     = new AIService();
        $result = $ai->gerarTextoMedida([
            'municipio'         => $medida['municipio'] ?? '',
            'tipo_medida'       => $medida['tipo_medida'],
            'artigo_eca'        => $medida['artigo_eca'],
            'situacao_relatada' => $medida['situacao_relatada'],
            'nome_crianca'      => $this->decrypt($medida['nome_crianca'] ?? ''),
            'conselheiro'       => $medida['conselheiro'],
            'cargo'             => $medida['registro_funcional']
                                    ? 'Conselheiro(a) Tutelar — Registro ' . $medida['registro_funcional']
                                    : 'Conselheiro(a) Tutelar',
        ]);

        // Salva o texto gerado
        if (!empty($result['texto_medida'])) {
            Database::update('medidas_campo', [
                'texto_medida' => $result['texto_medida'],
            ], 'id = ?', [$id]);
        }

        View::json(['success' => true, 'data' => $result]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // POST /medidas-campo/{id}/assinar  (AJAX)
    // ──────────────────────────────────────────────────────────────────────────
    public function assinar(string $id): void
    {
        Auth::requireAuth();
        if (!Request::verifyCsrf()) {
            View::json(['error' => 'Token inválido'], 403);
        }
        $tenantId = Auth::tenantId();

        $medida = Database::selectOne(
            "SELECT mc.*, u.nome as conselheiro, u.registro_funcional, t.municipio, t.nome as tenant_nome
             FROM medidas_campo mc
             JOIN users u ON u.id = mc.user_id
             JOIN tenants t ON t.id = mc.tenant_id
             WHERE mc.id = ? AND mc.tenant_id = ? AND mc.excluido = 0",
            [$id, $tenantId]
        );

        if (!$medida) {
            View::json(['error' => 'Não encontrado'], 404);
        }

        $assinatura  = Request::post('assinatura_data', '');
        $nomeAss     = Request::sanitize('assinante_nome') ?: $medida['assinante_nome'] ?: $medida['conselheiro'];
        $cargoAss    = Request::sanitize('assinante_cargo') ?: $medida['assinante_cargo'] ?: 'Conselheiro(a) Tutelar';
        $textoFinal  = Request::post('texto_medida', '') ?: $medida['texto_medida'];

        // Gera o documento HTML/PDF
        $pdfService  = new PDFService();
        $nomeCrianca = $this->decrypt($medida['nome_crianca'] ?? '');

        $docData = [
            'numero_doc'     => $medida['numero_doc'],
            'tipo_medida'    => $medida['tipo_medida'],
            'artigo_eca'     => $medida['artigo_eca'],
            'nome_crianca'   => $nomeCrianca,
            'texto_medida'   => $textoFinal,
            'assinante_nome' => $nomeAss,
            'assinante_cargo'=> $cargoAss,
            'municipio'      => $medida['municipio'],
            'tenant_nome'    => $medida['tenant_nome'],
        ];

        $filename = $pdfService->gerarMedidaCampo($docData, $assinatura);

        Database::update('medidas_campo', [
            'texto_medida'   => $textoFinal,
            'assinatura_data'=> $assinatura,
            'assinante_nome' => $nomeAss,
            'assinante_cargo'=> $cargoAss,
            'assinado'       => 1,
            'data_assinatura'=> date('Y-m-d H:i:s'),
            'pdf_arquivo'    => $filename,
            'status'         => 'assinado',
        ], 'id = ?', [$id]);

        AuditLog::record('medida_campo.assinada', 'medidas_campo', (int)$id, [
            'numero_doc' => $medida['numero_doc'],
        ]);

        View::json(['success' => true, 'filename' => $filename, 'doc_id' => $id]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /medidas-campo/{id}/download
    // ──────────────────────────────────────────────────────────────────────────
    public function download(string $id): void
    {
        Auth::requireAuth();
        $tenantId = Auth::tenantId();

        $medida = Database::selectOne(
            "SELECT * FROM medidas_campo WHERE id = ? AND tenant_id = ? AND excluido = 0 AND assinado = 1",
            [$id, $tenantId]
        );

        if (!$medida || !$medida['pdf_arquivo']) {
            Flash::error('Documento não encontrado ou ainda não assinado.');
            Request::redirect(url("/medidas-campo/{$id}"));
        }

        $pdfService = new PDFService();
        $pdfService->download($medida['pdf_arquivo']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // POST /medidas-campo/{id}/salvar-texto  (AJAX — salva rascunho)
    // ──────────────────────────────────────────────────────────────────────────
    public function salvarTexto(string $id): void
    {
        Auth::requireAuth();
        if (!Request::verifyCsrf()) {
            View::json(['error' => 'Token inválido'], 403);
        }
        $tenantId = Auth::tenantId();

        $exists = Database::selectOne(
            "SELECT id FROM medidas_campo WHERE id = ? AND tenant_id = ? AND excluido = 0",
            [$id, $tenantId]
        );
        if (!$exists) View::json(['error' => 'Não encontrado'], 404);

        Database::update('medidas_campo', [
            'texto_medida' => Request::post('texto_medida', ''),
        ], 'id = ?', [$id]);

        View::json(['success' => true]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Helpers AES-256-CBC (LGPD)
    // ──────────────────────────────────────────────────────────────────────────
    private function encryptionKey(): string
    {
        $raw = env('APP_KEY', 'VCT_2024_s3cur3K3y_LGPD_ECA_Gu4rdi40Digital!!');
        if (str_starts_with($raw, 'base64:')) {
            $raw = base64_decode(substr($raw, 7));
        }
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
