<?php
$title    = 'Super Admin';
$subtitle = 'Painel Administrativo Global';
$activeTab = $_GET['tab'] ?? 'overview';
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<style>
/* ── Admin Tab Navigation ─────────────────────────────── */
.admin-nav {
  display:flex;gap:4px;background:var(--bg-secondary);
  border-radius:var(--radius);padding:6px;margin-bottom:24px;
  overflow-x:auto;scrollbar-width:none;
}
.admin-nav::-webkit-scrollbar{display:none}
.admin-tab {
  display:flex;align-items:center;gap:7px;
  padding:10px 18px;border-radius:var(--radius-sm);
  font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;
  background:none;border:none;color:var(--text-secondary);
  transition:all .2s;font-family:inherit;
}
.admin-tab.active {
  background:var(--bg-card);color:var(--text-primary);
  box-shadow:0 1px 6px rgba(0,0,0,0.2);
}
.admin-tab:hover:not(.active){color:var(--text-primary);}
.tab-badge {
  background:var(--danger);color:#fff;border-radius:100px;
  font-size:10px;font-weight:800;padding:1px 7px;min-width:18px;text-align:center;
}
.admin-panel{display:none;}
.admin-panel.active{display:block;}

/* ── Online Dot ───────────────────────────── */
.online-dot{width:8px;height:8px;border-radius:50%;background:#10b981;
  display:inline-block;margin-right:6px;animation:blink 2s infinite;}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.3}}

/* ── Health Bar ───────────────────────────── */
.health-bar-wrap{background:var(--bg-secondary);border-radius:100px;height:8px;overflow:hidden;margin-top:6px;}
.health-bar{height:100%;border-radius:100px;transition:width .6s ease;}
.health-bar.ok{background:var(--success);}
.health-bar.warn{background:var(--warning);}
.health-bar.danger{background:var(--danger);}

/* ── Config Card ──────────────────────────── */
.config-section{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
@media(max-width:700px){.config-section{grid-template-columns:1fr;}}
.config-card{
  background:var(--bg-secondary);border-radius:var(--radius-sm);
  padding:20px;border:1px solid var(--border);
}
.config-card-title{font-size:13px;font-weight:700;margin-bottom:4px;display:flex;align-items:center;gap:8px;}
.config-card-desc{font-size:11px;color:var(--text-muted);margin-bottom:12px;}

/* ── Ticket Card ──────────────────────────── */
.ticket-card{
  padding:18px 20px;border-bottom:1px solid var(--border);
  display:flex;flex-direction:column;gap:8px;
}
.ticket-card:last-child{border-bottom:none;}
.ticket-meta{display:flex;gap:8px;align-items:center;flex-wrap:wrap;font-size:11px;color:var(--text-muted);}

/* ── Chart wrapper ────────────────────────── */
.chart-box{background:var(--bg-secondary);border-radius:var(--radius-sm);padding:20px;border:1px solid var(--border);}
.charts-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:20px;}
@media(max-width:800px){.charts-grid{grid-template-columns:1fr;}}

/* ── Online Users List ────────────────────── */
.online-list{display:flex;flex-direction:column;gap:0;}
.online-item{
  display:flex;align-items:center;gap:12px;
  padding:12px 16px;border-bottom:1px solid var(--border);
}
.online-item:last-child{border-bottom:none;}
.online-avatar{
  width:34px;height:34px;border-radius:50%;
  background:var(--accent);display:flex;align-items:center;
  justify-content:center;font-weight:700;font-size:13px;flex-shrink:0;
}
</style>

<!-- ══ Tab Navigation ══ -->
<div class="admin-nav">
  <button class="admin-tab <?= $activeTab==='overview'  ? 'active':'' ?>" data-tab="overview">
    📊 Visão Geral
    <?php if($stats['online']>0): ?><span class="tab-badge"><?= $stats['online'] ?></span><?php endif; ?>
  </button>
  <button class="admin-tab <?= $activeTab==='cadastros' ? 'active':'' ?>" data-tab="cadastros">
    ⏳ Cadastros
    <?php if($stats['pendentes']>0): ?><span class="tab-badge"><?= $stats['pendentes'] ?></span><?php endif; ?>
  </button>
  <button class="admin-tab <?= $activeTab==='tenants'   ? 'active':'' ?>" data-tab="tenants">🏛️ Conselhos</button>
  <button class="admin-tab <?= $activeTab==='usuarios'  ? 'active':'' ?>" data-tab="usuarios">👥 Usuários</button>
  <button class="admin-tab <?= $activeTab==='suporte'   ? 'active':'' ?>" data-tab="suporte">
    🎫 Suporte
    <?php if($stats['chamados_abertos']>0): ?><span class="tab-badge"><?= $stats['chamados_abertos'] ?></span><?php endif; ?>
  </button>
  <button class="admin-tab <?= $activeTab==='configs'       ? 'active':'' ?>" data-tab="configs">⚙️ Configurações</button>
  <button class="admin-tab <?= $activeTab==='paginas-legais'? 'active':'' ?>" data-tab="paginas-legais">📄 Páginas Legais</button>
  <button class="admin-tab <?= $activeTab==='saude'         ? 'active':'' ?>" data-tab="saude">🖥️ Saúde do Sistema</button>
</div>

