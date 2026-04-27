<?php
  $title    = 'Agenda';
  $subtitle = 'Compromissos e prazos do mês';

  $nomesMes = ['','Janeiro','Fevereiro','Março','Abril','Maio','Junho',
               'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];

  $mesAnterior = $mes === 1 ? 12 : $mes - 1;
  $anoAnterior = $mes === 1 ? $ano - 1 : $ano;
  $mesSeguinte = $mes === 12 ? 1 : $mes + 1;
  $anoSeguinte = $mes === 12 ? $ano + 1 : $ano;

  $primeiroDia    = (int) date('N', strtotime($inicioMes)); // 1=seg … 7=dom
  $diasNoMes      = (int) date('t', strtotime($inicioMes));
  $hoje           = (int) date('j');
  $mesHoje        = (int) date('m');
  $anoHoje        = (int) date('Y');
  $ehMesAtual     = ($mes === $mesHoje && $ano === $anoHoje);

  $tipoIcon = [
    'visita'    => '🏠',
    'audiencia' => '⚖️',
    'reuniao'   => '👥',
    'prazo'     => '⏰',
    'outro'     => '📌',
  ];
  $tipoLabel = [
    'visita'    => 'Visita Domiciliar',
    'audiencia' => 'Audiência',
    'reuniao'   => 'Reunião',
    'prazo'     => 'Prazo',
    'outro'     => 'Outro',
  ];
?>

<style>
/* ── Calendário ─────────────────────────────────────── */
.cal-nav {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 16px; flex-wrap: wrap; gap: 10px;
}
.cal-nav h2 { font-size: 20px; font-weight: 800; margin: 0; letter-spacing: -0.3px; }
.cal-nav-btns { display: flex; gap: 6px; }

.cal-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 3px;
}
.cal-header-cell {
  text-align: center;
  font-size: 11px; font-weight: 700;
  text-transform: uppercase; letter-spacing: 0.8px;
  color: var(--text-muted);
  padding: 8px 0;
}
.cal-cell {
  min-height: 90px;
  background: var(--bg-secondary);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  padding: 6px;
  position: relative;
  transition: border-color .15s;
  cursor: pointer;
}
.cal-cell:hover { border-color: var(--border-light); }
.cal-cell.empty { background: transparent; border-color: transparent; cursor: default; }
.cal-cell.hoje  {
  border-color: var(--accent);
  background: rgba(99,102,241,0.05);
  box-shadow: 0 0 0 1px rgba(99,102,241,0.2);
}
.cal-day-num {
  font-size: 12px; font-weight: 700;
  color: var(--text-secondary);
  margin-bottom: 4px;
  display: flex; align-items: center; justify-content: space-between;
}
.cal-cell.hoje .cal-day-num { color: var(--accent); }
.cal-hoje-dot {
  width: 6px; height: 6px; border-radius: 50%;
  background: var(--accent); flex-shrink: 0;
}
.cal-event {
  font-size: 10px; font-weight: 600;
  padding: 2px 5px;
  border-radius: 4px;
  margin-bottom: 2px;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  cursor: pointer;
  line-height: 1.5;
}
.cal-event.agendado  { background: rgba(99,102,241,0.15); color: var(--accent); }
.cal-event.realizado { background: rgba(34,197,94,0.15);  color: var(--success); }
.cal-event.cancelado { background: rgba(100,116,139,0.12); color: var(--text-muted); text-decoration: line-through; }
.cal-more {
  font-size: 10px; color: var(--text-muted);
  font-weight: 600; cursor: pointer;
}

/* ── Lista de próximos ─────────────────────────────── */
.agenda-item {
  display: flex; align-items: flex-start; gap: 14px;
  padding: 14px 16px;
  border-bottom: 1px solid var(--border);
  transition: background .15s;
}
.agenda-item:last-child { border-bottom: none; }
.agenda-item:hover { background: var(--bg-card-hover); }

.agenda-date-col {
  text-align: center; flex-shrink: 0;
  width: 44px;
}
.agenda-day  { font-size: 22px; font-weight: 900; line-height: 1; color: var(--accent); }
.agenda-mon  { font-size: 10px; font-weight: 700; text-transform: uppercase;
               letter-spacing: 0.5px; color: var(--text-muted); }
