<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>404 — VivensiCT</title>
<link rel="stylesheet" href="<?= url('/css/app.css') ?>">
</head>
<body>
<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;text-align:center;padding:20px;">
  <div>
    <div style="font-size:80px;margin-bottom:20px;">🛡️</div>
    <h1 style="font-size:64px;font-weight:900;margin:0;color:var(--accent);">404</h1>
    <h2 style="font-size:24px;margin:10px 0;color:var(--text-secondary);">Página não encontrada</h2>
    <p style="color:var(--text-muted);margin-bottom:32px;">A página que você procura não existe ou foi movida.</p>
    <a href="javascript:history.back()" class="btn btn-secondary" style="margin-right:10px;">← Voltar</a>
    <a href="<?= url('/dashboard') ?>" class="btn btn-primary">🏠 Dashboard</a>
  </div>
</div>
</body>
</html>