<!-- ══════════════════════════════════════════ -->
<!-- TAB: VISÃO GERAL                          -->
<!-- ══════════════════════════════════════════ -->
<div id="tab-overview" class="admin-panel <?= $activeTab==='overview'?'active':'' ?>">

  <!-- Stat Cards -->
  <div class="grid grid-4 mb-6">
    <div class="stat-card">
      <div class="stat-icon blue">🏛️</div>
      <div class="stat-value"><?= $stats['tenants'] ?></div>
      <div class="stat-label">Conselhos Ativos</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon purple">👥</div>
      <div class="stat-value"><?= $stats['users'] ?></div>
      <div class="stat-label">Conselheiros</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon green">📋</div>
      <div class="stat-value"><?= $stats['atendimentos'] ?></div>
      <div class="stat-label">Atendimentos</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon orange">📄</div>
      <div class="stat-value"><?= $stats['documentos'] ?></div>
      <div class="stat-label">Documentos Ativos</div>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start;margin-bottom:24px;">

    <!-- Usuários Online -->
    <div class="card">
      <div class="card-header">
        <h3><span class="online-dot"></span>Usuários Online <span class="text-muted fw-400 text-sm">(últimos 5 min)</span></h3>
        <span class="badge badge-encerrado"><?= count($onlineUsers) ?> online</span>
      </div>
      <?php if(empty($onlineUsers)): ?>
        <div style="padding:24px;text-align:center;color:var(--text-muted);font-size:13px;">
          Nenhum usuário ativo no momento.
        </div>
      <?php else: ?>
        <div class="online-list">
          <?php foreach($onlineUsers as $ou): ?>
          <div class="online-item">
            <div class="online-avatar"><?= strtoupper(substr($ou['nome'],0,1)) ?></div>
            <div style="flex:1;min-width:0;">
              <div class="fw-600 text-sm"><?= e($ou['nome']) ?></div>
              <div class="text-xs text-muted"><?= e($ou['email']) ?></div>
            </div>
            <div style="text-align:right;">
              <span class="badge badge-<?= $ou['role']==='super_admin'?'urgente':'media' ?>" style="font-size:10px;">
                <?= ucfirst(str_replace('_',' ',$ou['role'])) ?>
              </span>
              <div class="text-xs text-muted" style="margin-top:3px;">
                <?= date('H:i', strtotime($ou['last_seen'])) ?>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Alertas rápidos -->
    <div style="display:flex;flex-direction:column;gap:16px;">
      <?php if($stats['pendentes']>0): ?>
      <div class="card" style="border-color:rgba(245,158,11,0.4);padding:20px;cursor:pointer;" onclick="switchTab('cadastros')">
        <div style="display:flex;align-items:center;gap:14px;">
          <div style="font-size:28px;">⏳</div>
          <div>
            <div class="fw-600"><?= $stats['pendentes'] ?> cadastro<?= $stats['pendentes']>1?'s':'' ?> pendente<?= $stats['pendentes']>1?'s':'' ?></div>
            <div class="text-xs text-muted">Clique para revisar e aprovar</div>
          </div>
          <div style="margin-left:auto;color:var(--text-muted);">→</div>
        </div>
      </div>
      <?php endif; ?>

      <?php if($stats['chamados_abertos']>0): ?>
      <div class="card" style="border-color:rgba(239,68,68,0.4);padding:20px;cursor:pointer;" onclick="switchTab('suporte')">
        <div style="display:flex;align-items:center;gap:14px;">
          <div style="font-size:28px;">🎫</div>
          <div>
            <div class="fw-600"><?= $stats['chamados_abertos'] ?> chamado<?= $stats['chamados_abertos']>1?'s':'' ?> aberto<?= $stats['chamados_abertos']>1?'s':'' ?></div>
            <div class="text-xs text-muted">Aguardando sua resposta</div>
          </div>
          <div style="margin-left:auto;color:var(--text-muted);">→</div>
        </div>
      </div>
      <?php endif; ?>

      <div class="card" style="padding:20px;">
        <div class="fw-600 text-sm mb-2">📊 Resumo do Dia</div>
        <div style="display:flex;flex-direction:column;gap:8px;margin-top:8px;">
          <div style="display:flex;justify-content:space-between;font-size:13px;">
            <span class="text-muted">Atendimentos hoje</span>
            <span class="fw-600"><?= $atendHoje ?></span>
          </div>
          <div style="display:flex;justify-content:space-between;font-size:13px;">
            <span class="text-muted">Documentos gerados hoje</span>
            <span class="fw-600"><?= $docHoje ?></span>
          </div>
          <div style="display:flex;justify-content:space-between;font-size:13px;">
            <span class="text-muted">Usuários online agora</span>
            <span class="fw-600"><?= count($onlineUsers) ?></span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Gráficos -->
  <div class="charts-grid">
    <div class="chart-box">
      <div class="fw-600 text-sm mb-1">📋 Atendimentos — últimos 7 dias</div>
      <div class="text-xs text-muted" style="margin-bottom:12px;">Total de casos registrados por dia</div>
      <div style="position:relative;height:200px;width:100%;">
        <canvas id="chartAtend"></canvas>
      </div>
    </div>
    <div class="chart-box">
      <div class="fw-600 text-sm mb-1">🔐 Acessos ao Sistema — últimos 7 dias</div>
      <div class="text-xs text-muted" style="margin-bottom:12px;">Ações registradas nos logs de auditoria</div>
      <div style="position:relative;height:200px;width:100%;">
        <canvas id="chartAcess"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════ -->
<!-- TAB: CADASTROS                            -->
<!-- ══════════════════════════════════════════ -->
<div id="tab-cadastros" class="admin-panel <?= $activeTab==='cadastros'?'active':'' ?>">
  <?php if(empty($pendentes)): ?>
    <div class="card" style="padding:48px;text-align:center;">
      <div style="font-size:48px;margin-bottom:12px;">✅</div>
      <div class="fw-600">Nenhum cadastro pendente</div>
      <div class="text-sm text-muted" style="margin-top:6px;">Todos os pedidos foram processados.</div>
    </div>
  <?php else: ?>
  <div class="card" style="border-color:rgba(251,191,36,0.4);">
    <div class="card-header" style="background:rgba(251,191,36,0.06);border-bottom:1px solid rgba(251,191,36,0.2);">
      <div>
        <h3 style="margin:0;">⏳ Cadastros Aguardando Aprovação</h3>
        <p class="text-xs text-muted" style="margin:3px 0 0;">Novos conselheiros que solicitaram acesso</p>
      </div>
      <span class="badge badge-urgente" style="background:rgba(251,191,36,0.2);color:#f59e0b;border:1px solid rgba(251,191,36,0.4);">
        <?= count($pendentes) ?> pendente<?= count($pendentes)>1?'s':'' ?>
      </span>
    </div>
    <div class="table-wrapper" style="border:none;border-radius:0;">
      <table class="data-table">
        <thead>
          <tr>
            <th>Nome / E-mail</th>
            <th>Cidade</th>
            <th>Contato</th>
            <th>Posse</th>
            <th>Data</th>
            <th style="text-align:center;">Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($pendentes as $p): ?>
          <tr>
            <td>
              <div class="fw-600 text-sm"><?= e($p['nome']) ?></div>
              <div class="text-xs text-muted"><?= e($p['email']) ?></div>
            </td>
            <td class="text-sm"><?= e($p['cidade']) ?></td>
            <td>
              <div class="text-sm"><?= e($p['telefone']) ?></div>
              <div class="text-xs text-muted">CPF: <?= substr(preg_replace('/\D/','',$p['cpf']),0,3) ?>.***.***-<?= substr(preg_replace('/\D/','',$p['cpf']),-2) ?></div>
            </td>
            <td class="text-sm"><?= e($p['ano_posse']) ?></td>
            <td class="text-xs text-muted"><?= date('d/m/Y', strtotime($p['created_at'])) ?></td>
            <td>
              <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap;">
                <form method="POST" action="<?= url('/admin/cadastros/'.$p['id'].'/aprovar') ?>" style="display:inline;">
                  <?= csrf_field() ?>
                  <input type="hidden" name="acao" value="aprovar">
                  <button type="submit" class="btn btn-success btn-sm"
                          onclick="return confirm('Aprovar o cadastro de <?= e(addslashes($p['nome'])) ?>?')">✅ Aprovar</button>
                </form>
                <form method="POST" action="<?= url('/admin/cadastros/'.$p['id'].'/aprovar') ?>" style="display:inline;">
                  <?= csrf_field() ?>
                  <input type="hidden" name="acao" value="rejeitar">
                  <button type="submit" class="btn btn-danger btn-sm"
                          onclick="return confirm('Rejeitar o cadastro de <?= e(addslashes($p['nome'])) ?>?')">❌ Rejeitar</button>
                </form>
                <?php
                  $tel = preg_replace('/\D/','',$p['telefone']);
                  $msg = urlencode("Olá {$p['nome']}! Recebemos seu cadastro no VivensiCT. Estamos em análise e em breve entraremos em contato. 🛡️");
                ?>
                <a href="https://wa.me/55<?= $tel ?>?text=<?= $msg ?>" target="_blank"
                   class="btn btn-sm" style="background:#25d366;color:#fff;border-color:#25d366;">
                  💬 WhatsApp
                </a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>
