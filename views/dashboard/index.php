<?php
  $title    = 'Dashboard';
  $subtitle = 'Visão geral — ' . date('d/m/Y');
  $user     = \App\Core\Auth::user();
  $hour     = (int) date('H');
  $greeting = $hour < 12 ? 'Bom dia' : ($hour < 18 ? 'Boa tarde' : 'Boa noite');
?>

<!-- Saudação -->
<div style="margin-bottom:22px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
  <div>
    <h2 style="margin:0 0 4px;font-size:20px;font-weight:800;letter-spacing:-0.3px;">
      <?= $greeting ?>, <?= e(explode(' ', $user['nome'])[0]) ?>! 👋
    </h2>
    <p class="text-muted text-sm" style="margin:0;">
      <?= date('l, d \d\e F \d\e Y') ?> &nbsp;·&nbsp; Sistema ECA/SUAS ativo
    </p>
  </div>
  <a href="<?= url('/atendimentos/novo') ?>" class="btn btn-primary">
    ➕ Novo Atendimento com IA
  </a>
</div>

<!-- Stats Grid -->
<div class="grid grid-stat mb-6">

  <div class="stat-card urgente">
    <div class="stat-icon red">🚨</div>
    <div class="stat-value"><?= $urgentes ?></div>
    <div class="stat-label">Casos Urgentes</div>
    <?php if ($urgentes > 0): ?>
    <a href="<?= url('/atendimentos?prioridade=urgente') ?>"
       style="font-size:11px;color:var(--danger);text-decoration:none;margin-top:8px;display:inline-block;">
      Ver todos →
    </a>
    <?php endif; ?>
  </div>

  <div class="stat-card">
    <div class="stat-icon blue">📂</div>
    <div class="stat-value"><?= $abertos ?></div>
    <div class="stat-label">Em Aberto</div>
    <a href="<?= url('/atendimentos?status=aberto') ?>"
       style="font-size:11px;color:var(--info);text-decoration:none;margin-top:8px;display:inline-block;">
      Ver todos →
    </a>
  </div>

  <div class="stat-card">
    <div class="stat-icon purple">📁</div>
    <div class="stat-value"><?= $totalAtendimentos ?></div>
    <div class="stat-label">Total de Casos</div>
    <a href="<?= url('/atendimentos') ?>"
       style="font-size:11px;color:var(--accent);text-decoration:none;margin-top:8px;display:inline-block;">
      Ver todos →
    </a>
  </div>

  <div class="stat-card success">
    <div class="stat-icon green">👥</div>
    <div class="stat-value"><?= $totalUsuarios ?></div>
    <div class="stat-label">Conselheiros</div>
  </div>

</div>

<!-- Alertas LGPD -->
<?php if (!empty($docsExpirando)): ?>
<div class="alert alert-warning mb-6" style="flex-direction:column;gap:8px;">
  <div style="display:flex;align-items:center;gap:8px;">
    <strong>⏰ Alerta LGPD:</strong>
    <?= count($docsExpirando) ?> documento(s) expirarão nas próximas 24h e serão removidos automaticamente.
  </div>
  <div style="display:flex;flex-wrap:wrap;gap:6px;">
    <?php foreach ($docsExpirando as $d): ?>
      <a href="<?= url('/atendimentos/' . $d['atendimento_id']) ?>"
         class="badge badge-alta" style="text-decoration:none;">
        <?= e($d['numero_protocolo']) ?>
      </a>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<!-- Grid Principal -->
