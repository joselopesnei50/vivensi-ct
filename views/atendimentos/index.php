<?php $title = 'Atendimentos'; $subtitle = 'Gerenciamento de casos ativos'; ?>

<!-- Filters -->
<div class="card card-premium mb-6">
  <div class="card-body" style="padding:16px 24px;">
    <form method="GET" action="<?= url('/atendimentos') ?>"
          class="filter-form"
          style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
      <div style="flex:1;min-width:200px;position:relative;">
        <i class="fas fa-search" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:14px;"></i>
        <input type="text" name="busca" class="form-control" placeholder="Buscar por protocolo ou demanda..."
               value="<?= e($busca) ?>" style="padding-left:40px; border-radius:50px;">
      </div>

      <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
        <select name="status" class="form-control" style="min-width:160px;width:auto;border-radius:50px;">
          <option value="">Todos os Status</option>
          <?php foreach (['aberto','em_andamento','encerrado','arquivado'] as $s): ?>
          <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= strtoupper(str_replace('_',' ',$s)) ?></option>
          <?php endforeach; ?>
        </select>

        <select name="prioridade" class="form-control" style="min-width:160px;width:auto;border-radius:50px;">
          <option value="">Todas as Prioridades</option>
          <?php foreach (['urgente','alta','media','baixa'] as $p): ?>
          <option value="<?= $p ?>" <?= $prioridade === $p ? 'selected' : '' ?>><?= strtoupper($p) ?></option>
          <?php endforeach; ?>
        </select>

        <button type="submit" class="btn btn-primary" style="border-radius:50px; padding:8px 22px;">
          <i class="fas fa-filter me-1"></i> FILTRAR
        </button>
        <a href="<?= url('/atendimentos') ?>" class="btn btn-ghost" style="border-radius:50px; padding:8px 22px;">LIMPAR</a>
      </div>

      <a href="<?= url('/atendimentos/novo') ?>" class="btn btn-primary filter-new-btn" style="margin-left:auto; border-radius:50px; box-shadow: var(--premium-glow);">
        <i class="fas fa-plus me-1"></i> NOVO CASO
      </a>
    </form>
  </div>
</div>

<!-- Table -->
<div class="card card-premium">
  <div class="card-header" style="border-bottom: 1px solid var(--glass-border); padding: 20px 24px;">
    <div style="display:flex;align-items:center;gap:14px;">
      <div class="section-icon purple" style="background: var(--accent); color:#fff; box-shadow: var(--premium-glow);">📋</div>
      <div>
        <h3 style="font-size:16px;font-weight:800;margin:0;">Casos Registrados</h3>
        <span class="text-muted text-xs fw-600"><?= count($atendimentos) ?> REGISTROS ENCONTRADOS</span>
      </div>
    </div>
  </div>

  <?php if (empty($atendimentos)): ?>
    <div style="padding:80px 24px;text-align:center;">
      <div style="font-size:60px;margin-bottom:20px;opacity:.2;">📭</div>
      <h3 style="color:var(--text-secondary);margin:0 0 10px; font-weight:800;">Nenhum atendimento encontrado</h3>
      <p class="text-muted text-sm" style="font-weight:500;">Ajuste seus filtros ou registre o primeiro caso.</p>
      <a href="<?= url('/atendimentos/novo') ?>" class="btn btn-primary" style="margin-top:20px; border-radius:50px;">
        <i class="fas fa-plus me-1"></i> REGISTRAR ATENDIMENTO
      </a>
    </div>
  <?php else: ?>
  <div class="table-wrapper" style="border:none;border-radius:0;">
    <table class="data-table">
      <thead style="background: rgba(255,255,255,0.02);">
        <tr>
          <th style="padding-left:24px;">Protocolo</th>
          <th>Demanda</th>
          <th>Prioridade</th>
          <th>Status</th>
          <th>IA</th>
          <th>Data</th>
          <th class="text-end" style="padding-right:24px;">Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($atendimentos as $a): ?>
        <tr>
          <td style="padding-left:24px;">
            <a href="<?= url('/atendimentos/' . $a['id']) ?>"
               style="color:var(--accent);font-weight:900;text-decoration:none; letter-spacing:0.5px;">
              <?= e($a['numero_protocolo']) ?>
            </a>
          </td>
          <td>
            <div style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; font-weight:600;">
              <?= e($a['tipo_demanda']) ?>
            </div>
            <div class="text-xs text-muted">Conselheiro: <?= e($a['conselheiro']) ?></div>
          </td>
          <td>
            <div style="display:flex;align-items:center;gap:8px;">
              <div class="prioridade-dot <?= $a['prioridade'] ?>" style="box-shadow: 0 0 8px currentColor;"></div>
              <span class="badge badge-<?= $a['prioridade'] ?>" style="border-radius:4px; font-size:10px;"><?= strtoupper($a['prioridade']) ?></span>
            </div>
          </td>
          <td>
            <span class="badge badge-<?= $a['status'] ?>" style="background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border);"><?= strtoupper(str_replace('_',' ',$a['status'])) ?></span>
          </td>
          <td style="text-align:center;">
            <?php if ($a['analise_ia']): ?>
              <span title="Análise IA disponível" style="font-size:18px; filter: drop-shadow(0 0 4px var(--accent));">🤖</span>
            <?php else: ?>
              <span title="Sem análise IA" style="font-size:18px;opacity:0.2;">🤖</span>
            <?php endif; ?>
          </td>
          <td class="text-xs text-muted fw-600">
            <?= date('d/m/Y', strtotime($a['created_at'])) ?>
          </td>
          <td class="text-end" style="padding-right:24px;">
            <div style="display:flex;gap:6px;justify-content:flex-end;">
              <a href="<?= url('/atendimentos/' . $a['id']) ?>" class="btn btn-ghost btn-sm btn-icon" style="border-radius:50%; width:32px; height:32px;">
                <i class="fas fa-eye"></i>
              </a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
