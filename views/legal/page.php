<div class="legal-content">

  <!-- Cabeçalho do documento -->
  <h1><?= e($pagina['titulo'] ?: $title) ?></h1>
  <div class="legal-meta">
    <span class="badge badge-lgpd">🛡️ VivensiCT</span>
    <span class="badge badge-media">ECA/SUAS · LGPD</span>
    <?php if (!empty($pagina['updated_at'])): ?>
      <span class="text-xs text-muted">
        📅 Última atualização: <?= date('d/m/Y \à\s H\hi', strtotime($pagina['updated_at'])) ?>
      </span>
    <?php endif; ?>
  </div>

  <!-- Conteúdo editável pelo Super Admin -->
  <?php if (empty(trim($pagina['conteudo'] ?? ''))): ?>
    <div class="info-box">
      ℹ️ Esta página ainda não foi configurada. O administrador do sistema deve acessar o
      <strong>Painel Admin → Páginas Legais</strong> para editar o conteúdo.
    </div>
  <?php else: ?>
    <?= $pagina['conteudo'] ?>
  <?php endif; ?>

</div>
