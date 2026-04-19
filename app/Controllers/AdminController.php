<?php
namespace App\Controllers;

use App\Core\AuditLog;
use App\Core\Auth;
use App\Core\Database;
use App\Core\Flash;
use App\Core\Request;
use App\Core\View;
use App\Services\PDFService;
use App\Services\BrevoService;

class AdminController
{
    public function dashboard(): void
    {
        Auth::requireAdmin();

        $tenants   = Database::select("SELECT t.*, COUNT(u.id) as total_users FROM tenants t LEFT JOIN users u ON u.tenant_id = t.id GROUP BY t.id ORDER BY t.created_at DESC");
        $users     = Database::select("SELECT u.*, t.nome as tenant_nome FROM users u LEFT JOIN tenants t ON t.id = u.tenant_id ORDER BY u.created_at DESC LIMIT 30");
        $pendentes = Database::select("SELECT * FROM cadastros_pendentes WHERE status = 'pendente' ORDER BY created_at DESC");
        $chamados  = Database::select(
            "SELECT c.*, t.nome as tenant_nome, u.nome as user_nome
             FROM chamados_suporte c
             LEFT JOIN tenants t ON t.id = c.tenant_id
             LEFT JOIN users u ON u.id = c.user_id
             ORDER BY FIELD(c.status,'aberto','em_atendimento','resolvido','fechado'), c.created_at DESC
             LIMIT 50"
        );

        // API configs
        $configsArr = Database::select("SELECT chave, valor FROM api_configs");
        $configsMap = array_column($configsArr, 'valor', 'chave');

        // Usuários online (últimos 5 min via audit_logs)
        $onlineUsers = Database::select(
            "SELECT u.nome, u.email, u.role, MAX(al.created_at) as last_seen
             FROM audit_logs al
             JOIN users u ON u.id = al.user_id
             WHERE al.created_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
             GROUP BY al.user_id
             ORDER BY last_seen DESC"
        );

        // Charts — últimos 7 dias
        $chartLabels = $chartAtend = $chartAcess = [];
        for ($i = 6; $i >= 0; $i--) {
            $date          = date('Y-m-d', strtotime("-{$i} days"));
            $chartLabels[] = date('d/m', strtotime($date));
            $chartAtend[]  = (int)(Database::selectOne("SELECT COUNT(*) as c FROM atendimentos WHERE DATE(created_at) = ?", [$date])['c'] ?? 0);
            $chartAcess[]  = (int)(Database::selectOne("SELECT COUNT(*) as c FROM audit_logs WHERE DATE(created_at) = ?", [$date])['c'] ?? 0);
        }

        // Saúde do servidor
        $diskPath     = PHP_OS_FAMILY === 'Windows' ? 'C:' : '/';
        $serverHealth = [
            'php'        => PHP_VERSION,
            'mysql'      => Database::selectOne("SELECT VERSION() as v")['v'] ?? 'N/A',
            'disk_free'  => @disk_free_space($diskPath) ?: 0,
            'disk_total' => @disk_total_space($diskPath) ?: 0,
            'mem_usage'  => memory_get_usage(true),
            'mem_limit'  => ini_get('memory_limit'),
            'os'         => PHP_OS_FAMILY,
            'uptime'     => PHP_OS_FAMILY === 'Linux' ? @shell_exec('uptime -p') : 'N/A',
            'time'       => date('d/m/Y H:i:s'),
        ];

        $hoje      = date('Y-m-d');
        $atendHoje = (int)(Database::selectOne("SELECT COUNT(*) as c FROM atendimentos WHERE DATE(created_at) = ?", [$hoje])['c'] ?? 0);
        $docHoje   = (int)(Database::selectOne("SELECT COUNT(*) as c FROM documentos WHERE DATE(created_at) = ?", [$hoje])['c'] ?? 0);

        $stats = [
            'tenants'          => (int)(Database::selectOne("SELECT COUNT(*) as c FROM tenants")['c'] ?? 0),
            'users'            => (int)(Database::selectOne("SELECT COUNT(*) as c FROM users")['c'] ?? 0),
            'atendimentos'     => (int)(Database::selectOne("SELECT COUNT(*) as c FROM atendimentos")['c'] ?? 0),
            'documentos'       => (int)(Database::selectOne("SELECT COUNT(*) as c FROM documentos WHERE excluido = 0")['c'] ?? 0),
            'chamados_abertos' => (int)(Database::selectOne("SELECT COUNT(*) as c FROM chamados_suporte WHERE status = 'aberto'")['c'] ?? 0),
            'online'           => count($onlineUsers),
            'pendentes'        => count($pendentes),
        ];

        // Páginas legais
        $paginasArr = Database::select("SELECT * FROM paginas_legais");
        $paginasMap = array_column($paginasArr, null, 'slug');

        View::render('admin/dashboard', compact(
            'tenants', 'users', 'stats', 'pendentes',
            'chamados', 'configsMap', 'onlineUsers',
            'chartLabels', 'chartAtend', 'chartAcess', 'serverHealth',
            'atendHoje', 'docHoje', 'paginasMap'
        ));
    }

