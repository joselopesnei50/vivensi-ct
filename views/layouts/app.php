<!DOCTYPE html>
<html lang="pt-BR" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($title ?? 'Dashboard') ?> — VivensiCT</title>
<link rel="icon" type="image/png" href="<?= url('/images/favicon.png') ?>">
<!-- Aplica tema antes de qualquer render (evita flash) -->
<script>
  (function(){
    var t = localStorage.getItem('vct_theme') || 'dark';
    document.documentElement.setAttribute('data-theme', t);
  })();
</script>
<link rel="stylesheet" href="<?= url('/css/app.css') ?>">
<script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<meta name="csrf" content="<?= \App\Core\Request::csrf() ?>">
</head>
<body>
<?php
  $user     = \App\Core\Auth::user();
  $role     = \App\Core\Auth::role();
  $initials = strtoupper(substr($user['nome'] ?? 'U', 0, 2));
  $curPath  = strtok($_SERVER['REQUEST_URI'], '?');
  $base     = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

  function isActive(string $segment): string {
    global $curPath, $base;
    return str_contains($curPath, $base . '/' . ltrim($segment, '/')) ? 'active' : '';
  }
?>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
  <!-- Logo -->
  <div class="sidebar-logo">
    <img src="<?= url('/images/logo.png') ?>" alt="VivensiCT" style="height:38px;width:auto;object-fit:contain;flex-shrink:0;">
    <div>
      <div class="logo-text">VivensiCT</div>
      <div class="logo-sub">ECA · SUAS · LGPD</div>
    </div>
  </div>

  <!-- Navigation -->
  <nav class="sidebar-nav">
    <div class="nav-section-label">Principal</div>

    <a href="<?= url('/dashboard') ?>" class="nav-item <?= isActive('/dashboard') ?>">
      <span class="nav-icon">📊</span> Dashboard
    </a>
    <a href="<?= url('/atendimentos') ?>" class="nav-item <?= isActive('/atendimentos') ?>">
      <span class="nav-icon">📋</span> Atendimentos
    </a>
    <a href="<?= url('/medidas') ?>" class="nav-item <?= isActive('/medidas') ?>">
      <span class="nav-icon">⚖️</span> Medidas de Proteção
    </a>

    <div class="nav-section-label">Ferramentas</div>

    <a href="<?= url('/rede-servicos') ?>" class="nav-item <?= isActive('/rede-servicos') ?>">
      <span class="nav-icon">🌐</span> Rede de Serviços
    </a>
    <a href="<?= url('/atendimentos/novo') ?>" class="nav-item <?= isActive('/atendimentos/novo') ?>">
      <span class="nav-icon">➕</span> Novo Atendimento
    </a>
    <a href="<?= url('/chamados') ?>" class="nav-item <?= isActive('/chamados') ?>">
      <span class="nav-icon">🎫</span> Suporte
    </a>

    <?php if ($role === 'super_admin' || $role === 'admin'): ?>
    <div class="nav-section-label">Administração</div>
    <a href="<?= url('/admin') ?>" class="nav-item <?= isActive('/admin') ?>">
      <span class="nav-icon">⚙️</span> Painel Admin
    </a>
    <?php endif; ?>
  </nav>

  <!-- Sidebar Footer -->
  <div class="sidebar-footer">
    <!-- Donation Card -->
    <div class="donation-card">
      <h4>💙 Apoie o VivensiCT</h4>
      <p>Sistema gratuito para Conselhos Tutelares de todo o Brasil</p>
      <a href="#" class="btn-donation" onclick="openDonation();return false;">Contribuir via PIX</a>
    </div>

    <!-- Legal Links -->
    <div style="display:flex;justify-content:center;gap:14px;padding:8px 0;margin-top:6px;">
      <a href="<?= url('/privacidade') ?>" target="_blank"
         style="font-size:11px;color:var(--text-muted);text-decoration:none;transition:color .15s;"
         onmouseover="this.style.color='var(--text-secondary)'" onmouseout="this.style.color='var(--text-muted)'">
        🔒 Privacidade
      </a>
      <a href="<?= url('/termos-de-uso') ?>" target="_blank"
         style="font-size:11px;color:var(--text-muted);text-decoration:none;transition:color .15s;"
         onmouseover="this.style.color='var(--text-secondary)'" onmouseout="this.style.color='var(--text-muted)'">
        📋 Termos de Uso
      </a>
    </div>

    <!-- User Card -->
    <div class="user-card" style="margin-top:4px;">
      <div class="user-avatar"><?= $initials ?></div>
      <div class="user-info" style="flex:1;overflow:hidden;">
        <div class="user-name" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= e($user['nome']) ?></div>
        <div class="user-role"><?= e(ucfirst(str_replace('_', ' ', $role))) ?></div>
      </div>
      <a href="<?= url('/logout') ?>"
         style="color:var(--text-muted);font-size:18px;text-decoration:none;flex-shrink:0;width:28px;height:28px;display:flex;align-items:center;justify-content:center;border-radius:6px;transition:all 0.15s;"
         title="Sair"
         onmouseover="this.style.background='var(--bg-card-hover)';this.style.color='var(--danger)'"
         onmouseout="this.style.background='';this.style.color='var(--text-muted)'">↪</a>
    </div>
  </div>