</div>

<!-- ══════════════════════════════════════════ -->
<!-- TAB: CONSELHOS                            -->
<!-- ══════════════════════════════════════════ -->
<div id="tab-tenants" class="admin-panel <?= $activeTab==='tenants'?'active':'' ?>">
  <div class="card">
    <div class="card-header">
      <h3>🏛️ Conselhos Tutelares</h3>
      <button onclick="openTenantModal()" class="btn btn-primary btn-sm">+ Cadastrar</button>
    </div>
    <div class="table-wrapper" style="border:none;border-radius:0;">
      <table class="data-table">
        <thead>
          <tr><th>Nome</th><th>Município / UF</th><th>Plano</th><th>Usuários</th><th>Status</th></tr>
        </thead>
        <tbody>
          <?php foreach($tenants as $t): ?>
          <tr>
            <td class="fw-600 text-sm"><?= e($t['nome']) ?></td>
            <td class="text-sm"><?= e($t['municipio']) ?>/<?= e($t['estado']) ?></td>
            <td>
              <span class="badge badge-<?= $t['plano']==='profissional'?'encerrado':($t['plano']==='basico'?'media':'arquivado') ?>">
                <?= ucfirst($t['plano']) ?>
              </span>
            </td>
            <td class="text-sm"><?= $t['total_users'] ?></td>
            <td><span class="badge badge-<?= $t['ativo']?'encerrado':'arquivado' ?>"><?= $t['ativo']?'Ativo':'Inativo' ?></span></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════ -->
<!-- TAB: USUÁRIOS                             -->
<!-- ══════════════════════════════════════════ -->
<div id="tab-usuarios" class="admin-panel <?= $activeTab==='usuarios'?'active':'' ?>">
  <div class="card">
    <div class="card-header">
      <h3>👥 Usuários do Sistema</h3>
      <button onclick="openUserModal()" class="btn btn-primary btn-sm">+ Novo Usuário</button>
    </div>
    <div style="display:flex;flex-direction:column;gap:0;">
      <?php foreach($users as $u): ?>
      <div style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:12px;">
        <div class="flex gap-3" style="align-items:center;">
          <div style="width:36px;height:36px;background:var(--accent);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;flex-shrink:0;">
            <?= strtoupper(substr($u['nome'],0,1)) ?>
          </div>
          <div>
            <div class="fw-600 text-sm"><?= e($u['nome']) ?></div>
            <div class="text-xs text-muted"><?= e($u['email']) ?> · <?= e($u['tenant_nome']??'N/A') ?></div>
          </div>
        </div>
        <div class="flex gap-2" style="align-items:center;">
          <span class="badge badge-<?= $u['role']==='super_admin'?'urgente':'media' ?>">
            <?= ucfirst(str_replace('_',' ',$u['role'])) ?>
          </span>
          <button onclick="toggleUser(<?= $u['id'] ?>,this)"
                  class="btn btn-<?= $u['ativo']?'warning':'success' ?> btn-sm">
            <?= $u['ativo']?'⏸':'▶' ?>
          </button>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════ -->
<!-- TAB: SUPORTE                              -->
<!-- ══════════════════════════════════════════ -->
<div id="tab-suporte" class="admin-panel <?= $activeTab==='suporte'?'active':'' ?>">
  <div class="card">
    <div class="card-header">
      <h3>🎫 Chamados de Suporte</h3>
      <span class="badge badge-urgente"><?= $stats['chamados_abertos'] ?> aberto<?= $stats['chamados_abertos']!=1?'s':'' ?></span>
    </div>
    <?php if(empty($chamados)): ?>
      <div style="padding:48px;text-align:center;color:var(--text-muted);font-size:14px;">
        Nenhum chamado registrado ainda.
      </div>
    <?php else: ?>
    <div>
      <?php foreach($chamados as $ch): ?>
      <?php
        $sb = match($ch['status']){
          'aberto'=>'urgente','em_atendimento'=>'media','resolvido'=>'encerrado',default=>'arquivado'
        };
        $sl = match($ch['status']){
          'aberto'=>'🔴 Aberto','em_atendimento'=>'🟡 Em Atendimento','resolvido'=>'✅ Resolvido',default=>'🔒 Fechado'
        };
        $pr = match($ch['prioridade']){
          'urgente'=>'🔴','alta'=>'🟠','media'=>'🟡',default=>'🟢'
        };
      ?>
      <div class="ticket-card">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;">
          <div>
            <div class="fw-600 text-sm"><?= $pr ?> <?= e($ch['titulo']) ?></div>
            <div class="ticket-meta">
              <span><?= e($ch['tenant_nome']??'N/A') ?></span>
              <span>·</span>
              <span><?= e($ch['user_nome']??'N/A') ?></span>
              <span>·</span>
              <span><?= ucfirst($ch['tipo']) ?></span>
              <span>·</span>
              <span><?= date('d/m/Y H:i', strtotime($ch['created_at'])) ?></span>
            </div>
          </div>
          <div style="display:flex;gap:8px;align-items:center;flex-shrink:0;">
            <span class="badge badge-<?= $sb ?>"><?= $sl ?></span>
            <button class="btn btn-primary btn-sm" onclick="openTicketModal(<?= $ch['id'] ?>,<?= htmlspecialchars(json_encode($ch), ENT_QUOTES) ?>)">
              ✏️ Responder
            </button>
          </div>
        </div>
        <p class="text-xs text-muted" style="margin:0;line-height:1.6;max-width:600px;"><?= nl2br(e(substr($ch['descricao'],0,200))) ?><?= strlen($ch['descricao'])>200?'…':'' ?></p>
        <?php if($ch['resposta']): ?>
        <div style="background:var(--bg-secondary);border-radius:var(--radius-sm);padding:10px 14px;border-left:3px solid var(--success);">
          <div class="text-xs fw-600" style="color:var(--success);margin-bottom:3px;">Resposta enviada:</div>
          <p class="text-xs" style="margin:0;line-height:1.6;"><?= nl2br(e($ch['resposta'])) ?></p>
        </div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- ══════════════════════════════════════════ -->
