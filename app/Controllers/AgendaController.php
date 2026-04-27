<?php
namespace App\Controllers;

use App\Core\AuditLog;
use App\Core\Auth;
use App\Core\Database;
use App\Core\Flash;
use App\Core\Request;
use App\Core\View;

class AgendaController
{
    public function index(): void
    {
        Auth::requireAuth();
        $tenantId = Auth::tenantId();
        $userId   = Auth::id();
        $role     = Auth::role();

        // Mês/ano atual ou do filtro
        $ano  = (int)Request::get('ano',  date('Y'));
        $mes  = (int)Request::get('mes',  date('m'));
        $ano  = max(2020, min(2040, $ano));
        $mes  = max(1,    min(12,   $mes));

        $inicioMes = sprintf('%04d-%02d-01', $ano, $mes);
        $fimMes    = date('Y-m-t', strtotime($inicioMes));

        // Eventos do mês
        $where  = $role === 'super_admin'
            ? "WHERE a.tenant_id = ? AND a.data_inicio BETWEEN ? AND ?"
            : "WHERE a.tenant_id = ? AND a.data_inicio BETWEEN ? AND ?";

        $eventos = Database::select(
            "SELECT a.*, u.nome as user_nome, at.numero_protocolo
             FROM agenda a
             JOIN users u ON u.id = a.user_id
             LEFT JOIN atendimentos at ON at.id = a.atendimento_id
             WHERE a.tenant_id = ? AND a.data_inicio BETWEEN ? AND ?
             ORDER BY a.data_inicio ASC",
            [$tenantId, $inicioMes . ' 00:00:00', $fimMes . ' 23:59:59']
        );

        // Próximos eventos (7 dias)
        $proximosEventos = Database::select(
            "SELECT a.*, u.nome as user_nome, at.numero_protocolo
             FROM agenda a
             JOIN users u ON u.id = a.user_id
             LEFT JOIN atendimentos at ON at.id = a.atendimento_id
             WHERE a.tenant_id = ?
               AND a.status = 'agendado'
               AND a.data_inicio BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)
             ORDER BY a.data_inicio ASC
             LIMIT 10",
            [$tenantId]
        );

        // Atendimentos para vincular
        $atendimentos = Database::select(
            "SELECT id, numero_protocolo, tipo_demanda FROM atendimentos
             WHERE tenant_id = ? AND status != 'encerrado'
             ORDER BY created_at DESC LIMIT 50",
            [$tenantId]
        );

        // Monta mapa dia → eventos para o calendário
        $eventosPorDia = [];
        foreach ($eventos as $e) {
            $dia = date('j', strtotime($e['data_inicio']));
            $eventosPorDia[$dia][] = $e;
        }

        $title    = 'Agenda';
        $subtitle = 'Compromissos e prazos — ' . strftime('%B de %Y', strtotime($inicioMes));

        View::render('agenda/index', compact(
            'eventos', 'proximosEventos', 'eventosPorDia',
            'atendimentos', 'ano', 'mes', 'inicioMes'
        ));
    }

    public function store(): void
    {
        Auth::requireAuth();
        if (!Request::verifyCsrf()) {
            View::json(['error' => 'Token inválido'], 403);
        }

        $tenantId = Auth::tenantId();
        $userId   = Auth::id();

        $atendimentoId = Request::post('atendimento_id') ?: null;

        // Verifica se atendimento pertence ao tenant
        if ($atendimentoId) {
            $at = Database::selectOne(
                "SELECT id FROM atendimentos WHERE id = ? AND tenant_id = ?",
                [$atendimentoId, $tenantId]
            );
            if (!$at) $atendimentoId = null;
        }

        $dataInicio = Request::post('data_inicio', '');
        $dataFim    = Request::post('data_fim', '') ?: null;

        $id = Database::insert('agenda', [
            'tenant_id'      => $tenantId,
            'user_id'        => $userId,
            'atendimento_id' => $atendimentoId,
            'titulo'         => Request::sanitize('titulo'),
            'descricao'      => Request::post('descricao', ''),
            'tipo'           => Request::post('tipo', 'outro'),
            'data_inicio'    => $dataInicio,
            'data_fim'       => $dataFim,
            'local'          => Request::sanitize('local'),
            'prioridade'     => Request::post('prioridade', 'media'),
            'status'         => 'agendado',
        ]);

        AuditLog::record('agenda.create', 'agenda', $id, ['titulo' => Request::sanitize('titulo')]);

        if (Request::isAjax()) {
            View::json(['success' => true, 'id' => $id]);
        } else {
            Flash::success('Compromisso agendado com sucesso!');
            $mes = date('m', strtotime($dataInicio));
            $ano = date('Y', strtotime($dataInicio));
            Request::redirect(url("/agenda?mes={$mes}&ano={$ano}"));
        }
    }

    public function updateStatus(string $id): void
    {
        Auth::requireAuth();
        if (!Request::verifyCsrf()) {
            View::json(['error' => 'Token inválido'], 403);
        }

        $tenantId = Auth::tenantId();

        $evento = Database::selectOne(
            "SELECT id FROM agenda WHERE id = ? AND tenant_id = ?",
            [$id, $tenantId]
        );
        if (!$evento) {
            View::json(['error' => 'Evento não encontrado'], 404);
        }

        $allowed = ['agendado', 'realizado', 'cancelado'];
        $status  = Request::post('status', 'agendado');
        if (!in_array($status, $allowed, true)) {
            View::json(['error' => 'Status inválido'], 422);
        }

        Database::update('agenda', ['status' => $status], 'id = ?', [$id]);
        AuditLog::record('agenda.status_change', 'agenda', (int)$id, ['status' => $status]);

        View::json(['success' => true]);
    }

    public function destroy(string $id): void
    {
        Auth::requireAuth();
        if (!Request::verifyCsrf()) {
            View::json(['error' => 'Token inválido'], 403);
        }

        $tenantId = Auth::tenantId();

        $evento = Database::selectOne(
            "SELECT id FROM agenda WHERE id = ? AND tenant_id = ?",
            [$id, $tenantId]
        );
        if (!$evento) {
            View::json(['error' => 'Evento não encontrado'], 404);
        }

        Database::query("DELETE FROM agenda WHERE id = ?", [$id]);
        AuditLog::record('agenda.delete', 'agenda', (int)$id);

        View::json(['success' => true]);
    }
}
