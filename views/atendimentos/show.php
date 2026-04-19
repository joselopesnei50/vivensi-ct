<?php
$title    = 'Atendimento ' . $atendimento['numero_protocolo'];
$subtitle = $atendimento['tipo_demanda'];
?>

<!-- Header Info -->
<div class="flex-between mb-6" style="gap:16px;flex-wrap:wrap;">
  <div class="flex gap-3" style="align-items:center;flex-wrap:wrap;">
    <span class="badge badge-<?= $atendimento['prioridade'] ?>" style="font-size:13px;padding:6px 14px;">
      <?= ucfirst($atendimento['prioridade']) ?>
    </span>
    <span class="badge badge-<?= $atendimento['status'] ?>" style="font-size:13px;padding:6px 14px;">
      <?= ucfirst(str_replace('_',' ',$atendimento['status'])) ?>
    </span>
    <span class="text-muted text-sm">📅 <?= date('d/m/Y', strtotime($atendimento['created_at'])) ?></span>
    <span class="text-muted text-sm">👤 <?= e($atendimento['conselheiro']) ?></span>
  </div>
  <div class="flex gap-2 show-header-actions">
    <?php if (!$analiseIA): ?>
    <button onclick="analisarIA()" class="btn btn-primary" id="btnIA">
      🤖 Analisar com IA
    </button>
    <?php else: ?>
    <button onclick="analisarIA()" class="btn btn-secondary btn-sm">
      🔄 Re-analisar
    </button>
    <?php endif; ?>
    <button onclick="openDocModal()" class="btn btn-success">
      📄 Gerar Documento
    </button>
    <a href="<?= url('/atendimentos') ?>" class="btn btn-secondary">← Voltar</a>
  </div>
</div>

<!-- TABS -->
<div class="tabs">
  <button class="tab-btn active" onclick="openTab('visao-geral', this)">📋 Visão Geral</button>
  <button class="tab-btn" onclick="openTab('ia-analise', this)">🤖 Análise IA</button>
  <button class="tab-btn" onclick="openTab('mapa-mental', this)">🗺️ Mapa Mental</button>
  <button class="tab-btn" onclick="openTab('minutas', this)">📝 Minutas</button>
  <button class="tab-btn" onclick="openTab('medidas', this)">⚖️ Medidas</button>
  <button class="tab-btn" onclick="openTab('documentos', this)">📁 Documentos</button>
</div>