</aside>

<!-- MAIN CONTENT -->
<main class="main-content">
  <!-- Topbar -->
  <header class="topbar">
    <div class="topbar-title">
      <h1><?= e($title ?? 'Dashboard') ?></h1>
      <?php if (isset($subtitle)): ?>
        <p><?= e($subtitle) ?></p>
      <?php endif; ?>
    </div>
    <div class="topbar-actions">
      <!-- Mobile menu toggle -->
      <button id="sidebarToggle" class="btn btn-ghost btn-icon" title="Menu">☰</button>

      <!-- Theme Toggle -->
      <button class="theme-toggle" onclick="toggleTheme()" title="Alternar tema claro/escuro">
        <span class="icon-dark">🌙</span>
        <span class="icon-light">☀️</span>
      </button>

      <a href="<?= url('/atendimentos/novo') ?>" class="btn btn-primary btn-sm topbar-new-btn">
        ➕ Novo Atendimento
      </a>
    </div>
  </header>

  <!-- Flash Messages -->
  <div class="flash-container">
    <?php if ($suc = flash('success')): ?>
      <div class="alert alert-success">✅ <?= $suc ?></div>
    <?php endif; ?>
    <?php if ($err = flash('error')): ?>
      <div class="alert alert-error">⚠️ <?= e($err) ?></div>
    <?php endif; ?>
    <?php if ($info = flash('info')): ?>
      <div class="alert alert-info">ℹ️ <?= e($info) ?></div>
    <?php endif; ?>
  </div>

  <!-- Page Content -->
  <div class="page-body">
    <?= $content ?>
  </div>
</main>

<!-- SIDEBAR OVERLAY (mobile backdrop) -->
<div id="sidebarOverlay" class="sidebar-overlay"></div>

<!-- DONATION MODAL -->
<div id="donationModal" class="modal-overlay hidden">
  <div class="modal" style="max-width:460px;">
    <div class="modal-header">
      <h3>💙 Apoiar o VivensiCT</h3>
      <button onclick="closeDonation()" class="modal-close">×</button>
    </div>
    <div class="modal-body" style="text-align:center;">
      <p style="color:var(--text-secondary);margin-bottom:20px;font-size:14px;line-height:1.6;">
        O VivensiCT é um projeto social que apoia o trabalho de Conselheiros Tutelares
        em todo o Brasil. Sua contribuição ajuda a manter e evoluir o sistema.
      </p>
      <div style="background:var(--bg-secondary);border:1px solid var(--border);border-radius:var(--radius);padding:22px;margin-bottom:18px;">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);margin-bottom:6px;">Chave PIX</div>
        <div style="font-size:17px;font-weight:700;letter-spacing:0.5px;color:var(--text-primary);">guardiao@digital.org.br</div>
        <button onclick="copyPix()" class="btn btn-secondary btn-sm" style="margin-top:12px;">📋 Copiar Chave</button>
      </div>
      <p style="font-size:12px;color:var(--text-muted);">Qualquer valor é bem-vindo! Obrigado pelo apoio. 🙏</p>
    </div>
  </div>
</div>

<script>
// Mermaid
(function(){
  const theme = document.documentElement.getAttribute('data-theme');
  mermaid.initialize({
    theme: theme === 'light' ? 'default' : 'dark',
    themeVariables: theme === 'light'
      ? {}
      : {
          darkMode: true,
          background: '#0a0a0f',
          primaryColor: '#6366f1',
          primaryTextColor: '#ffffff',
          lineColor: '#4a4a6a',
          fontSize: '14px',
        },
    startOnLoad: true,
  });
})();

function openDonation()  { document.getElementById('donationModal').classList.remove('hidden'); }
function closeDonation() { document.getElementById('donationModal').classList.add('hidden'); }
function copyPix() {
  navigator.clipboard.writeText('guardiao@digital.org.br').then(() => showToast('Chave PIX copiada!', 'success'));
}

// Fechar modal ao clicar no overlay
document.getElementById('donationModal')?.addEventListener('click', function(e) {
  if (e.target === this) closeDonation();
});
</script>

<style>
@keyframes slideIn {
  from { transform: translateX(100px); opacity: 0; }
  to   { transform: translateX(0);     opacity: 1; }
}
/* Error field highlight */
.error-field {
  border-color: var(--danger) !important;
  box-shadow: 0 0 0 3px rgba(239,68,68,0.15) !important;
}
</style>

<?php \App\Core\View::partial('cookie-consent'); ?>
<script src="<?= url('/js/app.js') ?>"></script>
</body>
</html>
