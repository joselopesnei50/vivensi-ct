<!-- ===== COOKIE CONSENT BANNER ===== -->
<div id="cookieBanner" class="cookie-banner" style="display:none;" role="dialog" aria-label="Aviso de cookies">
  <div class="cookie-icon">🍪</div>
  <div class="cookie-text">
    <strong>Utilizamos cookies essenciais</strong> para segurança da sessão e funcionamento do sistema,
    em conformidade com a <strong>LGPD (Lei 13.709/18)</strong>.
    <span class="cookie-links">
      <a href="<?= url('/privacidade') ?>">Política de Privacidade</a>
      &middot;
      <a href="<?= url('/termos-de-uso') ?>">Termos de Uso</a>
    </span>
  </div>
  <div class="cookie-actions">
    <button onclick="rejectCookies()" class="btn btn-ghost btn-sm" type="button">Recusar não essenciais</button>
    <button onclick="acceptCookies()" class="btn btn-primary btn-sm" type="button">✅ Aceitar e continuar</button>
  </div>
</div>

<script>
(function () {
  var consent = localStorage.getItem('vct_cookie_consent');
  if (!consent) {
    var banner = document.getElementById('cookieBanner');
    if (banner) banner.style.display = 'flex';
  }
})();

function acceptCookies() {
  localStorage.setItem('vct_cookie_consent', 'accepted');
  _hideCookieBanner();
}

function rejectCookies() {
  localStorage.setItem('vct_cookie_consent', 'rejected');
  _hideCookieBanner();
}

function _hideCookieBanner() {
  var b = document.getElementById('cookieBanner');
  if (!b) return;
  b.style.transition = 'opacity .35s, transform .35s';
  b.style.opacity = '0';
  b.style.transform = 'translateY(100%)';
  setTimeout(function () { b.remove(); }, 380);
}
</script>