<!-- TAB: Visão Geral -->
<div id="tab-visao-geral" class="tab-panel active">
  <div class="grid grid-2" style="gap:24px;align-items:start;">
    <div style="display:flex;flex-direction:column;gap:16px;">
      <!-- Dados Identificação -->
      <div class="card">
        <div class="card-header">
          <h3>👤 Identificação <span class="text-xs text-muted">(Dado Criptografado)</span></h3>
        </div>
        <div class="card-body">
          <?php
            $campos = [
              ['Nome', $atendimento['nome_crianca_dec']],
              ['Gênero', $atendimento['genero']],
              ['Filiação', $atendimento['filiacao_dec']],
              ['Escola', $atendimento['escola']],
              ['Endereço', $atendimento['endereco_dec']],
            ];
            foreach ($campos as [$label, $valor]):
              if (!$valor) continue; ?>
          <div style="display:flex;gap:16px;padding:10px 0;border-bottom:1px solid var(--border);">
            <div class="text-sm text-muted" style="min-width:80px;"><?= $label ?></div>
            <div class="text-sm fw-600"><?= e($valor) ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Encaminhamentos registrados -->
      <div class="card">
        <div class="card-header">
          <h3>📍 Encaminhamentos</h3>
          <button onclick="openEncModal()" class="btn btn-secondary btn-sm">+ Adicionar</button>
        </div>
        <div class="card-body" style="padding:12px;">
          <?php if (empty($encaminhamentos)): ?>
            <p class="text-sm text-muted">Nenhum encaminhamento registrado.</p>
          <?php else: ?>
            <?php foreach ($encaminhamentos as $enc): ?>
            <div class="enc-card">
              <div class="enc-header">
                <span class="enc-orgao"><?= e($enc['orgao_destino']) ?></span>
                <span class="badge badge-<?= $enc['status'] ?>"><?= ucfirst($enc['status']) ?></span>
              </div>
              <p class="enc-desc"><?= e($enc['descricao'] ?? '') ?></p>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:16px;">
      <!-- Relato da Visita -->
      <div class="card">
        <div class="card-header"><h3>📋 Relato da Visita</h3></div>
        <div class="card-body">
          <p style="white-space:pre-wrap;font-size:14px;line-height:1.8;color:var(--text-secondary);"><?= e($atendimento['relato_visita']) ?></p>
        </div>
      </div>

      <!-- Levantamento -->
      <?php if ($atendimento['levantamento_preliminar']): ?>
      <div class="card">
        <div class="card-header"><h3>🔍 Levantamento Preliminar</h3></div>
        <div class="card-body">
          <p style="white-space:pre-wrap;font-size:14px;line-height:1.8;color:var(--text-secondary);"><?= e($atendimento['levantamento_preliminar']) ?></p>
        </div>
      </div>
      <?php endif; ?>

      <!-- Status Update -->
      <div class="card">
        <div class="card-header"><h3>⚙️ Atualizar Status</h3></div>
        <div class="card-body">
          <div class="flex gap-2" style="flex-wrap:wrap;">
            <?php foreach (['aberto','em_andamento','encerrado','arquivado'] as $s): ?>
            <button onclick="updateStatus('<?= $s ?>')"
              class="btn <?= $atendimento['status'] === $s ? 'btn-primary' : 'btn-secondary' ?> btn-sm">
              <?= ucfirst(str_replace('_',' ',$s)) ?>
            </button>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- TAB: Análise IA -->
<div id="tab-ia-analise" class="tab-panel">
  <?php if (!$analiseIA): ?>
    <div style="text-align:center;padding:60px 20px;">
      <div style="font-size:48px;margin-bottom:16px;">🤖</div>
      <h3 style="color:var(--text-secondary);">Nenhuma análise gerada ainda</h3>
      <p class="text-muted text-sm">Clique em "Analisar com IA" para obter orientação jurídica baseada no ECA/SUAS.</p>
      <button onclick="analisarIA()" class="btn btn-primary" style="margin-top:20px;" id="btnIA2">🤖 Iniciar Análise</button>
    </div>
  <?php else: ?>
    <div class="grid grid-2" style="gap:20px;align-items:start;">
      <div style="display:flex;flex-direction:column;gap:16px;">
        <!-- Análise de Leis -->
        <div class="card">
          <div class="card-header">
            <h3>⚖️ Análise de Leis</h3>
            <span class="badge badge-media">IA · ECA/SUAS</span>
          </div>
          <div class="card-body">
            <p style="font-size:14px;line-height:1.9;color:var(--text-secondary);">
              <?= nl2br(e($analiseIA['analise_juridica'] ?? '')) ?>
            </p>
          </div>
        </div>

        <!-- Observações -->
        <?php if (!empty($analiseIA['observacoes'])): ?>
        <div class="card" style="border-color:rgba(245,158,11,0.3);">
          <div class="card-header" style="background:rgba(245,158,11,0.05);">
            <h3>💡 Observações Importantes</h3>
          </div>
          <div class="card-body">
            <p style="font-size:13px;line-height:1.8;color:var(--text-secondary);"><?= nl2br(e($analiseIA['observacoes'] ?? '')) ?></p>
          </div>
        </div>
        <?php endif; ?>
      </div>

      <div style="display:flex;flex-direction:column;gap:16px;">
        <!-- Fluxo de Encaminhamento -->
        <div class="card">
          <div class="card-header"><h3>📍 Fluxo de Encaminhamentos</h3></div>
          <div class="card-body" style="padding:12px;">
            <?php foreach (($fluxo ?? []) as $enc): ?>
            <div class="enc-card" style="margin-bottom:10px;">
              <div class="enc-header">
                <span class="enc-orgao"><?= e($enc['orgao'] ?? '') ?></span>
                <span class="badge badge-<?= $enc['urgencia'] ?? 'media' ?>"><?= ucfirst($enc['urgencia'] ?? '') ?></span>
              </div>
              <div class="flex gap-2" style="margin:6px 0;flex-wrap:wrap;">
                <span class="badge badge-media text-xs"><?= e($enc['tipo'] ?? '') ?></span>
                <span class="text-xs text-muted"><?= e($enc['artigo_eca'] ?? '') ?></span>
              </div>
              <p class="enc-desc"><?= nl2br(e($enc['descricao'] ?? '')) ?></p>
              <?php if (!empty($enc['tipificacao_suas'])): ?>
              <p class="text-xs text-muted" style="margin:4px 0 0;">SUAS: <?= e($enc['tipificacao_suas']) ?></p>
              <?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Medidas Sugeridas -->
        <?php if (!empty($analiseIA['medidas_sugeridas'])): ?>
        <div class="card">
          <div class="card-header"><h3>🛡️ Medidas Sugeridas pela IA</h3></div>
          <div class="card-body" style="padding:12px;">
            <?php foreach ($analiseIA['medidas_sugeridas'] as $ms): ?>
            <div style="padding:12px;background:var(--bg-secondary);border-radius:var(--radius-sm);margin-bottom:8px;">
              <div class="flex-between">
                <span class="fw-600 text-sm"><?= e($ms['tipo'] ?? '') ?></span>
                <span class="badge badge-media text-xs"><?= e($ms['artigo_eca'] ?? '') ?></span>
              </div>
              <p class="text-sm text-muted" style="margin:6px 0 0;"><?= e($ms['descricao'] ?? '') ?></p>
              <button onclick="aplicarMedida('<?= e($ms['tipo'] ?? '') ?>','<?= e($ms['artigo_eca'] ?? '') ?>')"
                      class="btn btn-warning btn-sm" style="margin-top:8px;">
                ⚖️ Aplicar Medida
              </button>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