<!-- TAB: CONFIGURAÇÕES                        -->
<!-- ══════════════════════════════════════════ -->
<div id="tab-configs" class="admin-panel <?= $activeTab==='configs'?'active':'' ?>">
  <form method="POST" action="<?= url('/admin/configs') ?>" data-loading>
    <?= csrf_field() ?>

    <div class="card mb-4">
      <div class="card-header">
        <h3>⚙️ Integrações e Chaves de API</h3>
      </div>
      <div class="card-body">
        <div class="config-section">

          <!-- DeepSeek -->
          <div class="config-card">
            <div class="config-card-title">🤖 DeepSeek AI</div>
            <div class="config-card-desc">Modelo de IA para análise de leis ECA/SUAS (alternativa ao GPT)</div>
            <label class="form-label">Chave de API</label>
            <input type="password" name="deepseek_key" class="form-control"
                   value="<?= e($configsMap['deepseek_key']??'') ?>"
                   placeholder="sk-...">
          </div>

          <!-- Gemini -->
          <div class="config-card">
            <div class="config-card-title">✨ Google Gemini</div>
            <div class="config-card-desc">Modelo Gemini 1.5 Pro para geração de documentos e análises</div>
            <label class="form-label">Chave de API</label>
            <input type="password" name="gemini_key" class="form-control"
                   value="<?= e($configsMap['gemini_key']??'') ?>"
                   placeholder="AIza...">
          </div>

          <!-- Brevo -->
          <div class="config-card">
            <div class="config-card-title">📧 Brevo (E-mail Transacional)</div>
            <div class="config-card-desc">Envio de e-mails de aprovação, notificações e chamados de suporte</div>
            <label class="form-label">Chave de API Brevo</label>
            <input type="password" name="brevo_key" class="form-control"
                   value="<?= e($configsMap['brevo_key']??'') ?>"
                   placeholder="xkeysib-...">
          </div>

          <!-- AbacatePay -->
          <div class="config-card">
            <div class="config-card-title">💳 AbacatePay</div>
            <div class="config-card-desc">Gateway de pagamento para doações e planos dos inscritos</div>
            <label class="form-label">Chave de API AbacatePay</label>
            <input type="password" name="abacatepay_key" class="form-control"
                   value="<?= e($configsMap['abacatepay_key']??'') ?>"
                   placeholder="abacate_...">
          </div>

        </div>

        <!-- WhatsApp Business -->
        <div class="config-card" style="margin-top:20px;grid-column:1/-1;">
          <div class="config-card-title">💬 WhatsApp Business</div>
          <div class="config-card-desc">Número para envio de notificações de aprovação de cadastro e suporte</div>
          <div class="grid grid-2">
            <div class="form-group" style="margin:0;">
              <label class="form-label">Número (somente dígitos, com DDI 55)</label>
              <input type="text" name="whatsapp_number" class="form-control"
                     value="<?= e($configsMap['whatsapp_number']??'') ?>"
                     placeholder="5511999999999">
            </div>
          </div>
        </div>

      </div>
    </div>

    <button type="submit" class="btn btn-primary">
      💾 Salvar Configurações
    </button>
  </form>
</div>

<!-- ══════════════════════════════════════════ -->
<!-- TAB: PÁGINAS LEGAIS                       -->
<!-- ══════════════════════════════════════════ -->
<div id="tab-paginas-legais" class="admin-panel <?= $activeTab==='paginas-legais'?'active':'' ?>">

  <?php
    $privacidade = $paginasMap['privacidade'] ?? [];
    $termos      = $paginasMap['termos']      ?? [];
  ?>

  <div class="info-box accent mb-6" style="border-radius:var(--radius);">
    💡 O conteúdo destas páginas suporta <strong>HTML completo</strong>. Use tags como
    <code style="background:var(--bg-secondary);padding:1px 6px;border-radius:4px;font-size:12px;">&lt;h2&gt;</code>,
    <code style="background:var(--bg-secondary);padding:1px 6px;border-radius:4px;font-size:12px;">&lt;ul&gt;</code>,
    <code style="background:var(--bg-secondary);padding:1px 6px;border-radius:4px;font-size:12px;">&lt;p&gt;</code>,
    <code style="background:var(--bg-secondary);padding:1px 6px;border-radius:4px;font-size:12px;">&lt;strong&gt;</code>,
    <code style="background:var(--bg-secondary);padding:1px 6px;border-radius:4px;font-size:12px;">&lt;blockquote&gt;</code> etc.
    As páginas são acessíveis publicamente em
    <a href="<?= url('/privacidade') ?>" target="_blank" style="color:var(--accent);">/privacidade</a> e
    <a href="<?= url('/termos-de-uso') ?>" target="_blank" style="color:var(--accent);">/termos-de-uso</a>.
  </div>

  <div class="grid grid-2" style="gap:24px;align-items:start;">

    <!-- ── Política de Privacidade ─── -->
    <div class="card">
      <div class="card-header" style="background:rgba(99,102,241,0.04);border-bottom-color:rgba(99,102,241,0.2);">
        <div>
          <h3 style="color:var(--accent);">🔒 Política de Privacidade</h3>
          <?php if (!empty($privacidade['updated_at'])): ?>
          <p class="text-xs text-muted" style="margin:3px 0 0;">
            Atualizada em <?= date('d/m/Y H:i', strtotime($privacidade['updated_at'])) ?>
          </p>
          <?php endif; ?>
        </div>
        <a href="<?= url('/privacidade') ?>" target="_blank" class="btn btn-ghost btn-sm">↗ Ver página</a>
      </div>
      <form method="POST" action="<?= url('/admin/paginas-legais') ?>" id="formPrivacidade">
        <?= csrf_field() ?>
        <input type="hidden" name="slug" value="privacidade">
        <div class="card-body" style="display:flex;flex-direction:column;gap:14px;">
          <div class="form-group" style="margin:0;">
            <label class="form-label">Título da Página</label>
            <input type="text" name="titulo" class="form-control"
                   value="<?= e($privacidade['titulo'] ?? 'Política de Privacidade') ?>"
                   placeholder="Política de Privacidade" required>
          </div>
          <div class="form-group" style="margin:0;">
            <label class="form-label" style="justify-content:space-between;">
              <span>Conteúdo (HTML)</span>
              <button type="button" onclick="togglePreview('privacidade')"
                      class="btn btn-ghost btn-sm" style="font-size:11px;height:22px;padding:0 8px;">
                👁 Pré-visualizar
              </button>
            </label>
            <textarea id="editor-privacidade" name="conteudo" class="form-control"
                      rows="18" style="font-family:monospace;font-size:12px;resize:vertical;"
                      placeholder="Digite o conteúdo em HTML..."><?= e($privacidade['conteudo'] ?? '') ?></textarea>
            <div id="preview-privacidade" class="legal-preview" style="display:none;"></div>
          </div>
          <button type="submit" class="btn btn-primary btn-full">
            💾 Salvar Política de Privacidade
          </button>
        </div>
      </form>
    </div>

    <!-- ── Termos de Uso ─── -->
    <div class="card">
      <div class="card-header" style="background:rgba(34,197,94,0.04);border-bottom-color:rgba(34,197,94,0.2);">
        <div>
          <h3 style="color:var(--success);">📋 Termos de Uso</h3>
          <?php if (!empty($termos['updated_at'])): ?>
          <p class="text-xs text-muted" style="margin:3px 0 0;">
            Atualizado em <?= date('d/m/Y H:i', strtotime($termos['updated_at'])) ?>
          </p>
          <?php endif; ?>
        </div>
        <a href="<?= url('/termos-de-uso') ?>" target="_blank" class="btn btn-ghost btn-sm">↗ Ver página</a>
      </div>
      <form method="POST" action="<?= url('/admin/paginas-legais') ?>" id="formTermos">
        <?= csrf_field() ?>
        <input type="hidden" name="slug" value="termos">
        <div class="card-body" style="display:flex;flex-direction:column;gap:14px;">
          <div class="form-group" style="margin:0;">
            <label class="form-label">Título da Página</label>
            <input type="text" name="titulo" class="form-control"
                   value="<?= e($termos['titulo'] ?? 'Termos de Uso') ?>"
                   placeholder="Termos de Uso" required>
          </div>
          <div class="form-group" style="margin:0;">
            <label class="form-label" style="justify-content:space-between;">
              <span>Conteúdo (HTML)</span>
              <button type="button" onclick="togglePreview('termos')"
                      class="btn btn-ghost btn-sm" style="font-size:11px;height:22px;padding:0 8px;">
                👁 Pré-visualizar
              </button>
            </label>
            <textarea id="editor-termos" name="conteudo" class="form-control"
                      rows="18" style="font-family:monospace;font-size:12px;resize:vertical;"
                      placeholder="Digite o conteúdo em HTML..."><?= e($termos['conteudo'] ?? '') ?></textarea>
            <div id="preview-termos" class="legal-preview" style="display:none;"></div>
          </div>
          <button type="submit" class="btn btn-primary btn-full">
            💾 Salvar Termos de Uso
          </button>
        </div>
      </form>
    </div>

  </div>

  <!-- Templates prontos -->
  <div class="card mt-6" style="border-color:rgba(245,158,11,0.3);">
    <div class="card-header" style="background:rgba(245,158,11,0.04);">
      <h3>📝 Templates Prontos (LGPD / ECA)</h3>
      <span class="text-xs text-muted">Clique para preencher o editor com um texto base para edição</span>
    </div>
    <div class="card-body" style="display:flex;gap:12px;flex-wrap:wrap;">
      <button onclick="loadTemplate('privacidade')" class="btn btn-warning btn-sm">
        🔒 Template — Política de Privacidade
      </button>
      <button onclick="loadTemplate('termos')" class="btn btn-warning btn-sm">
        📋 Template — Termos de Uso
      </button>
    </div>
  </div>

