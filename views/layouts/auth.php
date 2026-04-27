<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>VivensiCT — <?= $title ?? 'Acesso' ?></title>
<link rel="icon" type="image/png" href="<?= url('/images/favicon.png') ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { scroll-behavior: smooth; }

body {
  font-family: 'Inter', -apple-system, sans-serif;
  background: #0a0a0a;
  color: #ffffff;
  min-height: 100vh;
  -webkit-font-smoothing: antialiased;
  display: flex;
  align-items: stretch;
}

/* ── LAYOUT SPLIT ─────────────────────────────── */
.split-layout {
  display: flex;
  width: 100%;
  min-height: 100vh;
}

/* ── LADO ESQUERDO — Branding ─────────────────── */
.side-left {
  flex: 0 0 45%;
  background: #0a0a0a;
  padding: 60px 56px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  position: relative;
  overflow: hidden;
}

.side-left::before {
  content: '';
  position: absolute;
  top: -150px; left: -150px;
  width: 500px; height: 500px;
  background: radial-gradient(circle, rgba(37,99,235,0.15) 0%, transparent 65%);
  pointer-events: none;
}
.side-left::after {
  content: '';
  position: absolute;
  bottom: -100px; right: -100px;
  width: 350px; height: 350px;
  background: radial-gradient(circle, rgba(16,185,129,0.10) 0%, transparent 65%);
  pointer-events: none;
}