</div>

<!-- TAB: Mapa Mental -->
<div id="tab-mapa-mental" class="tab-panel">
  <?php if (empty($mapaMermaid)): ?>
    <div style="text-align:center;padding:60px;">
      <div style="font-size:48px;margin-bottom:16px;">🗺️</div>
      <p class="text-muted">Gere uma análise com IA para visualizar o mapa mental do caso.</p>
    </div>
  <?php else: ?>
    <div class="card">
      <div class="card-header">
        <h3>🗺️ Mapa Mental do Fluxo de Atendimento</h3>
        <span class="text-sm text-muted">Gerado pela IA · Mermaid.js</span>
      </div>
      <div class="mermaid-container">
        <div class="mermaid"><?= e($mapaMermaid) ?></div>
      </div>
    </div>
  <?php endif; ?>
</div>

<!-- TAB: Minutas -->
<div id="tab-minutas" class="tab-panel">
  <?php if (empty($minutas)): ?>
    <div style="text-align:center;padding:60px;">
      <div style="font-size:48px;margin-bottom:16px;">📝</div>
      <p class="text-muted">Minutas serão geradas após a análise com IA.</p>
    </div>
  <?php else: ?>
    <div style="display:flex;flex-direction:column;gap:20px;">
      <?php foreach ($minutas as $tipo => $texto): ?>
      <div class="card">
        <div class="card-header">
          <h3>📄 <?= ucfirst(str_replace('_',' ', $tipo)) ?></h3>
          <button onclick="copyTexto('minuta_<?= $loop ?? 0 ?>')" class="btn btn-secondary btn-sm">📋 Copiar</button>
        </div>
        <div class="card-body">
          <textarea id="minuta_<?= $loop ?? 0 ?>" class="form-control"
                    rows="14" style="font-family:monospace;font-size:13px;"><?= e($texto) ?></textarea>
        </div>
      </div>
      <?php $loop = ($loop ?? 0) + 1; endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<!-- TAB: Medidas -->