</div>

<style>
.legal-preview {
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  padding: 16px;
  background: var(--bg-secondary);
  margin-top: 8px;
  font-size: 13px;
  line-height: 1.8;
  color: var(--text-secondary);
  max-height: 400px;
  overflow-y: auto;
}
.legal-preview h2 { font-size: 15px; font-weight: 700; color: var(--text-primary); margin: 16px 0 8px; }
.legal-preview h3 { font-size: 13px; font-weight: 600; color: var(--text-primary); margin: 12px 0 6px; }
.legal-preview p { margin: 0 0 10px; }
.legal-preview ul, .legal-preview ol { padding-left: 18px; margin: 0 0 10px; }
.legal-preview blockquote { border-left:3px solid var(--accent); padding:8px 14px; background:var(--accent-light); border-radius:0 4px 4px 0; margin:12px 0; }
.legal-preview strong { color: var(--text-primary); }
</style>

<script>
// ── Preview toggle ───────────────────────────────────
function togglePreview(slug) {
  var editor  = document.getElementById('editor-' + slug);
  var preview = document.getElementById('preview-' + slug);
  if (!preview) return;
  if (preview.style.display === 'none') {
    preview.innerHTML = editor.value || '<em style="color:var(--text-muted)">Nenhum conteúdo para pré-visualizar.</em>';
    preview.style.display = 'block';
    editor.style.display  = 'none';
  } else {
    preview.style.display = 'none';
    editor.style.display  = '';
  }
}

