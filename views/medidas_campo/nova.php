<?php $title = 'Nova Medida de Campo'; $subtitle = 'Documento avulso de medida de proteção'; ?>

<style>
.mc-wizard { max-width: 680px; margin: 0 auto; }
.mc-steps  { display: flex; gap: 0; margin-bottom: 28px; border-radius: 16px; overflow: hidden; }
.mc-step   { flex: 1; padding: 12px 8px; text-align: center; background: var(--card-bg); border: 1px solid var(--border); font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: .5px; transition: all .2s; }
.mc-step.active { background: var(--accent); color: #fff; border-color: var(--accent); }
.mc-step.done   { background: var(--success); color: #fff; border-color: var(--success); }
.mc-panel { display: none; }
.mc-panel.active { display: block; }
.tipo-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 10px; margin-top: 8px; }
.tipo-card { padding: 14px 12px; background: var(--card-bg); border: 2px solid var(--border); border-radius: 12px; cursor: pointer; transition: all .2s; }
.tipo-card:hover { border-color: var(--accent); background: rgba(99,102,241,.06); }
.tipo-card.selected { border-color: var(--accent); background: rgba(99,102,241,.12); }
.tipo-card .tipo-icon { font-size: 22px; margin-bottom: 6px; }
.tipo-card .tipo-nome { font-size: 11px; font-weight: 800; color: var(--text-primary); line-height: 1.3; }
.tipo-card .tipo-art  { font-size: 10px; color: var(--accent); margin-top: 3px; font-weight: 700; }
.nav-btns { display: flex; gap: 10px; margin-top: 24px; }
</style>

<div class="mc-wizard">

  <!-- Steps -->
  <div class="mc-steps">
    <div class="mc-step active" id="step-0">① Dados</div>
    <div class="mc-step" id="step-1">② Medida</div>
    <div class="mc-step" id="step-2">③ Situação</div>
  </div>

  <form method="POST" action="<?= url('/medidas-campo') ?>" id="formMedida">
    <?= csrf_field() ?>

    <!-- hidden para tipo_medida e artigo_eca -->
    <input type="hidden" name="tipo_medida" id="h_tipo_medida">
    <input type="hidden" name="artigo_eca"  id="h_artigo_eca">

    <!-- ── PAINEL 0: Dados da criança / família ───────────────────────── -->
    <div class="mc-panel active card" id="panel-0" style="padding:28px;">
      <div class="card-header" style="margin-bottom:20px;">
        <h3>👤 Criança / Adolescente</h3>
        <span class="badge badge-lgpd" style="font-size:10px;">🔐 LGPD</span>
      </div>

      <div class="info-box accent" style="margin-bottom:20px;font-size:12px;">
        🔒 Todos os dados pessoais são <strong>criptografados (AES-256)</strong> antes de serem salvos.
      </div>

      <div class="grid grid-2" style="gap:14px;">
        <div class="form-group">
          <label class="form-label">Nome completo <span class="required">*</span></label>
          <input type="text" name="nome_crianca" class="form-control" placeholder="Nome da criança ou adolescente" required>
          <p class="form-hint">🔐 Criptografado</p>
        </div>
        <div class="form-group">
          <label class="form-label">Data de Nascimento</label>
          <input type="date" name="data_nascimento" class="form-control">
          <p class="form-hint">🔐 Criptografado</p>
        </div>
      </div>

      <div class="grid grid-2" style="gap:14px;">
        <div class="form-group">
          <label class="form-label">Gênero</label>
          <select name="genero" class="form-control">
            <option value="">Selecione...</option>
            <option>Masculino</option>
            <option>Feminino</option>
            <option>Não-binário</option>
            <option>Prefere não informar</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Responsável / Filiação</label>
          <input type="text" name="nome_responsavel" class="form-control" placeholder="Nome do responsável">
          <p class="form-hint">🔐 Criptografado</p>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Endereço</label>
        <input type="text" name="endereco" class="form-control" placeholder="Rua, número, bairro, cidade">
        <p class="form-hint">🔐 Criptografado</p>
      </div>

      <div class="nav-btns">
        <a href="<?= url('/medidas-campo') ?>" class="btn btn-ghost" style="border-radius:50px;">← Voltar</a>
        <button type="button" onclick="irPara(1)" class="btn btn-primary" style="flex:1;border-radius:50px;font-weight:800;">
          Próximo: Tipo de Medida →
        </button>
      </div>
    </div>

    <!-- ── PAINEL 1: Tipo de Medida ──────────────────────────────────── -->
    <div class="mc-panel card" id="panel-1" style="padding:28px;">
      <div class="card-header" style="margin-bottom:20px;">
        <h3>⚖️ Tipo de Medida de Proteção</h3>
      </div>
      <p class="text-sm text-muted" style="margin-bottom:16px;">Selecione a medida de proteção prevista no ECA:</p>

      <div class="tipo-grid" id="tipoGrid">
        <?php
        $medidas = [
          ['Encaminhamento aos Pais / Responsável', 'Art. 101, I', '🏠'],
          ['Orientação e Apoio Temporário', 'Art. 101, II', '🤝'],
          ['Matrícula Compulsória', 'Art. 101, III', '🏫'],
          ['Inclusão em Programa de Saúde', 'Art. 101, IV', '🏥'],
          ['Requisição de Serviços Públicos', 'Art. 101, V', '📋'],
          ['Abrigo em Entidade', 'Art. 101, VII', '🏢'],
          ['Colocação em Família Substituta', 'Art. 101, VIII', '👨‍👩‍👧'],
          ['Acolhimento Institucional', 'Art. 101, IX', '🏡'],
          ['Medida Aplicável aos Pais', 'Art. 129', '⚠️'],
          ['Afastamento do Agressor do Lar', 'Art. 130', '🚫'],
          ['Comunicação ao Ministério Público', 'Art. 98', '📢'],
          ['Outra Medida', 'ECA', '📝'],
        ];
        foreach ($medidas as [$nome, $art, $icon]):
        ?>
        <div class="tipo-card" onclick="selecionarTipo('<?= addslashes($nome) ?>', '<?= $art ?>', this)">
          <div class="tipo-icon"><?= $icon ?></div>
          <div class="tipo-nome"><?= $nome ?></div>
          <div class="tipo-art"><?= $art ?></div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Medida personalizada -->
      <div class="form-group" style="margin-top:16px;">
        <label class="form-label" style="font-size:11px;">Ou descreva outro tipo de medida:</label>
        <input type="text" id="tipoPersonalizado" class="form-control" placeholder="Ex: Notificação à Vara da Infância">
      </div>

      <div class="form-group">
        <label class="form-label">Conselheiro Responsável <span class="required">*</span></label>
        <input type="text" name="assinante_nome" class="form-control"
               value="<?= htmlspecialchars($user['nome'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label class="form-label">Cargo</label>
        <input type="text" name="assinante_cargo" class="form-control" value="Conselheiro(a) Tutelar">
      </div>

      <div class="nav-btns">
        <button type="button" onclick="irPara(0)" class="btn btn-secondary" style="border-radius:50px;">← Voltar</button>
        <button type="button" onclick="irPara(2)" class="btn btn-primary" style="flex:1;border-radius:50px;font-weight:800;">
          Próximo: Descrever Situação →
        </button>
      </div>
    </div>

    <!-- ── PAINEL 2: Situação ─────────────────────────────────────────── -->
    <div class="mc-panel card" id="panel-2" style="padding:28px;">
      <div class="card-header" style="margin-bottom:20px;">
        <h3>📝 Situação Relatada</h3>
      </div>
      <p class="text-sm text-muted" style="margin-bottom:16px;">
        Descreva brevemente o que foi observado em campo. A IA vai ajudar a redigir o documento formal.
      </p>

      <div class="form-group">
        <label class="form-label">Situação observada / motivo da medida <span class="required">*</span></label>
        <textarea name="situacao_relatada" id="situacaoText" class="form-control" rows="5"
          placeholder="Ex: Durante visita domiciliar constatou-se situação de negligência — criança sem acesso a alimentação adequada, escola relatou ausências frequentes..."
          required></textarea>
        <p class="form-hint">Quanto mais detalhes, melhor será o texto gerado pela IA.</p>
      </div>

      <div class="form-group">
        <label class="form-label">Texto da Medida</label>
        <p class="form-hint" style="margin-bottom:8px;">Você pode preencher manualmente ou deixar a IA gerar após salvar.</p>
        <textarea name="texto_medida" id="textoMedida" class="form-control" rows="6"
          placeholder="O texto formal será gerado pela IA depois que você salvar. Ou escreva aqui diretamente."></textarea>
      </div>

      <div class="nav-btns">
        <button type="button" onclick="irPara(1)" class="btn btn-secondary" style="border-radius:50px;">← Voltar</button>
        <button type="submit" class="btn btn-primary" style="flex:1;border-radius:50px;font-weight:800;">
          💾 Salvar e Continuar
        </button>
      </div>
    </div>

  </form>
</div>

<script>
let tipoSelecionado = '';

function irPara(n) {
  // Validação básica
  if (n === 1) {
    const nome = document.querySelector('[name=nome_crianca]').value.trim();
    if (!nome) { alert('Informe o nome da criança ou adolescente.'); return; }
  }
  if (n === 2) {
    if (!tipoSelecionado && !document.getElementById('tipoPersonalizado').value.trim()) {
      alert('Selecione ou descreva o tipo de medida.'); return;
    }
    // Se personalizado foi preenchido, usa ele
    const custom = document.getElementById('tipoPersonalizado').value.trim();
    if (custom) {
      document.getElementById('h_tipo_medida').value = custom;
      document.getElementById('h_artigo_eca').value  = 'ECA';
    }
  }

  // Ocultar todos
  document.querySelectorAll('.mc-panel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.mc-step').forEach((s, i) => {
    s.classList.remove('active', 'done');
    if (i < n) s.classList.add('done');
  });

  document.getElementById('panel-' + n).classList.add('active');
  document.getElementById('step-' + n).classList.add('active');
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function selecionarTipo(nome, artigo, el) {
  document.querySelectorAll('.tipo-card').forEach(c => c.classList.remove('selected'));
  el.classList.add('selected');
  tipoSelecionado = nome;
  document.getElementById('h_tipo_medida').value = nome;
  document.getElementById('h_artigo_eca').value  = artigo;
  document.getElementById('tipoPersonalizado').value = '';
}

// Valida ao submeter
document.getElementById('formMedida').addEventListener('submit', function(e) {
  const situacao = document.getElementById('situacaoText').value.trim();
  if (!situacao) { e.preventDefault(); alert('Descreva a situação observada.'); return; }
  if (!document.getElementById('h_tipo_medida').value) {
    e.preventDefault(); alert('Selecione o tipo de medida.'); return;
  }
});
</script>
