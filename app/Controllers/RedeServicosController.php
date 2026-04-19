<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Flash;
use App\Core\Request;
use App\Core\View;

class RedeServicosController
{
    public function index(): void
    {
        Auth::requireAuth();
        $tenantId = Auth::tenantId();

        $servicos = Database::select(
            "SELECT * FROM rede_servicos WHERE tenant_id = ? ORDER BY tipo_servico, nome_servico",
            [$tenantId]
        );

        View::render('rede_servicos/index', compact('servicos'));
    }

    public function store(): void
    {
        Auth::requireAuth();
        if (!Request::verifyCsrf()) {
            Flash::error('Token inválido.');
            Request::redirect(url('/rede-servicos'));
        }

        $tenantId = Auth::tenantId();

        Database::insert('rede_servicos', [
            'tenant_id'            => $tenantId,
            'nome_servico'         => Request::sanitize('nome_servico'),
            'tipo_servico'         => Request::sanitize('tipo_servico'),
            'tipificacao_suas'     => Request::sanitize('tipificacao_suas'),
            'orgao_responsavel'    => Request::sanitize('orgao_responsavel'),
            'endereco'             => Request::post('endereco', ''),
            'telefone'             => Request::sanitize('telefone'),
            'email'                => filter_var(Request::post('email', ''), FILTER_SANITIZE_EMAIL),
            'responsavel'          => Request::sanitize('responsavel'),
            'horario_funcionamento'=> Request::sanitize('horario_funcionamento'),
            'observacoes'          => Request::post('observacoes', ''),
        ]);

        Flash::success('Serviço adicionado à rede municipal com sucesso!');
        Request::redirect(url('/rede-servicos'));
    }

    public function update(string $id): void
    {
        Auth::requireAuth();
        $tenantId = Auth::tenantId();

        Database::update('rede_servicos', [
            'nome_servico'         => Request::sanitize('nome_servico'),
            'tipo_servico'         => Request::sanitize('tipo_servico'),
            'tipificacao_suas'     => Request::sanitize('tipificacao_suas'),
            'orgao_responsavel'    => Request::sanitize('orgao_responsavel'),
            'telefone'             => Request::sanitize('telefone'),
            'responsavel'          => Request::sanitize('responsavel'),
            'ativo'                => Request::post('ativo', 1),
        ], 'id = ? AND tenant_id = ?', [$id, $tenantId]);

        Flash::success('Serviço atualizado!');
        Request::redirect(url('/rede-servicos'));
    }

    public function destroy(string $id): void
    {
        Auth::requireAuth();
        $tenantId = Auth::tenantId();
        Database::update('rede_servicos', ['ativo' => 0], 'id = ? AND tenant_id = ?', [$id, $tenantId]);
        View::json(['success' => true]);
    }
}
