<?php $title = 'Atendimentos'; $subtitle = 'Gerenciamento de casos ativos'; ?>

<!-- Filters -->
<div class="card mb-6">
  <div class="card-body" style="padding:16px 24px;">
    <form method="GET" action="<?= url('/atendimentos') ?>"
          class="filter-form"
          style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
      <input type="text" name="busca" class="form-control" placeholder="🔍 Buscar por protocolo ou demanda..."
             value="<?= e($busca) ?>" style="flex:1;min-width:180px;">

      <select name="status" class="form-control" style="min-width:140px;width:auto;">
        <option value="">Todos os Status</option>
        <?php foreach (['aberto','em_andamento','encerrado','arquivado'] as $s): ?>
        <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ',$s)) ?></option>
        <?php endforeach; ?>
      </select>

      <select name="prioridade" class="form-control" style="min-width:150px;width:auto;">
        <option value="">Todas as Prioridades</option>
        <?php foreach (['urgente','alta','media','baixa'] as $p): ?>
        <option value="<?= $p ?>" <?= $prioridade === $p ? 'selected' : '' ?>><?= ucfirst($p) ?></option>
        <?php endforeach; ?>
      </select>

      <button type="submit" class="btn btn-primary">Filtrar</button>
      <a href="<?= url('/atendimentos') ?>" class="btn btn-secondary">Limpar</a>
      <a href="<?= url('/atendimentos/novo') ?>" class="btn btn-primary filter-new-btn" style="margin-left:auto;">➕ Novo</a>
    </form>
  </div>
</div>

<!-- Table -->
<div class="card">
  <div class="card-header">
    <h3>📋 Casos Registrados</h3>
    <span class="text-muted text-sm"><?= count($atendimentos) ?> caso(s)</span>
  </div>

  <?php if (empty($atendimentos)): ?>
    <div style="padding:60px;text-align:center;">
      <div style="font-size:48px;margin-bottom:16px;">📭</div>
      <h3 style="color:var(--text-secondary);margin:0 0 8px;">Nenhum atendimento encontrado</h3>
      <p class="text-muted text-sm">Registre o primeiro caso de proteção à criança e ao adolescente.</p>
      <a href="<?= url('/atendimentos/novo') ?>" class="btn btn-primary" style="margin-top:16px;">➕ Registrar Atendimento</a>
    </div>
  <?php else: ?>
  <div class="table-wrapper" style="border:none;border-radius:0;">
    <table class="data-table">
      <thead>
        <tr>
          <th>Protocolo</th>
          <th>Tipo de Demanda</th>
          <th>Prioridade</th>
          <th>Status</th>
          <th>Conselheiro</th>
          <th>IA</th>
          <th>Data</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($atendimentos as $a): ?>
        <tr>
          <td>
            <a href="<?= url('/atendimentos/' . $a['id']) ?>"
               style="color:var(--accent);font-weight:700;text-decoration:none;">
              <?= e($a['numero_protocolo']) ?>
            </a>
          </td>
          <td>
            <div style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
              <?= e($a['tipo_demanda']) ?>
            </div>
          </td>
          <td>
            <div style="display:flex;align-items:center;gap:6px;">
              <div class="prioridade-dot <?= $a['prioridade'] ?>"></div>
              <span class="badge badge-<?= $a['prioridade'] ?>"><?= ucfirst($a['prioridade']) ?></span>
            </div>
          </td>
          <td>
            <span class="badge badge-<?= $a['status'] ?>"><?= ucfirst(str_replace('_',' ',$a['status'])) ?></span>
          </td>
          <td class="text-sm"><?= e($a['conselheiro']) ?></td>
          <td>
            <?php if ($a['analise_ia']): ?>
              <span title="Análise IA disponível" style="font-size:18px;">🤖</span>
            <?php else: ?>
              <span title="Sem análise IA" style="font-size:18px;opacity:0.3;">🤖</span>
            <?php endif; ?>
          </td>
          <td class="text-sm text-muted">
            <?= date('d/m/Y', strtotime($a['created_at'])) ?>
          </td>
          <td>
            <div style="display:flex;gap:6px;">
              <a href="<?= url('/atendimentos/' . $a['id']) ?>" class="btn btn-secondary btn-sm">Ver</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