<div class="grid grid-2" style="gap:22px;align-items:start;">

  <!-- Atendimentos Recentes -->
  <div class="card">
    <div class="card-header">
      <h3>📋 Atendimentos Recentes</h3>
      <a href="<?= url('/atendimentos') ?>" class="btn btn-ghost btn-sm">Ver todos</a>
    </div>
    <?php if (empty($ultimosAtendimentos)): ?>
      <div style="padding:48px 24px;text-align:center;color:var(--text-muted);">
        <div style="font-size:48px;margin-bottom:12px;opacity:.5;">📭</div>
        <p style="margin:0 0 16px;font-size:14px;">Nenhum atendimento registrado ainda.</p>
        <a href="<?= url('/atendimentos/novo') ?>" class="btn btn-primary btn-sm">
          Criar primeiro atendimento
        </a>
      </div>
    <?php else: ?>
      <div style="overflow-x:auto;">
        <table class="data-table">
          <thead>
            <tr>
              <th>Protocolo</th>
              <th>Demanda</th>
              <th>Prioridade</th>
              <th>Status</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($ultimosAtendimentos as $a): ?>
            <tr>
              <td>
                <span style="font-weight:700;color:var(--accent);font-size:13px;">
                  <?= e($a['numero_protocolo']) ?>
                </span>
              </td>
              <td>
                <div style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:13px;">
                  <?= e($a['tipo_demanda']) ?>
                </div>
              </td>
              <td>
                <div style="display:flex;align-items:center;gap:6px;">
                  <span class="prioridade-dot <?= $a['prioridade'] ?>"></span>
                  <span class="badge badge-<?= $a['prioridade'] ?>"><?= ucfirst($a['prioridade']) ?></span>
                </div>
              </td>
              <td>
                <span class="badge badge-<?= $a['status'] ?>">
                  <?= ucfirst(str_replace('_', ' ', $a['status'])) ?>
                </span>
              </td>
              <td>
                <a href="<?= url('/atendimentos/' . $a['id']) ?>"
                   class="btn btn-ghost btn-sm btn-icon" title="Abrir caso">→</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

  <!-- Painel Lateral -->
  <div class="flex-col" style="gap:18px;">

    <!-- Ações Rápidas -->
    <div class="card">
      <div class="card-header">
        <h3>⚡ Ações Rápidas</h3>
      </div>
      <div class="card-body flex-col" style="gap:8px;">
        <a href="<?= url('/atendimentos/novo') ?>" class="btn btn-primary btn-full">
          🤖 Novo Atendimento + Análise IA
        </a>
        <a href="<?= url('/medidas') ?>" class="btn btn-secondary btn-full">
          ⚖️ Medidas de Proteção
        </a>
        <a href="<?= url('/rede-servicos') ?>" class="btn btn-secondary btn-full">
          🌐 Rede de Serviços Municipal
        </a>
        <?php if (\App\Core\Auth::isAdmin()): ?>
        <a href="<?= url('/admin') ?>" class="btn btn-ghost btn-full">
          ⚙️ Painel Administrativo
        </a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Base Legal -->
    <div class="card">
      <div class="card-header">
        <h3>📖 Base Jurídica</h3>
      </div>
      <div class="card-body flex-col" style="gap:8px;">
        <?php
          $leis = [
            ['📜', 'ECA — Lei 8.069/90',     'Estatuto da Criança e do Adolescente',  'var(--info)'],
            ['🏛️', 'SUAS — LOAS 8.742/93',   'Sistema Único de Assistência Social',   'var(--success)'],
            ['⚖️', 'Lei Henry Borel 14.344/22','Violência Doméstica e Familiar',       'var(--danger)'],
            ['🔒', 'LGPD — Lei 13.709/18',   'Proteção de Dados Pessoais',            'var(--accent)'],
            ['✍️', 'Lei 14.063/20',           'Assinatura Eletrônica',                 'var(--warning)'],
          ];
          foreach ($leis as [$icon, $nome, $desc, $color]): ?>
        <div style="display:flex;align-items:center;gap:12px;padding:10px 12px;background:var(--bg-secondary);border:1px solid var(--border);border-radius:var(--radius-sm);transition:all .15s;"
             onmouseover="this.style.borderColor='var(--border-light)'"
             onmouseout="this.style.borderColor='var(--border)'">
          <span style="font-size:18px;width:26px;text-align:center;"><?= $icon ?></span>
          <div>
            <div style="font-size:12px;font-weight:700;color:<?= $color ?>;"><?= $nome ?></div>
            <div class="text-xs text-muted"><?= $desc ?></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- LGPD Status -->
    <div class="info-box success" style="border-radius:var(--radius);">
      <div style="font-size:24px;">🔐</div>
      <div>
        <div style="font-size:13px;font-weight:700;color:var(--success);">Proteção de Dados Ativa</div>
        <div class="text-xs text-muted">Expurgo automático em 3 dias · Criptografia AES-128 · LGPD</div>
      </div>
    </div>

  </div>
</div>