<div id="tab-medidas" class="tab-panel">
  <div class="flex-between mb-4">
    <h3 style="margin:0;">Medidas de Proteção Aplicadas</h3>
    <button onclick="openMedidaModal()" class="btn btn-primary">⚖️ Aplicar Nova Medida</button>
  </div>

  <?php if (empty($medidas)): ?>
    <div class="card" style="text-align:center;padding:60px;">
      <div style="font-size:48px;margin-bottom:16px;">⚖️</div>
      <p class="text-muted">Nenhuma medida de proteção aplicada ainda.</p>
    </div>
  <?php else: ?>
    <div style="display:flex;flex-direction:column;gap:12px;">
      <?php foreach ($medidas as $m): ?>
      <div class="card">
        <div class="card-body">
          <div class="flex-between" style="flex-wrap:wrap;gap:8px;">
            <div>
              <div class="fw-600"><?= e($m['tipo_medida']) ?></div>
              <div class="text-sm text-muted"><?= e($m['artigo_eca']) ?> · Aplicada em <?= date('d/m/Y', strtotime($m['created_at'])) ?></div>
            </div>
            <span class="badge badge-<?= $m['status'] === 'aplicada' ? 'media' : 'encerrado' ?>"><?= ucfirst($m['status']) ?></span>
          </div>
          <?php if ($m['descricao']): ?>
            <p style="margin:10px 0 0;font-size:13px;color:var(--text-secondary);"><?= nl2br(e($m['descricao'])) ?></p>
          <?php endif; ?>
          <?php if ($m['prazo_cumprimento']): ?>
            <p class="text-sm text-muted" style="margin-top:6px;">⏰ Prazo: <?= date('d/m/Y', strtotime($m['prazo_cumprimento'])) ?></p>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<!-- TAB: Documentos -->
<div id="tab-documentos" class="tab-panel">
  <div class="flex-between mb-4">
    <h3 style="margin:0;">Documentos Gerados</h3>
    <button onclick="openDocModal()" class="btn btn-success">📄 Gerar Documento com Assinatura</button>
  </div>

  <div class="alert alert-warning">
    🔐 <strong>LGPD:</strong> Documentos são automaticamente excluídos do servidor após 3 dias da geração, conforme política de retenção de dados.
  </div>

  <?php if (empty($documentos)): ?>
    <div class="card" style="text-align:center;padding:60px;">
      <div style="font-size:48px;margin-bottom:16px;">📁</div>
      <p class="text-muted">Nenhum documento gerado para este atendimento.</p>
    </div>
  <?php else: ?>
    <div style="display:flex;flex-direction:column;gap:12px;">
      <?php foreach ($documentos as $doc): ?>
      <div class="card">
        <div class="card-body">
          <div class="flex-between" style="flex-wrap:wrap;gap:8px;">
            <div class="flex gap-3" style="align-items:center;">
              <span style="font-size:24px;">📄</span>
              <div>
                <div class="fw-600"><?= e($doc['tipo_documento']) ?></div>
                <div class="text-sm text-muted">
                  Gerado em <?= date('d/m/Y H:i', strtotime($doc['created_at'])) ?>
                  · Expira <?= date('d/m/Y', strtotime($doc['expira_em'])) ?>
                </div>
              </div>
            </div>
            <div class="flex gap-2" style="align-items:center;">
              <?php if ($doc['assinado']): ?>
                <span class="badge badge-encerrado">✅ Assinado</span>
              <?php else: ?>
                <span class="badge badge-alta">Sem assinatura</span>
              <?php endif; ?>
              <a href="<?= url('/atendimentos/' . $atendimento['id'] . '/download/' . $doc['id']) ?>"
                 class="btn btn-secondary btn-sm">⬇️ Baixar</a>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<!-- ===== MODAIS ===== -->

