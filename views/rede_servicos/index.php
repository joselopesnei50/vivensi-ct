<?php $title = 'Rede de Serviços Municipal'; $subtitle = 'Tipificação Nacional SUAS — Serviços do Município'; ?>

<div class="flex-between mb-6">
  <div></div>
  <button onclick="openAddModal()" class="btn btn-primary">+ Adicionar Serviço</button>
</div>

<!-- Cards por tipo -->
<?php
  $tipos = [];
  foreach ($servicos as $s) {
    $tipos[$s['tipo_servico']][] = $s;
  }
  $tipoIcons = [
    'CRAS'        => '🏢', 'CREAS'      => '🏛️', 'Saúde'      => '🏥',
    'Saúde Mental'=> '🧠', 'Educação'   => '🏫', 'Delegacia'  => '👮',
    'Ministério Público' => '⚖️', 'Vara da Infância' => '🏛️',
    'default'     => '📍'
  ];
?>

<?php if (empty($servicos)): ?>
<div class="card" style="text-align:center;padding:60px;">
  <div style="font-size:48px;margin-bottom:16px;">🌐</div>
  <h3 style="color:var(--text-secondary);">Nenhum serviço cadastrado</h3>
  <p class="text-muted text-sm">Cadastre a rede de serviços do seu município para que a IA possa sugerir encaminhamentos personalizados.</p>
  <button onclick="openAddModal()" class="btn btn-primary" style="margin-top:16px;">+ Adicionar Primeiro Serviço</button>
</div>

<?php else: ?>
<?php foreach ($tipos as $tipo => $lista): ?>
<div class="card mb-6">
  <div class="card-header">
    <h3>
      <?= $tipoIcons[$tipo] ?? $tipoIcons['default'] ?>
      <?= e($tipo) ?>
      <span class="badge badge-media" style="margin-left:8px;"><?= count($lista) ?></span>
    </h3>
  </div>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1px;background:var(--border);">
    <?php foreach ($lista as $s): ?>
    <div style="background:var(--bg-card);padding:20px;">
      <div class="flex-between" style="margin-bottom:12px;">
        <div style="font-weight:600;font-size:14px;"><?= e($s['nome_servico']) ?></div>
        <?php if (!$s['ativo']): ?>
          <span class="badge badge-arquivado">Inativo</span>
        <?php endif; ?>
      </div>

      <?php if ($s['tipificacao_suas']): ?>
      <p class="text-xs text-muted" style="margin:0 0 8px;">
        📋 <?= e($s['tipificacao_suas']) ?>
      </p>
      <?php endif; ?>

      <div style="display:flex;flex-direction:column;gap:4px;">
        <?php if ($s['orgao_responsavel']): ?>
        <span class="text-sm" style="color:var(--text-secondary);">🏛️ <?= e($s['orgao_responsavel']) ?></span>
        <?php endif; ?>
        <?php if ($s['telefone']): ?>
        <span class="text-sm" style="color:var(--text-secondary);">📞 <?= e($s['telefone']) ?></span>
        <?php endif; ?>
        <?php if ($s['responsavel']): ?>
        <span class="text-sm" style="color:var(--text-secondary);">👤 <?= e($s['responsavel']) ?></span>
        <?php endif; ?>
        <?php if ($s['horario_funcionamento']): ?>
        <span class="text-sm" style="color:var(--text-secondary);">⏰ <?= e($s['horario_funcionamento']) ?></span>
        <?php endif; ?>
      </div>

      <div class="flex gap-2" style="margin-top:14px;">
        <button onclick="editServico(<?= htmlspecialchars(json_encode($s)) ?>)"
                class="btn btn-secondary btn-sm">✏️ Editar</button>
        <button onclick="deleteServico(<?= $s['id'] ?>)"
                class="btn btn-danger btn-sm">🗑️</button>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php endforeach; ?>
<?php endif; ?>

<!-- Tipificação SUAS Reference -->
<div class="card">
  <div class="card-header">
    <h3>📖 Tipificação Nacional de Serviços Socioassistenciais (SUAS)</h3>
    <span class="text-xs text-muted">Resolução CNAS 109/2009</span>
  </div>
  <div class="card-body">
    <div class="grid grid-2" style="gap:12px;">
      <?php
      $suas = [
        ['PAIF', 'Serviço de Proteção e Atendimento Integral à Família', 'CRAS'],
        ['PAEFI', 'Serviço de Proteção e Atendimento Especializado a Famílias e Indivíduos', 'CREAS'],
        ['SCFV', 'Serviço de Convivência e Fortalecimento de Vínculos', 'CRAS/CREAS'],
        ['Abordagem Social', 'Serviço Especializado em Abordagem Social', 'CREAS'],
        ['SEAS-PCD/Idoso', 'Serviço Especializado para PCD e Idosos', 'CREAS'],
        ['Acolhimento Institucional', 'Serviço de Acolhimento Institucional para Crianças', 'Alta Complexidade'],
        ['Família Acolhedora', 'Serviço de Acolhimento em Família Acolhedora', 'Alta Complexidade'],
        ['LA e PSC', 'Medidas Socioeducativas em Meio Aberto', 'CREAS'],
      ];
      foreach ($suas as [$sigla, $nome, $onde]): ?>
      <div style="padding:12px;background:var(--bg-secondary);border-radius:var(--radius-sm);">
        <div class="flex-between" style="margin-bottom:4px;">
          <span class="fw-600 text-sm"><?= $sigla ?></span>
          <span class="badge badge-media text-xs"><?= $onde ?></span>
        </div>
        <span class="text-xs text-muted"><?= $nome ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- Modal: Adicionar/Editar Serviço -->