// ── Templates padrão ────────────────────────────────
var _templates = {
  privacidade: `<h2>1. Introdução</h2>
<p>A presente Política de Privacidade descreve como o <strong>VivensiCT</strong> coleta, utiliza, armazena e protege os dados pessoais dos usuários do Sistema de Gestão para Conselheiros Tutelares, em conformidade com a <strong>Lei Geral de Proteção de Dados Pessoais (LGPD — Lei nº 13.709/2018)</strong> e o <strong>Estatuto da Criança e do Adolescente (ECA — Lei nº 8.069/1990)</strong>.</p>

<h2>2. Dados Coletados</h2>
<p>O VivensiCT coleta e trata os seguintes dados:</p>
<ul>
  <li><strong>Dados dos Conselheiros (Usuários):</strong> nome completo, CPF, e-mail, telefone, registro funcional e município.</li>
  <li><strong>Dados de Crianças e Adolescentes (Sensíveis):</strong> nome, filiação, endereço, escola e situação familiar — todos <strong>criptografados com AES-128-CBC</strong> antes do armazenamento.</li>
  <li><strong>Dados de Uso:</strong> logs de auditoria com ações, IP e timestamp para rastreabilidade e segurança.</li>
  <li><strong>Cookies de Sessão:</strong> utilizados para manter autenticação durante o uso do sistema.</li>
</ul>

<h2>3. Finalidade do Tratamento</h2>
<ul>
  <li>Registro e gestão de atendimentos do Conselho Tutelar (Art. 136 do ECA);</li>
  <li>Geração de documentos oficiais com assinatura digital (Lei nº 14.063/2020);</li>
  <li>Análises de proteção com Inteligência Artificial baseadas no ECA/SUAS;</li>
  <li>Integração com o SIPIA CT Web do Governo Federal;</li>
  <li>Auditoria e rastreabilidade de ações (LGPD — Art. 37).</li>
</ul>

<h2>4. Base Legal</h2>
<ul>
  <li><strong>Cumprimento de obrigação legal (Art. 7º, II da LGPD):</strong> atendimento às atribuições do Conselho Tutelar pelo ECA.</li>
  <li><strong>Exercício regular de direitos (Art. 7º, VI da LGPD):</strong> instrução de procedimentos administrativos.</li>
  <li><strong>Proteção da vida e integridade física (Art. 7º, VII da LGPD):</strong> proteção de crianças em situação de vulnerabilidade.</li>
</ul>

<h2>5. Segurança dos Dados</h2>
<ul>
  <li>Dados identificáveis de crianças são criptografados com <strong>AES-128-CBC</strong>;</li>
  <li>Documentos gerados são excluídos automaticamente após <strong>3 dias</strong> (expurgo LGPD);</li>
  <li>Senhas armazenadas com <strong>hash BCrypt</strong>;</li>
  <li>Acesso protegido por autenticação e controle de perfil;</li>
  <li>Todos os acessos são registrados em log de auditoria.</li>
</ul>

<h2>6. Compartilhamento de Dados</h2>
<p>Os dados <strong>não são vendidos ou compartilhados para fins comerciais</strong>. Podem ser compartilhados somente com órgãos públicos por determinação legal ou com o SIPIA CT Web.</p>

<h2>7. Direitos do Titular (LGPD — Art. 18)</h2>
<ul>
  <li>Confirmação da existência de tratamento e acesso aos dados;</li>
  <li>Correção, portabilidade e eliminação dos dados;</li>
  <li>Informação sobre compartilhamento com terceiros.</li>
</ul>
<p>Solicitações devem ser encaminhadas ao administrador do sistema ou ao DPO do Conselho Tutelar.</p>

<h2>8. Contato</h2>
<p>Dúvidas sobre esta política podem ser enviadas ao administrador do sistema ou ao Encarregado de Proteção de Dados (DPO) do Conselho Tutelar responsável.</p>

<hr>
<p><em>Esta política é atualizada pelo administrador do sistema. A data da última atualização consta no topo desta página.</em></p>`,

  termos: `<h2>1. Aceitação dos Termos</h2>
<p>Ao acessar e utilizar o <strong>VivensiCT</strong>, o usuário declara ter lido, compreendido e concordado com estes Termos de Uso. O uso continuado implica na aceitação integral das condições aqui estabelecidas.</p>

<h2>2. Descrição do Serviço</h2>
<p>O VivensiCT é um sistema de gestão digital destinado exclusivamente a <strong>Conselheiros Tutelares</strong> devidamente nomeados e a administradores de Conselhos Tutelares municipais. O sistema oferece:</p>
<ul>
  <li>Registro e gestão de atendimentos de proteção à criança e ao adolescente;</li>
  <li>Análise jurídica com Inteligência Artificial baseada no ECA e SUAS;</li>
  <li>Geração de documentos com assinatura digital (Lei nº 14.063/2020);</li>
  <li>Integração com o SIPIA CT Web do Governo Federal;</li>
  <li>Gestão da rede de serviços municipal (Tipificação SUAS — Resolução CNAS 109/2009).</li>
</ul>

<h2>3. Responsabilidades do Usuário</h2>
<ul>
  <li>Utilizar o sistema exclusivamente no exercício das funções de Conselheiro Tutelar;</li>
  <li>Manter a confidencialidade de sua senha e não compartilhá-la com terceiros;</li>
  <li>Registrar informações verídicas e precisas sobre os atendimentos;</li>
  <li>Respeitar a legislação de proteção de dados (LGPD) no uso das informações;</li>
  <li>Comunicar imediatamente qualquer acesso não autorizado ao administrador.</li>
</ul>

<h2>4. Sigilo e Proteção de Dados</h2>
<p>Os dados inseridos — especialmente de crianças e adolescentes — são de natureza <strong>sensível e sigilosa</strong>, nos termos do ECA e da LGPD.</p>
<blockquote>
  <strong>⚖️ Art. 17 do ECA:</strong> O direito ao respeito consiste na inviolabilidade da integridade física, psíquica e moral da criança e do adolescente, abrangendo a preservação da imagem, da identidade, da autonomia, dos valores, ideias e crenças, dos espaços e objetos pessoais.
</blockquote>

<h2>5. Análises por Inteligência Artificial</h2>
<p>As análises geradas pela IA têm caráter <strong>orientativo e complementar</strong>. Não substituem o julgamento profissional do Conselheiro Tutelar nem possuem valor jurídico autônomo. O usuário é inteiramente responsável pelas decisões tomadas com base nessas análises.</p>

<h2>6. Documentos Gerados</h2>
<p>Os documentos são de responsabilidade do Conselheiro assinante. A assinatura digital aplicada possui validade nos termos da <strong>Lei nº 14.063/2020</strong>. O sistema não se responsabiliza pelo uso indevido dos documentos.</p>

<h2>7. Disponibilidade do Serviço</h2>
<p>O sistema é fornecido sem garantia de disponibilidade ininterrupta. A equipe VivensiCT envidará esforços para manter o serviço disponível, sem responsabilidade por indisponibilidades causadas por manutenção ou falhas externas.</p>

<h2>8. Suspensão e Cancelamento</h2>
<p>O acesso pode ser suspenso em caso de descumprimento destes Termos, uso indevido, encerramento do mandato do Conselheiro ou solicitação do próprio usuário.</p>

<h2>9. Legislação Aplicável</h2>
<p>Estes Termos são regidos pela legislação brasileira, especialmente o <strong>ECA (Lei nº 8.069/1990)</strong>, a <strong>LGPD (Lei nº 13.709/2018)</strong> e a <strong>Lei nº 14.063/2020</strong>. Controvérsias serão submetidas ao foro da comarca do Município sede do Conselho Tutelar.</p>

<hr>
<p><em>Estes Termos podem ser atualizados pelo administrador do sistema. A data da última atualização consta no topo desta página.</em></p>`
};

