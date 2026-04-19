/**
 * VivensiCT — App JS v2
 */

/* ===== THEME TOGGLE ===== */
(function () {
  const KEY = 'vct_theme';
  const html = document.documentElement;

  // Aplica tema salvo imediatamente (evita flash)
  const saved = localStorage.getItem(KEY) || 'dark';
  html.setAttribute('data-theme', saved);

  window.toggleTheme = function () {
    const current = html.getAttribute('data-theme') || 'dark';
    const next = current === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem(KEY, next);

    // Atualiza tema do Mermaid se estiver disponível
    if (window.mermaid) {
      mermaid.initialize({ theme: next === 'light' ? 'default' : 'dark' });
    }
  };
})();

/* ===== MOBILE SIDEBAR ===== */
(function () {
  const btn     = document.getElementById('sidebarToggle');
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');
  if (!btn || !sidebar) return;

  function openSidebar() {
    sidebar.classList.add('open');
    overlay?.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeSidebar() {
    sidebar.classList.remove('open');
    overlay?.classList.remove('active');
    document.body.style.overflow = '';
  }

  btn.addEventListener('click', (e) => {
    e.stopPropagation();
    sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
  });

  overlay?.addEventListener('click', closeSidebar);

  // Fechar ao clicar em link dentro da sidebar em mobile
  sidebar.querySelectorAll('.nav-item').forEach((link) => {
    link.addEventListener('click', () => {
      if (window.innerWidth <= 768) closeSidebar();
    });
  });
})();

/* ===== AUTO-DISMISS ALERTS ===== */
document.querySelectorAll('.alert').forEach((el) => {
  setTimeout(() => {
    el.style.transition = 'opacity 0.5s, transform 0.5s';
    el.style.opacity = '0';
    el.style.transform = 'translateY(-8px)';
    setTimeout(() => el.remove(), 500);
  }, 5000);
});

/* ===== CONFIRM DELETES ===== */
document.querySelectorAll('[data-confirm]').forEach((el) => {
  el.addEventListener('click', function (e) {
    if (!confirm(this.dataset.confirm)) e.preventDefault();
  });
});

/* ===== FORM LOADING STATE ===== */
document.querySelectorAll('form[data-loading]').forEach((form) => {
  form.addEventListener('submit', function () {
    const btn = this.querySelector('[type=submit]');
    if (btn) {
      btn.disabled = true;
      btn.dataset.original = btn.innerHTML;
      btn.innerHTML = '<span class="loading-spinner"></span> Aguarde...';
    }
  });
});

/* ===== TEXTAREA CHAR COUNTER ===== */
document.querySelectorAll('textarea[data-counter]').forEach((ta) => {
  const max     = parseInt(ta.dataset.counter, 10) || 2000;
  const counter = document.createElement('div');
  counter.className = 'char-counter';
  ta.parentNode.insertBefore(counter, ta.nextSibling);

  function update() {
    const len = ta.value.length;
    counter.textContent = `${len} / ${max} caracteres`;
    counter.classList.toggle('warn', len > max * 0.85);
  }

  ta.addEventListener('input', update);
  update();
});

/* ===== WIZARD STEPS ===== */
window.Wizard = (function () {
  let current = 0;

  function init() {
    const panels = document.querySelectorAll('.wizard-panel');
    if (!panels.length) return;
    showStep(0);
  }

  function showStep(idx) {
    const panels = document.querySelectorAll('.wizard-panel');
    const steps  = document.querySelectorAll('.wizard-step');
    if (!panels[idx]) return;

    current = idx;
    const total = panels.length;

    panels.forEach((p, i) => p.classList.toggle('active', i === idx));

    steps.forEach((s, i) => {
      s.classList.remove('active', 'done');
      if (i < idx)   s.classList.add('done');
      if (i === idx) s.classList.add('active');
    });

    // Navegação única
    const btnPrev = document.getElementById('wizardPrev');
    const btnNext = document.getElementById('wizardNext');
    const navBar  = document.getElementById('wizardNav');

    if (btnPrev) btnPrev.style.visibility = idx === 0 ? 'hidden' : 'visible';

    // Na última etapa esconde a barra inferior (o submit está no card)
    if (navBar) navBar.style.display = idx === total - 1 ? 'none' : 'flex';
    if (btnNext) btnNext.textContent = idx === total - 2 ? 'Próximo: Revisão e IA →' : 'Próximo →';

    const indicator = document.getElementById('stepIndicator');
    if (indicator) indicator.textContent = `Etapa ${idx + 1} de ${total}`;

    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  function next() {
    const panel    = document.querySelectorAll('.wizard-panel')[current];
    const required = panel.querySelectorAll('[required]');
    let valid = true;

    required.forEach((field) => {
      if (!field.value.trim()) {
        field.classList.add('error-field');
        field.focus();
        field.addEventListener('input', () => field.classList.remove('error-field'), { once: true });
        valid = false;
      }
    });

    if (!valid) {
      showToast('Preencha os campos obrigatórios antes de avançar.', 'warning');
      return;
    }

    showStep(current + 1);
  }

  function prev() { showStep(current - 1); }

  return { init, next, prev, goTo: showStep };
})();

/* ===== IA TOGGLE ===== */
window.toggleIA = function (checkbox) {
  const wrap = document.getElementById('iaWrap');
  if (wrap) wrap.classList.toggle('active', checkbox.checked);
};

/* ===== GLOBAL TOAST ===== */
window.showToast = function (msg, type = 'info') {
  const icons = { success: '✅', error: '⚠️', warning: '⚡', info: 'ℹ️' };
  const toast = document.createElement('div');
  toast.className = `alert alert-${type}`;
  toast.style.cssText =
    'position:fixed;bottom:24px;right:24px;z-index:9999;min-width:280px;max-width:400px;' +
    'box-shadow:0 8px 32px rgba(0,0,0,0.4);animation:slideIn .3s ease;cursor:pointer;';
  toast.innerHTML = `${icons[type] || ''} ${msg}`;
  toast.addEventListener('click', () => toast.remove());
  document.body.appendChild(toast);
  setTimeout(() => {
    toast.style.transition = 'opacity .4s, transform .4s';
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(20px)';
    setTimeout(() => toast.remove(), 400);
  }, 4000);
};

/* ===== GLOBAL AJAX HELPER ===== */
window.api = function (url, method = 'GET', data = {}) {
  const token = document.querySelector('meta[name="csrf"]')?.content || '';
  return fetch(url, {
    method,
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': token,
    },
    body: method !== 'GET' ? JSON.stringify({ ...data, _token: token }) : undefined,
  }).then((r) => r.json());
};

/* ===== INIT ===== */
document.addEventListener('DOMContentLoaded', () => {
  Wizard.init();
});