.sl-logo {
  display: flex;
  align-items: center;
  gap: 12px;
  text-decoration: none;
  position: relative; z-index: 1;
}
.sl-logo-icon {
  width: 44px; height: 44px;
  background: linear-gradient(135deg, #2563eb, #3b82f6);
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 16px; font-weight: 900; color: #fff; letter-spacing: -.5px;
  box-shadow: 0 4px 16px rgba(37,99,235,0.4);
}
.sl-logo-name {
  font-size: 18px;
  font-weight: 800;
  color: #ffffff;
  letter-spacing: -0.3px;
}

.sl-main {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 48px 0;
  position: relative; z-index: 1;
}

.sl-badge {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  background: rgba(37,99,235,0.12);
  border: 1px solid rgba(37,99,235,0.3);
  padding: 7px 16px;
  border-radius: 100px;
  font-size: 12px;
  font-weight: 700;
  color: #93c5fd;
  letter-spacing: 0.4px;
  text-transform: uppercase;
  margin-bottom: 28px;
  width: fit-content;
}
.sl-badge-dot {
  width: 7px; height: 7px;
  border-radius: 50%;
  background: #3b82f6;
  animation: blink 2s infinite;
}
@keyframes blink { 0%,100%{opacity:1}50%{opacity:.35} }

.sl-title {
  font-size: clamp(30px, 3.2vw, 44px);
  font-weight: 900;
  line-height: 1.1;
  letter-spacing: -2px;
  color: #ffffff;
  margin-bottom: 20px;
}
.sl-title span {
  background: linear-gradient(90deg, #60a5fa, #93c5fd);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.sl-sub {
  font-size: 15px;
  color: rgba(255,255,255,0.5);
  line-height: 1.75;
  margin-bottom: 40px;
  max-width: 360px;
}

.sl-bullets {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.sl-bullet {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  font-size: 14px;
  color: rgba(255,255,255,0.65);
  line-height: 1.5;
}
.sl-bullet-icon {
  width: 32px; height: 32px;
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-size: 11px; font-weight: 900; color: #fff; letter-spacing: -.3px;
  flex-shrink: 0;
  margin-top: 1px;
}
.bi-blue   { background: rgba(59,130,246,0.2); color: #93c5fd; }
.bi-green  { background: rgba(16,185,129,0.2); color: #6ee7b7; }
.bi-purple { background: rgba(139,92,246,0.2); color: #c4b5fd; }
.bi-orange { background: rgba(245,158,11,0.2); color: #fcd34d; }

.sl-bullet strong { color: rgba(255,255,255,0.9); }

/* Compliance badges */
.sl-compliance {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  margin-top: 36px;
}
.sl-badge-comp {
  padding: 5px 12px;
  border-radius: 100px;
  font-size: 11px; font-weight: 700;
  border: 1px solid rgba(255,255,255,0.1);
  color: rgba(255,255,255,0.35);
}

.sl-footer {
  font-size: 12px;
  color: rgba(255,255,255,0.2);
  position: relative; z-index: 1;
}

/* ── LADO DIREITO — Formulário ────────────────── */
.side-right {
  flex: 1;
  background: #f8faff;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 48px 40px;
  position: relative;
}

.form-card {
  background: #ffffff;
  border-radius: 24px;
  padding: 48px 40px;
  width: 100%;
  max-width: 460px;
  box-shadow: 0 8px 48px rgba(0,0,0,0.08);
}

/* Flash alerts */
.alert-error {
  background: #fee2e2;
  border: 1.5px solid #fca5a5;
  border-radius: 12px;
  padding: 14px 18px;
  margin-bottom: 24px;
  font-size: 13px;
  color: #991b1b;
  display: flex;
  gap: 10px;
  align-items: flex-start;
}
.alert-success {
  background: #d1fae5;
  border: 1.5px solid #a7f3d0;
  border-radius: 12px;
  padding: 14px 18px;
  margin-bottom: 24px;
  font-size: 13px;
  color: #065f46;
  display: flex;
  gap: 10px;
  align-items: flex-start;
}

/* ── RESPONSIVE ────────────────────────────────── */
@media (max-width: 900px) {
  .split-layout { flex-direction: column; }
  .side-left { flex: none; padding: 40px 32px; }
  .sl-main { padding: 28px 0; }
  .side-right { padding: 32px 20px; }
  .form-card { padding: 32px 24px; }
}
@media (max-width: 480px) {
  .side-left { padding: 32px 20px; }
}
</style>
</head>
<body>

<div class="split-layout">

  <!-- ══ LADO ESQUERDO — Branding ══ -->
  <div class="side-left">

    <a href="<?= url('/') ?>" class="sl-logo">
      <div class="sl-logo-icon">VCT</div>
      <span class="sl-logo-name">VivensiCT</span>
    </a>

    <div class="sl-main">

      <div class="sl-badge">
        <span class="sl-badge-dot"></span>
        Sistema Ativo
      </div>

      <h1 class="sl-title">
        Seu painel de<br>proteção à criança<br><span>começa aqui.</span>
      </h1>

      <p class="sl-sub">
        Acesse o sistema inteligente ECA/SUAS para Conselheiros Tutelares.
        Análise de casos com Bruce AI, encaminhamentos e documentação legal em segundos.
      </p>

      <ul class="sl-bullets">
        <li class="sl-bullet">
          <div class="sl-bullet-icon bi-blue">IA</div>
          <div>
            <strong>Bruce AI — Análise jurídica instantânea</strong><br>
            ECA, SUAS e Lei Henry Borel cruzados em segundos
          </div>
        </li>
        <li class="sl-bullet">
          <div class="sl-bullet-icon bi-green">MAP</div>
          <div>
            <strong>Mapa mental do caso</strong><br>
            Encaminhamentos estruturados por órgão e urgência
          </div>
        </li>
        <li class="sl-bullet">
          <div class="sl-bullet-icon bi-purple">ASS</div>
          <div>
            <strong>Assinatura digital no celular</strong><br>
            Documentos com validade jurídica — Lei 14.063/20
          </div>
        </li>
        <li class="sl-bullet">
          <div class="sl-bullet-icon bi-orange">LGPD</div>
          <div>
            <strong>Conformidade LGPD total</strong><br>
            Criptografia AES-256 · Expurgo automático em 3 dias
          </div>
        </li>
      </ul>

      <div class="sl-compliance">
        <span class="sl-badge-comp">ECA ✓</span>
        <span class="sl-badge-comp">SUAS ✓</span>
        <span class="sl-badge-comp">LGPD ✓</span>
        <span class="sl-badge-comp">Lei 14.063/20 ✓</span>
      </div>

    </div>

    <div class="sl-footer">
      © <?= date('Y') ?> VivensiCT · Projeto Social · ECA/SUAS · LGPD
    </div>

  </div>

  <!-- ══ LADO DIREITO — Conteúdo ══ -->
  <div class="side-right">
    <div class="form-card">

      <?php if ($err = flash('error')): ?>
        <div class="alert-error">
          <span style="font-size:16px;flex-shrink:0;">&#9888;</span>
          <span><?= e($err) ?></span>
        </div>
      <?php endif; ?>
      <?php if ($suc = flash('success')): ?>
        <div class="alert-success">
          <span style="font-size:16px;flex-shrink:0;">&#10003;</span>
          <span><?= e($suc) ?></span>
        </div>
      <?php endif; ?>

      <?= $content ?>

    </div>

    <p style="font-size:12px;color:#93a8cc;text-align:center;margin-top:24px;line-height:1.8;">
      Dados protegidos pela <strong style="color:#5b78a8;">LGPD — Lei 13.709/18</strong><br>
      <a href="<?= url('/privacidade') ?>" target="_blank" style="color:#93a8cc;text-decoration:underline;text-underline-offset:2px;">Política de Privacidade</a>
      &nbsp;·&nbsp;
      <a href="<?= url('/termos-de-uso') ?>" target="_blank" style="color:#93a8cc;text-decoration:underline;text-underline-offset:2px;">Termos de Uso</a>
    </p>
  </div>

</div>

<?php \App\Core\View::partial('cookie-consent'); ?>
<script src="<?= url('/js/app.js') ?>"></script>
</body>
</html>
