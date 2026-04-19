<div class="auth-logo">
  <div class="logo-badge">🛡️</div>
  <h1>VivensiCT</h1>
  <p>Sistema de Proteção à Criança e ao Adolescente</p>
  <p style="font-size:12px;color:var(--text-muted);margin-top:4px;">ECA · SUAS · LGPD</p>
</div>

<div class="auth-form-card">
  <h2 style="font-size:22px;font-weight:700;margin:0 0 6px;">Bem-vindo(a) de volta</h2>
  <p style="color:var(--text-secondary);font-size:14px;margin:0 0 28px;">Acesse o sistema com suas credenciais</p>

  <form method="POST" action="<?= url('/login') ?>">
    <?= csrf_field() ?>

    <div class="form-group">
      <label class="form-label" for="email">E-mail</label>
      <input type="email" id="email" name="email" class="form-control"
             placeholder="seu@email.com" required autofocus
             value="<?= e(\App\Core\Request::post('email', '')) ?>">
    </div>

    <div class="form-group">
      <label class="form-label" for="password">Senha</label>
      <div style="position:relative;">
        <input type="password" id="password" name="password" class="form-control"
               placeholder="••••••••" required>
        <button type="button" onclick="togglePass()"
          style="position:absolute;right:12px;top:50%;transform:translateY(-50%);
                 background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:18px;">
          👁
        </button>
      </div>
    </div>

    <button type="submit" class="btn btn-primary btn-full btn-lg" style="margin-top:8px;">
      Entrar no Sistema →
    </button>
  </form>

  <div style="margin-top:28px;padding-top:20px;border-top:1px solid var(--border);">
    <p style="font-size:12px;color:var(--text-muted);text-align:center;margin:0;">
      Credenciais de demonstração:<br>
      <strong style="color:var(--text-secondary);">admin@guardiao.digital</strong>
      <span style="margin:0 8px;">|</span>
      <strong style="color:var(--text-secondary);">password</strong>
    </p>
  </div>
</div>

<div style="text-align:center;margin-top:24px;">
  <p style="font-size:12px;color:var(--text-muted);line-height:1.8;">
    🔐 Sistema seguro · Dados protegidos pela LGPD<br>
    <a href="<?= url('/privacidade') ?>" target="_blank"
       style="color:var(--text-muted);text-decoration:underline;text-underline-offset:2px;">
      Política de Privacidade
    </a>
    &nbsp;·&nbsp;
    <a href="<?= url('/termos-de-uso') ?>" target="_blank"
       style="color:var(--text-muted);text-decoration:underline;text-underline-offset:2px;">
      Termos de Uso
    </a>
    <br>VivensiCT © <?= date('Y') ?> · ECA/SUAS Compliance
  </p>
</div>

<script>
function togglePass() {
  const p = document.getElementById('password');
  p.type = p.type === 'password' ? 'text' : 'password';
}
</script>
