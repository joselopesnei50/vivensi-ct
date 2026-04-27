<?php
  $title    = 'Dashboard';
  $subtitle = 'Visão geral — ' . date('d/m/Y');
  $user     = \App\Core\Auth::user();
  $hour     = (int) date('H');
  $greeting = $hour < 12 ? 'Bom dia' : ($hour < 18 ? 'Boa tarde' : 'Boa noite');
  $initials = strtoupper(substr($user['nome'] ?? 'U', 0, 2));
?>

<!-- ── Saudação ──────────────────────────────────────────────── -->
<div class="card card-premium mb-6" style="padding: 24px; border: 1px solid var(--glass-border); background: var(--card-gradient); position: relative; overflow: hidden;">
  <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: var(--accent); opacity: 0.1; filter: blur(80px); border-radius: 50%;"></div>
  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:20px;position:relative;z-index:2;">
    <div style="display:flex; align-items:center; gap:20px;">
      <div class="user-avatar" style="width: 54px; height: 54px; font-size: 20px; box-shadow: 0 0 15px rgba(99,102,241,0.2);"><?= $initials ?></div>
      <div>
        <h2 style="margin:0 0 4px;font-size:22px;font-weight:800;letter-spacing:-0.5px; background: linear-gradient(to right, var(--text-primary), var(--accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
          <?= $greeting ?>, <?= e(explode(' ', $user['nome'])[0]) ?>! 🛡️
        </h2>
        <p class="text-muted text-sm" style="margin:0; font-weight:500;">
          <i class="far fa-calendar-alt me-1"></i> <?= date('d/m/Y') ?> &nbsp;·&nbsp; <span style="color: var(--success); font-weight:700;">● ONLINE</span>
        </p>
      </div>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
      <?php if ($urgentes > 0): ?>
      <a href="<?= url('/atendimentos?prioridade=urgente') ?>"
         class="btn btn-danger"
         style="border-radius: 50px; font-weight:800; animation: pulse 2s infinite;">
        🚨 <?= $urgentes ?> URGENTE<?= $urgentes > 1 ? 'S' : '' ?>
      </a>
      <?php endif; ?>
      <a href="<?= url('/atendimentos') ?>" class="btn btn-ghost" style="border-radius: 50px; font-weight:700;">📋 LISTA GERAL</a>
    </div>
  </div>
</div>

<!-- ── Alerta LGPD ───────────────────────────────────────────── -->
<?php if (!empty($docsExpirando)): ?>
<div class="alert alert-warning mb-4" style="flex-direction:column;gap:8px;">
  <div style="display:flex;align-items:center;gap:8px;">
    <strong>⏰ Alerta LGPD:</strong>
    <?= count($docsExpirando) ?> documento(s) expirarão nas próximas 24h e serão removidos automaticamente.
  </div>
  <div style="display:flex;flex-wrap:wrap;gap:6px;">
    <?php foreach ($docsExpirando as $d): ?>
      <a href="<?= url('/atendimentos/' . $d['atendimento_id']) ?>"
         class="badge badge-alta" style="text-decoration:none;">
        <?= e($d['numero_protocolo']) ?>
      </a>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<!-- ════════════════════════════════════════════════════════════
     FORMULÁRIO — CORAÇÃO DO SISTEMA
