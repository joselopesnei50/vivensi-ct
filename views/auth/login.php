<?php
// Inline styles reutilizados pelo auth layout
?>
<style>
.fc-header { margin-bottom: 32px; }
.fc-header h2 {
  font-size: 26px; font-weight: 900; color: #020c1b;
  letter-spacing: -0.8px; margin-bottom: 6px;
}
.fc-header p { font-size: 14px; color: #5b78a8; line-height: 1.6; }

.form-group {
  display: flex; flex-direction: column;
  gap: 6px; margin-bottom: 18px;
}
label {
  font-size: 13px; font-weight: 600;
  color: #1e3a5f; letter-spacing: -0.1px;
}
.field-wrap { position: relative; }
input[type="email"],
input[type="password"],
input[type="text"] {
  background: #f0f4ff;
  border: 1.5px solid #dbe6ff;
  border-radius: 10px;
  padding: 13px 16px;
  font-size: 14px; color: #020c1b;
  font-family: inherit; width: 100%;
  transition: all .2s; outline: none;
}
input:focus {
  border-color: #3b82f6;
  background: #ffffff;
  box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
}
input::placeholder { color: #93a8cc; }

.toggle-pass {
  position: absolute; right: 14px; top: 50%;
  transform: translateY(-50%);
  background: none; border: none; cursor: pointer;
  color: #93a8cc; font-size: 12px; font-weight: 700;
  letter-spacing: .3px; padding: 0; font-family: inherit;
  transition: color .15s;
}
.toggle-pass:hover { color: #1d4ed8; }

.btn-submit {
  width: 100%; padding: 15px;
  background: #020c1b; color: #ffffff;
  border: none; border-radius: 12px;
  font-size: 15px; font-weight: 800;
  font-family: inherit; cursor: pointer;
  transition: all .2s; margin-top: 4px;
  letter-spacing: -0.2px;
}
.btn-submit:hover {
  background: #0f2d5e;
  transform: translateY(-1px);
  box-shadow: 0 8px 24px rgba(2,12,27,0.25);
}

.form-footer {
  margin-top: 28px; padding-top: 22px;
  border-top: 1px solid #dbe6ff;
  text-align: center;
  font-size: 13px; color: #5b78a8; line-height: 1.7;
}
.form-footer a { color: #2563eb; text-decoration: none; font-weight: 600; }
.form-footer a:hover { text-decoration: underline; }
</style>

<div class="fc-header">
  <h2>Bem-vindo(a) de volta</h2>
  <p>Acesse o sistema com as credenciais enviadas por e-mail após a aprovação do seu cadastro.</p>
</div>

<form method="POST" action="<?= url('/login') ?>">
  <?= csrf_field() ?>

  <div class="form-group">
    <label for="email">E-mail</label>
    <input type="email" id="email" name="email"
           placeholder="seu@email.com.br" required autofocus
           value="<?= e(\App\Core\Request::post('email', '')) ?>">
  </div>

  <div class="form-group">
    <label for="password">Senha</label>
    <div class="field-wrap">
      <input type="password" id="password" name="password"
             placeholder="••••••••" required>
      <button type="button" class="toggle-pass" onclick="togglePass()" id="toggleBtn">MOSTRAR</button>
    </div>
  </div>

  <button type="submit" class="btn-submit">
    Entrar no Sistema
  </button>
</form>

<div class="form-footer">
  Ainda não tem acesso?
  <a href="<?= url('/cadastro') ?>">Solicitar cadastro</a>
  <br>
  <span style="font-size:12px;color:#93a8cc;">
    Credenciais enviadas por e-mail após aprovação do cadastro.
  </span>
</div>

<script>
function togglePass() {
  const p   = document.getElementById('password');
  const btn = document.getElementById('toggleBtn');
  const show = p.type === 'password';
  p.type    = show ? 'text' : 'password';
  btn.textContent = show ? 'OCULTAR' : 'MOSTRAR';
}
</script>
