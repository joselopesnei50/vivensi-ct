<?php $title = 'Novo Atendimento'; $subtitle = 'Registre e analise um caso com IA'; ?>

<div class="wizard-wrap">

  <!-- Progress Bar -->
  <div class="wizard-progress">
    <div class="wizard-step active" id="wstep-0">
      <div class="step-circle">1</div>
      <div class="step-meta">
        <div class="step-num">Etapa 1</div>
        <div class="step-title">Identificação</div>
      </div>
    </div>
    <div class="wizard-step" id="wstep-1">
      <div class="step-circle">2</div>
      <div class="step-meta">
        <div class="step-num">Etapa 2</div>
        <div class="step-title">Dados do Caso</div>
      </div>
    </div>
    <div class="wizard-step" id="wstep-2">
      <div class="step-circle">3</div>
      <div class="step-meta">
        <div class="step-num">Etapa 3</div>
        <div class="step-title">Revisão e IA</div>
      </div>
    </div>
  </div>

  <!-- FORM -->
  <form method="POST" action="<?= url('/atendimentos') ?>" id="formAtendimento">
    <?= csrf_field() ?>

    <!-- ============================================================
         ETAPA 1 — Identificação
    ============================================================ -->
    <div class="wizard-panel active" id="wpanel-0">
      <div class="card">
        <div class="card-header">
          <h3>👤 Identificação da Criança / Adolescente</h3>
          <span class="badge badge-lgpd">🔐 LGPD</span>
        </div>
        <div class="card-body">

          <div class="info-box accent mb-4">
            🔒 Os dados pessoais são <strong>criptografados</strong> antes de serem gravados,
            em conformidade com a LGPD (Lei 13.709/18).
          </div>

          <div class="grid grid-2">
            <div class="form-group">
              <label class="form-label" for="nome_crianca">
                Nome Completo <span class="required">*</span>
              </label>
              <div class="input-icon-wrap">
                <span class="input-icon">👤</span>
                <input type="text" id="nome_crianca" name="nome_crianca"
                       class="form-control" placeholder="Nome completo" required>
              </div>
              <p class="form-hint">🔐 Armazenado criptografado (AES-128)</p>
            </div>
            <div class="form-group">
              <label class="form-label" for="genero">Gênero</label>
              <select id="genero" name="genero" class="form-control">
                <option value="">Selecione...</option>
                <option>Masculino</option>
                <option>Feminino</option>
                <option>Não-binário</option>
                <option>Prefere não informar</option>
              </select>
            </div>
          </div>

          <div class="grid grid-2">
            <div class="form-group">
              <label class="form-label" for="filiacao">Filiação (Responsáveis)</label>
              <div class="input-icon-wrap">
                <span class="input-icon">👨‍👩‍👧</span>
                <input type="text" id="filiacao" name="filiacao"
                       class="form-control" placeholder="Nome dos pais ou responsáveis">
              </div>
              <p class="form-hint">🔐 Dado sensível — criptografado</p>
            </div>
            <div class="form-group">
              <label class="form-label" for="escola">Escola / Instituição</label>
              <div class="input-icon-wrap">
                <span class="input-icon">🏫</span>
                <input type="text" id="escola" name="escola"
                       class="form-control" placeholder="Nome da escola">
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="endereco">Endereço</label>
            <div class="input-icon-wrap">
              <span class="input-icon">📍</span>
              <input type="text" id="endereco" name="endereco"
                     class="form-control" placeholder="Rua, número, bairro, cidade">
            </div>
            <p class="form-hint">🔐 Criptografado conforme LGPD</p>
          </div>

        </div>
      </div>
    </div>

    <!-- ============================================================
         ETAPA 2 — Dados do Caso
    ============================================================ -->
    <div class="wizard-panel" id="wpanel-1">
      <div class="card">
        <div class="card-header">
          <h3>📋 Dados do Atendimento</h3>
        </div>
        <div class="card-body">

          <div class="grid grid-2">
            <div class="form-group">
              <label class="form-label" for="tipo_demanda">
                Tipo de Demanda <span class="required">*</span>
              </label>
              <select id="tipo_demanda" name="tipo_demanda" class="form-control" required>
                <option value="">Selecione o tipo...</option>
                <optgroup label="🔴 Violência">
                  <option>Violência Física</option>
                  <option>Violência Psicológica</option>
                  <option>Violência Sexual</option>
                  <option>Negligência</option>
                </optgroup>
                <optgroup label="🟡 Vulnerabilidade Social">
                  <option>Situação de Rua</option>
                  <option>Trabalho Infantil</option>
                  <option>Evasão Escolar</option>
                  <option>Uso de Substâncias</option>
                  <option>Pobreza Extrema</option>
                </optgroup>
                <optgroup label="🟠 Conflito Familiar">
                  <option>Conflito Familiar</option>
                  <option>Abandono</option>
                  <option>Disputa de Guarda</option>
                  <option>Alienação Parental</option>
                </optgroup>
                <optgroup label="🔵 Saúde">
                  <option>Saúde Mental</option>
                  <option>Acesso à Saúde Negado</option>
                  <option>Deficiência sem Suporte</option>
                </optgroup>
                <optgroup label="⚫ Outros">
                  <option>Ato Infracional</option>
                  <option>Exploração</option>
                  <option>Outros</option>
                </optgroup>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label" for="data_ocorrencia">Data da Ocorrência</label>
              <div class="input-icon-wrap">
                <span class="input-icon">📅</span>
                <input type="date" id="data_ocorrencia" name="data_ocorrencia"
                       class="form-control" value="<?= date('Y-m-d') ?>">
              </div>
            </div>
          </div>

          <!-- Prioridade visual -->
          <div class="form-group">
            <label class="form-label">Prioridade do Caso</label>
            <div class="prioridade-grid">
              <div class="prioridade-option baixa">
                <input type="radio" name="prioridade" id="p_baixa" value="baixa">
                <label for="p_baixa">
                  <div class="p-dot" style="background:#22c55e;"></div>
                  <span class="p-label">Baixa</span>
                </label>
              </div>
              <div class="prioridade-option media">
                <input type="radio" name="prioridade" id="p_media" value="media" checked>
                <label for="p_media">
                  <div class="p-dot" style="background:#3b82f6;"></div>
                  <span class="p-label">Média</span>
                </label>
              </div>
              <div class="prioridade-option alta">
                <input type="radio" name="prioridade" id="p_alta" value="alta">
                <label for="p_alta">
                  <div class="p-dot" style="background:#f59e0b;"></div>
                  <span class="p-label">Alta</span>
                </label>
              </div>
              <div class="prioridade-option urgente">
                <input type="radio" name="prioridade" id="p_urgente" value="urgente">
                <label for="p_urgente">
                  <div class="p-dot" style="background:#ef4444;"></div>
                  <span class="p-label">Urgente</span>
                </label>
              </div>
            </div>
          </div>

          <!-- Relato -->
          <div class="form-group">
            <label class="form-label" for="relato_visita">
              Relato da Visita <span class="required">*</span>
            </label>
            <textarea id="relato_visita" name="relato_visita"
                      class="form-control" rows="6"
                      data-counter="3000"
                      placeholder="Descreva detalhadamente o que foi observado durante a visita domiciliar, as condições encontradas, o estado das crianças/adolescentes e demais informações relevantes..."
                      required></textarea>
            <p class="form-hint">💡 Quanto mais detalhado, mais precisa será a análise da IA</p>
          </div>

          <!-- Levantamento -->
          <div class="form-group">
            <label class="form-label" for="levantamento_preliminar">Levantamento Preliminar</label>
            <textarea id="levantamento_preliminar" name="levantamento_preliminar"
                      class="form-control" rows="4"
                      data-counter="2000"
                      placeholder="Histórico do caso, atendimentos anteriores, situação socioeconômica da família..."></textarea>
          </div>

        </div>
      </div>
    </div>

    <!-- ============================================================
         ETAPA 3 — SIPIA + IA + Submit
    ============================================================ -->
    <div class="wizard-panel" id="wpanel-2">

      <div class="grid grid-2" style="align-items:start;gap:20px;">

        <!-- Coluna A -->
        <div class="flex-col" style="gap:18px;">

          <!-- IA Toggle -->
          <div class="card" style="border-color:rgba(99,102,241,0.35);">
            <div class="card-header" style="background:rgba(99,102,241,0.04);">
              <h3>🤖 Análise com Inteligência Artificial</h3>
            </div>
            <div class="card-body">
              <ul class="ia-features">
                <li>⚖️ Análise de leis ECA / SUAS fundamentada</li>
                <li>📍 Fluxo de encaminhamentos por urgência</li>
                <li>🗺️ Mapa Mental do caso (Mermaid.js)</li>
                <li>📄 Minutas de relatório e ofício prontas</li>
                <li>🛡️ Sugestão de Medidas de Proteção (Art. 101)</li>
              </ul>

              <div class="ia-toggle active" id="iaWrap"
                   onclick="var cb=document.getElementById('analisarIA');cb.checked=!cb.checked;this.classList.toggle('active',cb.checked);"
                   style="cursor:pointer;">
                <div class="ia-switch"></div>
                <div style="flex:1;">
                  <div style="font-size:14px;font-weight:700;">Analisar com IA ao salvar</div>
                  <div class="text-sm text-muted">Recomendado — resultado instantâneo</div>
                </div>
              </div>
              <input type="checkbox" name="analisar_ia" value="1" id="analisarIA"
                     style="display:none;" checked>
            </div>
          </div>

          <!-- Rede de Serviços -->
          <div class="card">
            <div class="card-header">
              <h3>🌐 Rede Municipal</h3>
              <a href="<?= url('/rede-servicos') ?>" class="btn btn-ghost btn-sm">Gerenciar</a>
            </div>
            <div style="max-height:200px;overflow-y:auto;padding:10px 14px;">
              <?php if (empty($redeServicos)): ?>
                <div style="padding:20px;text-align:center;">
                  <p class="text-sm text-muted">Nenhum serviço cadastrado.</p>
                  <a href="<?= url('/rede-servicos') ?>" class="btn btn-ghost btn-sm">Cadastrar →</a>
                </div>
              <?php else: ?>
                <?php foreach ($redeServicos as $s): ?>
                <div class="rede-item">
                  <span style="font-size:18px;">
                    <?= match($s['tipo_servico']) {
                      'CRAS' => '🏢', 'CREAS' => '🏛️', 'Saúde' => '🏥',
                      'Saúde Mental' => '🧠', 'Educação' => '🏫',
                      default => '📍'
                    } ?>
                  </span>
                  <div>
                    <div class="text-sm fw-600"><?= e($s['nome_servico']) ?></div>
                    <div class="text-xs text-muted"><?= e($s['tipo_servico']) ?></div>
                  </div>
                </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>

        </div>

        <!-- Coluna B: SIPIA -->
        <div class="card" style="border-color:rgba(37,99,235,0.3);">
          <div class="card-header" style="background:rgba(37,99,235,0.04);">
            <h3>📋 SIPIA CT Web</h3>
            <span class="badge badge-sipia">Sistema Nacional</span>
          </div>
          <div class="card-body">

            <div class="info-box mb-4" style="font-size:12px;">
              ℹ️ Após salvar, use os dados abaixo para registrar no sistema federal obrigatório SIPIA CT Web.
            </div>

            <div class="form-group">
              <label class="form-label" for="sipia_natureza">Natureza da Violação (SIPIA)</label>
              <select id="sipia_natureza" name="sipia_natureza" class="form-control">
                <option value="">Selecione...</option>
                <optgroup label="Violência">
                  <option value="V01">V01 — Violência Física</option>
                  <option value="V02">V02 — Violência Psicológica</option>
                  <option value="V03">V03 — Violência Sexual</option>
                  <option value="V04">V04 — Negligência / Abandono</option>
                  <option value="V05">V05 — Violência Institucional</option>
                </optgroup>
                <optgroup label="Vulnerabilidade Social">
                  <option value="S01">S01 — Situação de Rua</option>
                  <option value="S02">S02 — Trabalho Infantil</option>
                  <option value="S03">S03 — Evasão / Abandono Escolar</option>
                  <option value="S04">S04 — Uso de Substâncias Psicoativas</option>
                  <option value="S05">S05 — Pobreza Extrema</option>
                </optgroup>
                <optgroup label="Convivência Familiar">
                  <option value="F01">F01 — Conflito Familiar</option>
                  <option value="F02">F02 — Abandono Familiar</option>
                  <option value="F03">F03 — Disputa de Guarda</option>
                  <option value="F04">F04 — Alienação Parental</option>
                </optgroup>
                <optgroup label="Saúde / Desenvolvimento">
                  <option value="D01">D01 — Acesso Negado à Saúde</option>
                  <option value="D02">D02 — Saúde Mental</option>
                  <option value="D03">D03 — Deficiência sem Suporte</option>
                </optgroup>
                <optgroup label="Ato Infracional">
                  <option value="A01">A01 — Ato Infracional</option>
                </optgroup>
              </select>
              <p class="form-hint">Módulo I do SIPIA CT — classificação obrigatória</p>
            </div>

            <div class="form-group">
              <label class="form-label" for="sipia_abrangencia">Abrangência</label>
              <select id="sipia_abrangencia" name="sipia_abrangencia" class="form-control">
                <option value="individual" selected>Individual — criança ou adolescente específico</option>
                <option value="coletiva">Coletiva — comunidade ou grupo</option>
              </select>
            </div>

            <div class="info-box warning mb-4" style="flex-direction:column;gap:6px;">
              <div style="font-weight:700;font-size:12px;">📌 Após salvar, registre no SIPIA:</div>
              <ol style="margin:0;padding-left:16px;font-size:11px;line-height:1.9;">
                <li>Acesse <strong>gov.br → SIPIA CT Web</strong> com seu CPF</li>
                <li>Clique em <strong>"Nova Ocorrência"</strong></li>
                <li>Use a natureza selecionada acima e os dados da criança</li>
                <li>Registre os encaminhamentos gerados pelo VivensiCT</li>
                <li>Cole o protocolo SIPIA no campo abaixo</li>
              </ol>
            </div>

            <div class="form-group">
              <label class="form-label" for="sipia_protocolo">Protocolo SIPIA (após registrar)</label>
              <div class="input-icon-wrap">
                <span class="input-icon">🔑</span>
                <input type="text" id="sipia_protocolo" name="sipia_protocolo"
                       class="form-control" placeholder="Ex.: SIPIA-2026-BA-00001">
              </div>
              <p class="form-hint">🔐 Armazenado com criptografia LGPD</p>
            </div>

          </div>
        </div>

      </div>

      <!-- Submit Card -->
      <div class="card mt-4" style="border-color:rgba(99,102,241,0.3);">
        <div class="card-body" style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
          <div style="flex:1;min-width:200px;">
            <div style="font-size:15px;font-weight:700;margin-bottom:4px;">✅ Tudo pronto!</div>
            <div class="text-sm text-muted">
              Clique em <strong>Registrar Atendimento</strong> para salvar e iniciar a análise da IA.
            </div>
          </div>
          <div style="display:flex;flex-direction:column;gap:8px;min-width:220px;">
            <button type="submit" class="btn btn-primary btn-lg btn-full" id="submitBtn">
              💾 Registrar Atendimento
            </button>
            <a href="<?= url('/atendimentos') ?>" class="btn btn-ghost btn-full">
              ← Cancelar
            </a>
          </div>
        </div>
      </div>

    </div><!-- /wpanel-2 -->

    <!-- ============================================================
         NAVEGAÇÃO ÚNICA (fora dos painéis)
    ============================================================ -->
    <div class="wizard-nav" id="wizardNav">
      <button type="button" id="wizardPrev" class="btn btn-ghost" style="visibility:hidden;" onclick="Wizard.prev()">
        ← Anterior
      </button>
      <span id="stepIndicator" class="text-sm text-muted fw-600">Etapa 1 de 3</span>
      <button type="button" id="wizardNext" class="btn btn-primary" onclick="Wizard.next()">
        Próximo →
      </button>
    </div>

  </form>
</div>

<script>
document.getElementById('formAtendimento').addEventListener('submit', function () {
  const btn = document.getElementById('submitBtn');
  const ia  = document.getElementById('analisarIA').checked;
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = ia
      ? '<span class="loading-spinner"></span> Analisando com IA...'
      : '<span class="loading-spinner"></span> Salvando...';
  }
});
</script>