<!-- Modal: Encaminhamento -->
<div id="encModal" class="modal-overlay hidden">
  <form id="formEnc" onsubmit="salvarEncaminhamento(event)">
    <?= csrf_field() ?>
    <div class="modal" style="max-width:500px;">
      <div class="modal-header">
        <h3>📍 Registrar Encaminhamento</h3>
        <button type="button" onclick="closeEncModal()" class="modal-close">×</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Órgão de Destino <span class="required">*</span></label>
          <input type="text" name="orgao_destino" class="form-control"
                 placeholder="Ex.: CRAS, CREAS, UBS, Delegacia..." required>
        </div>
        <div class="form-group">
          <label class="form-label">Descrição do Encaminhamento <span class="required">*</span></label>
          <textarea name="descricao" class="form-control" rows="4" required
                    placeholder="Descreva o motivo e detalhes do encaminhamento..."></textarea>
        </div>
        <div class="form-group">
          <label class="form-label">Status</label>
          <select name="status" class="form-control">
            <option value="pendente">Pendente</option>
            <option value="realizado">Realizado</option>
            <option value="cancelado">Cancelado</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" onclick="closeEncModal()" class="btn btn-secondary">Cancelar</button>
        <button type="submit" class="btn btn-primary">📍 Registrar Encaminhamento</button>
      </div>
    </div>
  </form>
</div>

<!-- Modal: Gerar Documento + Assinatura -->
<div id="docModal" class="modal-overlay hidden">
  <div class="modal" style="max-width:680px;">
    <div class="modal-header">
      <h3>📄 Gerar Documento com Assinatura Digital</h3>
      <button onclick="closeDocModal()" class="modal-close">×</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Tipo de Documento</label>
        <select id="tipoDoc" class="form-control">
          <option>Relatório de Atendimento</option>
          <option>Ofício de Encaminhamento</option>
          <option>Termo de Medida de Proteção</option>
          <option>Termo de Advertência</option>
          <option>Comunicado ao Ministério Público</option>
          <option>Declaração do Conselho Tutelar</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Conteúdo do Documento</label>
        <textarea id="docConteudo" class="form-control" rows="8"
                  placeholder="Digite o conteúdo do documento ou cole a minuta gerada pela IA..."><?= e($minutas['relatorio_atendimento'] ?? '') ?></textarea>
      </div>

      <div class="grid grid-2">
        <div class="form-group">
          <label class="form-label">Nome do Assinante</label>
          <input type="text" id="assinanteNome" class="form-control"
                 value="<?= e(\App\Core\Auth::user()['nome'] ?? '') ?>" placeholder="Nome completo">
        </div>
        <div class="form-group">
          <label class="form-label">Cargo</label>
          <input type="text" id="assinanteCargo" class="form-control"
                 value="Conselheiro(a) Tutelar" placeholder="Cargo/função">
        </div>
      </div>

      <!-- Área de Assinatura -->
      <div class="form-group">
        <label class="form-label">Assinatura Digital <span class="text-muted text-xs">(Desenhe abaixo)</span></label>
        <div class="signature-wrapper">
          <canvas id="signaturePad" height="180" style="background:#fff;cursor:crosshair;width:100%;"></canvas>
        </div>
        <div class="signature-actions">
          <button onclick="clearSignature()" class="btn btn-secondary btn-sm">🗑️ Limpar</button>
          <span class="text-xs text-muted" style="margin-left:8px;">Use o mouse ou dedo (touch) para assinar</span>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button onclick="closeDocModal()" class="btn btn-secondary">Cancelar</button>
      <button onclick="gerarDocumento()" class="btn btn-success" id="btnGerarDoc">
        📄 Gerar e Salvar Documento
      </button>
    </div>
  </div>
</div>

