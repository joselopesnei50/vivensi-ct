<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>VivensiCT — Sistema ECA/SUAS para Conselheiros Tutelares</title>
<meta name="description" content="O sistema inteligente que transforma o trabalho do Conselho Tutelar. Análise de leis com IA, medidas de proteção, assinatura digital e conformidade LGPD.">
<link rel="icon" type="image/png" href="images/favicon.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>

/* ═══════════════════════════════════════════════
   GUARDIÃO DIGITAL — LANDING PAGE
   Paleta: Azul & Branco · Premium SaaS
   ═══════════════════════════════════════════════ */

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { scroll-behavior: smooth; }

:root {
  /* Azuis */
  --navy:      #020c1b;
  --navy2:     #041022;
  --navy3:     #071629;
  --blue-dark: #0f2d5e;
  --blue:      #1d4ed8;
  --blue-mid:  #2563eb;
  --blue-bright:#3b82f6;
  --blue-light: #60a5fa;
  --blue-pale:  #bfdbfe;
  --blue-ultra: #eff6ff;

  /* Branco / Neutros */
  --white:     #ffffff;
  --off-white: #f8faff;
  --gray-100:  #f0f4ff;
  --gray-200:  #dbe6ff;
  --gray-400:  #93a8cc;
  --gray-600:  #5b78a8;
  --gray-800:  #1e3a5f;

  /* Utilitários */
  --green:   #10b981;
  --orange:  #f59e0b;
  --red:     #ef4444;

  /* Sombras */
  --shadow-sm:  0 2px 12px rgba(29,78,216,0.08);
  --shadow-md:  0 8px 32px rgba(29,78,216,0.12);
  --shadow-lg:  0 20px 60px rgba(29,78,216,0.18);
  --shadow-xl:  0 40px 100px rgba(2,12,27,0.5);

  --radius:    16px;
  --radius-sm: 10px;
  --radius-lg: 24px;
}

body {
  font-family: 'Inter', -apple-system, sans-serif;
  background: var(--white);
  color: var(--navy);
  overflow-x: hidden;
  -webkit-font-smoothing: antialiased;
}

/* ══════════ NAVBAR ══════════ */
nav {
  position: fixed;
  top: 0; left: 0; right: 0;
  z-index: 200;
  padding: 18px 64px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  transition: all .35s ease;
}

nav.scrolled {
  background: rgba(255,255,255,0.92);
  backdrop-filter: blur(24px);
  border-bottom: 1px solid rgba(29,78,216,0.1);
  box-shadow: var(--shadow-sm);
  padding: 12px 64px;
}

.nav-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
}

.nav-logo .logo-icon {
  width: 40px; height: 40px;
  background: linear-gradient(135deg, var(--blue-mid), var(--blue-bright));
  border-radius: 11px;
  display: flex; align-items: center; justify-content: center;
  font-size: 20px;
  box-shadow: 0 4px 14px rgba(37,99,235,0.35);
}

.nav-logo .logo-name {
  font-size: 17px;
  font-weight: 800;
  color: var(--white);
  letter-spacing: -0.4px;
  transition: color .3s;
}
nav.scrolled .nav-logo .logo-name { color: var(--navy); }

.nav-links {
  display: flex;
  align-items: center;
  gap: 36px;
  list-style: none;
}

.nav-links a {
  color: rgba(255,255,255,0.75);
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  transition: color .2s;
}
.nav-links a:hover { color: var(--white); }
nav.scrolled .nav-links a { color: var(--gray-600); }
nav.scrolled .nav-links a:hover { color: var(--navy); }

.nav-cta { display: flex; align-items: center; gap: 10px; }

.btn-nav-ghost {
  color: rgba(255,255,255,0.9);
  text-decoration: none;
  font-size: 14px;
  font-weight: 600;
  padding: 9px 18px;
  border-radius: 100px;
  border: 1.5px solid rgba(255,255,255,0.3);
  transition: all .2s;
}
.btn-nav-ghost:hover { border-color: rgba(255,255,255,0.7); color: var(--white); }
nav.scrolled .btn-nav-ghost { color: var(--navy); border-color: var(--gray-200); }
nav.scrolled .btn-nav-ghost:hover { border-color: var(--blue-bright); color: var(--blue-mid); }

.btn-nav-solid {
  background: var(--blue-mid);
  color: var(--white);
  text-decoration: none;
  font-size: 14px;
  font-weight: 700;
  padding: 10px 22px;
  border-radius: 100px;
  transition: all .2s;
  box-shadow: 0 4px 14px rgba(37,99,235,0.3);
  display: inline-flex; align-items: center; gap: 6px;
}
.btn-nav-solid:hover { background: var(--blue); transform: translateY(-1px); box-shadow: 0 8px 20px rgba(37,99,235,0.4); }

/* ══════════ HERO — NOVO DESIGN (centrado, LGPD em destaque) ══════════ */

/* ══════════ HERO KEYFRAMES ══════════ */
@keyframes heroFadeUp {
  from { opacity:0; transform:translateY(24px); }
  to   { opacity:1; transform:translateY(0); }
}
@keyframes blink {
  0%,100% { opacity:1; } 50% { opacity:.35; }
}
@keyframes shieldPulse {
  0%,100% { box-shadow: 0 0 0 0 rgba(16,185,129,0); }
  50%     { box-shadow: 0 0 0 10px rgba(16,185,129,0.12); }
}

