<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>VivensiCT — <?= $title ?? 'Acesso' ?></title>
<link rel="icon" type="image/png" href="<?= url('/images/favicon.png') ?>">
<link rel="stylesheet" href="<?= url('/css/app.css') ?>">
</head>
<body>
<div class="auth-page">
  <div class="auth-card">
    <?php if ($err = flash('error')): ?>
      <div class="alert alert-error" style="margin-bottom:20px;">⚠️ <?= e($err) ?></div>
    <?php endif; ?>
    <?php if ($suc = flash('success')): ?>
      <div class="alert alert-success" style="margin-bottom:20px;">✅ <?= e($suc) ?></div>
    <?php endif; ?>

    <?= $content ?>
  </div>
</div>
<?php \App\Core\View::partial('cookie-consent'); ?>
<script src="<?= url('/js/app.js') ?>"></script>
</body>
</html>
