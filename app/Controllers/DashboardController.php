<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\View;

class DashboardController
{
    public function index(): void
    {
        Auth::requireAuth();
        $tenantId = Auth::tenantId();
        $userId   = Auth::id();
        $role     = Auth::role();

        // Stats
        if ($role === 'super_admin') {
            $totalAtendimentos = Database::selectOne("SELECT COUNT(*) as c FROM atendimentos")['c'] ?? 0;
            $totalUsuarios     = Database::selectOne("SELECT COUNT(*) as c FROM users")['c'] ?? 0;
            $totalTenants      = Database::selectOne("SELECT COUNT(*) as c FROM tenants")['c'] ?? 0;
            $abertos           = Database::selectOne("SELECT COUNT(*) as c FROM atendimentos WHERE status = 'aberto'")['c'] ?? 0;
        } else {
            $totalAtendimentos = Database::selectOne("SELECT COUNT(*) as c FROM atendimentos WHERE tenant_id = ?", [$tenantId])['c'] ?? 0;
            $totalUsuarios     = Database::selectOne("SELECT COUNT(*) as c FROM users WHERE tenant_id = ?", [$tenantId])['c'] ?? 0;
            $totalTenants      = 1;
            $abertos           = Database::selectOne("SELECT COUNT(*) as c FROM atendimentos WHERE tenant_id = ? AND status = 'aberto'", [$tenantId])['c'] ?? 0;
        }

        $urgentes = Database::selectOne(
            "SELECT COUNT(*) as c FROM atendimentos WHERE tenant_id = ? AND prioridade = 'urgente' AND status != 'encerrado'",
            [$tenantId]
        )['c'] ?? 0;

        // Últimos atendimentos
        if ($role === 'super_admin') {
            $ultimosAtendimentos = Database::select(
                "SELECT a.*, u.nome as conselheiro FROM atendimentos a
                 JOIN users u ON u.id = a.user_id
                 ORDER BY a.created_at DESC LIMIT 8"
            );
        } else {
            $ultimosAtendimentos = Database::select(
                "SELECT a.*, u.nome as conselheiro FROM atendimentos a
                 JOIN users u ON u.id = a.user_id
                 WHERE a.tenant_id = ?
                 ORDER BY a.created_at DESC LIMIT 8",
                [$tenantId]
            );
        }

        // Docs expirando em 24h
        $docsExpirando = Database::select(
            "SELECT d.*, a.numero_protocolo FROM documentos d
             JOIN atendimentos a ON a.id = d.atendimento_id
             WHERE d.expira_em <= DATE_ADD(NOW(), INTERVAL 1 DAY) AND d.excluido = 0
             AND a.tenant_id = ?
             ORDER BY d.expira_em ASC",
            [$tenantId]
        );

        $redeServicos = Database::select(
            "SELECT * FROM rede_servicos WHERE tenant_id = ? AND ativo = 1 ORDER BY nome_servico",
            [$tenantId]
        );

        View::render('dashboard/index', compact(
            'totalAtendimentos', 'totalUsuarios', 'totalTenants',
            'abertos', 'urgentes', 'ultimosAtendimentos', 'docsExpirando',
            'redeServicos'
        ));
    }
}