<!-- Modal: Medida de Proteção -->
<div id="medidaModal" class="modal-overlay hidden">
  <form id="formMedida" onsubmit="salvarMedida(event)">
    <?= csrf_field() ?>
    <div class="modal" style="max-width:560px;">
      <div class="modal-header">
        <h3>⚖️ Aplicar Medida de Proteção</h3>
        <button type="button" onclick="closeMedidaModal()" class="modal-close">×</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Medida de Proteção (ECA Art. 101)</label>
          <select id="tipoMedida" name="tipo_medida" class="form-control" required>
            <option value="">Selecione a medida...</option>
            <option>Encaminhamento aos pais ou responsável</option>
            <option>Orientação, apoio e acompanhamento temporários</option>
            <option>Matrícula e frequência obrigatórias em estabelecimento de ensino</option>
            <option>Inclusão em programa de auxílio à família</option>
            <option>Requisição de tratamento médico, psicológico ou psiquiátrico</option>
            <option>Inclusão em programa oficial ou comunitário de proteção à família</option>
            <option>Acolhimento institucional</option>
            <option>Inclusão em programa de acolhimento familiar</option>
            <option>Colocação em família substituta</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Fundamentação Legal</label>
          <input type="text" id="artigoECA" name="artigo_eca" class="form-control" placeholder="Ex: Art. 101, IV do ECA">
        </div>
        <div class="form-group">
          <label class="form-label">Descrição da Medida *</label>
          <textarea name="descricao" class="form-control" rows="4" required
                    placeholder="Detalhe como a medida será aplicada..."></textarea>
        </div>
        <div class="grid grid-2">
          <div class="form-group">
            <label class="form-label">Prazo de Cumprimento</label>
            <input type="date" name="prazo_cumprimento" class="form-control">
          </div>
          <div class="form-group">
            <label class="form-label">Fundamentação Jurídica</label>
            <input type="text" name="fundamentacao_legal" class="form-control" placeholder="Base legal adicional">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" onclick="closeMedidaModal()" class="btn btn-secondary">Cancelar</button>
        <button type="submit" class="btn btn-primary">⚖️ Aplicar Medida</button>
      </div>
    </div>
  </form>
</div>

<script>
const ATEND_ID = <?= (int)$atendimento['id'] ?>;

// TABS
function openTab(name, btn) {
  document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.getElementById('tab-' + name).classList.add('active');
  if (btn) btn.classList.add('active');
  if (name === 'mapa-mental') setTimeout(() => mermaid.run(), 200);
}

// ANÁLISE IA
function analisarIA() {
  const btn = document.getElementById('btnIA') || document.getElementById('btnIA2');
  if (btn) { btn.disabled = true; btn.innerHTML = '<span class="loading-spinner"></span> Analisando...'; }

  fetch('<?= url('/atendimentos/' . $atendimento['id'] . '/analisar-ia') ?>', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': '<?= \App\Core\Request::csrf() ?>',
    },
    body: JSON.stringify({ _token: '<?= \App\Core\Request::csrf() ?>' }),
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      showToast('✅ Análise concluída! Atualizando página...', 'success');
      setTimeout(() => location.reload(), 1500);
    } else {
      showToast('❌ Erro na análise: ' + (data.error || 'Tente novamente'), 'error');
      if (btn) { btn.disabled = false; btn.innerHTML = '🤖 Analisar com IA'; }
    }
  })
  .catch(err => {
    showToast('❌ Erro de conexão', 'error');
    if (btn) { btn.disabled = false; btn.innerHTML = '🤖 Analisar com IA'; }
  });
}

// STATUS UPDATE
function updateStatus(status) {
  fetch('<?= url('/atendimentos/' . $atendimento['id'] . '/status') ?>', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': '<?= \App\Core\Request::csrf() ?>',
    },
    body: JSON.stringify({ _token: '<?= \App\Core\Request::csrf() ?>', status }),
  })
  .then(r => r.json())
  .then(() => {
    showToast('✅ Status atualizado!', 'success');
    setTimeout(() => location.reload(), 800);
  });
}

// DOCUMENTO MODAL
let signPad;

function openDocModal() {
  document.getElementById('docModal').classList.remove('hidden');
  setTimeout(initSignaturePad, 100);
}

function closeDocModal() {
  document.getElementById('docModal').classList.add('hidden');
}