    public function storeTenant(): void
    {
        Auth::requireAdmin();
        if (!Request::verifyCsrf()) { Flash::error('Token inválido.'); Request::redirect(url('/admin')); }

        Database::insert('tenants', [
            'nome'      => Request::sanitize('nome'),
            'municipio' => Request::sanitize('municipio'),
            'estado'    => strtoupper(substr(Request::sanitize('estado'), 0, 2)),
            'cnpj'      => Request::sanitize('cnpj'),
            'telefone'  => Request::sanitize('telefone'),
            'email'     => filter_var(Request::post('email', ''), FILTER_SANITIZE_EMAIL),
            'plano'     => Request::post('plano', 'gratuito'),
        ]);

        AuditLog::record('admin.tenant_create', 'tenants', null, ['nome' => Request::sanitize('nome')]);
        Flash::success('Conselho Tutelar cadastrado com sucesso!');
        Request::redirect(url('/admin') . '?tab=tenants');
    }

    public function storeUser(): void
    {
        Auth::requireAdmin();
        if (!Request::verifyCsrf()) { Flash::error('Token inválido.'); Request::redirect(url('/admin')); }

        $email = filter_var(Request::post('email', ''), FILTER_SANITIZE_EMAIL);
        if (Database::selectOne("SELECT id FROM users WHERE email = ?", [$email])) {
            Flash::error('Este e-mail já está cadastrado.');
            Request::redirect(url('/admin') . '?tab=usuarios');
        }

        // Usa senha fornecida ou gera uma aleatória forte
        $senhaFornecida = Request::post('password', '');
        $senha = $senhaFornecida !== '' ? $senhaFornecida : bin2hex(random_bytes(8));

        $nome = Request::sanitize('nome');
        Database::insert('users', [
            'tenant_id'          => Request::post('tenant_id'),
            'nome'               => $nome,
            'email'              => $email,
            'password'           => password_hash($senha, PASSWORD_BCRYPT),
            'role'               => Request::post('role', 'conselheiro'),
            'registro_funcional' => Request::sanitize('registro_funcional'),
            'telefone'           => Request::sanitize('telefone'),
        ]);

        AuditLog::record('admin.user_create', 'users', null, ['email' => $email, 'role' => Request::post('role', 'conselheiro')]);

        // Tenta enviar senha por e-mail se foi gerada automaticamente
        if ($senhaFornecida === '') {
            try {
                BrevoService::sendWelcomeEmail($nome, $email, $senha, url('/login'));
                Flash::success("Usuário criado. Senha temporária enviada para {$email}.");
            } catch (\Throwable) {
                Flash::success("Usuário criado. Senha temporária gerada: <code>{$senha}</code> — anote antes de sair desta página.");
            }
        } else {
            Flash::success('Usuário criado com sucesso!');
        }

        Request::redirect(url('/admin') . '?tab=usuarios');
    }

    public function purgeDocuments(): void
    {
        Auth::requireAdmin();
        $pdfService = new PDFService();
        $count = $pdfService->purgeExpired();
        View::json(['success' => true, 'purged' => $count, 'message' => "{$count} documentos expirados removidos."]);
    }

