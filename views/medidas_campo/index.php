<?php $title = 'Medidas de Campo'; $subtitle = 'Documentos avulsos gerados em campo'; ?>

<div class="flex-between mb-5" style="flex-wrap:wrap;gap:12px;">
  <div>
    <h2 style="margin:0;font-size:20px;font-weight:800;">📝 Medidas de Campo</h2>
    <p class="text-muted text-sm" style="margin-top:4px;">Documentos de medida de proteção gerados em campo, sem vínculo a atendimento.</p>
  </div>
  <a href="<?= url('/medidas-campo/nova') ?>" class="btn btn-primary" style="border-radius:50px;font-weight:800;">
    ➕ Nova Medida de Campo
  </a>
</div>

<!-- Filtros -->
<div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
  <?php foreach (['' => 'Todas', 'rascunho' => 'Rascunho', 'assinado' => 'Assinadas'] as $val => $label): ?>
  <a href="<?= url('/medidas-campo' . ($val ? '?status='.$val : '')) ?>"
     class="btn btn-sm <?= $status === $val ? 'btn-primary' : 'btn-secondary' ?>"
     style="border-radius:50px;">
    <?= $label ?>
  </a>
  <?php endforeach; ?>
</div>

<?php if (empty($medidas)): ?>
  <div class="card" style="text-align:center;padding:60px 20px;">
    <div style="font-size:52px;margin-bottom:16px;">📋</div>
    <h3 style="color:var(--text-secondary);margin-bottom:8px;">Nenhuma medida registrada</h3>
    <p class="text-muted text-sm">Use o botão acima para gerar um documento de medida de proteção em campo.</p>
    <a href="<?= url('/medidas-campo/nova') ?>" class="btn btn-primary" style="margin-top:20px;border-radius:50px;">
      📝 Criar Primeira Medida
    </a>
  </div>
<?php else: ?>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px;">
    <?php foreach ($medidas as $m): ?>
    <a href="<?= url('/medidas-campo/' . $m['id']) ?>" style="text-decoration:none;">
      <div class="card" style="padding:20px;border-left:4px solid <?= $m['status'] === 'assinado' ? 'var(--success)' : 'var(--warning)' ?>;transition:transform .15s,box-shadow .15s;cursor:pointer;"
           onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,.15)'"
           onmouseout="this.style.transform='';this.style.boxShadow=''">

        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;margin-bottom:12px;">
          <div>
            <div style="font-size:11px;font-weight:800;color:var(--accent);letter-spacing:1px;margin-bottom:4px;">
              <?= htmlspecialchars($m['numero_doc']) ?>
            </div>
            <div style="font-size:14px;font-weight:700;color:var(--text-primary);">
              <?= htmlspecialchars($m['nome_crianca_dec'] ?: '—') ?>
            </div>
          </div>
          <span class="badge badge-<?= $m['status'] ?>" style="flex-shrink:0;font-size:10px;padding:3px 10px;border-radius:50px;">
            <?= $m['status'] === 'assinado' ? '✅ Assinado' : '✏️ Rascunho' ?>
          </span>
        </div>

        <div style="font-size:12px;color:var(--text-secondary);margin-bottom:10px;">
          <strong>Medida:</strong> <?= htmlspecialchars($m['tipo_medida']) ?>
        </div>
        <?php if ($m['artigo_eca']): ?>
        <span style="font-size:10px;background:rgba(99,102,241,.1);color:var(--accent);padding:2px 8px;border-radius:4px;font-weight:700;">
          <?= htmlspecialchars($m['artigo_eca']) ?>
        </span>
        <?php endif; ?>

        <div style="margin-top:12px;font-size:11px;color:var(--text-muted);display:flex;justify-content:space-between;">
          <span>👤 <?= htmlspecialchars($m['conselheiro']) ?></span>
          <span><?= date('d/m/Y H:i', strtotime($m['created_at'])) ?></span>
        </div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
