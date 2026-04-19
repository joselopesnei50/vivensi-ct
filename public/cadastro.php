<?php
/**
 * Cadastro Público de Conselheiros Tutelares
 * Página standalone — não usa o layout do sistema
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── CSRF básico ──────────────────────────────────────────────────────────────
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

// ── Conexão simples com o banco ──────────────────────────────────────────────
function dbConnect(): \PDO
{
    static $pdo = null;
    if ($pdo === null) {
        // Tenta carregar as constantes do sistema; se não existir, usa defaults
        $host   = defined('DB_HOST') ? DB_HOST : 'localhost';
        $name   = defined('DB_NAME') ? DB_NAME : 'ct_ai1';
        $user   = defined('DB_USER') ? DB_USER : 'root';
        $pass   = defined('DB_PASS') ? DB_PASS : '';

        $pdo = new \PDO(
            "mysql:host={$host};dbname={$name};charset=utf8mb4",
            $user,
            $pass,
            [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]
        );
    }
    return $pdo;
}

// ── Processamento do POST ────────────────────────────────────────────────────
$sucesso  = false;
$erros    = [];
$waLink   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validação CSRF
    if (!isset($_POST['csrf_token']) || !hash_equals($csrf, $_POST['csrf_token'])) {
        $erros[] = 'Token de segurança inválido. Recarregue a página e tente novamente.';
    } else {
        // Sanitização básica
        $nome           = trim(htmlspecialchars($_POST['nome']          ?? '', ENT_QUOTES, 'UTF-8'));
        $cpf            = preg_replace('/\D/', '', $_POST['cpf']        ?? '');
        $data_nasc      = $_POST['data_nascimento'] ?? '';
        $email          = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $telefone       = preg_replace('/\D/', '', $_POST['telefone']   ?? '');
        $cidade         = trim(htmlspecialchars($_POST['cidade']        ?? '', ENT_QUOTES, 'UTF-8'));
        $endereco       = trim(htmlspecialchars($_POST['endereco']      ?? '', ENT_QUOTES, 'UTF-8'));
        $ano_posse      = (int) ($_POST['ano_posse'] ?? 0);

        // Validações
        if (strlen($nome) < 3)               $erros[] = 'Nome completo é obrigatório.';
        if (strlen($cpf) !== 11)             $erros[] = 'CPF inválido. Digite apenas os números.';
        if (empty($data_nasc))               $erros[] = 'Data de nascimento é obrigatória.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = 'E-mail inválido.';
        if (strlen($telefone) < 10)          $erros[] = 'Telefone/WhatsApp inválido.';
        if (empty($cidade))                  $erros[] = 'Cidade é obrigatória.';
        if (empty($endereco))                $erros[] = 'Endereço é obrigatório.';
        if ($ano_posse < 2000 || $ano_posse > (int) date('Y') + 1) {
            $erros[] = 'Ano de posse inválido.';
        }

        if (empty($erros)) {
            try {
                $db = dbConnect();

                // Verifica duplicidade de CPF/e-mail
                $dup = $db->prepare("SELECT id FROM cadastros_pendentes WHERE cpf = ? OR email = ? LIMIT 1");
                $dup->execute([$cpf, $email]);
                if ($dup->fetch()) {
                    $erros[] = 'Já existe um cadastro com este CPF ou e-mail.';
                } else {
                    $ins = $db->prepare("
                        INSERT INTO cadastros_pendentes
                            (nome, cpf, data_nascimento, email, telefone, cidade, endereco, ano_posse)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $ins->execute([
                        $nome,
                        $cpf,
                        $data_nasc,
                        $email,
                        $telefone,
                        $cidade,
                        $endereco,
                        $ano_posse,
                    ]);

                    // Monta link WhatsApp Business para contato da administração
                    $msg = urlencode(
                        "Olá! Meu nome é {$nome} e acabei de realizar o cadastro no sistema VivensiCT para Conselheiros Tutelares. " .
                        "Aguardo a aprovação do meu acesso. CPF: " . substr($cpf, 0, 3) . ".XXX.XXX-XX · Cidade: {$cidade}."
                    );
                    $waLink = "https://wa.me/5511999999999?text={$msg}"; // Substitua pelo número real

                    $sucesso = true;
                    // Envia e-mail de boas-vindas via Brevo (não-bloqueante)
                    if (class_exists('\App\Services\BrevoService')) {
                        try { \App\Services\BrevoService::sendRegistrationEmail($nome, $email); } catch (\Throwable $ex) {}
                    }
                    // Regenera CSRF após uso bem-sucedido
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    $csrf = $_SESSION['csrf_token'];
                }
            } catch (\Exception $e) {
                $erros[] = 'Erro ao salvar o cadastro. Tente novamente em instantes.';
                // error_log($e->getMessage()); // descomente em produção
            }
        }
    }
}

// ── Formata CPF para exibição no input ──────────────────────────────────────
$cpfDisplay = '';
if (!empty($_POST['cpf'])) {
    $d = preg_replace('/\D/', '', $_POST['cpf']);
    if (strlen($d) === 11) {
        $cpfDisplay = substr($d,0,3) . '.' . substr($d,3,3) . '.' . substr($d,6,3) . '-' . substr($d,9,2);
    } else {
        $cpfDisplay = htmlspecialchars($_POST['cpf'], ENT_QUOTES, 'UTF-8');
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cadastro de Conselheiro Tutelar — VivensiCT</title>
<meta name="description" content="Cadastre seu Conselho Tutelar no VivensiCT e receba acesso ao sistema inteligente ECA/SUAS com Bruce AI.">
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🛡️</text></svg>">
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

/* Orbe decorativo */
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
  font-size: 22px;
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
  background: rgba(16,185,129,0.12);
  border: 1px solid rgba(16,185,129,0.3);
  padding: 7px 16px;
  border-radius: 100px;
  font-size: 12px;
  font-weight: 700;
  color: #6ee7b7;
  letter-spacing: 0.4px;
  text-transform: uppercase;
  margin-bottom: 28px;
  width: fit-content;
}
.sl-badge-dot {
  width: 7px; height: 7px;
  border-radius: 50%;
  background: #10b981;
  animation: blink 2s infinite;
}
@keyframes blink { 0%,100%{opacity:1}50%{opacity:.35} }

