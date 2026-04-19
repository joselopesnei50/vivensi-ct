<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Flash;
use App\Core\Request;
use App\Core\View;

class ChamadoController
{
    public function index(): void
    {
        Auth::requireAuth();
        $tenantId = Auth::tenantId();
        $chamados = Database::select(
            "SELECT c.*, u.nome as user_nome FROM chamados_suporte c
             LEFT JOIN users u ON u.id = c.user_id
             WHERE c.tenant_id = ?
             ORDER BY FIELD(c.status,'aberto','em_atendimento','resolvido','fechado'), c.created_at DESC",
            [$tenantId]
        );
        View::render('chamados/index', compact('chamados'));
    }

    public function store(): void
    {
        Auth::requireAuth();
        if (!Request::verifyCsrf()) {
            Flash::error('Token inválido.');
            Request::redirect(url('/chamados'));
        }

        $titulo    = Request::sanitize('titulo');
        $descricao = Request::sanitize('descricao');
        $tipo      = Request::post('tipo', 'duvida');
        $prioridade = Request::post('prioridade', 'media');

        if (strlen($titulo) < 5) {
            Flash::error('O título deve ter ao menos 5 caracteres.');
            Request::redirect(url('/chamados'));
        }

        Database::insert('chamados_suporte', [
            'tenant_id'  => Auth::tenantId(),
            'user_id'    => Auth::id(),
            'titulo'     => $titulo,
            'descricao'  => $descricao,
            'tipo'       => in_array($tipo, ['bug','duvida','financeiro','outro']) ? $tipo : 'duvida',
            'prioridade' => in_array($prioridade, ['baixa','media','alta','urgente']) ? $prioridade : 'media',
        ]);

        Flash::success('Chamado aberto com sucesso! Nossa equipe responderá em breve.');
        Request::redirect(url('/chamados'));
    }
}
