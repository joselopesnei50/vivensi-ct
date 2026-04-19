<!DOCTYPE html>
<html lang="pt-BR" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($title ?? 'VivensiCT') ?> — VivensiCT</title>
<link rel="icon" type="image/png" href="<?= url('/images/favicon.png') ?>">
<script>
  (function(){
    var t = localStorage.getItem('vct_theme') || 'dark';
    document.documentElement.setAttribute('data-theme', t);
  })();
</script>
<link rel="stylesheet" href="<?= url('/css/app.css') ?>">
<style>
/* ── Public Header ───────────────────── */
.public-header {
  background: var(--bg-secondary);
  border-bottom: 1px solid var(--border);
  padding: 0 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  height: 56px;
  position: sticky;
  top: 0;
  z-index: 50;
}
.public-logo {
  display: flex; align-items: center; gap: 10px;
  text-decoration: none; color: var(--text-primary);
  flex-shrink: 0;
}
.public-logo-icon {
  width: 32px; height: 32px;
  background: var(--accent);
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-size: 15px;
  box-shadow: 0 2px 8px rgba(99,102,241,.35);
  flex-shrink: 0;
}
.public-logo-text { font-size: 15px; font-weight: 800; letter-spacing: -0.3px; }
.public-nav { display: flex; align-items: center; gap: 4px; }
.public-nav-link {
  font-size: 13px; font-weight: 500;
  color: var(--text-secondary);
  text-decoration: none;
  padding: 6px 10px;
  border-radius: var(--radius-sm);
  transition: all .15s;
  white-space: nowrap;
}
.public-nav-link:hover { color: var(--text-primary); background: var(--bg-card-hover); }
/* ── Page Body ───────────────────────── */
.public-body {
  min-height: calc(100vh - 56px - 70px);
  background: var(--bg-primary);
}
.public-main {
  max-width: 820px;
  margin: 0 auto;
  padding: 40px 24px 60px;
}
/* ── Footer ──────────────────────────── */
.public-footer {
  background: var(--bg-secondary);
  border-top: 1px solid var(--border);
  padding: 20px 24px;
  text-align: center;
  font-size: 12px;
  color: var(--text-muted);
}
.public-footer a { color: var(--text-muted); text-decoration: none; margin: 0 10px; transition: color .15s; }
.public-footer a:hover { color: var(--text-secondary); }
/* ── Legal Content Typography ─────────── */
.legal-content { line-height: 1.9; color: var(--text-secondary); font-size: 14px; }
.legal-content h1 { font-size: 26px; font-weight: 800; color: var(--text-primary); margin: 0 0 6px; letter-spacing: -0.5px; }
.legal-content h2 { font-size: 17px; font-weight: 700; color: var(--text-primary); margin: 32px 0 10px; padding-top: 8px; }
.legal-content h3 { font-size: 15px; font-weight: 600; color: var(--text-primary); margin: 20px 0 8px; }
.legal-content p { margin: 0 0 14px; }
.legal-content ul, .legal-content ol { padding-left: 22px; margin: 0 0 16px; }
.legal-content li { margin-bottom: 6px; }
.legal-content a { color: var(--accent); }
.legal-content strong { color: var(--text-primary); font-weight: 600; }
.legal-content hr { border: none; border-top: 1px solid var(--border); margin: 28px 0; }
.legal-content blockquote {
  border-left: 3px solid var(--accent);
  padding: 12px 18px;
  background: var(--accent-light);
  border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
  margin: 20px 0;
  font-size: 13px;
}
.legal-meta {
  display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
  margin-top: 10px; margin-bottom: 28px;
  padding-bottom: 24px;
  border-bottom: 1px solid var(--border);
}
@media (max-width: 600px) {
  .public-main { padding: 24px 16px 40px; }
  .public-nav .hide-sm { display: none; }
  .legal-content h1 { font-size: 20px; }
  .legal-content h2 { font-size: 15px; }
}
</style>
</head>
<body>

<header class="public-header">
  <a href="<?= url('/') ?>" class="public-logo">
    <img src="<?= url('/images/logo.png') ?>" alt="VivensiCT" style="height:32px;width:auto;object-fit:contain;flex-shrink:0;">
    <span class="public-logo-text">VivensiCT</span>
  </a>
  <nav class="public-nav">
    <a href="<?= url('/privacidade') ?>" class="public-nav-link hide-sm">🔒 Privacidade</a>
    <a href="<?= url('/termos-de-uso') ?>" class="public-nav-link hide-sm">📋 Termos de Uso</a>
    <button class="theme-toggle" onclick="toggleTheme()" title="Alternar tema claro/escuro" style="margin: 0 4px;">
      <span class="icon-dark">🌙</span>
      <span class="icon-light">☀️</span>
    </button>
    <a href="<?= url('/login') ?>" class="btn btn-primary btn-sm">Entrar</a>
  </nav>
</header>

<div class="public-body">
  <main class="public-main">
    <?= $content ?>
  </main>
</div>

<footer class="public-footer">
  <p style="margin:0 0 8px;">
    <a href="<?= url('/privacidade') ?>">🔒 Política de Privacidade</a>
    <a href="<?= url('/termos-de-uso') ?>">📋 Termos de Uso</a>
    <a href="<?= url('/login') ?>">🔐 Entrar no Sistema</a>
  </p>
  <p style="margin:0;">VivensiCT © <?= date('Y') ?> · Sistema de Gestão para Conselheiros Tutelares · ECA/SUAS · LGPD</p>
</footer>

<?php \App\Core\View::partial('cookie-consent'); ?>
<script src="<?= url('/js/app.js') ?>"></script>
</body>
</html>