function initSignaturePad() {
  const canvas = document.getElementById('signaturePad');
  const wrapper = canvas.parentElement;
  canvas.width  = wrapper.offsetWidth || 600;
  canvas.height = 180;
  signPad = new SignaturePad(canvas, {
    backgroundColor: 'rgba(255,255,255,1)',
    penColor: '#1a1a2e',
    minWidth: 1.5,
    maxWidth: 3,
  });
}

function clearSignature() { signPad?.clear(); }

function gerarDocumento() {
  const btn = document.getElementById('btnGerarDoc');
  btn.disabled = true;
  btn.innerHTML = '<span class="loading-spinner"></span> Gerando...';

  const assinatura = signPad && !signPad.isEmpty() ? signPad.toDataURL() : '';

  fetch('<?= url('/atendimentos/' . $atendimento['id'] . '/documento') ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    body: JSON.stringify({
      _token:          '<?= \App\Core\Request::csrf() ?>',
      tipo_documento:  document.getElementById('tipoDoc').value,
      conteudo:        document.getElementById('docConteudo').value,
      assinante_nome:  document.getElementById('assinanteNome').value,
      assinante_cargo: document.getElementById('assinanteCargo').value,
      assinatura_data: assinatura,
    }),
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      showToast('✅ Documento gerado com sucesso!', 'success');
      closeDocModal();
      setTimeout(() => location.reload(), 1200);
    } else {
      showToast('❌ Erro ao gerar documento', 'error');
    }
    btn.disabled = false;
    btn.innerHTML = '📄 Gerar e Salvar Documento';
  });
}

// MEDIDAS
function openMedidaModal() { document.getElementById('medidaModal').classList.remove('hidden'); }
function closeMedidaModal() { document.getElementById('medidaModal').classList.add('hidden'); }

function aplicarMedida(tipo, artigo) {
  document.getElementById('tipoMedida').value = tipo;
  document.getElementById('artigoECA').value  = artigo;
  openMedidaModal();
}

function salvarMedida(e) {
  e.preventDefault();
  const fd   = new FormData(e.target);
  const data = Object.fromEntries(fd.entries());

  fetch('<?= url('/atendimentos/' . $atendimento['id'] . '/medidas') ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    body: JSON.stringify(data),
  })
  .then(r => r.json())
  .then(res => {
    if (res.success) {
      showToast('✅ Medida aplicada com sucesso!', 'success');
      closeMedidaModal();
      setTimeout(() => location.reload(), 800);
    } else {
      showToast('❌ Erro ao aplicar medida', 'error');
    }
  });
}

function copyTexto(id) {
  const el = document.getElementById(id);
  el.select();
  navigator.clipboard.writeText(el.value).then(() => showToast('✅ Texto copiado!', 'success'));
}

// ENCAMINHAMENTO MODAL
function openEncModal()  { document.getElementById('encModal').classList.remove('hidden'); }
function closeEncModal() { document.getElementById('encModal').classList.add('hidden'); }

function salvarEncaminhamento(e) {
  e.preventDefault();
  const fd   = new FormData(e.target);
  const data = Object.fromEntries(fd.entries());

  fetch('<?= url('/atendimentos/' . $atendimento['id'] . '/encaminhamentos') ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    body: JSON.stringify(data),
  })
  .then(r => r.json())
  .then(res => {
    if (res.success) {
      showToast('✅ Encaminhamento registrado!', 'success');
      closeEncModal();
      setTimeout(() => location.reload(), 800);
    } else {
      showToast('❌ ' + (res.error || 'Erro ao registrar encaminhamento'), 'error');
    }
  })
  .catch(() => showToast('❌ Erro de conexão', 'error'));
}

// Close on overlay click
document.getElementById('encModal').addEventListener('click', function(e) {
  if (e.target === this) closeEncModal();
});
document.getElementById('docModal').addEventListener('click', function(e) {
  if (e.target === this) closeDocModal();
});
document.getElementById('medidaModal').addEventListener('click', function(e) {
  if (e.target === this) closeMedidaModal();
});
</script>