════════════════════════════════════════════════════════════ -->
<div class="card mb-6" style="border-color:rgba(99,102,241,0.3);box-shadow:0 4px 24px rgba(99,102,241,0.08);">

  <!-- Cabeçalho do card -->
  <div class="card-header" style="background:rgba(99,102,241,0.06);border-bottom:1px solid rgba(99,102,241,0.1); padding: 20px 24px;">
    <div style="display:flex;align-items:center;gap:14px;">
      <div class="section-icon purple" style="background: var(--accent); color: #fff; box-shadow: 0 0 15px rgba(99,102,241,0.4);">🛡️</div>
      <div>
        <div style="font-size:16px;font-weight:800;letter-spacing:-0.2px;">Fluxo de Atendimento Inteligente</div>
        <div class="text-xs text-muted" style="font-weight:500;">IA-Powered · ECA · SUAS · LGPD Compliance</div>
      </div>
    </div>
    <a href="<?= url('/atendimentos/novo') ?>" class="btn btn-ghost btn-sm" style="border-radius: 50px;">
      <i class="fas fa-expand-alt me-1"></i> TELA CHEIA
    </a>
  </div>

  <div class="card-body" style="padding:0;">

    <div class="wizard-wrap" style="padding:24px;">

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

        <!-- ── ETAPA 1 — Identificação ─────────────────────────── -->
        <div class="wizard-panel active" id="wpanel-0">

          <div class="info-box accent mb-4" style="font-size:12px;">
            🔒 Dados pessoais <strong>criptografados com AES-256</strong> antes de serem gravados — LGPD (Lei 13.709/18).
          </div>

          <div class="grid grid-2">
            <div class="form-group">
              <label class="form-label" for="nome_crianca">
                Nome Completo <span class="required">*</span>
              </label>
              <div class="input-icon-wrap">
                <span class="input-icon">👤</span>
                <input type="text" id="nome_crianca" name="nome_crianca"
                       class="form-control" placeholder="Nome completo da criança/adolescente" required>
              </div>
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
          </div>

        </div>

        <!-- ── ETAPA 2 — Dados do Caso ─────────────────────────── -->
        <div class="wizard-panel" id="wpanel-1">

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

          <div class="form-group">
            <label class="form-label">Prioridade do Caso</label>
            <div class="prioridade-grid">
              <div class="prioridade-option baixa">
                <input type="radio" name="prioridade" id="p_baixa" value="baixa">
                <label for="p_baixa"><div class="p-dot" style="background:#22c55e;"></div><span class="p-label">Baixa</span></label>
              </div>
              <div class="prioridade-option media">
                <input type="radio" name="prioridade" id="p_media" value="media" checked>
                <label for="p_media"><div class="p-dot" style="background:#3b82f6;"></div><span class="p-label">Média</span></label>
              </div>
              <div class="prioridade-option alta">
                <input type="radio" name="prioridade" id="p_alta" value="alta">
                <label for="p_alta"><div class="p-dot" style="background:#f59e0b;"></div><span class="p-label">Alta</span></label>
              </div>
              <div class="prioridade-option urgente">
                <input type="radio" name="prioridade" id="p_urgente" value="urgente">
                <label for="p_urgente"><div class="p-dot" style="background:#ef4444;"></div><span class="p-label">Urgente</span></label>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="relato_visita">
              Relato da Visita <span class="required">*</span>
            </label>
            <textarea id="relato_visita" name="relato_visita"
                      class="form-control" rows="5"
                      data-counter="3000"
                      placeholder="Descreva o que foi observado durante a visita — condições encontradas, estado das crianças/adolescentes e informações relevantes..."
                      required></textarea>
            <p class="form-hint">💡 Quanto mais detalhado, mais precisa será a análise da IA</p>
          </div>

          <div class="form-group">
            <label class="form-label" for="levantamento_preliminar">Levantamento Preliminar</label>
            <textarea id="levantamento_preliminar" name="levantamento_preliminar"
                      class="form-control" rows="3"
                      data-counter="2000"
                      placeholder="Histórico do caso, atendimentos anteriores, situação socioeconômica..."></textarea>
          </div>

        </div>

        <!-- ── ETAPA 3 — IA + SIPIA + Submit ──────────────────── -->
        <div class="wizard-panel" id="wpanel-2">

          <div class="grid grid-2" style="align-items:start;gap:20px;">

            <!-- IA + Rede -->
            <div class="flex-col" style="gap:16px;">

              <div class="card" style="border-color:rgba(99,102,241,0.35);">
                <div class="card-header" style="background:rgba(99,102,241,0.04);">
                  <h3>🤖 Análise com IA</h3>
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
                  <input type="checkbox" name="analisar_ia" value="1" id="analisarIA" style="display:none;" checked>
                </div>
              </div>

              <div class="card">
                <div class="card-header">
                  <h3>🌐 Rede Municipal</h3>
                  <a href="<?= url('/rede-servicos') ?>" class="btn btn-ghost btn-sm">Gerenciar</a>
                </div>
                <div style="max-height:180px;overflow-y:auto;padding:10px 14px;">
                  <?php if (empty($redeServicos)): ?>
                    <div style="padding:16px;text-align:center;">
                      <p class="text-sm text-muted">Nenhum serviço cadastrado.</p>
                      <a href="<?= url('/rede-servicos') ?>" class="btn btn-ghost btn-sm">Cadastrar →</a>
                    </div>
                  <?php else: ?>
                    <?php foreach ($redeServicos as $s): ?>
                    <div class="rede-item">
                      <span style="font-size:18px;">
                        <?= match($s['tipo_servico']) {
                          'CRAS' => '🏢', 'CREAS' => '🏛️', 'Saúde' => '🏥',
                          'Saúde Mental' => '🧠', 'Educação' => '🏫', default => '📍'
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

            <!-- SIPIA -->
            <div class="card" style="border-color:rgba(37,99,235,0.3);">
              <div class="card-header" style="background:rgba(37,99,235,0.04);">
                <h3>📋 SIPIA CT Web</h3>
                <span class="badge badge-sipia">Sistema Nacional</span>
              </div>
              <div class="card-body">
                <div class="info-box mb-4" style="font-size:12px;">
                  ℹ️ Após salvar, use os dados abaixo para registrar no SIPIA CT Web (sistema federal obrigatório).
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
                      <option value="S04">S04 — Uso de Substâncias</option>
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

          <!-- Submit -->
          <div class="card mt-4" style="border-color:rgba(99,102,241,0.3);">
            <div class="card-body" style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
              <div style="flex:1;min-width:200px;">
                <div style="font-size:15px;font-weight:700;margin-bottom:4px;">✅ Tudo pronto!</div>
                <div class="text-sm text-muted">Clique em <strong>Registrar</strong> para salvar e iniciar a análise da IA.</div>
              </div>
              <div style="display:flex;flex-direction:column;gap:8px;min-width:220px;">
                <button type="submit" class="btn btn-primary btn-lg btn-full" id="submitBtn">
                  💾 Registrar Atendimento
                </button>
                <button type="button" onclick="Wizard.goTo(0)" class="btn btn-ghost btn-full">
                  ↺ Recomeçar
                </button>
              </div>
            </div>
          </div>

        </div><!-- /wpanel-2 -->

        <!-- Navegação do wizard -->
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
    </div><!-- /wizard-wrap -->
  </div><!-- /card-body -->
</div><!-- /card principal -->

<!-- ════════════════════════════════════════════════════════════
     SEÇÃO INFERIOR — Stats + Histórico + Info
════════════════════════════════════════════════════════════ -->

<!-- Stats compactas -->
<div class="grid grid-stat mb-6">
  <div class="card card-premium stat-card urgente">
    <div class="stat-icon red" style="background: rgba(239,68,68,0.2);">🚨</div>
    <div class="stat-value" style="color: var(--urgente);"><?= $urgentes ?></div>
    <div class="stat-label">CASOS URGENTES</div>
    <?php if ($urgentes > 0): ?>
    <a href="<?= url('/atendimentos?prioridade=urgente') ?>"
       style="font-size:11px;color:var(--urgente);text-decoration:none;margin-top:10px;display:inline-block; font-weight:800;">VER PRIORITÁRIOS →</a>
    <?php endif; ?>
  </div>
  <div class="card card-premium stat-card">
    <div class="stat-icon blue" style="background: rgba(59,130,246,0.2);">📂</div>
    <div class="stat-value" style="color: var(--info);"><?= $abertos ?></div>
    <div class="stat-label">EM PROCESSAMENTO</div>
    <a href="<?= url('/atendimentos?status=aberto') ?>"
       style="font-size:11px;color:var(--info);text-decoration:none;margin-top:10px;display:inline-block; font-weight:800;">FILTRAR ABERTOS →</a>
  </div>
  <div class="card card-premium stat-card accent">
    <div class="stat-icon purple" style="background: rgba(99,102,241,0.2);">📁</div>
    <div class="stat-value" style="color: var(--accent);"><?= $totalAtendimentos ?></div>
    <div class="stat-label">TOTAL DE REGISTROS</div>
    <a href="<?= url('/atendimentos') ?>"
       style="font-size:11px;color:var(--accent);text-decoration:none;margin-top:10px;display:inline-block; font-weight:800;">HISTÓRICO COMPLETO →</a>
  </div>
  <div class="card card-premium stat-card success">
    <div class="stat-icon green" style="background: rgba(34,197,94,0.2);">👥</div>
    <div class="stat-value" style="color: var(--success);"><?= $totalUsuarios ?></div>
    <div class="stat-label">CONSELHEIROS ATIVOS</div>
  </div>
</div>

<!-- Grid: Histórico + Informações -->
<div class="grid grid-2" style="gap:22px;align-items:start;">

  <!-- Atendimentos Recentes -->
  <div class="card card-premium">
    <div class="card-header" style="border-bottom: 1px solid var(--glass-border);">
      <h3 style="font-size: 16px; font-weight: 800;"><i class="fas fa-history me-2" style="color: var(--accent);"></i> Atendimentos Recentes</h3>
      <a href="<?= url('/atendimentos') ?>" class="btn btn-ghost btn-sm" style="border-radius: 50px;">VER TODOS</a>
    </div>
    <?php if (empty($ultimosAtendimentos)): ?>
      <div style="padding:60px 24px;text-align:center;color:var(--text-muted);">
        <div style="font-size:50px;margin-bottom:15px;opacity:.2;">📭</div>
        <p style="margin:0;font-size:14px; font-weight:500;">Nenhum atendimento registrado no momento.<br>Inicie um novo caso acima.</p>
      </div>
    <?php else: ?>
      <div style="overflow-x:auto;">
        <table class="data-table">
          <thead style="background: rgba(255,255,255,0.02);">
            <tr>
              <th style="padding-left:24px;">Protocolo</th>
              <th>Demanda</th>
              <th>Prioridade</th>
              <th>Status</th>
              <th class="text-end" style="padding-right:24px;">Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($ultimosAtendimentos as $a): ?>
            <tr>
              <td style="padding-left:24px;"><span style="font-weight:900;color:var(--accent); letter-spacing: 0.5px;"><?= e($a['numero_protocolo']) ?></span></td>
              <td><div style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:13px; font-weight:600;"><?= e($a['tipo_demanda']) ?></div></td>
              <td>
                <div style="display:flex;align-items:center;gap:8px;">
                  <span class="prioridade-dot <?= $a['prioridade'] ?>" style="box-shadow: 0 0 8px currentColor;"></span>
                  <span class="badge badge-<?= $a['prioridade'] ?>" style="border-radius: 4px; font-size: 10px;"><?= strtoupper($a['prioridade']) ?></span>
                </div>
              </td>
              <td><span class="badge badge-<?= $a['status'] ?>" style="background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border);"><?= strtoupper(str_replace('_', ' ', $a['status'])) ?></span></td>
              <td class="text-end" style="padding-right:24px;">
                <a href="<?= url('/atendimentos/' . $a['id']) ?>" class="btn btn-ghost btn-sm btn-icon" style="border-radius: 50%; width: 32px; height: 32px;">
                  <i class="fas fa-chevron-right"></i>
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

  <!-- Coluna direita: Base Legal + Status LGPD -->
  <div class="flex-col" style="gap:18px;">

    <!-- Base Jurídica -->
    <div class="card">
      <div class="card-header"><h3>📖 Base Jurídica</h3></div>
      <div class="card-body flex-col" style="gap:8px;">
        <?php
          $leis = [
            ['📜', 'ECA — Lei 8.069/90',        'Estatuto da Criança e do Adolescente', 'var(--info)'],
            ['🏛️', 'SUAS — LOAS 8.742/93',       'Sistema Único de Assistência Social',  'var(--success)'],
            ['⚖️', 'Lei Henry Borel 14.344/22',  'Violência Doméstica e Familiar',       'var(--danger)'],
            ['🔒', 'LGPD — Lei 13.709/18',       'Proteção de Dados Pessoais',           'var(--accent)'],
            ['✍️', 'Lei 14.063/20',              'Assinatura Eletrônica',                'var(--warning)'],
          ];
          foreach ($leis as [$icon, $nome, $desc, $color]): ?>
        <div style="display:flex;align-items:center;gap:12px;padding:10px 12px;background:var(--bg-secondary);border:1px solid var(--border);border-radius:var(--radius-sm);transition:border-color .15s;"
             onmouseover="this.style.borderColor='var(--border-light)'"
             onmouseout="this.style.borderColor='var(--border)'">
          <span style="font-size:18px;width:26px;text-align:center;"><?= $icon ?></span>
          <div>
            <div style="font-size:12px;font-weight:700;color:<?= $color ?>;"><?= $nome ?></div>
            <div class="text-xs text-muted"><?= $desc ?></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- LGPD + Ações rápidas -->
    <div class="info-box success" style="border-radius:var(--radius);">
      <div style="font-size:24px;">🔐</div>
      <div>
        <div style="font-size:13px;font-weight:700;color:var(--success);">Proteção de Dados Ativa</div>
        <div class="text-xs text-muted">Expurgo automático 3 dias · AES-256 · LGPD compliant</div>
      </div>
    </div>

    <?php if (\App\Core\Auth::isAdmin()): ?>
    <a href="<?= url('/admin') ?>" class="btn btn-ghost btn-full">⚙️ Painel Administrativo</a>
    <?php endif; ?>

  </div>
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