.sl-title {
  font-size: clamp(32px, 3.5vw, 48px);
  font-weight: 900;
  line-height: 1.08;
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
  font-size: 16px;
  color: rgba(255,255,255,0.55);
  line-height: 1.75;
  margin-bottom: 40px;
  max-width: 380px;
}

.sl-bullets {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.sl-bullet {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  font-size: 14px;
  color: rgba(255,255,255,0.7);
  line-height: 1.5;
}
.sl-bullet-icon {
  width: 32px; height: 32px;
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-size: 16px;
  flex-shrink: 0;
  margin-top: 1px;
}
.bi-blue   { background: rgba(59,130,246,0.15); }
.bi-green  { background: rgba(16,185,129,0.15); }
.bi-purple { background: rgba(139,92,246,0.15); }
.bi-orange { background: rgba(245,158,11,0.15); }

.sl-bullet strong { color: rgba(255,255,255,0.92); }

.sl-footer {
  font-size: 12px;
  color: rgba(255,255,255,0.25);
  position: relative; z-index: 1;
}

/* ── LADO DIREITO — Formulário ────────────────── */
.side-right {
  flex: 1;
  background: #f8faff;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 48px 40px;
}

.form-card {
  background: #ffffff;
  border-radius: 24px;
  padding: 48px 40px;
  width: 100%;
  max-width: 520px;
  box-shadow: 0 8px 48px rgba(0,0,0,0.08);
}

.fc-header {
  margin-bottom: 32px;
}
.fc-header h2 {
  font-size: 26px;
  font-weight: 900;
  color: #020c1b;
  letter-spacing: -0.8px;
  margin-bottom: 6px;
}
.fc-header p {
  font-size: 14px;
  color: #5b78a8;
  line-height: 1.6;
}

/* Mensagem de sucesso */
.alert-success {
  background: #d1fae5;
  border: 1.5px solid #a7f3d0;
  border-radius: 14px;
  padding: 20px 24px;
  margin-bottom: 24px;
  display: flex;
  gap: 14px;
  align-items: flex-start;
}
.alert-success-icon { font-size: 24px; flex-shrink: 0; }
.alert-success h4 { font-size: 16px; font-weight: 800; color: #065f46; margin-bottom: 4px; }
.alert-success p  { font-size: 13px; color: #047857; line-height: 1.55; }

/* Mensagem de erro */
.alert-error {
  background: #fee2e2;
  border: 1.5px solid #fca5a5;
  border-radius: 14px;
  padding: 16px 20px;
  margin-bottom: 24px;
  font-size: 13px;
  color: #991b1b;
}
.alert-error ul { list-style: disc; padding-left: 18px; margin-top: 6px; }
.alert-error li { margin-bottom: 3px; }

/* Campos */
.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  margin-bottom: 16px;
}
.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-bottom: 16px;
}
.form-group.full { grid-column: 1/-1; }

label {
  font-size: 13px;
  font-weight: 600;
  color: #1e3a5f;
  letter-spacing: -0.1px;
}

input[type="text"],
input[type="email"],
input[type="tel"],
input[type="date"],
input[type="number"],
textarea {
  background: #f0f4ff;
  border: 1.5px solid #dbe6ff;
  border-radius: 10px;
  padding: 12px 16px;
  font-size: 14px;
  color: #020c1b;
  font-family: inherit;
  width: 100%;
  transition: all .2s;
  outline: none;
}
input:focus,
textarea:focus {
  border-color: #3b82f6;
  background: #ffffff;
  box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
}
input::placeholder,
textarea::placeholder { color: #93a8cc; }

textarea { resize: vertical; min-height: 72px; }

/* Botão principal */
.btn-submit {
  width: 100%;
  padding: 16px;
  background: #020c1b;
  color: #ffffff;
  border: none;
  border-radius: 12px;
  font-size: 15px;
  font-weight: 800;
  font-family: inherit;
  cursor: pointer;
  transition: all .2s;
  margin-bottom: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  letter-spacing: -0.2px;
}
.btn-submit:hover {
  background: #041022;
  transform: translateY(-1px);
  box-shadow: 0 8px 24px rgba(2,12,27,0.3);
}

/* Botão WhatsApp */
.btn-whatsapp {
  width: 100%;
  padding: 14px;
  background: #25D366;
  color: #ffffff;
  border: none;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 700;
  font-family: inherit;
  cursor: pointer;
  text-decoration: none;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: all .2s;
  margin-top: 12px;
}
.btn-whatsapp:hover {
  background: #1ebe57;
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(37,211,102,0.35);
}

.form-note {
  font-size: 12px;
  color: #93a8cc;
  text-align: center;
  margin-top: 16px;
  line-height: 1.6;
}
.form-note a { color: #2563eb; text-decoration: none; }
.form-note a:hover { text-decoration: underline; }

.divider {
  display: flex;
  align-items: center;
  gap: 12px;
  margin: 20px 0;
  color: #93a8cc;
  font-size: 12px;
}
.divider::before,
.divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: #dbe6ff;
}

/* ── RESPONSIVE ────────────────────────────────── */
@media (max-width: 900px) {
  .split-layout { flex-direction: column; }
  .side-left {
    flex: none;
    padding: 40px 32px;
  }
  .sl-main { padding: 32px 0; }
  .side-right { padding: 32px 20px; }
  .form-row { grid-template-columns: 1fr; }
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

    <!-- Logo -->
    <a href="/" class="sl-logo">
      <div class="sl-logo-icon">🛡️</div>
      <span class="sl-logo-name">VivensiCT</span>
    </a>

    <!-- Conteúdo principal -->
    <div class="sl-main">

      <div class="sl-badge">
        <span class="sl-badge-dot"></span>
        Sistema Gratuito Para Conselhos Tutelares
      </div>

      <h1 class="sl-title">
        Feito para quem<br>protege crianças e<br><span>adolescentes.</span>
      </h1>

      <p class="sl-sub">
        Registre seu Conselho Tutelar e tenha acesso ao sistema inteligente
        ECA/SUAS com Bruce AI, assinatura digital e conformidade LGPD —
        sem custo algum.
      </p>

      <ul class="sl-bullets">
        <li class="sl-bullet">
          <div class="sl-bullet-icon bi-blue">🤖</div>
          <div>
            <strong>Bruce AI com análise de leis</strong><br>
            ECA, SUAS e Lei Henry Borel em segundos
          </div>
        </li>
        <li class="sl-bullet">
          <div class="sl-bullet-icon bi-green">📍</div>
          <div>
            <strong>Encaminhamentos automáticos</strong><br>
            CRAS, CREAS, UBS e Judiciário com base no caso
          </div>
        </li>
        <li class="sl-bullet">
          <div class="sl-bullet-icon bi-purple">✍️</div>
          <div>
            <strong>Assinatura digital no celular</strong><br>
            Documentos com validade jurídica — Lei 14.063/20
          </div>
        </li>
        <li class="sl-bullet">
          <div class="sl-bullet-icon bi-orange">🔒</div>
          <div>
            <strong>Conformidade LGPD total</strong><br>
            Criptografia AES-128 · Expurgo automático em 3 dias
          </div>
        </li>
      </ul>

    </div>

    <!-- Rodapé esquerdo -->
    <div class="sl-footer">
      © <?= date('Y') ?> VivensiCT · Projeto Social · ECA/SUAS · LGPD
    </div>

  </div>

  <!-- ══ LADO DIREITO — Formulário ══ -->
  <div class="side-right">
    <div class="form-card">

      <div class="fc-header">
        <h2>Cadastrar Conselho Tutelar</h2>
        <p>Preencha seus dados. Após a revisão, enviaremos seu acesso por WhatsApp.</p>
      </div>

      <?php if ($sucesso): ?>

        <!-- ── Mensagem de sucesso ── -->
        <div class="alert-success">
          <div class="alert-success-icon">✅</div>
          <div>
            <h4>Cadastro enviado com sucesso!</h4>
            <p>
              Seus dados foram recebidos e estão em análise pela equipe VivensiCT.
              Em breve você receberá uma mensagem via WhatsApp com as instruções de acesso.
              <br><br>
              <strong>Status:</strong> Pendente de aprovação
            </p>
          </div>
        </div>

        <a href="<?= htmlspecialchars($waLink, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="btn-whatsapp">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
          Confirmar Solicitação via WhatsApp
        </a>

        <p class="form-note" style="margin-top:20px;">
          <a href="/">← Voltar para a página inicial</a>
        </p>

      <?php else: ?>

        <?php if (!empty($erros)): ?>
          <div class="alert-error">
            <strong>Corrija os campos abaixo:</strong>
            <ul>
              <?php foreach ($erros as $e): ?>
                <li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form method="POST" action="" novalidate>
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">

          <!-- Nome -->
          <div class="form-group">
            <label for="nome">Nome Completo *</label>
            <input type="text" id="nome" name="nome" placeholder="Maria da Silva Santos"
                   value="<?= htmlspecialchars($_POST['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                   required autocomplete="name">
          </div>

          <!-- CPF + Data de Nascimento -->
          <div class="form-row">
            <div class="form-group">
              <label for="cpf">CPF *</label>
              <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00"
                     value="<?= htmlspecialchars($cpfDisplay, ENT_QUOTES, 'UTF-8') ?>"
                     maxlength="14" required autocomplete="off">
            </div>
            <div class="form-group">
              <label for="data_nascimento">Data de Nascimento *</label>
              <input type="date" id="data_nascimento" name="data_nascimento"
                     value="<?= htmlspecialchars($_POST['data_nascimento'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                     required>
            </div>
          </div>

          <!-- E-mail + Telefone -->
          <div class="form-row">
            <div class="form-group">
              <label for="email">E-mail *</label>
              <input type="email" id="email" name="email" placeholder="maria@exemplo.com.br"
                     value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                     required autocomplete="email">
            </div>
            <div class="form-group">
              <label for="telefone">WhatsApp *</label>
              <input type="tel" id="telefone" name="telefone" placeholder="(11) 99999-9999"
                     value="<?= htmlspecialchars($_POST['telefone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                     required autocomplete="tel">
            </div>
          </div>

          <!-- Cidade + Ano de Posse -->
          <div class="form-row">
            <div class="form-group">
              <label for="cidade">Cidade/Município *</label>
              <input type="text" id="cidade" name="cidade" placeholder="São Paulo — SP"
                     value="<?= htmlspecialchars($_POST['cidade'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                     required>
            </div>
            <div class="form-group">
              <label for="ano_posse">Ano de Posse *</label>
              <input type="number" id="ano_posse" name="ano_posse"
                     placeholder="<?= date('Y') ?>"
                     min="2000" max="<?= date('Y') + 1 ?>"
                     value="<?= htmlspecialchars($_POST['ano_posse'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                     required>
            </div>
          </div>

          <!-- Endereço -->
          <div class="form-group">
            <label for="endereco">Endereço Completo *</label>
            <textarea id="endereco" name="endereco"
                      placeholder="Rua, número, bairro, CEP, cidade/UF"
                      required><?= htmlspecialchars($_POST['endereco'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
          </div>

          <!-- Botão submit -->
          <button type="submit" class="btn-submit">
            🛡️ Enviar Cadastro para Análise
          </button>

          <div class="divider">ou</div>

          <!-- Botão WhatsApp alternativo -->
          <a href="https://wa.me/5511999999999?text=<?= urlencode('Olá! Quero cadastrar meu Conselho Tutelar no VivensiCT.') ?>"
             target="_blank" rel="noopener" class="btn-whatsapp">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
            Enviar Solicitação via WhatsApp
          </a>

          <p class="form-note">
            Seus dados são protegidos pela <strong>LGPD — Lei 13.709/18</strong>.<br>
            Usados exclusivamente para liberar seu acesso ao sistema.<br>
            Já tem acesso? <a href="/login">Entrar no sistema →</a>
          </p>

        </form>

      <?php endif; ?>

    </div>
  </div>

</div>

<script>
// Máscara CPF
document.getElementById('cpf')?.addEventListener('input', function(){
  let v = this.value.replace(/\D/g,'');
  if(v.length > 11) v = v.slice(0,11);
  v = v.replace(/(\d{3})(\d)/,'$1.$2')
        .replace(/(\d{3})\.(\d{3})(\d)/,'$1.$2.$3')
        .replace(/(\d{3})\.(\d{3})\.(\d{3})(\d)/,'$1.$2.$3-$4');
  this.value = v;
});

// Máscara Telefone
document.getElementById('telefone')?.addEventListener('input', function(){
  let v = this.value.replace(/\D/g,'');
  if(v.length > 11) v = v.slice(0,11);
  if(v.length <= 10){
    v = v.replace(/(\d{2})(\d)/,'($1) $2')
          .replace(/(\d{4})(\d)/,'$1-$2');
  } else {
    v = v.replace(/(\d{2})(\d)/,'($1) $2')
          .replace(/(\d{5})(\d)/,'$1-$2');
  }
  this.value = v;
});
</script>

</body>
</html>
