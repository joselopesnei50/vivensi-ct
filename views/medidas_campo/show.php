<?php
$title    = 'Medida de Campo — ' . $medida['numero_doc'];
$subtitle = $medida['tipo_medida'];
?>

<style>
.mc-show { max-width: 820px; margin: 0 auto; }
.info-row { display:flex; gap:8px; padding:10px 0; border-bottom:1px solid var(--border); align-items:flex-start; }
.info-row .lbl { min-width:130px; font-size:11px; font-weight:800; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px; flex-shrink:0; }
.info-row .val { font-size:13px; color:var(--text-primary); font-weight:600; }
#textoEditor { width:100%; min-height:260px; padding:16px; background:var(--input-bg); border:1px solid var(--border); border-radius:12px; color:var(--text-primary); font-size:13px; line-height:1.8; resize:vertical; font-family:inherit; }
.sig-box { background:var(--card-bg); border:2px dashed var(--border); border-radius:16px; padding:16px; text-align:center; }
#sigCanvas { background:#fff; border-radius:10px; border:1px solid #ddd; cursor:crosshair; width:100%; max-width:460px; height:160px; display:block; margin:0 auto; }
.ai-loading { display:none; align-items:center; gap:10px; padding:12px 16px; background:rgba(99,102,241,.08); border:1px solid var(--accent); border-radius:10px; }
.dot-pulse { display:inline-block; width:8px; height:8px; border-radius:50%; background:var(--accent); animation: dotPulse 1s infinite; }
@keyframes dotPulse { 0%,80%,100%{transform:scale(0)} 40%{transform:scale(1)} }
.orgao-tag { display:inline-block; background:rgba(99,102,241,.1); color:var(--accent); border:1px solid rgba(99,102,241,.3); padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; margin:2px; }
.btn-ia { background:linear-gradient(135deg, var(--accent), #8b5cf6); color:#fff; border:none; padding:10px 20px; border-radius:50px; font-weight:800; font-size:12px; cursor:pointer; box-shadow:0 4px 15px rgba(99,102,241,.3); transition:all .2s; }
.btn-ia:hover { transform:translateY(-1px); box-shadow:0 6px 20px rgba(99,102,241,.4); }
</style>

<div class="mc-show">

  <!-- Cabeçalho do documento -->
  <div class="card card-premium" style="padding:24px;margin-bottom:20px;">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px;">
      <div style="display:flex;align-items:center;gap:16px;">
        <div style="width:48px;height:48px;background:var(--accent);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:24px;box-shadow:var(--premium-glow);">⚖️</div>
        <div>
          <div style="font-size:11px;font-weight:800;color:var(--accent);letter-spacing:1px;"><?= htmlspecialchars($medida['numero_doc']) ?></div>
          <div style="font-size:18px;font-weight:800;color:var(--text-primary);margin-top:2px;"><?= htmlspecialchars($medida['tipo_medida']) ?></div>
          <?php if ($medida['artigo_eca']): ?>
          <span style="font-size:11px;background:rgba(99,102,241,.12);color:var(--accent);padding:2px 10px;border-radius:20px;font-weight:700;"><?= htmlspecialchars($medida['artigo_eca']) ?></span>
          <?php endif; ?>
        </div>
      </div>
      <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <span class="badge badge-<?= $medida['status'] ?>" style="padding:6px 14px;border-radius:50px;font-weight:800;">
          <?= $medida['status'] === 'assinado' ? '✅ Assinado' : '✏️ Rascunho' ?>
        </span>
        <?php if ($medida['assinado'] && $medida['pdf_arquivo']): ?>
        <a href="<?= url('/medidas-campo/' . $medida['id'] . '/download') ?>" class="btn btn-success btn-sm" style="border-radius:50px;font-weight:800;">
          ⬇️ Baixar PDF
        </a>
        <?php endif; ?>
        <a href="<?= url('/medidas-campo') ?>" class="btn btn-ghost btn-sm" style="border-radius:50px;">← Voltar</a>
      </div>
    </div>
  </div>

  <!-- Dados da criança -->
  <div class="card" style="padding:24px;margin-bottom:16px;">
    <div class="card-header" style="margin-bottom:16px;"><h3>👤 Identificação <span style="font-size:11px;color:var(--text-muted);">(🔐 Dado Criptografado)</span></h3></div>
    <?php
    $campos = [
      ['Nome', $medida['nome_crianca_dec']],
      ['Data de Nascimento', $medida['nascimento_dec']],
      ['Gênero', $medida['genero']],
      ['Responsável', $medida['nome_responsavel_dec']],
      ['Endereço', $medida['endereco_dec']],
    ];
    foreach ($campos as [$l, $v]):
      if (!$v) continue;
    ?>
    <div class="info-row"><div class="lbl"><?= $l ?></div><div class="val"><?= htmlspecialchars($v) ?></div></div>
    <?php endforeach; ?>
    <div class="info-row">
      <div class="lbl">Conselheiro</div>
      <div class="val"><?= htmlspecialchars($medida['conselheiro']) ?><?= $medida['registro_funcional'] ? ' — Reg. ' . htmlspecialchars($medida['registro_funcional']) : '' ?></div>
    </div>
    <div class="info-row">
      <div class="lbl">Data</div>
      <div class="val"><?= date('d/m/Y H:i', strtotime($medida['created_at'])) ?></div>
    </div>
  </div>

  <!-- Situação relatada -->
  <?php if ($medida['situacao_relatada']): ?>
  <div class="card" style="padding:24px;margin-bottom:16px;">
    <div class="card-header" style="margin-bottom:12px;"><h3>📋 Situação Relatada</h3></div>
    <p style="white-space:pre-wrap;font-size:13px;line-height:1.8;color:var(--text-secondary);"><?= htmlspecialchars($medida['situacao_relatada']) ?></p>
  </div>
  <?php endif; ?>

  <!-- Texto da Medida + IA -->
  <div class="card" style="padding:24px;margin-bottom:16px;" id="secaoTexto">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
      <h3 style="margin:0;">📝 Texto da Medida</h3>
      <?php if (!$medida['assinado']): ?>
      <button class="btn-ia" onclick="gerarTextoIA()" id="btnIA">
        🤖 Gerar com IA
      </button>
      <?php endif; ?>
    </div>

    <!-- Loading IA -->
    <div class="ai-loading" id="aiLoading">
      <span class="dot-pulse"></span>
      <span style="font-size:12px;font-weight:700;color:var(--accent);">IA redigindo o documento formal...</span>
    </div>

    <?php if ($medida['assinado']): ?>
      <div style="white-space:pre-wrap;font-size:13px;line-height:1.9;color:var(--text-secondary);padding:16px;background:var(--input-bg);border-radius:12px;border:1px solid var(--border);">
        <?= htmlspecialchars($medida['texto_medida']) ?>
      </div>
    <?php else: ?>
      <textarea id="textoEditor"><?= htmlspecialchars($medida['texto_medida'] ?? '') ?></textarea>
      <div style="display:flex;gap:8px;margin-top:10px;">
        <button onclick="salvarTexto()" class="btn btn-secondary btn-sm" style="border-radius:50px;">
          💾 Salvar Rascunho
        </button>
        <span id="salvoMsg" style="display:none;font-size:11px;color:var(--success);font-weight:700;align-self:center;">✅ Salvo</span>
      </div>
    <?php endif; ?>

    <!-- Órgãos sugeridos pela IA -->
    <div id="orgaosSugeridos" style="margin-top:14px;display:none;">
      <p style="font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;margin-bottom:8px;">Órgãos a notificar:</p>
      <div id="orgaosLista"></div>
    </div>
  </div>

  <!-- Assinatura Digital -->
  <?php if (!$medida['assinado']): ?>
  <div class="card" style="padding:24px;margin-bottom:16px;" id="secaoAssinatura">
    <div class="card-header" style="margin-bottom:16px;"><h3>✍️ Assinatura Digital</h3></div>
    <p class="text-sm text-muted" style="margin-bottom:16px;">
      Assine abaixo para finalizar o documento. O PDF será gerado automaticamente.
    </p>

    <div class="sig-box">
      <canvas id="sigCanvas"></canvas>
      <div style="display:flex;gap:8px;justify-content:center;margin-top:12px;">
        <button type="button" onclick="limparAssinatura()" class="btn btn-secondary btn-sm" style="border-radius:50px;">🗑️ Limpar</button>
      </div>
    </div>

    <div class="grid grid-2" style="gap:12px;margin-top:16px;">
      <div class="form-group">
        <label class="form-label">Nome do Assinante</label>
        <input type="text" id="nomeAssinante" class="form-control" value="<?= htmlspecialchars($medida['assinante_nome'] ?? $medida['conselheiro']) ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Cargo</label>
        <input type="text" id="cargoAssinante" class="form-control" value="<?= htmlspecialchars($medida['assinante_cargo'] ?? 'Conselheiro(a) Tutelar') ?>">
      </div>
    </div>

    <button onclick="assinarDocumento()" id="btnAssinar"
      class="btn btn-primary" style="width:100%;border-radius:50px;font-weight:800;font-size:14px;padding:14px;margin-top:8px;">
      ✅ Assinar e Gerar Documento
    </button>
  </div>
  <?php else: ?>
  <!-- Documento assinado -->
  <div class="card" style="padding:24px;background:rgba(34,197,94,.05);border:1px solid rgba(34,197,94,.3);">
    <div style="text-align:center;">
      <div style="font-size:48px;margin-bottom:12px;">✅</div>
      <h3 style="color:var(--success);">Documento Assinado</h3>
      <p class="text-muted text-sm">Assinado em <?= date('d/m/Y H:i', strtotime($medida['data_assinatura'])) ?> por <strong><?= htmlspecialchars($medida['assinante_nome']) ?></strong></p>
      <?php if ($medida['assinatura_data']): ?>
      <div style="margin:16px auto;">
        <img src="<?= $medida['assinatura_data'] ?>" style="max-width:260px;display:block;margin:0 auto;filter:invert(var(--sig-invert,0));" alt="Assinatura">
      </div>
      <?php endif; ?>
      <a href="<?= url('/medidas-campo/' . $medida['id'] . '/download') ?>" class="btn btn-success" style="border-radius:50px;font-weight:800;margin-top:12px;">
        ⬇️ Baixar Documento PDF
      </a>
    </div>
  </div>
  <?php endif; ?>

</div><!-- /mc-show -->

<script>
const csrfToken   = document.querySelector('meta[name=csrf]')?.content || '';
const medidaId    = <?= (int)$medida['id'] ?>;
const urlGerarIA  = '<?= url('/medidas-campo/' . $medida['id'] . '/gerar-texto') ?>';
const urlSalvar   = '<?= url('/medidas-campo/' . $medida['id'] . '/salvar-texto') ?>';
const urlAssinar  = '<?= url('/medidas-campo/' . $medida['id'] . '/assinar') ?>';

// ── Signature Pad ──────────────────────────────────────────────────────────
let sigPad;
const canvas = document.getElementById('sigCanvas');
if (canvas && typeof SignaturePad !== 'undefined') {
  // Ajustar resolução do canvas ao tamanho real
  function resizeCanvas() {
    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    canvas.width  = canvas.offsetWidth  * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    canvas.getContext('2d').scale(ratio, ratio);
  }
  resizeCanvas();
  sigPad = new SignaturePad(canvas, { backgroundColor: 'rgba(255,255,255,0)', penColor: '#1a1a2e', minWidth: 1.5, maxWidth: 3 });
  window.addEventListener('resize', resizeCanvas);
}

function limparAssinatura() { sigPad?.clear(); }

// ── Gerar texto com IA ─────────────────────────────────────────────────────
async function gerarTextoIA() {
  const editor  = document.getElementById('textoEditor');
  const loading = document.getElementById('aiLoading');
  const btn     = document.getElementById('btnIA');

  loading.style.display = 'flex';
  btn.disabled = true;
  btn.textContent = '⏳ Gerando...';

  try {
    const res  = await fetch(urlGerarIA, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: '_token=' + encodeURIComponent(csrfToken),
    });
    const data = await res.json();

    if (data.success && data.data?.texto_medida) {
      editor.value = data.data.texto_medida;

      // Mostrar órgãos sugeridos
      const orgaos = data.data.orgaos_notificar || [];
      if (orgaos.length) {
        document.getElementById('orgaosLista').innerHTML = orgaos.map(o => `<span class="orgao-tag">${o}</span>`).join('');
        document.getElementById('orgaosSugeridos').style.display = 'block';
      }
    } else {
      alert('Erro ao gerar texto. Tente novamente ou escreva manualmente.');
    }
  } catch (e) {
    alert('Falha na conexão com a IA.');
  } finally {
    loading.style.display = 'none';
    btn.disabled = false;
    btn.textContent = '🔄 Regerar com IA';
  }
}

// ── Salvar rascunho ────────────────────────────────────────────────────────
async function salvarTexto() {
  const texto = document.getElementById('textoEditor')?.value || '';
  const msg   = document.getElementById('salvoMsg');

  await fetch(urlSalvar, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: '_token=' + encodeURIComponent(csrfToken) + '&texto_medida=' + encodeURIComponent(texto),
  });

  msg.style.display = 'inline';
  setTimeout(() => msg.style.display = 'none', 2500);
}