/* ══════════ HERO ══════════ */
.hero {
  background: linear-gradient(155deg, var(--navy) 0%, #041530 55%, #071a3e 100%);
  min-height: 100vh;
  padding: 0 64px 0;
  position: relative;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

/* Orbe de luz sutil — apenas dois, discretos */
.hero::before {
  content: '';
  position: absolute;
  top: -200px; left: -150px;
  width: 650px; height: 650px;
  background: radial-gradient(circle, rgba(37,99,235,0.18) 0%, transparent 65%);
  pointer-events: none;
}
.hero::after {
  content: '';
  position: absolute;
  bottom: -80px; right: -80px;
  width: 500px; height: 500px;
  background: radial-gradient(circle, rgba(16,185,129,0.10) 0%, transparent 65%);
  pointer-events: none;
}

/* Grid de pontos — bem sutil */
.hero-grid-bg {
  position: absolute;
  inset: 0;
  background-image: radial-gradient(rgba(96,165,250,0.12) 1px, transparent 1px);
  background-size: 44px 44px;
  pointer-events: none;
  mask-image: radial-gradient(ellipse 70% 70% at 50% 50%, black 20%, transparent 100%);
}

/* ── CONTEÚDO CENTRALIZADO ── */
.hero-inner {
  max-width: 860px;
  width: 100%;
  text-align: center;
  position: relative;
  z-index: 1;
  padding: 140px 0 90px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0;
}

/* Linha de badges superiores */
.hero-top-badges {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  flex-wrap: wrap;
  margin-bottom: 36px;
  animation: heroFadeUp 0.9s cubic-bezier(0.23,1,0.32,1) 0.2s both;
}

/* Badge "Sistema Ativo" */
.badge-active {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  background: rgba(96,165,250,0.10);
  border: 1px solid rgba(96,165,250,0.22);
  padding: 7px 15px;
  border-radius: 100px;
  font-size: 12px;
  font-weight: 700;
  color: var(--blue-pale);
  letter-spacing: 0.3px;
}
.badge-dot {
  width: 7px; height: 7px;
  border-radius: 50%;
  background: var(--green);
  animation: blink 2s infinite;
}

/* Badge "GRÁTIS PARA SEMPRE" */
.badge-free {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  background: rgba(16,185,129,0.12);
  border: 1px solid rgba(16,185,129,0.3);
  padding: 7px 15px;
  border-radius: 100px;
  font-size: 12px;
  font-weight: 800;
  color: #6ee7b7;
  letter-spacing: 0.5px;
  text-transform: uppercase;
}

/* Título principal */
.hero-title {
  font-size: clamp(44px, 6.5vw, 82px);
  font-weight: 900;
  line-height: 1.0;
  letter-spacing: -3px;
  color: var(--white);
  margin-bottom: 26px;
  animation: heroFadeUp 0.9s cubic-bezier(0.23,1,0.32,1) 0.35s both;
}
.hero-title .hl-blue {
  background: linear-gradient(90deg, var(--blue-light) 0%, #93c5fd 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}
.hero-title .hl-green {
  background: linear-gradient(90deg, #6ee7b7 0%, #34d399 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Subtítulo */
.hero-sub {
  font-size: 17px;
  color: rgba(191,219,254,0.75);
  line-height: 1.8;
  margin-bottom: 44px;
  max-width: 580px;
  animation: heroFadeUp 0.9s cubic-bezier(0.23,1,0.32,1) 0.5s both;
}

/* CTAs */
.hero-btns {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  justify-content: center;
  margin-bottom: 64px;
  animation: heroFadeUp 0.9s cubic-bezier(0.23,1,0.32,1) 0.65s both;
}

.btn-hero-white {
  background: var(--white);
  color: var(--navy);
  padding: 15px 34px;
  border-radius: 100px;
  font-size: 15px;
  font-weight: 800;
  text-decoration: none;
  display: inline-flex; align-items: center; gap: 8px;
  transition: all .3s;
  box-shadow: 0 6px 24px rgba(0,0,0,0.3);
}
.btn-hero-white:hover { transform: translateY(-2px); box-shadow: 0 14px 36px rgba(0,0,0,0.35); }

.btn-hero-outline {
  color: rgba(255,255,255,0.9);
  padding: 15px 28px;
  border-radius: 100px;
  font-size: 15px;
  font-weight: 600;
  text-decoration: none;
  display: inline-flex; align-items: center; gap: 8px;
  border: 1.5px solid rgba(255,255,255,0.18);
  transition: all .3s;
}
.btn-hero-outline:hover { border-color: rgba(255,255,255,0.45); background: rgba(255,255,255,0.05); }

/* ── COMPLIANCE GRID ── */
.hero-compliance {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr;
  gap: 10px;
  width: 100%;
  animation: heroFadeUp 0.9s cubic-bezier(0.23,1,0.32,1) 0.8s both;
}

.compliance-card {
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.09);
  border-radius: 14px;
  padding: 18px 20px;
  text-align: left;
  transition: all .25s;
  backdrop-filter: blur(8px);
}
.compliance-card:hover {
  background: rgba(255,255,255,0.07);
  border-color: rgba(255,255,255,0.15);
  transform: translateY(-2px);
}

/* Card LGPD — destaque, ocupa 2 colunas */
.compliance-card.lgpd-card {
  background: rgba(16,185,129,0.08);
  border-color: rgba(16,185,129,0.25);
  animation: shieldPulse 3s ease-in-out infinite;
}
.compliance-card.lgpd-card:hover {
  background: rgba(16,185,129,0.13);
  border-color: rgba(16,185,129,0.4);
}

.cc-icon {
  font-size: 24px;
  margin-bottom: 10px;
  display: block;
}

.cc-title {
  font-size: 13px;
  font-weight: 800;
  color: var(--white);
  margin-bottom: 3px;
  letter-spacing: -0.2px;
}
.lgpd-card .cc-title {
  font-size: 16px;
  color: #6ee7b7;
}

.cc-desc {
  font-size: 11px;
  color: rgba(191,219,254,0.55);
  line-height: 1.5;
}
.lgpd-card .cc-desc {
  color: rgba(110,231,183,0.7);
  font-size: 12px;
  line-height: 1.6;
}

/* Linha separadora com cadeado LGPD */
.lgpd-lock-row {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 10px;
  padding-top: 10px;
  border-top: 1px solid rgba(16,185,129,0.2);
  font-size: 11px;
  font-weight: 700;
  color: #6ee7b7;
  letter-spacing: 0.3px;
}

/* ══════════ TRUST BAR ══════════ */
.trust-bar {
  background: var(--off-white);
  border-top: 1px solid var(--gray-200);
  border-bottom: 1px solid var(--gray-200);
  padding: 24px 64px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 24px;
  flex-wrap: wrap;
}

.trust-label {
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 1.2px;
  color: var(--gray-400);
  white-space: nowrap;
}

.trust-items { display: flex; gap: 36px; align-items: center; flex-wrap: wrap; }

.trust-item {
  display: flex; align-items: center; gap: 10px;
  font-size: 13px;
  font-weight: 700;
  color: var(--gray-800);
}

.trust-icon {
  width: 34px; height: 34px;
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-size: 16px;
  flex-shrink: 0;
}
.ti-green  { background: #d1fae5; }
.ti-blue   { background: #dbeafe; }
.ti-red    { background: #fee2e2; }
.ti-yellow { background: #fef3c7; }
.ti-purple { background: #ede9fe; }

/* ══════════ SEÇÃO GENÉRICA ══════════ */
section { padding: 100px 64px; }

.s-label {
  display: inline-flex; align-items: center; gap: 7px;
  font-size: 12px; font-weight: 700;
  text-transform: uppercase; letter-spacing: 1.8px;
  color: var(--blue-mid);
  margin-bottom: 16px;
  background: var(--blue-ultra);
  padding: 6px 14px;
  border-radius: 100px;
  border: 1px solid var(--blue-pale);
}

.s-title {
  font-size: clamp(32px,4.5vw, 56px);
  font-weight: 900;
  line-height: 1.08;
  letter-spacing: -1.8px;
  color: var(--navy);
  margin-bottom: 18px;
  max-width: 680px;
}

.s-sub {
  font-size: 17px;
  color: var(--gray-600);
  line-height: 1.75;
  max-width: 560px;
}

/* ══════════ FEATURES ══════════ */
.feat-section { background: var(--white); }

.feat-grid {
  display: grid;
  grid-template-columns: repeat(3,1fr);
  gap: 24px;
  margin-top: 60px;
}

.feat-card {
  background: var(--off-white);
  border: 1.5px solid var(--gray-200);
  border-radius: var(--radius);
  padding: 36px 32px;
  transition: all .3s;
  position: relative;
  overflow: hidden;
}

.feat-card::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 3px;
  background: linear-gradient(90deg, var(--blue-mid), var(--blue-bright));
  opacity: 0;
  transition: opacity .3s;
}

.feat-card:hover {
  border-color: var(--blue-pale);
  box-shadow: var(--shadow-md);
  transform: scale(1.04) translateY(-3px);
  background: var(--white);
  z-index: 1;
}
.feat-card:hover::before { opacity: 1; }


.feat-icon-wrap {
  width: 54px; height: 54px;
  background: var(--blue-ultra);
  border: 1.5px solid var(--blue-pale);
  border-radius: 14px;
  display: flex; align-items: center; justify-content: center;
  font-size: 24px;
  margin-bottom: 20px;
}

.feat-card h3 {
  font-size: 18px;
  font-weight: 800;
  color: var(--navy);
  margin-bottom: 10px;
  letter-spacing: -0.4px;
}

.feat-card p {
  font-size: 14px;
  color: var(--gray-600);
  line-height: 1.75;
  margin-bottom: 16px;
}

.feat-tag {
  display: inline-block;
  font-size: 11px;
  font-weight: 700;
  color: var(--blue-mid);
  background: var(--blue-ultra);
  border: 1px solid var(--blue-pale);
  padding: 4px 12px;
  border-radius: 100px;
}

/* ══════════ COMO FUNCIONA ══════════ */
.how-section { background: var(--navy); }

.how-section .s-title { color: var(--white); max-width: 100%; }
.how-section .s-sub   { color: var(--blue-pale); opacity: .8; }
.how-section .s-label { background: rgba(96,165,250,.1); border-color: rgba(96,165,250,.2); color: var(--blue-pale); }

.steps-grid {
  display: grid;
  grid-template-columns: repeat(3,1fr);
  gap: 2px;
  margin-top: 60px;
  background: rgba(255,255,255,0.06);
  border-radius: var(--radius);
  overflow: hidden;
  border: 1px solid rgba(255,255,255,0.06);
}

.step-card {
  background: var(--navy2);
  padding: 48px 36px;
  position: relative;
  transition: background .3s;
}
.step-card:hover { background: var(--navy3); }

.step-num-bg {
  position: absolute;
  top: 16px; right: 24px;
  font-size: 96px;
  font-weight: 900;
  color: rgba(37,99,235,0.08);
  line-height: 1;
  pointer-events: none;
}

.step-icon {
  width: 56px; height: 56px;
  background: rgba(37,99,235,0.15);
  border: 1px solid rgba(37,99,235,0.25);
  border-radius: var(--radius-sm);
  display: flex; align-items: center; justify-content: center;
  font-size: 26px;
  margin-bottom: 24px;
}

.step-card h3 {
  font-size: 22px;
  font-weight: 800;
  color: var(--white);
  margin-bottom: 12px;
  letter-spacing: -0.5px;
}

.step-card p {
  font-size: 14px;
  color: var(--blue-pale);
  line-height: 1.75;
  opacity: .75;
}

.step-connector {
  position: absolute;
  right: -14px; top: 50%;
  transform: translateY(-50%);
  width: 28px; height: 28px;
  background: var(--blue-mid);
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 12px;
  color: white;
  font-weight: 700;
  z-index: 2;
}

/* ══════════ NÚMEROS ══════════ */
.nums-section { background: var(--blue-mid); text-align: center; }

.nums-section .s-label {
  background: rgba(255,255,255,.1);
  border-color: rgba(255,255,255,.2);
  color: rgba(255,255,255,.9);
  justify-content: center;
}
.nums-section .s-title { color: var(--white); max-width: 100%; text-align: center; margin: 0 auto 60px; }

.nums-grid {
  display: grid;
  grid-template-columns: repeat(4,1fr);
  gap: 2px;
  background: rgba(255,255,255,0.1);
  border-radius: var(--radius);
  overflow: hidden;
}

.num-card {
  background: rgba(255,255,255,0.06);
  padding: 48px 24px;
  transition: background .3s;
}
.num-card:hover { background: rgba(255,255,255,0.12); }

.num-card .big-n {
  font-size: 58px;
  font-weight: 900;
  color: var(--white);
  letter-spacing: -2px;
  line-height: 1;
  margin-bottom: 10px;
}
.num-card .big-l {
  font-size: 14px;
  color: rgba(255,255,255,.65);
  font-weight: 500;
  line-height: 1.4;
}

/* ══════════ LEGAL ══════════ */
.legal-section { background: var(--off-white); }

.legal-grid {
  display: grid;
  grid-template-columns: repeat(2,1fr);
  gap: 20px;
  margin-top: 60px;
}

.legal-card {
  background: var(--white);
  border: 1.5px solid var(--gray-200);
  border-radius: var(--radius);
  padding: 32px;
  display: flex;
  gap: 20px;
  transition: all .3s;
}
.legal-card:hover {
  border-color: var(--blue-pale);
  box-shadow: var(--shadow-md);
  transform: translateY(-2px);
}

.lc-icon-wrap {
  width: 52px; height: 52px;
  background: var(--blue-ultra);
  border: 1.5px solid var(--blue-pale);
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 24px;
  flex-shrink: 0;
}

.legal-card h4 {
  font-size: 17px;
  font-weight: 800;
  color: var(--navy);
  margin-bottom: 8px;
  letter-spacing: -0.3px;
}

.legal-card p {
  font-size: 13.5px;
  color: var(--gray-600);
  line-height: 1.7;
}

.lc-badge {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  margin-top: 12px;
  font-size: 11px;
  font-weight: 700;
  color: var(--green);
  background: #d1fae5;
  border: 1px solid #a7f3d0;
  padding: 4px 12px;
  border-radius: 100px;
}

/* ══════════ MOCKUP SECTION ══════════ */
.mockup-section { background: var(--gray-100); overflow: hidden; }

.mockup-section .s-title { max-width: 100%; }

.mockup-browser-wrap {
  margin-top: 56px;
  position: relative;
  max-width: 960px;
  margin-left: auto;
  margin-right: auto;
}

.mb {
  background: var(--white);
  border: 1.5px solid var(--gray-200);
  border-radius: 20px;
  overflow: hidden;
  box-shadow: var(--shadow-xl);
}

.mb-bar {
  background: var(--off-white);
  border-bottom: 1px solid var(--gray-200);
  padding: 14px 20px;
  display: flex;
  align-items: center;
  gap: 12px;
}

.mb-dots { display: flex; gap: 6px; }
.mb-dots span { width: 12px; height: 12px; border-radius: 50%; }

.mb-url {
  flex: 1;
  background: var(--white);
  border: 1px solid var(--gray-200);
  border-radius: 8px;
  padding: 7px 16px;
  font-size: 12px;
  color: var(--gray-600);
  text-align: center;
  max-width: 380px;
  margin: 0 auto;
}

.mb-body {
  display: flex;
  background: #f8faff;
  min-height: 420px;
}

/* Sidebar */
.mb-sidebar {
  width: 180px;
  background: var(--navy);
  padding: 20px 10px;
  flex-shrink: 0;
}

.mb-logo-row {
  display: flex; align-items: center; gap: 8px;
  padding: 8px 10px 18px;
  border-bottom: 1px solid rgba(255,255,255,0.08);
  margin-bottom: 10px;
}
.mb-logo-icon { width: 26px; height: 26px; background: var(--blue-mid); border-radius: 6px; display:flex;align-items:center;justify-content:center;font-size:12px; }
.mb-logo-name { font-size: 11px; font-weight: 700; color: white; }

.mb-nav { padding: 7px 10px; border-radius: 7px; font-size: 11px; color: rgba(255,255,255,.45); margin-bottom:2px; display:flex;align-items:center;gap:6px; }
.mb-nav.on { background: rgba(37,99,235,.3); color: var(--blue-pale); }

/* Main */
.mb-main { flex: 1; padding: 20px; }
.mb-page-title { font-size: 16px; font-weight: 800; color: var(--navy); margin-bottom: 16px; }

.mb-stats { display: grid; grid-template-columns: repeat(4,1fr); gap: 10px; margin-bottom: 14px; }

.mb-stat {
  background: var(--white);
  border: 1px solid var(--gray-200);
  border-radius: 10px;
  padding: 14px;
}
.mb-stat-n { font-size: 22px; font-weight: 900; color: var(--navy); }
.mb-stat-l { font-size: 10px; color: var(--gray-400); margin-top: 2px; }

.mb-card {
  background: var(--white);
  border: 1px solid var(--gray-200);
  border-radius: 10px;
  padding: 14px;
  margin-bottom: 10px;
}
.mb-card-title { font-size: 11px; font-weight: 700; color: var(--gray-600); margin-bottom: 10px; text-transform: uppercase; letter-spacing: .5px; }

.mb-row { display:flex;align-items:center;gap:10px;padding:6px 0;border-bottom:1px solid var(--gray-100); }
.mb-row:last-child { border-bottom: none; }

.mb-proto { font-size: 11px; font-weight: 700; color: var(--blue-mid); min-width: 80px; }
.mb-tipo { font-size: 11px; color: var(--gray-600); flex: 1; }
.mb-badge { font-size: 9px; font-weight: 700; padding: 2px 8px; border-radius: 100px; }

.mb-ai {
  background: var(--blue-ultra);
  border: 1px solid var(--blue-pale);
  border-radius: 10px;
  padding: 12px 14px;
}
.mb-ai-title { font-size: 11px; font-weight: 700; color: var(--blue-mid); margin-bottom: 4px; }
.mb-ai-text  { font-size: 11px; color: var(--gray-600); line-height: 1.5; }

/* ══════════ DEPOIMENTO ══════════ */
.quote-section {
  background: linear-gradient(160deg, var(--navy) 0%, #071a3e 100%);
  text-align: center;
  padding: 100px 64px;
  position: relative;
  overflow: hidden;
}
.quote-section::before {
  content:'';
  position:absolute; top:-200px; left:50%; transform:translateX(-50%);
  width:600px; height:600px;
  background: radial-gradient(circle, rgba(37,99,235,0.2) 0%, transparent 65%);
  pointer-events:none;
}

.quote-mark {
  font-size: 100px;
  line-height: 0.7;
  color: rgba(96,165,250,0.25);
  font-family: Georgia, serif;
  display: block;
  margin-bottom: 12px;
}

.quote-text {
  font-size: clamp(20px,2.5vw,30px);
  font-weight: 700;
  color: var(--white);
  line-height: 1.5;
  letter-spacing: -0.5px;
  max-width: 800px;
  margin: 0 auto 36px;
  position: relative; z-index: 1;
}

.quote-author {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 16px;
  position: relative; z-index: 1;
}

.q-avatar {
  width: 52px; height: 52px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--blue-mid), var(--blue-bright));
  display:flex; align-items:center; justify-content:center;
  font-size: 22px; font-weight: 900; color: white;
  box-shadow: 0 4px 16px rgba(37,99,235,.4);
}

.q-name { font-size: 15px; font-weight: 700; color: var(--white); text-align: left; }
.q-role { font-size: 13px; color: var(--blue-pale); opacity: .7; text-align: left; }

/* ══════════ CTA ══════════ */
.cta-section {
  background: var(--blue-mid);
  text-align: center;
  padding: 120px 64px;
  position: relative;
  overflow: hidden;
}

.cta-section::before {
  content: '';
  position: absolute;
  top: -150px; right: -150px;
  width: 500px; height: 500px;
  background: rgba(255,255,255,0.05);
  border-radius: 50%;
  pointer-events: none;
}
.cta-section::after {
  content: '';
  position: absolute;
  bottom: -100px; left: -100px;
  width: 400px; height: 400px;
  background: rgba(255,255,255,0.04);
  border-radius: 50%;
  pointer-events: none;
}

.cta-title {
  font-size: clamp(40px,6vw,76px);
  font-weight: 900;
  letter-spacing: -2.5px;
  line-height: 1;
  color: var(--white);
  margin-bottom: 20px;
  position: relative; z-index: 1;
}

.cta-sub {
  font-size: 18px;
  color: rgba(255,255,255,.75);
  margin-bottom: 48px;
  position: relative; z-index: 1;
}

.cta-btns {
  display: flex;
  justify-content: center;
  gap: 14px;
  flex-wrap: wrap;
  position: relative; z-index: 1;
}

.btn-cta-white {
  background: var(--white);
  color: var(--blue-mid);
  padding: 18px 40px;
  border-radius: 100px;
  font-size: 16px;
  font-weight: 800;
  text-decoration: none;
  display: inline-flex; align-items: center; gap: 8px;
  transition: all .3s;
  box-shadow: 0 8px 30px rgba(0,0,0,0.2);
}
.btn-cta-white:hover { transform: translateY(-3px); box-shadow: 0 16px 40px rgba(0,0,0,0.25); }

.btn-cta-ghost {
  color: var(--white);
  padding: 18px 36px;
  border-radius: 100px;
  font-size: 16px;
  font-weight: 700;
  text-decoration: none;
  display: inline-flex; align-items: center; gap: 8px;
  border: 2px solid rgba(255,255,255,.35);
  transition: all .3s;
  cursor: pointer;
  background: transparent;
  font-family: inherit;
}
.btn-cta-ghost:hover { border-color: rgba(255,255,255,.7); background: rgba(255,255,255,.08); transform: translateY(-2px); }

.cta-note {
  margin-top: 24px;
  font-size: 13px;
  color: rgba(255,255,255,.55);
  position: relative; z-index: 1;
}

/* ══════════ FOOTER ══════════ */
footer {
  background: var(--navy);
  padding: 72px 64px 40px;
}

.footer-grid {
  display: grid;
  grid-template-columns: 1.8fr 1fr 1fr 1fr;
  gap: 60px;
  margin-bottom: 56px;
}

.f-brand .fb-logo { display:flex; align-items:center; gap:10px; text-decoration:none; margin-bottom:16px; }
.f-brand .fb-logo-icon { width:38px;height:38px;background:linear-gradient(135deg,var(--blue-mid),var(--blue-bright));border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;box-shadow:0 4px 14px rgba(37,99,235,.4); }
.f-brand .fb-logo-name { font-size:16px;font-weight:800;color:white; }
.f-brand p { font-size:14px; color: rgba(255,255,255,.4); line-height:1.7; max-width:260px; }

.f-col h5 { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:1.2px; color:rgba(255,255,255,.4); margin-bottom:18px; }
.f-col ul { list-style:none; }
.f-col li { margin-bottom:11px; }
.f-col a { color:rgba(255,255,255,.55); text-decoration:none; font-size:14px; transition:color .2s; }
.f-col a:hover { color:white; }

.footer-bottom {
  border-top: 1px solid rgba(255,255,255,.07);
  padding-top: 32px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 16px;
}

.footer-bottom p { font-size:13px; color:rgba(255,255,255,.3); }

.f-badges { display:flex; gap:8px; flex-wrap:wrap; }
.f-badge {
  padding: 5px 12px;
  border-radius: 100px;
  font-size: 11px; font-weight: 700;
  border: 1px solid rgba(255,255,255,.1);
  color: rgba(255,255,255,.4);
}

/* ══════════ REVEAL ANIM ══════════ */
.reveal {
  opacity: 0;
  transform: translateY(28px);
  transition: opacity .65s ease, transform .65s ease;
}
.reveal.visible { opacity:1; transform:translateY(0); }
.reveal-delay-1 { transition-delay: .1s; }
.reveal-delay-2 { transition-delay: .2s; }
.reveal-delay-3 { transition-delay: .3s; }

/* ══════════ PIX MODAL ══════════ */
.pix-overlay {
  position: fixed; inset: 0;
  background: rgba(2,12,27,.75);
  backdrop-filter: blur(10px);
  z-index: 1000;
  display: none;
  align-items: center;
  justify-content: center;
  padding: 20px;
}
.pix-overlay.open { display:flex; }

.pix-modal {
  background: var(--white);
  border-radius: 24px;
  padding: 48px 40px;
  max-width: 460px; width: 100%;
  text-align: center;
  box-shadow: 0 40px 100px rgba(0,0,0,0.4);
}

.pix-modal h3 { font-size:26px; font-weight:900; color:var(--navy); margin:16px 0 10px; letter-spacing:-0.5px; }
.pix-modal .pm-sub { font-size:15px; color:var(--gray-600); line-height:1.65; margin-bottom:28px; }

.pix-heart { font-size:48px; }

.pix-box {
  background: var(--blue-ultra);
  border: 2px solid var(--blue-pale);
  border-radius: 14px;
  padding: 20px;
  margin-bottom: 20px;
}
.pix-box .pix-lbl { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:var(--gray-400); margin-bottom:8px; }
.pix-box .pix-key { font-size:20px; font-weight:800; color:var(--blue-mid); letter-spacing:.5px; }

.btn-copy {
  width:100%; padding:14px;
  background: var(--blue-mid);
  color: white;
  border:none; border-radius:100px;
  font-size:15px; font-weight:700;
  cursor:pointer; font-family:inherit;
  display:flex; align-items:center; justify-content:center; gap:8px;
  transition:all .2s; margin-bottom:10px;
}
.btn-copy:hover { background:var(--blue); transform:translateY(-1px); }

.btn-close-modal {
  width:100%; padding:12px;
  background:transparent;
  color:var(--gray-600); border:1.5px solid var(--gray-200);
  border-radius:100px; font-size:14px; font-weight:600;
  cursor:pointer; font-family:inherit; transition:all .2s;
}
.btn-close-modal:hover { border-color:var(--gray-400); color:var(--navy); }

/* ══════════ BRUCE AI SECTION ══════════ */
.bruce-section {
  background: var(--white);
  padding: 100px 64px;
}

.bruce-mindmap-wrap {
  max-width: 1100px;
  margin: 0 auto 60px;
  background: var(--navy);
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 24px 80px rgba(2,12,27,0.25);
}

.bruce-sample-label {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 14px 24px;
  background: rgba(255,255,255,0.05);
  border-bottom: 1px solid rgba(255,255,255,0.07);
  font-size: 12px;
  font-weight: 600;
  color: rgba(255,255,255,0.5);
  letter-spacing: 0.3px;
}
.bsl-dot {
  width: 8px; height: 8px;
  border-radius: 50%;
  background: var(--green);
  animation: blink 2s infinite;
}

.bruce-mindmap {
  padding: 40px 32px;
  display: flex;
  align-items: flex-start;
  gap: 40px;
}

.mm-center {
  flex-shrink: 0;
  width: 180px;
  background: linear-gradient(135deg, var(--blue-mid), #4f46e5);
  border-radius: 16px;
  padding: 20px 16px;
  text-align: center;
  box-shadow: 0 8px 32px rgba(37,99,235,0.4);
  align-self: center;
}
.mm-c-icon { font-size: 28px; margin-bottom: 8px; }
.mm-c-title { font-size: 12px; font-weight: 800; color: #fff; line-height: 1.3; margin-bottom: 4px; }
.mm-c-sub { font-size: 10px; color: rgba(255,255,255,0.6); }

.mm-branches {
  flex: 1;
  display: grid;
  grid-template-columns: repeat(2,1fr);
  gap: 12px;
}

.mm-branch {
  border-radius: 12px;
  overflow: hidden;
  border: 1px solid rgba(255,255,255,0.08);
}

.mm-branch-head {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 16px;
  font-size: 12px;
  font-weight: 800;
  letter-spacing: 0.2px;
}
.mm-bh-icon { font-size: 16px; }

.mm-branch-blue .mm-branch-head { background: rgba(59,130,246,0.2); color: #93c5fd; }
.mm-branch-green .mm-branch-head { background: rgba(34,197,94,0.2); color: #86efac; }
.mm-branch-orange .mm-branch-head { background: rgba(245,158,11,0.2); color: #fcd34d; }
.mm-branch-purple .mm-branch-head { background: rgba(139,92,246,0.2); color: #c4b5fd; }

.mm-leaves { padding: 8px 12px; }
.mm-leaf {
  font-size: 11px;
  color: rgba(255,255,255,0.6);
  padding: 5px 8px;
  border-radius: 6px;
  margin-bottom: 3px;
  line-height: 1.4;
  border-left: 2px solid rgba(255,255,255,0.08);
  padding-left: 10px;
}
.mm-leaf-urgent { color: #fca5a5; border-left-color: #ef4444; }
.mm-leaf-high   { color: #fcd34d; border-left-color: #f59e0b; }

.bruce-mindmap-footer {
  padding: 16px 32px;
  border-top: 1px solid rgba(255,255,255,0.07);
  display: flex;
  align-items: center;
  gap: 32px;
  flex-wrap: wrap;
  background: rgba(255,255,255,0.03);
}
.bmf-stat {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: rgba(255,255,255,0.55);
}
.bmf-icon { font-size: 16px; }
.bmf-stat strong { color: rgba(255,255,255,0.9); }

.bruce-features {
  display: grid;
  grid-template-columns: repeat(3,1fr);
  gap: 24px;
  max-width: 1100px;
  margin: 0 auto;
}
.bf-item {
  padding: 28px;
  background: var(--off-white);
  border: 1.5px solid var(--gray-200);
  border-radius: 16px;
  transition: all .3s;
}
.bf-item:hover { border-color: var(--blue-pale); box-shadow: var(--shadow-md); transform: translateY(-3px); }
.bf-icon {
  width: 48px; height: 48px;
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 22px;
  margin-bottom: 16px;
}
.bf-title { font-size: 16px; font-weight: 800; color: var(--navy); margin-bottom: 8px; }
.bf-desc { font-size: 14px; color: var(--gray-600); line-height: 1.75; }

/* Responsive Bruce */
@media(max-width:900px) {
  .bruce-mindmap { flex-direction: column; }
  .mm-center { width: 100%; align-self: auto; }
  .mm-branches { grid-template-columns: 1fr; }
  .bruce-features { grid-template-columns: 1fr; }
}

/* ══════════ RESPONSIVE ══════════ */
@media(max-width:1100px){
  nav,section,footer { padding-left:32px; padding-right:32px; }
  nav.scrolled { padding-left:32px; padding-right:32px; }
  .hero { padding:0 32px; }
  .hero-compliance { grid-template-columns: 1fr 1fr; }
  .feat-grid { grid-template-columns:repeat(2,1fr); }
  .nums-grid { grid-template-columns:repeat(2,1fr); }
  .footer-grid { grid-template-columns:1fr 1fr; gap:40px; }
  .trust-bar { padding:20px 32px; }
  .quote-section { padding:80px 32px; }
  .cta-section { padding:80px 32px; }
}

@media(max-width:768px){
  nav { padding:16px 20px; }
  nav.scrolled { padding:12px 20px; }
  .nav-links { display:none; }
  .hero { padding:0 20px; }
  .hero-inner { padding:110px 0 60px; }
  .hero-title { font-size: clamp(36px, 10vw, 52px); letter-spacing: -2px; }
  .hero-compliance { grid-template-columns: 1fr; }
  section { padding:64px 20px; }
  .feat-grid { grid-template-columns:1fr; }
  .steps-grid { grid-template-columns:1fr; }
  .nums-grid { grid-template-columns:1fr 1fr; }
  .legal-grid { grid-template-columns:1fr; }
  .footer-grid { grid-template-columns:1fr; gap:32px; }
  .footer-bottom { flex-direction:column; text-align:center; }
  .trust-bar { padding:20px; flex-direction:column; align-items:flex-start; gap:16px; }
  .trust-items { gap:16px; }
  .mb-stats { grid-template-columns:repeat(2,1fr); }
  .cta-section,.quote-section { padding:64px 20px; }
  footer { padding:48px 20px 32px; }
}
</style>
</head>
<body>

<!-- ═══ NAVBAR ═══ -->
<nav id="navbar">
  <a href="#" class="nav-logo">
    <img src="images/logo.png" alt="VivensiCT" style="height:38px;width:auto;object-fit:contain;" id="navLogo">
  </a>

  <ul class="nav-links">
    <li><a href="#recursos">Recursos</a></li>
    <li><a href="#como-funciona">Como Funciona</a></li>
    <li><a href="#legal">Base Legal</a></li>
    <li><a href="#contato">Contato</a></li>
    <li><a href="cadastro">Cadastrar Conselho</a></li>
  </ul>

  <div class="nav-cta">
    <a href="cadastro" class="btn-nav-solid" style="background:linear-gradient(135deg,#10b981,#059669);">Registrar Conselho</a>
    <a href="login" class="btn-nav-ghost">Entrar</a>
    <a href="login" class="btn-nav-solid">Começar Grátis →</a>
  </div>
</nav>

<!-- ═══ HERO ═══ -->
<div class="hero">
  <div class="hero-grid-bg" aria-hidden="true"></div>

  <div class="hero-inner">

    <!-- Badges superiores -->
    <div class="hero-top-badges">
      <span class="badge-active">
        <span class="badge-dot"></span>
        Sistema Ativo
      </span>
      <span class="badge-free">
        Gratuito Para Conselhos Tutelares
      </span>
    </div>

    <!-- Título -->
    <h1 class="hero-title">
      Proteção <span class="hl-blue">inteligente</span><br>
      com conformidade<br>
      <span class="hl-green">LGPD</span> total.
    </h1>

    <!-- Subtítulo -->
    <p class="hero-sub">
      O sistema ECA/SUAS para Conselheiros Tutelares. Análise de leis com IA,
      encaminhamentos automáticos, assinatura digital e proteção de dados pessoais
      garantida por lei — sem custo para Conselhos Tutelares.
    </p>

    <!-- CTAs -->
    <div class="hero-btns">
      <a href="login" class="btn-hero-white">Acessar o Sistema</a>
      <a href="#como-funciona" class="btn-hero-outline">Como funciona</a>
    </div>

    <!-- Compliance Grid -->
    <div class="hero-compliance">

      <!-- Card LGPD — destaque (2 colunas) -->
      <div class="compliance-card lgpd-card">
        <span class="cc-icon">🔒</span>
        <div class="cc-title">LGPD — Lei 13.709/18</div>
        <div class="cc-desc">
          Dados pessoais de crianças e adolescentes criptografados com AES-128,
          expurgo automático em 3 dias e conformidade total com a Lei Geral de Proteção de Dados.
        </div>
        <div class="lgpd-lock-row">
          <span>🔐</span> Criptografia AES-128 · Expurgo automático · Conformidade legal
        </div>
      </div>

      <!-- ECA -->
      <div class="compliance-card">
        <span class="cc-icon">⚖️</span>
        <div class="cc-title">ECA</div>
        <div class="cc-desc">Lei 8.069/90<br>Art. 98, 101, 129 e correlatos</div>
      </div>

      <!-- SUAS -->
      <div class="compliance-card">
        <span class="cc-icon">🏛️</span>
        <div class="cc-title">SUAS</div>
        <div class="cc-desc">Tipificação Nacional<br>PAIF · PAEFI · CRAS · CREAS</div>
      </div>

      <!-- Bruce AI -->
      <div class="compliance-card">
        <span class="cc-icon" style="font-size:18px;font-weight:900;color:#60a5fa;letter-spacing:-.5px;">Bruce AI</span>
        <div class="cc-title">Análise Jurídica</div>
        <div class="cc-desc">ECA · SUAS em segundos<br>Bruce AI — Motor interno</div>
      </div>

    </div>

  </div>
</div>

<!-- ═══ COMO FUNCIONA ═══ -->
<section id="como-funciona" class="how-section">
  <div class="reveal">
    <div class="s-label">Processo</div>
    <h2 class="s-title">De 0 ao encaminhamento<br>em 3 passos.</h2>
    <p class="s-sub">Bruce AI faz o trabalho jurídico. Você faz o que importa: proteger a criança.</p>
  </div>

  <div class="steps-grid reveal">
    <div class="step-card">
      <div class="step-num-bg">01</div>
      <div class="step-icon" style="font-size:13px;font-weight:900;color:#60a5fa;letter-spacing:-.3px;">REG</div>
      <h3>Registre o Caso</h3>
      <p>Insira o relato da visita e o levantamento preliminar. Os dados sensíveis são criptografados automaticamente conforme a LGPD, garantindo total segurança.</p>
    </div>
    <div class="step-card">
      <div class="step-num-bg">02</div>
      <div class="step-icon" style="font-size:13px;font-weight:900;color:#60a5fa;letter-spacing:-.3px;">IA</div>
      <h3>Bruce AI Analisa o ECA/SUAS</h3>
      <p>O sistema cruza o caso com o Estatuto da Criança e a Tipificação Nacional SUAS. Gera análise de leis, fluxo de encaminhamentos, medidas e mapa mental em segundos.</p>
    </div>
    <div class="step-card">
      <div class="step-num-bg">03</div>
      <div class="step-icon" style="font-size:13px;font-weight:900;color:#60a5fa;letter-spacing:-.3px;">DOC</div>
      <h3>Documente e Assine</h3>
      <p>Gere minutas de relatórios e ofícios. Colete assinatura digital na tela (mobile). O documento é salvo com validade jurídica e removido automaticamente após 3 dias.</p>
    </div>
  </div>
</section>

<!-- ═══ BRUCE AI — MAPA MENTAL ═══ -->
<section class="bruce-section" id="bruce">

  <div class="reveal" style="text-align:center;max-width:760px;margin:0 auto 56px;">
    <div class="s-label" style="justify-content:center;">Bruce AI — O Coração do Sistema</div>
    <h2 class="s-title" style="text-align:center;max-width:100%;">Um caso registrado.<br>Um mapa mental gerado<br><span style="color:var(--blue-mid);">em segundos.</span></h2>
    <p class="s-sub" style="text-align:center;max-width:580px;margin:0 auto;">
      A Bruce AI analisa o relato do Conselheiro, cruza com ECA, SUAS e Lei Henry Borel,
      e gera automaticamente um mapa mental estruturado com encaminhamentos, medidas de
      proteção e minutas prontas para uso.
    </p>
  </div>

  <!-- Mapa Mental Visual (SVG estático representando output da IA) -->
  <div class="bruce-mindmap-wrap reveal">

    <!-- Label "Saída real da Bruce AI" -->
    <div class="bruce-sample-label">
      <span class="bsl-dot"></span> Saída real da Bruce AI · Caso CT-2026-00042
    </div>

    <div class="bruce-mindmap">

      <!-- Nó central -->
      <div class="mm-center">
        <div class="mm-c-icon">🛡️</div>
        <div class="mm-c-title">Caso CT-2026-00042</div>
        <div class="mm-c-sub">Negligência · Alta prioridade</div>
      </div>

      <!-- Ramos -->
      <div class="mm-branches">

        <!-- Análise de Leis -->
        <div class="mm-branch mm-branch-blue">
          <div class="mm-branch-head">
            <span class="mm-bh-icon">⚖️</span>
            <span class="mm-bh-title">Análise de Leis</span>
          </div>
          <div class="mm-leaves">
            <div class="mm-leaf">Art. 98 ECA — Violação de direitos</div>
            <div class="mm-leaf">Art. 101, IV — Inclusão em programa</div>
            <div class="mm-leaf">Art. 129, I — Encaminhamento ao CRAS</div>
          </div>
        </div>

        <!-- Encaminhamentos -->
        <div class="mm-branch mm-branch-green">
          <div class="mm-branch-head">
            <span class="mm-bh-icon">📍</span>
            <span class="mm-bh-title">Encaminhamentos</span>
          </div>
          <div class="mm-leaves">
            <div class="mm-leaf mm-leaf-urgent">🔴 CREAS — Urgente · PAEFI</div>
            <div class="mm-leaf mm-leaf-high">🟡 CRAS — Alta · PAIF</div>
            <div class="mm-leaf">🔵 UBS Centro — Média</div>
          </div>
        </div>

        <!-- Medidas de Proteção -->
        <div class="mm-branch mm-branch-orange">
          <div class="mm-branch-head">
            <span class="mm-bh-icon">🛡️</span>
            <span class="mm-bh-title">Medidas de Proteção</span>
          </div>
          <div class="mm-leaves">
            <div class="mm-leaf">Art. 101, I — Orientação familiar</div>
            <div class="mm-leaf">Art. 101, IV — Programa PAIF</div>
            <div class="mm-leaf">Art. 101, VI — Abrigo temporário</div>
          </div>
        </div>

        <!-- Documentos Gerados -->
        <div class="mm-branch mm-branch-purple">
          <div class="mm-branch-head">
            <span class="mm-bh-icon">📄</span>
            <span class="mm-bh-title">Documentos Prontos</span>
          </div>
          <div class="mm-leaves">
            <div class="mm-leaf">Relatório de Atendimento</div>
            <div class="mm-leaf">Ofício ao CREAS</div>
            <div class="mm-leaf">Notificação ao MP</div>
          </div>
        </div>

      </div><!-- /mm-branches -->

    </div><!-- /bruce-mindmap -->

    <!-- Rodapé do card -->
    <div class="bruce-mindmap-footer">
      <div class="bmf-stat">
        <span class="bmf-icon">⚡</span>
        <span>Análise em <strong>~4 segundos</strong></span>
      </div>
      <div class="bmf-stat">
        <span class="bmf-icon">📋</span>
        <span><strong>3 documentos</strong> gerados automaticamente</span>
      </div>
      <div class="bmf-stat">
        <span class="bmf-icon">⚖️</span>
        <span>Fundamentado em <strong>ECA + SUAS + Lei Henry Borel</strong></span>
      </div>
    </div>

  </div><!-- /bruce-mindmap-wrap -->

  <!-- Features Bruce AI -->
  <div class="bruce-features reveal">
    <div class="bf-item">
      <div class="bf-icon" style="background:rgba(59,130,246,0.1);">🧠</div>
      <div class="bf-title">Análise de Leis Automática</div>
      <div class="bf-desc">Cruza o relato com ECA, SUAS e Lei Henry Borel em segundos. Cita artigos relevantes e identifica violações de direitos.</div>
    </div>
    <div class="bf-item">
      <div class="bf-icon" style="background:rgba(34,197,94,0.1);">🗺️</div>
      <div class="bf-title">Mapa Mental Estruturado</div>
      <div class="bf-desc">Gera um diagrama visual do caso com todos os encaminhamentos, medidas e documentos organizados por prioridade.</div>
    </div>
    <div class="bf-item">
      <div class="bf-icon" style="background:rgba(245,158,11,0.1);">📄</div>
      <div class="bf-title">Minutas Prontas para Assinar</div>
      <div class="bf-desc">Relatório de atendimento e ofícios de encaminhamento gerados automaticamente, prontos para assinatura digital.</div>
    </div>
  </div>

</section>

<!-- ═══ TRUST BAR ═══ -->
<div class="trust-bar">
  <span class="trust-label">Fundamentado em:</span>
  <div class="trust-items">
    <div class="trust-item"><div class="trust-icon ti-green">📜</div> ECA — Lei 8.069/90</div>
    <div class="trust-item"><div class="trust-icon ti-blue">🏛️</div> SUAS — LOAS 8.742/93</div>
    <div class="trust-item"><div class="trust-icon ti-red">🔒</div> LGPD — Lei 13.709/18</div>
    <div class="trust-item"><div class="trust-icon ti-yellow">✍️</div> Lei 14.063/20</div>
    <div class="trust-item"><div class="trust-icon ti-purple" style="font-size:11px;font-weight:900;letter-spacing:-.3px;">AI</div> Bruce AI</div>
  </div>
</div>

<!-- ═══ RECURSOS ═══ -->
<section id="recursos" class="feat-section">
  <div class="reveal">
    <div class="s-label">Recursos</div>
    <h2 class="s-title">Tudo que o Conselheiro precisa,<br>em um só lugar.</h2>
    <p class="s-sub">Do registro ao documento assinado — com inteligência artificial fundamentada em lei.</p>
  </div>

  <div class="feat-grid reveal">
    <div class="feat-card">
      <div class="feat-icon-wrap" style="font-size:13px;font-weight:900;color:#1d4ed8;letter-spacing:-.5px;">IA</div>
      <h3>Análise de legislação com Bruce AI</h3>
      <p>Bruce AI cruza o relato da visita com o ECA e a Tipificação Nacional SUAS, indicando artigos específicos, encaminhamentos e medidas fundamentadas em lei.</p>
      <span class="feat-tag">Bruce AI — Motor Interno</span>
    </div>
    <div class="feat-card">
      <div class="feat-icon-wrap" style="font-size:13px;font-weight:900;color:#1d4ed8;letter-spacing:-.5px;">ENC</div>
      <h3>Fluxo de Encaminhamento</h3>
      <p>Fluxo completo com órgãos prioritários, urgência, artigo do ECA aplicável e o que solicitar em cada serviço da rede SUAS.</p>
      <span class="feat-tag">ECA Art. 101 · SUAS</span>
    </div>
    <div class="feat-card">
      <div class="feat-icon-wrap" style="font-size:13px;font-weight:900;color:#1d4ed8;letter-spacing:-.5px;">MAP</div>
      <h3>Mapa Mental Interativo</h3>
      <p>Visualização do caso em mapa mental gerado automaticamente com Mermaid.js. Facilita o entendimento do fluxo e o planejamento das intervenções.</p>
      <span class="feat-tag">Mermaid.js · Auto-gerado</span>
    </div>
    <div class="feat-card">
      <div class="feat-icon-wrap" style="font-size:13px;font-weight:900;color:#1d4ed8;letter-spacing:-.5px;">ASS</div>
      <h3>Assinatura Digital</h3>
      <p>Coleta de assinatura na tela via touch ou mouse. Mobile-friendly. Documentos gerados com assinatura embutida e validade jurídica pela Lei 14.063/20.</p>
      <span class="feat-tag">Mobile · Lei 14.063/20</span>
    </div>
    <div class="feat-card">
      <div class="feat-icon-wrap" style="font-size:13px;font-weight:900;color:#1d4ed8;letter-spacing:-.5px;">DOC</div>
      <h3>Automação Documental</h3>
      <p>Minutas automáticas de relatórios, ofícios de encaminhamento e termos de medidas de proteção. Templates baseados nos modelos do CONANDA.</p>
      <span class="feat-tag">Relatórios · Ofícios</span>
    </div>
    <div class="feat-card">
      <div class="feat-icon-wrap" style="font-size:11px;font-weight:900;color:#1d4ed8;letter-spacing:-.3px;">LGPD</div>
      <h3>Segurança & LGPD Total</h3>
      <p>Dados de crianças criptografados com AES-128-CBC. Expurgo automático de documentos após 3 dias. Log de auditoria. Multi-tenancy seguro.</p>
      <span class="feat-tag">LGPD · AES · Audit Log</span>
    </div>
  </div>
</section>

<!-- ═══ MOCKUP ═══ -->
<section class="mockup-section">
  <div style="text-align:center;" class="reveal">
    <div class="s-label" style="justify-content:center;">Interface</div>
    <h2 class="s-title" style="max-width:100%;text-align:center;">Projetado para a rotina<br>do Conselheiro(a) Tutelar.</h2>
  </div>

  <div class="mockup-browser-wrap reveal">
    <div class="mb">
      <div class="mb-bar">
        <div class="mb-dots">
          <span style="background:#ef4444;"></span>
          <span style="background:#f59e0b;"></span>
          <span style="background:#22c55e;"></span>
        </div>
        <div class="mb-url">🔒 localhost/ct-ai1/public/dashboard</div>
      </div>
      <div class="mb-body">
        <div class="mb-sidebar">
          <div class="mb-logo-row">
            <div class="mb-logo-icon">🛡️</div>
            <span class="mb-logo-name">Guardião</span>
          </div>
          <div class="mb-nav on">📊 Dashboard</div>
          <div class="mb-nav">📋 Atendimentos</div>
          <div class="mb-nav">⚖️ Medidas</div>
          <div class="mb-nav">🌐 Rede Municipal</div>
          <div class="mb-nav">➕ Novo Atendimento</div>
          <div class="mb-nav" style="margin-top:20px;">⚙️ Admin</div>
        </div>

        <div class="mb-main">
          <div class="mb-page-title">Dashboard — Visão Geral</div>
          <div class="mb-stats">
            <div class="mb-stat">
              <div class="mb-stat-n" style="color:#ef4444;">2</div>
              <div class="mb-stat-l">⚠️ Casos Urgentes</div>
            </div>
            <div class="mb-stat">
              <div class="mb-stat-n" style="color:var(--blue-mid);">14</div>
              <div class="mb-stat-l">📋 Em Aberto</div>
            </div>
            <div class="mb-stat">
              <div class="mb-stat-n">47</div>
              <div class="mb-stat-l">📁 Total</div>
            </div>
            <div class="mb-stat">
              <div class="mb-stat-n" style="color:var(--green);font-size:16px;">✅</div>
              <div class="mb-stat-l">🔐 LGPD Ativo</div>
            </div>
          </div>

          <div class="mb-card">
            <div class="mb-card-title">📋 Atendimentos Recentes</div>
            <div class="mb-row">
              <span class="mb-proto">CT-2026-001</span>
              <span class="mb-tipo">Violência Física</span>
              <span class="mb-badge" style="background:#fee2e2;color:#ef4444;">Urgente</span>
              <span class="mb-badge" style="background:#ede9fe;color:#7c3aed;margin-left:4px;">🤖 IA</span>
            </div>
            <div class="mb-row">
              <span class="mb-proto">CT-2026-002</span>
              <span class="mb-tipo">Negligência Familiar</span>
              <span class="mb-badge" style="background:#fef3c7;color:#d97706;">Alta</span>
              <span class="mb-badge" style="background:#ede9fe;color:#7c3aed;margin-left:4px;">🤖 IA</span>
            </div>
            <div class="mb-row">
              <span class="mb-proto">CT-2026-003</span>
              <span class="mb-tipo">Evasão Escolar</span>
              <span class="mb-badge" style="background:#dbeafe;color:#1d4ed8;">Média</span>
            </div>
          </div>

          <div class="mb-ai">
            <div class="mb-ai-title">🤖 Análise IA — CT-2026-001 — Art. 101 ECA</div>
            <div class="mb-ai-text">
              CREAS → Inclusão imediata no PAEFI · UBS → Acompanhamento médico · Escola → Acionamento do responsável · Mapa mental gerado · Minuta do ofício disponível.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══ NÚMEROS ═══ -->
<section class="nums-section">
  <div class="s-label reveal" style="justify-content:center;">Impacto</div>
  <h2 class="s-title reveal">Construído para fazer diferença.</h2>
  <div class="nums-grid reveal">
    <div class="num-card">
      <div class="big-n">9</div>
      <div class="big-l">Medidas de Proteção<br>ECA Art. 101</div>
    </div>
    <div class="num-card">
      <div class="big-n">3s</div>
      <div class="big-l">Análise de leis<br>completa com IA</div>
    </div>
    <div class="num-card">
      <div class="big-n">3d</div>
      <div class="big-l">Expurgo automático<br>LGPD de documentos</div>
    </div>
    <div class="num-card">
      <div class="big-n">R$0</div>
      <div class="big-l">Custo para<br>Conselhos Tutelares</div>
    </div>
  </div>

</section>

<!-- ═══ BASE LEGAL ═══ -->
<section id="legal" class="legal-section">
  <div class="reveal">
    <div class="s-label">Base Jurídica</div>
    <h2 class="s-title">Fundamentado na lei.<br>Desenvolvido para o Brasil.</h2>
    <p class="s-sub">Cada sugestão da IA cita o artigo específico. Nada de achismos — só legislação vigente e técnica socioassistencial.</p>
  </div>

  <div class="legal-grid reveal">
    <div class="legal-card">
      <div class="lc-icon-wrap">📜</div>
      <div>
        <h4>ECA — Lei nº 8.069/1990</h4>
        <p>Cada análise referencia artigos específicos do ECA, especialmente Art. 98 (situações de ameaça) e Art. 101 (medidas de proteção aplicáveis ao caso).</p>
        <span class="lc-badge">✅ Integrado à IA</span>
      </div>
    </div>
    <div class="legal-card">
      <div class="lc-icon-wrap">🏛️</div>
      <div>
        <h4>SUAS — Tipificação (Res. CNAS 109/2009)</h4>
        <p>Encaminhamentos seguem a Tipificação Nacional de Serviços Socioassistenciais. A IA identifica PAIF, PAEFI, SCFV e outros conforme a demanda do caso.</p>
        <span class="lc-badge">✅ Integrado à IA</span>
      </div>
    </div>
    <div class="legal-card">
      <div class="lc-icon-wrap">🔒</div>
      <div>
        <h4>LGPD — Lei nº 13.709/2018</h4>
        <p>Dados pessoais de crianças criptografados com AES-128-CBC. Retenção rígida: documentos expiram em 3 dias. Log de auditoria completo para conformidade.</p>
        <span class="lc-badge">✅ Conformidade total</span>
      </div>
    </div>
    <div class="legal-card">
      <div class="lc-icon-wrap">✍️</div>
      <div>
        <h4>Assinatura Eletrônica — Lei nº 14.063/2020</h4>
        <p>Documentos assinados digitalmente com captura em tela. Validade jurídica conforme a Lei de Governo Digital. Compatível com todos os dispositivos.</p>
        <span class="lc-badge">✅ Validade jurídica</span>
      </div>
    </div>
  </div>
</section>

<!-- ═══ DEPOIMENTO ═══ -->
<section class="quote-section">
  <div class="reveal">
    <span class="quote-mark">"</span>
    <p class="quote-text">
      Antes eu levava horas para redigir um ofício e identificar o serviço certo.
      Com o VivensiCT, em menos de 3 minutos o sistema já me diz o artigo do ECA,
      o encaminhamento correto e gera a minuta. É como ter um especialista jurídico 24 horas.
    </p>
    <div class="quote-author">
      <div class="q-avatar">M</div>
      <div>
        <div class="q-name">Maria Silva</div>
        <div class="q-role">Conselheira Tutelar · São Paulo/SP · CT-001/2024</div>
      </div>
    </div>
  </div>
</section>

<!-- ═══ CTA ═══ -->
<section class="cta-section" id="contato">
  <h2 class="cta-title reveal">Proteja quem<br>mais precisa.</h2>
  <p class="cta-sub reveal">Comece agora, é gratuito. Sem burocracia, sem contrato.</p>
  <div class="cta-btns reveal">
    <a href="login" class="btn-cta-white">🛡️ Acessar o Sistema Agora</a>
    <button onclick="openPix()" class="btn-cta-ghost">💙 Apoiar com Doação PIX</button>
  </div>
  <p class="cta-note reveal">Sistema 100% gratuito · Dados protegidos pela LGPD · Sem dados de cartão</p>
</section>

<!-- ═══ FOOTER ═══ -->
<footer>
  <div class="footer-grid">
    <div class="f-brand">
      <a href="#" class="fb-logo">
        <img src="images/logo.png" alt="VivensiCT" style="height:36px;width:auto;object-fit:contain;flex-shrink:0;">
        <span class="fb-logo-name">VivensiCT</span>
      </a>
      <p>Sistema inteligente para Conselhos Tutelares. Proteção à criança e ao adolescente com tecnologia, lei e propósito social.</p>
    </div>
    <div class="f-col">
      <h5>Sistema</h5>
      <ul>
        <li><a href="dashboard">Dashboard</a></li>
        <li><a href="atendimentos">Atendimentos</a></li>
        <li><a href="medidas">Medidas de Proteção</a></li>
        <li><a href="rede-servicos">Rede de Serviços</a></li>
        <li><a href="login">Entrar</a></li>
      </ul>
    </div>
    <div class="f-col">
      <h5>Base Legal</h5>
      <ul>
        <li><a href="#">ECA — Lei 8.069/90</a></li>
        <li><a href="#">SUAS — LOAS 8.742/93</a></li>
        <li><a href="#">LGPD — Lei 13.709/18</a></li>
        <li><a href="#">Lei 14.063/20</a></li>
        <li><a href="#">Res. CNAS 109/2009</a></li>
      </ul>
    </div>
    <div class="f-col">
      <h5>Apoio</h5>
      <ul>
        <li><a href="#" onclick="openPix();return false;">💙 Doação PIX</a></li>
        <li><a href="<?= url('/privacidade') ?>">🔒 Política de Privacidade</a></li>
        <li><a href="<?= url('/termos-de-uso') ?>">📋 Termos de Uso</a></li>
        <li><a href="<?= url('/cadastro') ?>">Registrar Conselho</a></li>
      </ul>
    </div>
  </div>

  <!-- ── Faixa Legal em Destaque ───────────────────────────────────────── -->
  <div style="
    border-top: 1px solid rgba(255,255,255,0.07);
    border-bottom: 1px solid rgba(255,255,255,0.07);
    background: rgba(16,185,129,0.06);
    padding: 22px 0;
    margin-bottom: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
  ">
    <span style="font-size:13px;color:rgba(255,255,255,0.4);letter-spacing:0.3px;">🔐 Transparência &amp; LGPD:</span>

    <a href="<?= url('/privacidade') ?>" style="
      display: inline-flex; align-items: center; gap: 7px;
      background: rgba(16,185,129,0.12);
      border: 1px solid rgba(16,185,129,0.3);
      color: #6ee7b7;
      text-decoration: none;
      font-size: 13px; font-weight: 700;
      padding: 8px 18px;
      border-radius: 100px;
      transition: all .2s;
    "
    onmouseover="this.style.background='rgba(16,185,129,0.22)';this.style.borderColor='rgba(16,185,129,0.5)'"
    onmouseout="this.style.background='rgba(16,185,129,0.12)';this.style.borderColor='rgba(16,185,129,0.3)'">
      🔒 Política de Privacidade
    </a>

    <a href="<?= url('/termos-de-uso') ?>" style="
      display: inline-flex; align-items: center; gap: 7px;
      background: rgba(96,165,250,0.10);
      border: 1px solid rgba(96,165,250,0.25);
      color: var(--blue-pale);
      text-decoration: none;
      font-size: 13px; font-weight: 700;
      padding: 8px 18px;
      border-radius: 100px;
      transition: all .2s;
    "
    onmouseover="this.style.background='rgba(96,165,250,0.2)';this.style.borderColor='rgba(96,165,250,0.45)'"
    onmouseout="this.style.background='rgba(96,165,250,0.10)';this.style.borderColor='rgba(96,165,250,0.25)'">
      📋 Termos de Uso
    </a>
  </div>

  <div class="footer-bottom">
    <p>© <?= date('Y') ?> VivensiCT · Projeto Social · Proteção à Criança e ao Adolescente · 🇧🇷</p>
    <div class="f-badges">
      <span class="f-badge">ECA ✓</span>
      <span class="f-badge">SUAS ✓</span>
      <span class="f-badge">LGPD ✓</span>
      <span class="f-badge">IA ✓</span>
    </div>
  </div>
</footer>

<!-- ═══ PIX MODAL ═══ -->
<div class="pix-overlay" id="pixOverlay">
  <div class="pix-modal">
    <div class="pix-heart">💙</div>
    <h3>Apoie o VivensiCT</h3>
    <p class="pm-sub">Este sistema é gratuito para Conselheiros Tutelares de todo o Brasil. Sua colaboração mantém o projeto vivo e em evolução constante.</p>

    <div class="pix-box">
      <div class="pix-lbl">Chave PIX</div>
      <div class="pix-key">guardiao@digital.org.br</div>
    </div>

    <button class="btn-copy" onclick="copyPix()">📋 Copiar Chave PIX</button>
    <button class="btn-close-modal" onclick="closePix()">Fechar</button>

    <p style="font-size:12px;color:var(--gray-400);margin-top:16px;">Qualquer valor faz diferença. Obrigado! 🙏</p>
  </div>
</div>

<script>
// Navbar scroll
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
  navbar.classList.toggle('scrolled', window.scrollY > 40);
});

// Scroll reveal
const obs = new IntersectionObserver(entries => {
  entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
}, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
document.querySelectorAll('.reveal').forEach(el => obs.observe(el));

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const t = document.querySelector(a.getAttribute('href'));
    if (t) { e.preventDefault(); t.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
  });
});

// PIX Modal
function openPix()  { document.getElementById('pixOverlay').classList.add('open'); document.body.style.overflow='hidden'; }
function closePix() { document.getElementById('pixOverlay').classList.remove('open'); document.body.style.overflow=''; }

function copyPix() {
  navigator.clipboard.writeText('guardiao@digital.org.br').then(() => {
    const btn = document.querySelector('.btn-copy');
    const o = btn.innerHTML;
    btn.innerHTML = '✅ Chave copiada!';
    btn.style.background = '#10b981';
    setTimeout(() => { btn.innerHTML = o; btn.style.background = ''; }, 2500);
  });
}

document.getElementById('pixOverlay').addEventListener('click', e => { if (e.target.id === 'pixOverlay') closePix(); });
document.addEventListener('keydown', e => { if (e.key === 'Escape') closePix(); });
</script>

</body>
</html>