function loadTemplate(slug) {
  if (!confirm('Substituir o conteúdo atual pelo template padrão? Esta ação não pode ser desfeita.')) return;
  var editor  = document.getElementById('editor-' + slug);
  var preview = document.getElementById('preview-' + slug);
  if (editor) {
    editor.value        = _templates[slug] || '';
    editor.style.display = '';
    if (preview) preview.style.display = 'none';
    showToast('✅ Template carregado! Revise e salve.', 'success');
    editor.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
}
</script>

<!-- ══════════════════════════════════════════ -->
<!-- TAB: SAÚDE DO SISTEMA                     -->
<!-- ══════════════════════════════════════════ -->
<div id="tab-saude" class="admin-panel <?= $activeTab==='saude'?'active':'' ?>">

  <?php
    $diskPct  = $serverHealth['disk_total'] > 0 ? round((1 - $serverHealth['disk_free']/$serverHealth['disk_total'])*100) : 0;
    $diskCls  = $diskPct > 80 ? 'danger' : ($diskPct > 60 ? 'warn' : 'ok');
    $memBytes = $serverHealth['mem_usage'];
    $memLimitBytes = (int)ini_get('memory_limit') * 1024 * 1024;
    $memPct   = $memLimitBytes > 0 ? round($memBytes/$memLimitBytes*100) : 0;
    $memCls   = $memPct > 80 ? 'danger' : ($memPct > 60 ? 'warn' : 'ok');

    function fmtBytes(int $b): string {
      if($b >= 1073741824) return round($b/1073741824,1).' GB';
      if($b >= 1048576)    return round($b/1048576,1).' MB';
      return round($b/1024,1).' KB';
    }
  ?>

  <div class="grid grid-2" style="gap:20px;margin-bottom:20px;">

    <!-- Stack -->
    <div class="card">
      <div class="card-header"><h3>🖥️ Stack do Servidor</h3></div>
      <div class="card-body">
        <div style="display:flex;flex-direction:column;gap:12px;">
          <?php $items = [
            ['PHP', $serverHealth['php'], $serverHealth['php'] >= '8.0' ? 'encerrado':'urgente'],
            ['MySQL', $serverHealth['mysql'], 'encerrado'],
            ['Sistema Operacional', $serverHealth['os'], 'media'],
            ['Limite de Memória PHP', $serverHealth['mem_limit'], 'media'],
            ['Hora do Servidor', $serverHealth['time'], 'arquivado'],
          ]; foreach($items as [$k,$v,$b]): ?>
          <div style="display:flex;justify-content:space-between;align-items:center;font-size:13px;padding:8px 0;border-bottom:1px solid var(--border);">
            <span class="text-muted"><?= $k ?></span>
            <span class="badge badge-<?= $b ?>"><?= e($v) ?></span>
          </div>
          <?php endforeach; ?>
          <?php if($serverHealth['uptime'] && $serverHealth['uptime']!=='N/A'): ?>
          <div style="display:flex;justify-content:space-between;align-items:center;font-size:13px;padding:8px 0;">
            <span class="text-muted">Uptime</span>
            <span class="fw-600 text-sm"><?= e(trim($serverHealth['uptime'])) ?></span>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Recursos -->
    <div class="card">
      <div class="card-header"><h3>📊 Recursos</h3></div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:20px;">
        <div>
          <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px;">
            <span class="text-muted">Disco utilizado</span>
            <span class="fw-600"><?= $diskPct ?>% — <?= fmtBytes($serverHealth['disk_total']-$serverHealth['disk_free']) ?> / <?= fmtBytes($serverHealth['disk_total']) ?></span>
          </div>
          <div class="health-bar-wrap"><div class="health-bar <?= $diskCls ?>" style="width:<?= $diskPct ?>%"></div></div>
        </div>
        <div>
          <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px;">
            <span class="text-muted">Memória PHP (requisição atual)</span>
            <span class="fw-600"><?= $memPct ?>% — <?= fmtBytes($memBytes) ?> / <?= $serverHealth['mem_limit'] ?></span>
          </div>
          <div class="health-bar-wrap"><div class="health-bar <?= $memCls ?>" style="width:<?= min($memPct,100) ?>%"></div></div>
        </div>
        <div style="background:var(--bg-secondary);border-radius:var(--radius-sm);padding:14px 16px;">
          <div class="text-xs fw-600" style="margin-bottom:4px;">💡 AWS Lightsail</div>
          <p class="text-xs text-muted" style="margin:0;line-height:1.65;">
            Configure o servidor em AWS Lightsail para ambiente de produção.
            As métricas detalhadas (CPU, rede) ficam disponíveis no console Lightsail.
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- LGPD Tools -->
  <div class="card" style="border-color:rgba(239,68,68,0.3);">
    <div class="card-header" style="background:rgba(239,68,68,0.05);">
      <h3>🔐 Ferramentas LGPD</h3>
    </div>
    <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
      <div style="background:var(--bg-secondary);border-radius:var(--radius-sm);padding:16px;">
        <div class="fw-600 text-sm" style="margin-bottom:6px;">Expurgo de Documentos</div>
        <p class="text-xs text-muted" style="margin:0 0 12px;">Remove PDFs expirados (&gt;3 dias) do servidor.</p>
        <button onclick="executarExpurgo()" class="btn btn-danger btn-sm" id="btnExpurgo">🗑️ Executar Expurgo</button>
        <div id="expurgoResult" style="display:none;margin-top:10px;"></div>
      </div>
      <div style="background:var(--bg-secondary);border-radius:var(--radius-sm);padding:16px;">
        <div class="fw-600 text-sm" style="margin-bottom:4px;">Criptografia AES-128-CBC</div>
        <p class="text-xs text-muted" style="margin:0 0 10px;">Dados pessoais de crianças são criptografados.</p>
        <span class="badge badge-encerrado">✅ Ativo</span>
      </div>
      <div style="background:var(--bg-secondary);border-radius:var(--radius-sm);padding:16px;">
        <div class="fw-600 text-sm" style="margin-bottom:4px;">Log de Auditoria</div>
        <p class="text-xs text-muted" style="margin:0 0 10px;">Todas as ações registradas com IP e timestamp.</p>
        <span class="badge badge-encerrado">✅ Ativo</span>
      </div>
    </div>
  </div>
</div>

<!-- ═══════════════════════ MODAIS ═══════════════════════ -->

<!-- Modal Tenant -->
<div id="tenantModal" class="modal-overlay hidden">
  <div class="modal" style="max-width:520px;">
    <div class="modal-header">
      <h3>🏛️ Cadastrar Conselho Tutelar</h3>
      <button onclick="closeTenantModal()" style="background:none;border:none;color:var(--text-secondary);font-size:22px;cursor:pointer;">×</button>
    </div>
    <form method="POST" action="<?= url('/admin/tenants') ?>">
      <?= csrf_field() ?>
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Nome do Conselho Tutelar</label>
          <input type="text" name="nome" class="form-control" required placeholder="Ex: Conselho Tutelar do Centro">
        </div>
        <div class="grid grid-2">
          <div class="form-group">
            <label class="form-label">Município</label>
            <input type="text" name="municipio" class="form-control" required>
          </div>
          <div class="form-group">
            <label class="form-label">UF</label>
            <input type="text" name="estado" class="form-control" required maxlength="2" placeholder="SP">
          </div>
        </div>
        <div class="grid grid-2">
          <div class="form-group">
            <label class="form-label">E-mail</label>
            <input type="email" name="email" class="form-control">
          </div>
          <div class="form-group">
            <label class="form-label">Plano</label>
            <select name="plano" class="form-control">
              <option value="gratuito">Gratuito</option>
              <option value="basico">Básico</option>
              <option value="profissional">Profissional</option>
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" onclick="closeTenantModal()" class="btn btn-secondary">Cancelar</button>
        <button type="submit" class="btn btn-primary">💾 Cadastrar</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Usuário -->
<div id="userModal" class="modal-overlay hidden">
  <div class="modal" style="max-width:520px;">
    <div class="modal-header">
      <h3>👤 Criar Novo Usuário</h3>
      <button onclick="closeUserModal()" style="background:none;border:none;color:var(--text-secondary);font-size:22px;cursor:pointer;">×</button>
    </div>
    <form method="POST" action="<?= url('/admin/users') ?>">
      <?= csrf_field() ?>
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Conselho Tutelar</label>
          <select name="tenant_id" class="form-control" required>
            <?php foreach($tenants as $t): ?>
            <option value="<?= $t['id'] ?>"><?= e($t['nome']) ?> — <?= e($t['municipio']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="grid grid-2">
          <div class="form-group">
            <label class="form-label">Nome Completo</label>
            <input type="text" name="nome" class="form-control" required>
          </div>
          <div class="form-group">
            <label class="form-label">Registro Funcional</label>
            <input type="text" name="registro_funcional" class="form-control" placeholder="CT-001/2024">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">E-mail</label>
          <input type="email" name="email" class="form-control" required>
        </div>
        <div class="grid grid-2">
          <div class="form-group">
            <label class="form-label">Senha</label>
            <input type="password" name="password" class="form-control" value="vivensict123">
          </div>
          <div class="form-group">
            <label class="form-label">Perfil</label>
            <select name="role" class="form-control">
              <option value="conselheiro">Conselheiro</option>
              <option value="admin">Admin</option>
              <option value="super_admin">Super Admin</option>
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" onclick="closeUserModal()" class="btn btn-secondary">Cancelar</button>
        <button type="submit" class="btn btn-primary">💾 Criar Usuário</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Responder Chamado -->
<div id="ticketModal" class="modal-overlay hidden">
  <div class="modal" style="max-width:540px;">
    <div class="modal-header">
      <h3>🎫 Responder Chamado</h3>
      <button onclick="closeTicketModal()" style="background:none;border:none;color:var(--text-secondary);font-size:22px;cursor:pointer;">×</button>
    </div>
    <form method="POST" id="ticketForm">
      <?= csrf_field() ?>
      <div class="modal-body">
        <div id="ticketInfo" style="background:var(--bg-secondary);border-radius:var(--radius-sm);padding:14px 16px;margin-bottom:16px;">
          <div class="fw-600 text-sm" id="ticketTitulo"></div>
          <div class="text-xs text-muted" id="ticketMeta" style="margin-top:3px;"></div>
          <p class="text-xs" id="ticketDesc" style="margin:10px 0 0;line-height:1.6;"></p>
        </div>
        <div class="form-group">
          <label class="form-label">Resposta *</label>
          <textarea name="resposta" class="form-control" rows="5" required
                    placeholder="Escreva sua resposta para o usuário…"></textarea>
        </div>
        <div class="form-group">
          <label class="form-label">Novo Status</label>
          <select name="status" class="form-control">
            <option value="em_atendimento">🟡 Em Atendimento</option>
            <option value="resolvido">✅ Resolvido</option>
            <option value="fechado">🔒 Fechado</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" onclick="closeTicketModal()" class="btn btn-secondary">Cancelar</button>
        <button type="submit" class="btn btn-primary">📨 Salvar Resposta</button>
      </div>
    </form>
  </div>
</div>

<script>
/* ── Tab switching ──────────────────────────── */
function switchTab(name) {
  document.querySelectorAll('.admin-tab').forEach(t => t.classList.toggle('active', t.dataset.tab === name));
  document.querySelectorAll('.admin-panel').forEach(p => p.classList.toggle('active', p.id === 'tab-'+name));
  history.replaceState(null,'','?tab='+name);
}
document.querySelectorAll('.admin-tab').forEach(btn => {
  btn.addEventListener('click', () => switchTab(btn.dataset.tab));
});

/* ── Modais Tenant / User ───────────────────── */
function openTenantModal()  { document.getElementById('tenantModal').classList.remove('hidden'); }
function closeTenantModal() { document.getElementById('tenantModal').classList.add('hidden'); }
function openUserModal()    { document.getElementById('userModal').classList.remove('hidden'); }
function closeUserModal()   { document.getElementById('userModal').classList.add('hidden'); }

/* ── Modal Chamado ──────────────────────────── */
function openTicketModal(id, data) {
  document.getElementById('ticketForm').action = '<?= url('/admin/chamados/') ?>' + id + '/responder';
  document.getElementById('ticketTitulo').textContent = data.titulo;
  document.getElementById('ticketMeta').textContent   = (data.tenant_nome||'') + ' · ' + (data.user_nome||'') + ' · ' + data.tipo;
  document.getElementById('ticketDesc').textContent   = data.descricao;
  document.getElementById('ticketModal').classList.remove('hidden');
}
function closeTicketModal() { document.getElementById('ticketModal').classList.add('hidden'); }

/* ── Toggle User ────────────────────────────── */
function toggleUser(id, btn) {
  fetch('<?= url('/admin/users/') ?>' + id + '/toggle', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    body: JSON.stringify({ _token: '<?= \App\Core\Request::csrf() ?>' }),
  }).then(r => r.json()).then(() => {
    showToast('Usuário atualizado!', 'success');
    setTimeout(() => location.reload(), 800);
  });
}

/* ── Expurgo LGPD ───────────────────────────── */
function executarExpurgo() {
  if (!confirm('Executar expurgo de documentos expirados? Esta ação remove arquivos permanentemente.')) return;
  const btn = document.getElementById('btnExpurgo');
  btn.disabled = true;
  btn.innerHTML = '<span class="loading-spinner"></span> Executando…';
  fetch('<?= url('/admin/purge') ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    body: JSON.stringify({ _token: '<?= \App\Core\Request::csrf() ?>' }),
  }).then(r => r.json()).then(data => {
    const el = document.getElementById('expurgoResult');
    el.style.display = 'block';
    el.className = 'alert alert-' + (data.success ? 'success' : 'error');
    el.textContent = data.message;
    btn.disabled = false;
    btn.innerHTML = '🗑️ Executar Expurgo';
  });
}

