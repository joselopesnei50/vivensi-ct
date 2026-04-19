<?php $title = 'Suporte'; $subtitle = 'Chamados de atendimento'; ?>

<div class="grid grid-2" style="gap:24px;align-items:start;">

  <!-- Formulário novo chamado -->
  <div class="card">
    <div class="card-header">
      <h3>🎫 Abrir Chamado</h3>
    </div>
    <div class="card-body">
      <form method="POST" action="<?= url('/chamados') ?>" data-loading>
        <?= csrf_field() ?>
        <div class="form-group">
          <label class="form-label">Título do Chamado *</label>
          <input type="text" name="titulo" class="form-control" required placeholder="Descreva o problema em poucas palavras">
        </div>
        <div class="grid grid-2">
          <div class="form-group">
            <label class="form-label">Tipo</label>
            <select name="tipo" class="form-control">
              <option value="bug">🐛 Bug / Erro</option>
              <option value="duvida">❓ Dúvida</option>
              <option value="financeiro">💳 Financeiro</option>
              <option value="outro">📌 Outro</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Prioridade</label>
            <select name="prioridade" class="form-control">
              <option value="baixa">🟢 Baixa</option>
              <option value="media" selected>🟡 Média</option>
              <option value="alta">🟠 Alta</option>
              <option value="urgente">🔴 Urgente</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Descrição detalhada *</label>
          <textarea name="descricao" class="form-control" rows="5" required
                    placeholder="Descreva o problema com o máximo de detalhes possível…" data-counter="2000"></textarea>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;">
          📨 Enviar Chamado
        </button>
      </form>
    </div>
  </div>

  <!-- Lista de chamados -->
  <div style="display:flex;flex-direction:column;gap:16px;">
    <div class="card-header" style="padding:0 0 8px;">
      <h3>Meus Chamados</h3>
      <span class="badge badge-media"><?= count($chamados) ?> total</span>
    </div>

    <?php if (empty($chamados)): ?>
      <div class="info-box">
        <p class="text-sm text-muted" style="margin:0;">Nenhum chamado aberto ainda. Use o formulário ao lado para solicitar suporte.</p>
      </div>
    <?php else: ?>
      <?php foreach ($chamados as $c): ?>
      <?php
        $statusBadge = match($c['status']) {
          'aberto'        => 'urgente',
          'em_atendimento'=> 'media',
          'resolvido'     => 'encerrado',
          default         => 'arquivado',
        };
        $statusLabel = match($c['status']) {
          'aberto'        => '🔴 Aberto',
          'em_atendimento'=> '🟡 Em Atendimento',
          'resolvido'     => '✅ Resolvido',
          default         => '🔒 Fechado',
        };
      ?>
      <div class="card" style="padding:20px;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;margin-bottom:10px;">
          <div class="fw-600 text-sm"><?= e($c['titulo']) ?></div>
          <span class="badge badge-<?= $statusBadge ?>" style="white-space:nowrap;"><?= $statusLabel ?></span>
        </div>
        <p class="text-xs text-muted" style="margin:0 0 10px;line-height:1.6;"><?= nl2br(e(substr($c['descricao'], 0, 180))) ?>…</p>
        <div style="display:flex;gap:8px;flex-wrap:wrap;font-size:11px;color:var(--text-muted);">
          <span><?= date('d/m/Y', strtotime($c['created_at'])) ?></span>
          <span>·</span>
          <span><?= ucfirst($c['tipo']) ?></span>
          <span>·</span>
          <span>Prioridade: <?= ucfirst($c['prioridade']) ?></span>
        </div>
        <?php if ($c['resposta']): ?>
        <div style="margin-top:12px;background:var(--bg-secondary);border-radius:var(--radius-sm);padding:12px 14px;border-left:3px solid var(--success);">
          <div class="text-xs fw-600" style="color:var(--success);margin-bottom:4px;">✅ Resposta da equipe VivensiCT:</div>
          <p class="text-xs" style="margin:0;line-height:1.65;"><?= nl2br(e($c['resposta'])) ?></p>
        </div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

</div>