    public function toggleUser(string $id): void
    {
        Auth::requireAdmin();
        $user = Database::selectOne("SELECT ativo FROM users WHERE id = ?", [$id]);
        if ($user) {
            $novoEstado = $user['ativo'] ? 0 : 1;
            Database::update('users', ['ativo' => $novoEstado], 'id = ?', [$id]);
            AuditLog::record($novoEstado ? 'admin.user_activate' : 'admin.user_deactivate', 'users', (int)$id);
        }
        View::json(['success' => true]);
    }

    public function aprovarCadastro(string $id): void
    {
        Auth::requireAdmin();
        $acao   = Request::post('acao', 'aprovar');
        $status = $acao === 'aprovar' ? 'aprovado' : 'rejeitado';

        Database::update('cadastros_pendentes', [
            'status'         => $status,
            'aprovado_por'   => Auth::id(),
            'data_aprovacao' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$id]);

        $cadastro = Database::selectOne("SELECT * FROM cadastros_pendentes WHERE id = ?", [$id]);

        if ($status === 'aprovado' && $cadastro) {
            // Envia e-mail de aprovação
            try { BrevoService::sendApprovalEmail($cadastro['nome'], $cadastro['email'], url('/login')); } catch (\Throwable $e) {}

            $msg    = urlencode("Olá {$cadastro['nome']}! 🎉 Seu cadastro no VivensiCT foi APROVADO. Acesse: " . url('/login') . " — Bem-vindo ao sistema!");
            $tel    = preg_replace('/\D/', '', $cadastro['telefone']);
            $waLink = "https://wa.me/55{$tel}?text={$msg}";
            Flash::success("Cadastro de {$cadastro['nome']} aprovado. <a href=\"{$waLink}\" target=\"_blank\" style=\"color:inherit;font-weight:700;\">📲 Notificar via WhatsApp</a>");
        } else {
            Flash::success("Cadastro de " . ($cadastro['nome'] ?? '') . " rejeitado.");
        }

        header('Location: ' . url('/admin') . '?tab=cadastros');
        exit;
    }

    public function saveConfigs(): void
    {
        Auth::requireAdmin();
        if (!Request::verifyCsrf()) { Flash::error('Token inválido.'); Request::redirect(url('/admin')); }

        $keys = ['deepseek_key', 'gemini_key', 'brevo_key', 'abacatepay_key', 'whatsapp_number'];
        foreach ($keys as $key) {
            $val = Request::post($key, '');
            Database::query(
                "INSERT INTO api_configs (chave, valor) VALUES (?, ?) ON DUPLICATE KEY UPDATE valor = VALUES(valor), updated_at = NOW()",
                [$key, $val]
            );
        }

        Flash::success('Configurações salvas com sucesso.');
        header('Location: ' . url('/admin') . '?tab=configs');
        exit;
    }

    public function saveLegalPage(): void
    {
        Auth::requireAdmin();
        if (!Request::verifyCsrf()) {
            Flash::error('Token inválido.');
            Request::redirect(url('/admin') . '?tab=paginas-legais');
        }

        $slug = Request::post('slug', '');
        if (!in_array($slug, ['privacidade', 'termos'], true)) {
            Flash::error('Página inválida.');
            Request::redirect(url('/admin') . '?tab=paginas-legais');
        }

        $titulo   = Request::sanitize('titulo');
        $conteudo = Request::post('conteudo', ''); // HTML permitido — super_admin confiável

        Database::query(
            "INSERT INTO paginas_legais (slug, titulo, conteudo, updated_by)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE titulo = VALUES(titulo), conteudo = VALUES(conteudo),
                                     updated_by = VALUES(updated_by), updated_at = NOW()",
            [$slug, $titulo, $conteudo, Auth::id()]
        );

        Flash::success('Página "' . $titulo . '" salva com sucesso!');
        header('Location: ' . url('/admin') . '?tab=paginas-legais');
        exit;
    }

    public function responderChamado(string $id): void
    {
        Auth::requireAdmin();
        if (!Request::verifyCsrf()) { Flash::error('Token inválido.'); Request::redirect(url('/admin')); }

        Database::update('chamados_suporte', [
            'resposta'       => Request::sanitize('resposta'),
            'status'         => Request::post('status', 'em_atendimento'),
            'respondido_por' => Auth::id(),
        ], 'id = ?', [$id]);

        Flash::success('Chamado atualizado com sucesso.');
        header('Location: ' . url('/admin') . '?tab=suporte');
        exit;
    }
}