/* ── Charts ─────────────────────────────────── */
window.addEventListener('load', function() {
  if (typeof Chart === 'undefined') return;

  const labels = <?= json_encode($chartLabels) ?>;
  const tickColor = '#5b78a8';
  const gridColor = 'rgba(100,120,160,0.12)';

  new Chart(document.getElementById('chartAtend'), {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Atendimentos',
        data: <?= json_encode($chartAtend) ?>,
        backgroundColor: 'rgba(59,130,246,0.7)',
        borderColor: '#3b82f6',
        borderWidth: 1,
        borderRadius: 6,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        y: { beginAtZero: true, ticks: { stepSize: 1, color: tickColor }, grid: { color: gridColor } },
        x: { ticks: { color: tickColor }, grid: { display: false } }
      }
    }
  });

  new Chart(document.getElementById('chartAcess'), {
    type: 'line',
    data: {
      labels,
      datasets: [{
        label: 'Acessos',
        data: <?= json_encode($chartAcess) ?>,
        borderColor: '#10b981',
      backgroundColor: 'rgba(16,185,129,0.08)',
      borderWidth: 2,
      pointRadius: 4,
      pointBackgroundColor: '#10b981',
      fill: true,
      tension: 0.4,
    }]
  },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        y: { beginAtZero: true, ticks: { stepSize: 1, color: tickColor }, grid: { color: gridColor } },
        x: { ticks: { color: tickColor }, grid: { display: false } }
      }
    }
  });
}); // end window.load
</script>
