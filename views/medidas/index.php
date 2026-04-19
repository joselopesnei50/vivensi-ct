<?php $title = 'Medidas de Proteção'; $subtitle = 'Art. 101 do ECA — Medidas aplicadas'; ?>

<div class="flex-between mb-6">
  <div></div>
  <a href="<?= url('/atendimentos') ?>" class="btn btn-secondary">← Atendimentos</a>
</div>

<!-- Stats rápidos -->
<div class="grid grid-4 mb-6">
  <?php
    $total    = count($medidas);
    $aplicadas = array_filter($medidas, fn($m) => $m['status'] === 'aplicada');
    $cumpridas = array_filter($medidas, fn($m) => $m['status'] === 'cumprida');
    $vencendo  = array_filter($medidas, fn($m) => $m['prazo_cumprimento'] && strtotime($m['prazo_cumprimento']) < strtotime('+7 days') && $m['status'] === 'aplicada');
  ?>
  <div class="stat-card">
    <div class="stat-icon purple">⚖️</div>
    <div class="stat-value"><?= $total ?></div>
    <div class="stat-label">Total de Medidas</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon blue">📋</div>
    <div class="stat-value"><?= count($aplicadas) ?></div>
    <div class="stat-label">Em Aplicação</div>
  </div>
  <div class="stat-card success">
    <div class="stat-icon green">✅</div>
    <div class="stat-value"><?= count($cumpridas) ?></div>
    <div class="stat-label">Cumpridas</div>
  </div>
  <div class="stat-card <?= count($vencendo) > 0 ? 'urgente' : '' ?>">
    <div class="stat-icon red">⏰</div>
    <div class="stat-value"><?= count($vencendo) ?></div>
    <div class="stat-label">Prazo em 7 dias</div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h3>⚖️ Medidas de Proteção (ECA Art. 101)</h3>
  </div>

  <?php if (empty($medidas)): ?>
    <div style="padding:60px;text-align:center;">
      <div style="font-size:48px;margin-bottom:16px;">⚖️</div>
      <h3 style="color:var(--text-secondary);margin:0 0 8px;">Nenhuma medida registrada</h3>
      <p class="text-muted text-sm">Medidas de proteção são aplicadas dentro de cada atendimento.</p>
      <a href="<?= url('/atendimentos') ?>" class="btn btn-primary" style="margin-top:16px;">Ver Atendimentos</a>
    </div>
  <?php else: ?>
    <div class="table-wrapper" style="border:none;border-radius:0;">
      <table class="data-table">
        <thead>
          <tr>
            <th>Protocolo</th>
            <th>Medida de Proteção</th>
            <th>Base Legal</th>
            <th>Prazo</th>
            <th>Status</th>
            <th>Conselheiro</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($medidas as $m):
            $vencida = $m['prazo_cumprimento'] && strtotime($m['prazo_cumprimento']) < time() && $m['status'] === 'aplicada';
            $urgente = $m['prazo_cumprimento'] && strtotime($m['prazo_cumprimento']) < strtotime('+7 days') && $m['status'] === 'aplicada';
          ?>
          <tr <?= $vencida ? "style='background:rgba(239,68,68,0.05);'" : ($urgente ? "style='background:rgba(245,158,11,0.05);'" : '') ?>>
            <td>
              <a href="<?= url('/atendimentos') ?>" style="color:var(--accent);font-weight:600;text-decoration:none;">
                <?= e($m['numero_protocolo']) ?>
              </a>
            </td>
            <td>
              <div style="max-width:240px;">
                <div class="fw-600 text-sm"><?= e($m['tipo_medida']) ?></div>
                <?php if ($m['descricao']): ?>
                <div class="text-xs text-muted" style="margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                  <?= e($m['descricao']) ?>
                </div>
                <?php endif; ?>
              </div>
            </td>
            <td><span class="badge badge-media text-xs"><?= e($m['artigo_eca']) ?></span></td>
            <td>
              <?php if ($m['prazo_cumprimento']): ?>
                <span style="color:<?= $vencida ? 'var(--danger)' : ($urgente ? 'var(--warning)' : 'var(--text-secondary)') ?>;font-size:13px;">
                  <?= $vencida ? '⚠️ ' : ($urgente ? '⏰ ' : '') ?>
                  <?= date('d/m/Y', strtotime($m['prazo_cumprimento'])) ?>
                </span>
              <?php else: ?>
                <span class="text-muted text-sm">—</span>
              <?php endif; ?>
            </td>
            <td>
              <select onchange="updateMedidaStatus(<?= $m['id'] ?>, this.value)"
                      class="form-control" style="padding:4px 8px;font-size:12px;width:auto;">
                <?php foreach (['aplicada','cumprida','descumprida','suspensa'] as $st): ?>
                <option value="<?= $st ?>" <?= $m['status'] === $st ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
                <?php endforeach; ?>
              </select>
            </td>
            <td class="text-sm text-muted"><?= e($m['conselheiro']) ?></td>
            <td>
              <a href="<?= url('/atendimentos') ?>" class="btn btn-secondary btn-sm">Ver Caso</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<!-- Referência Legal -->
<div class="card mt-6">
  <div class="card-header">
    <h3>📖 Referência — ECA Art. 101: Medidas de Proteção</h3>
  </div>
  <div class="card-body">
    <div class="grid grid-2" style="gap:12px;">
      <?php
      $arts = [
        ['Art. 101, I',   'Encaminhamento aos pais ou responsável'],
        ['Art. 101, II',  'Orientação, apoio e acompanhamento temporários'],
        ['Art. 101, III', 'Matrícula e frequência em estabelecimento de ensino'],
        ['Art. 101, IV',  'Inclusão em programa de auxílio à família'],
        ['Art. 101, V',   'Requisição de tratamento médico, psicológico ou psiquiátrico'],
        ['Art. 101, VI',  'Inclusão em programa de proteção à família'],
        ['Art. 101, VII', 'Acolhimento institucional'],
        ['Art. 101, VIII','Inclusão em programa de acolhimento familiar'],
        ['Art. 101, IX',  'Colocação em família substituta'],
      ];
      foreach ($arts as [$art, $desc]): ?>
      <div style="display:flex;gap:12px;padding:10px;background:var(--bg-secondary);border-radius:var(--radius-sm);">
        <span class="badge badge-media" style="flex-shrink:0;height:fit-content;"><?= $art ?></span>
        <span class="text-sm" style="color:var(--text-secondary);"><?= $desc ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<script>
function updateMedidaStatus(id, status) {
  fetch('<?= url('/medidas/') ?>' + id + '/status', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    body: JSON.stringify({ _token: '<?= \App\Core\Request::csrf() ?>', status }),
  })
  .then(r => r.json())
  .then(() => showToast('✅ Status atualizado!', 'success'));
}
</script>