<div id="addModal" class="modal-overlay hidden">
  <div class="modal">
    <div class="modal-header">
      <h3 id="modalTitle">➕ Adicionar Serviço à Rede Municipal</h3>
      <button onclick="closeAddModal()" class="modal-close">×</button>
    </div>
    <form id="formServico" method="POST" action="<?= url('/rede-servicos') ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="_method" id="formMethod" value="POST">
      <input type="hidden" name="_id" id="formId" value="">
      <div class="modal-body">
        <div class="grid grid-2">
          <div class="form-group">
            <label class="form-label">Nome do Serviço *</label>
            <input type="text" name="nome_servico" id="fNome" class="form-control" required placeholder="Ex: CRAS Centro">
          </div>
          <div class="form-group">
            <label class="form-label">Tipo de Serviço *</label>
            <select name="tipo_servico" id="fTipo" class="form-control" required>
              <option value="">Selecione...</option>
              <?php foreach (['CRAS','CREAS','Saúde','Saúde Mental','Educação','Delegacia','Ministério Público','Vara da Infância','Outros'] as $t): ?>
              <option><?= $t ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Tipificação SUAS</label>
          <input type="text" name="tipificacao_suas" id="fTipSuas" class="form-control"
                 placeholder="Ex: PAIF - Serviço de Proteção Integral à Família">
        </div>
        <div class="grid grid-2">
          <div class="form-group">
            <label class="form-label">Órgão Responsável</label>
            <input type="text" name="orgao_responsavel" id="fOrgao" class="form-control" placeholder="Ex: Secretaria de Assistência Social">
          </div>
          <div class="form-group">
            <label class="form-label">Responsável</label>
            <input type="text" name="responsavel" id="fResp" class="form-control" placeholder="Nome do coordenador">
          </div>
        </div>
        <div class="grid grid-2">
          <div class="form-group">
            <label class="form-label">Telefone</label>
            <input type="text" name="telefone" id="fTel" class="form-control" placeholder="(00) 0000-0000">
          </div>
          <div class="form-group">
            <label class="form-label">E-mail</label>
            <input type="email" name="email" id="fEmail" class="form-control" placeholder="contato@prefeitura.gov.br">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Endereço</label>
          <input type="text" name="endereco" id="fEnd" class="form-control" placeholder="Rua, número, bairro">
        </div>
        <div class="form-group">
          <label class="form-label">Horário de Funcionamento</label>
          <input type="text" name="horario_funcionamento" id="fHorario" class="form-control" placeholder="Seg-Sex 08h-17h">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" onclick="closeAddModal()" class="btn btn-secondary">Cancelar</button>
        <button type="submit" class="btn btn-primary">💾 Salvar Serviço</button>
      </div>
    </form>
  </div>
</div>

<script>
function openAddModal() {
  document.getElementById('modalTitle').textContent = '➕ Adicionar Serviço à Rede Municipal';
  document.getElementById('formServico').action = '<?= url('/rede-servicos') ?>';
  document.getElementById('formMethod').value = 'POST';
  ['fNome','fTipo','fTipSuas','fOrgao','fResp','fTel','fEmail','fEnd','fHorario'].forEach(id => {
    document.getElementById(id).value = '';
  });
  document.getElementById('addModal').classList.remove('hidden');
}

function closeAddModal() {
  document.getElementById('addModal').classList.add('hidden');
}

function editServico(s) {
  document.getElementById('modalTitle').textContent = '✏️ Editar Serviço';
  document.getElementById('formServico').action = '<?= url('/rede-servicos/') ?>' + s.id;
  document.getElementById('formId').value = s.id;
  document.getElementById('fNome').value   = s.nome_servico || '';
  document.getElementById('fTipo').value   = s.tipo_servico || '';
  document.getElementById('fTipSuas').value= s.tipificacao_suas || '';
  document.getElementById('fOrgao').value  = s.orgao_responsavel || '';
  document.getElementById('fResp').value   = s.responsavel || '';
  document.getElementById('fTel').value    = s.telefone || '';
  document.getElementById('fEmail').value  = s.email || '';
  document.getElementById('fEnd').value    = s.endereco || '';
  document.getElementById('fHorario').value= s.horario_funcionamento || '';
  document.getElementById('addModal').classList.remove('hidden');
}

function deleteServico(id) {
  if (!confirm('Remover este serviço da rede municipal?')) return;
  fetch('<?= url('/rede-servicos/') ?>' + id + '/delete', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    body: JSON.stringify({ _token: '<?= \App\Core\Request::csrf() ?>' }),
  }).then(() => { showToast('Serviço removido', 'success'); setTimeout(() => location.reload(), 800); });
}

document.getElementById('addModal').addEventListener('click', function(e) {
  if (e.target === this) closeAddModal();
});
</script>