// ── Assinar e gerar PDF ────────────────────────────────────────────────────
async function assinarDocumento() {
  const btn    = document.getElementById('btnAssinar');
  const texto  = document.getElementById('textoEditor')?.value?.trim() || '';
  const nome   = document.getElementById('nomeAssinante')?.value?.trim() || '';
  const cargo  = document.getElementById('cargoAssinante')?.value?.trim() || '';

  if (!texto) { alert('O texto da medida é obrigatório. Preencha ou gere com IA.'); return; }
  if (sigPad && sigPad.isEmpty()) { alert('Por favor, assine o documento antes de finalizar.'); return; }

  const assinaturaBase64 = sigPad ? sigPad.toDataURL('image/png') : '';

  btn.disabled = true;
  btn.textContent = '⏳ Gerando documento...';

  try {
    const body = new URLSearchParams({
      _token:          csrfToken,
      assinatura_data: assinaturaBase64,
      assinante_nome:  nome,
      assinante_cargo: cargo,
      texto_medida:    texto,
    });

    const res  = await fetch(urlAssinar, { method: 'POST', body });
    const data = await res.json();

    if (data.success) {
      window.location.reload();
    } else {
      alert(data.error || 'Erro ao gerar documento.');
      btn.disabled = false;
      btn.textContent = '✅ Assinar e Gerar Documento';
    }
  } catch (e) {
    alert('Falha na conexão.');
    btn.disabled = false;
    btn.textContent = '✅ Assinar e Gerar Documento';
  }
}
</script>