.agenda-time { font-size: 11px; color: var(--text-muted); margin-top: 2px; }

.agenda-content { flex: 1; min-width: 0; }
.agenda-title   { font-size: 14px; font-weight: 700; margin-bottom: 3px;
                  white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.agenda-meta    { font-size: 12px; color: var(--text-muted); display: flex; gap: 10px; flex-wrap: wrap; }

.agenda-actions { display: flex; gap: 6px; flex-shrink: 0; align-items: center; }

/* ── Tipo badge ──────────────────────────────────────── */
.badge-visita    { background:rgba(16,185,129,.12);  color:var(--success); }
.badge-audiencia { background:rgba(239,68,68,.12);   color:var(--danger); }
.badge-reuniao   { background:rgba(99,102,241,.12);  color:var(--accent); }
.badge-prazo     { background:rgba(245,158,11,.12);  color:var(--warning); }
.badge-outro     { background:rgba(100,116,139,.12); color:var(--text-secondary); }

@media(max-width:768px) {
  .cal-cell { min-height: 56px; }
  .cal-event { display: none; }
  .cal-more  { display: none; }
  .cal-cell.tem-evento::after {
    content: '●';
    font-size: 8px;
    color: var(--accent);
    display: block;
    text-align: center;
  }
}
</style>

<!-- ── Header ────────────────────────────────────────── -->
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px;">
  <div></div>
  <button class="btn btn-primary" onclick="openNovoEvento()">
    ➕ Novo Compromisso
  </button>
</div>

<!-- ── Layout 2 colunas ──────────────────────────────── -->
<div style="display:grid;grid-template-columns:1fr 320px;gap:22px;align-items:start;">

  <!-- CALENDÁRIO -->
  <div class="card">
    <div class="card-body" style="padding:20px;">

      <!-- Navegação mês -->
      <div class="cal-nav">
        <h2>📅 <?= $nomesMes[$mes] ?> <?= $ano ?></h2>
        <div class="cal-nav-btns">
          <a href="<?= url("/agenda?mes={$mesAnterior}&ano={$anoAnterior}") ?>"
             class="btn btn-ghost btn-sm">‹ Anterior</a>
          <?php if (!$ehMesAtual): ?>
          <a href="<?= url('/agenda') ?>" class="btn btn-ghost btn-sm">Hoje</a>
          <?php endif; ?>
          <a href="<?= url("/agenda?mes={$mesSeguinte}&ano={$anoSeguinte}") ?>"
             class="btn btn-ghost btn-sm">Próximo ›</a>
        </div>
      </div>

      <!-- Grid do calendário -->
      <div class="cal-grid">
        <!-- Cabeçalho dias da semana -->
        <?php foreach (['Seg','Ter','Qua','Qui','Sex','Sáb','Dom'] as $d): ?>
          <div class="cal-header-cell"><?= $d ?></div>
        <?php endforeach; ?>

        <!-- Células vazias antes do dia 1 -->
        <?php for ($v = 1; $v < $primeiroDia; $v++): ?>
          <div class="cal-cell empty"></div>
        <?php endfor; ?>

        <!-- Dias do mês -->
        <?php for ($dia = 1; $dia <= $diasNoMes; $dia++):
          $isHoje    = $ehMesAtual && $dia === $hoje;
          $evsDia    = $eventosPorDia[$dia] ?? [];
          $temEvento = !empty($evsDia);
        ?>
          <div class="cal-cell <?= $isHoje ? 'hoje' : '' ?> <?= $temEvento ? 'tem-evento' : '' ?>"
               onclick="abrirDia(<?= $dia ?>, <?= $mes ?>, <?= $ano ?>)">
            <div class="cal-day-num">
              <?= $dia ?>
              <?php if ($isHoje): ?><span class="cal-hoje-dot"></span><?php endif; ?>
            </div>
            <?php foreach (array_slice($evsDia, 0, 2) as $ev): ?>
              <div class="cal-event <?= $ev['status'] ?>"
                   title="<?= e($ev['titulo']) ?>"
                   onclick="event.stopPropagation();abrirEvento(<?= $ev['id'] ?>)">
                <?= $tipoIcon[$ev['tipo']] ?> <?= e(mb_substr($ev['titulo'], 0, 18)) ?>
              </div>
            <?php endforeach; ?>
            <?php if (count($evsDia) > 2): ?>
              <div class="cal-more">+<?= count($evsDia) - 2 ?> mais</div>
            <?php endif; ?>
          </div>
        <?php endfor; ?>
      </div>

      <!-- Legenda -->
      <div style="display:flex;gap:16px;flex-wrap:wrap;margin-top:16px;padding-top:14px;border-top:1px solid var(--border);">
        <?php foreach ($tipoIcon as $tipo => $icon): ?>
        <span style="font-size:12px;color:var(--text-muted);display:flex;align-items:center;gap:4px;">
          <?= $icon ?> <?= $tipoLabel[$tipo] ?>
        </span>
        <?php endforeach; ?>
      </div>

    </div>
  </div>

  <!-- COLUNA DIREITA -->
  <div style="display:flex;flex-direction:column;gap:18px;">

    <!-- Próximos 7 dias -->
    <div class="card">
      <div class="card-header">
        <h3>⏰ Próximos 7 dias</h3>
        <span class="badge" style="background:rgba(99,102,241,.1);color:var(--accent);">
          <?= count($proximosEventos) ?>
        </span>
      </div>
      <?php if (empty($proximosEventos)): ?>
        <div style="padding:32px 16px;text-align:center;color:var(--text-muted);">
          <div style="font-size:32px;margin-bottom:8px;opacity:.4;">✅</div>
          <p style="font-size:13px;margin:0;">Nenhum compromisso<br>nos próximos 7 dias.</p>
        </div>
      <?php else: ?>
        <?php foreach ($proximosEventos as $ev): ?>
        <div class="agenda-item">
          <div class="agenda-date-col">
            <div class="agenda-day"><?= date('d', strtotime($ev['data_inicio'])) ?></div>
            <div class="agenda-mon"><?= $nomesMes[(int)date('m', strtotime($ev['data_inicio']))] ?></div>
            <div class="agenda-time"><?= date('H:i', strtotime($ev['data_inicio'])) ?></div>
          </div>
          <div class="agenda-content">
            <div class="agenda-title"><?= e($ev['titulo']) ?></div>
            <div class="agenda-meta">
              <span><?= $tipoIcon[$ev['tipo']] ?> <?= $tipoLabel[$ev['tipo']] ?></span>
              <?php if ($ev['local']): ?>
                <span>📍 <?= e($ev['local']) ?></span>
              <?php endif; ?>
              <?php if ($ev['numero_protocolo']): ?>
                <span>📋 <?= e($ev['numero_protocolo']) ?></span>
              <?php endif; ?>
            </div>
          </div>
          <div class="agenda-actions">
            <button onclick="marcarRealizado(<?= $ev['id'] ?>)"
                    class="btn btn-ghost btn-sm btn-icon" title="Marcar como realizado">✅</button>
            <button onclick="excluirEvento(<?= $ev['id'] ?>)"
                    class="btn btn-ghost btn-sm btn-icon" title="Excluir" style="color:var(--danger);">🗑</button>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- Resumo do mês -->
    <?php
      $totais = ['agendado' => 0, 'realizado' => 0, 'cancelado' => 0];
      foreach ($eventos as $ev) $totais[$ev['status']]++;
    ?>
    <div class="card">
      <div class="card-header"><h3>📊 Resumo do Mês</h3></div>
      <div class="card-body flex-col" style="gap:10px;">
        <div style="display:flex;justify-content:space-between;align-items:center;">
          <span style="font-size:13px;">📌 Agendados</span>
          <span style="font-weight:800;color:var(--accent);"><?= $totais['agendado'] ?></span>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;">
          <span style="font-size:13px;">✅ Realizados</span>
          <span style="font-weight:800;color:var(--success);"><?= $totais['realizado'] ?></span>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;">
          <span style="font-size:13px;">❌ Cancelados</span>
          <span style="font-weight:800;color:var(--text-muted);"><?= $totais['cancelado'] ?></span>
        </div>
        <div style="border-top:1px solid var(--border);padding-top:10px;display:flex;justify-content:space-between;">
          <span style="font-size:13px;font-weight:700;">Total</span>
          <span style="font-weight:800;"><?= count($eventos) ?></span>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- ══════════════════ MODAL NOVO EVENTO ══════════════════ -->
<div id="modalEvento" class="modal-overlay hidden">
  <div class="modal" style="max-width:540px;">
    <div class="modal-header">
      <h3 id="modalEventoTitulo">➕ Novo Compromisso</h3>
      <button onclick="fecharModal()" class="modal-close">×</button>
    </div>
    <form method="POST" action="<?= url('/agenda') ?>" id="formEvento">
      <?= csrf_field() ?>
      <div class="modal-body flex-col" style="gap:14px;">

        <div class="form-group" style="margin:0;">
          <label class="form-label">Título <span class="required">*</span></label>
          <input type="text" name="titulo" class="form-control" placeholder="Ex.: Visita domiciliar família Silva" required>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
          <div class="form-group" style="margin:0;">
            <label class="form-label">Tipo</label>
            <select name="tipo" class="form-control">
              <option value="visita">🏠 Visita Domiciliar</option>
              <option value="audiencia">⚖️ Audiência</option>
              <option value="reuniao">👥 Reunião</option>
              <option value="prazo">⏰ Prazo</option>
              <option value="outro">📌 Outro</option>
            </select>
          </div>
          <div class="form-group" style="margin:0;">
            <label class="form-label">Prioridade</label>
            <select name="prioridade" class="form-control">
              <option value="baixa">🟢 Baixa</option>
              <option value="media" selected>🔵 Média</option>
              <option value="alta">🟡 Alta</option>
              <option value="urgente">🔴 Urgente</option>
            </select>
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
          <div class="form-group" style="margin:0;">
            <label class="form-label">Data e Hora Início <span class="required">*</span></label>
            <input type="datetime-local" name="data_inicio" id="inputDataInicio" class="form-control" required>
          </div>
          <div class="form-group" style="margin:0;">
            <label class="form-label">Data e Hora Fim</label>
            <input type="datetime-local" name="data_fim" class="form-control">
          </div>
        </div>

        <div class="form-group" style="margin:0;">
          <label class="form-label">Local</label>
          <input type="text" name="local" class="form-control" placeholder="Endereço ou nome do local">
        </div>

        <div class="form-group" style="margin:0;">
          <label class="form-label">Atendimento Vinculado</label>
          <select name="atendimento_id" class="form-control">
            <option value="">— Nenhum —</option>
            <?php foreach ($atendimentos as $at): ?>
            <option value="<?= $at['id'] ?>">
              <?= e($at['numero_protocolo']) ?> — <?= e(mb_substr($at['tipo_demanda'], 0, 40)) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group" style="margin:0;">
          <label class="form-label">Descrição / Observações</label>
          <textarea name="descricao" class="form-control" rows="3"
                    placeholder="Detalhes adicionais sobre o compromisso..."></textarea>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" onclick="fecharModal()" class="btn btn-secondary">Cancelar</button>
        <button type="submit" class="btn btn-primary">📅 Agendar</button>
      </div>
    </form>
  </div>
</div>

<!-- ══════════════════ MODAL DIA ══════════════════ -->
<div id="modalDia" class="modal-overlay hidden">
  <div class="modal" style="max-width:480px;">
    <div class="modal-header">
      <h3 id="modalDiaTitulo">📅 Compromissos do dia</h3>
      <button onclick="document.getElementById('modalDia').classList.add('hidden')" class="modal-close">×</button>
    </div>
    <div id="modalDiaBody" class="modal-body"></div>
    <div class="modal-footer">
      <button onclick="document.getElementById('modalDia').classList.add('hidden')" class="btn btn-secondary">Fechar</button>
      <button onclick="document.getElementById('modalDia').classList.add('hidden');openNovoEvento()" class="btn btn-primary">➕ Novo</button>
    </div>
  </div>
</div>

<script>
const CSRF   = document.querySelector('meta[name="csrf"]')?.content || '';
const nomesMes = ['','Janeiro','Fevereiro','Março','Abril','Maio','Junho',
                  'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];

// Dados dos eventos do mês para uso no JS
const eventosMes = <?= json_encode($eventos, JSON_UNESCAPED_UNICODE) ?>;
const tipoIcon  = <?= json_encode($tipoIcon) ?>;
const tipoLabel = <?= json_encode($tipoLabel) ?>;

function openNovoEvento(data = null) {
  if (data) {
    document.getElementById('inputDataInicio').value = data + 'T09:00';
  }
  document.getElementById('modalEvento').classList.remove('hidden');
}

function fecharModal() {
  document.getElementById('modalEvento').classList.add('hidden');
}

document.getElementById('modalEvento').addEventListener('click', function(e) {
  if (e.target === this) fecharModal();
});

function abrirDia(dia, mes, ano) {
  const pad  = n => String(n).padStart(2, '0');
  const data = `${ano}-${pad(mes)}-${pad(dia)}`;
  const evsDia = eventosMes.filter(e => e.data_inicio.startsWith(data));

  document.getElementById('modalDiaTitulo').textContent =
    `📅 ${dia} de ${nomesMes[mes]} de ${ano}`;

  const body = document.getElementById('modalDiaBody');
  if (evsDia.length === 0) {
    body.innerHTML = `<div style="padding:24px;text-align:center;color:var(--text-muted);">
      <div style="font-size:32px;margin-bottom:8px;">📭</div>
      <p style="font-size:13px;margin:0;">Nenhum compromisso neste dia.</p>
    </div>`;
  } else {
    body.innerHTML = evsDia.map(ev => `
      <div style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;border-bottom:1px solid var(--border);">
        <div style="font-size:24px;flex-shrink:0;">${tipoIcon[ev.tipo]}</div>
        <div style="flex:1;min-width:0;">
          <div style="font-size:14px;font-weight:700;margin-bottom:3px;">${ev.titulo}</div>
          <div style="font-size:12px;color:var(--text-muted);display:flex;gap:8px;flex-wrap:wrap;">
            <span>🕐 ${ev.data_inicio.substring(11,16)}</span>
            ${ev.local ? `<span>📍 ${ev.local}</span>` : ''}
            ${ev.numero_protocolo ? `<span>📋 ${ev.numero_protocolo}</span>` : ''}
          </div>
        </div>
        <div style="display:flex;gap:4px;">
          <button onclick="marcarRealizado(${ev.id})" class="btn btn-ghost btn-sm btn-icon" title="Realizado">✅</button>
          <button onclick="excluirEvento(${ev.id})" class="btn btn-ghost btn-sm btn-icon" style="color:var(--danger);" title="Excluir">🗑</button>
        </div>
      </div>
    `).join('');
  }

  document.getElementById('modalDia').classList.remove('hidden');
  // pré-preenche data no form de novo evento
  document.getElementById('inputDataInicio').value = `${data}T09:00`;
}

function abrirEvento(id) {
  // Abre o modal do dia com foco neste evento
  const ev = eventosMes.find(e => e.id == id);
  if (!ev) return;
  const data = ev.data_inicio.substring(0, 10).split('-');
  abrirDia(parseInt(data[2]), parseInt(data[1]), parseInt(data[0]));
}

function marcarRealizado(id) {
  if (!confirm('Marcar este compromisso como realizado?')) return;
  fetch('<?= url('/agenda') ?>/' + id + '/status', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
    body: JSON.stringify({ _token: CSRF, status: 'realizado' }),
  }).then(r => r.json()).then(d => {
    if (d.success) { showToast('✅ Marcado como realizado!', 'success'); setTimeout(() => location.reload(), 800); }
  });
}

function excluirEvento(id) {
  if (!confirm('Excluir este compromisso?')) return;
  fetch('<?= url('/agenda') ?>/' + id + '/delete', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
    body: JSON.stringify({ _token: CSRF }),
  }).then(r => r.json()).then(d => {
    if (d.success) { showToast('🗑 Compromisso removido.', 'success'); setTimeout(() => location.reload(), 800); }
  });
}
</script>
