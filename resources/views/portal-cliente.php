<?php
require_once __DIR__ . '/includes/bootstrap.php';
// Portal del cliente registrado. Auth de CLIENTES (no empleados).
// TODO BACKEND: validar $_SESSION['cliente'] y redirigir a index.php si no existe.
$SIGRA_CLIENTE = $_SESSION['cliente'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>La Antigua — Restaurante Caribeño</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    :root {
      --navy:  #1A2E4A;
      --amber: #E8A020;
      --cream: #FAF7F2;
      --dark:  #0D1B2A;
      --text:  #2D3748;
      --muted: #6B7280;
      --border: #E5E7EB;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    html { scroll-behavior: smooth; }

    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: var(--cream);
      color: var(--text);
      line-height: 1.6;
    }

    /* ──────────────────────────────
       NAVBAR
    ────────────────────────────── */
    .navbar {
      position: fixed; top: 0; left: 0; right: 0; z-index: 100;
      padding: 0 5%;
      height: 68px;
      display: flex; align-items: center; justify-content: space-between;
      background: transparent;
      transition: background .35s, box-shadow .35s;
    }
    .navbar.scrolled {
      background: rgba(13,27,42,.94);
      backdrop-filter: blur(14px);
      box-shadow: 0 2px 20px rgba(0,0,0,.3);
    }

    .nav-brand {
      display: flex; align-items: center; gap: 10px;
      text-decoration: none;
    }
    .nav-brand-icon {
      width: 38px; height: 38px; border-radius: 10px;
      background: var(--amber);
      display: flex; align-items: center; justify-content: center;
    }
    .nav-brand-icon i { color: #fff; font-size: 19px; }
    .nav-brand-text { color: #fff; font-size: 18px; font-weight: 800; letter-spacing: -.3px; }

    .nav-links {
      display: flex; align-items: center; gap: 6px;
      list-style: none;
    }
    .nav-links a {
      color: rgba(255,255,255,.85);
      text-decoration: none; font-size: 13.5px; font-weight: 500;
      padding: 7px 14px; border-radius: 8px;
      transition: color .15s, background .15s;
    }
    .nav-links a:hover { color: #fff; background: rgba(255,255,255,.1); }

    .nav-right { display: flex; align-items: center; gap: 10px; }

    .nav-user {
      display: flex; align-items: center; gap: 9px;
      background: rgba(255,255,255,.12); border-radius: 20px;
      padding: 5px 14px 5px 6px;
    }
    .nav-avatar {
      width: 30px; height: 30px; border-radius: 50%;
      background: var(--amber);
      display: flex; align-items: center; justify-content: center;
      font-size: 12px; font-weight: 700; color: #fff;
    }
    .nav-user-name { color: rgba(255,255,255,.9); font-size: 13px; font-weight: 500; }

    .btn-nav-reservar {
      background: var(--amber); color: #fff;
      border: none; border-radius: 10px;
      padding: 9px 18px; font-size: 13px; font-weight: 700;
      cursor: pointer; text-decoration: none;
      transition: filter .15s, transform .1s;
      display: flex; align-items: center; gap: 6px;
    }
    .btn-nav-reservar:hover { filter: brightness(1.1); transform: translateY(-1px); }

    .nav-hamburger {
      display: none; background: none; border: none;
      color: #fff; font-size: 24px; cursor: pointer;
    }

    /* ──────────────────────────────
       HERO
    ────────────────────────────── */
    .hero {
      position: relative; min-height: 100vh;
      display: flex; align-items: center; justify-content: center;
      overflow: hidden;
    }
    .hero-bg {
      position: absolute; inset: 0;
      background-image: url('https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=1600&fit=crop&auto=format&q=80');
      background-size: cover; background-position: center;
    }
    .hero-bg::after {
      content: '';
      position: absolute; inset: 0;
      background: linear-gradient(to bottom, rgba(13,27,42,.6) 0%, rgba(13,27,42,.4) 50%, rgba(13,27,42,.75) 100%);
    }

    .hero-content {
      position: relative; z-index: 1;
      text-align: center; padding: 0 20px;
      max-width: 680px;
    }
    .hero-badge {
      display: inline-flex; align-items: center; gap: 8px;
      background: rgba(232,160,32,.2); border: 1px solid rgba(232,160,32,.5);
      color: #FFC947; font-size: 12.5px; font-weight: 600; letter-spacing: .5px;
      text-transform: uppercase; padding: 6px 16px; border-radius: 999px;
      margin-bottom: 24px;
    }
    .hero-title {
      color: #fff; font-size: clamp(38px, 7vw, 72px);
      font-weight: 900; line-height: 1.1;
      letter-spacing: -1.5px; margin-bottom: 20px;
    }
    .hero-title span { color: var(--amber); }
    .hero-sub {
      color: rgba(255,255,255,.82); font-size: clamp(15px, 2.5vw, 19px);
      margin-bottom: 36px; line-height: 1.55;
    }
    .hero-actions { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
    .btn-hero-primary {
      background: var(--amber); color: #1A2E4A;
      border: none; border-radius: 14px;
      padding: 15px 32px; font-size: 15px; font-weight: 800;
      cursor: pointer; text-decoration: none;
      box-shadow: 0 8px 28px rgba(232,160,32,.45);
      transition: transform .15s, box-shadow .15s;
      display: flex; align-items: center; gap: 8px;
    }
    .btn-hero-primary:hover { transform: translateY(-2px); box-shadow: 0 12px 36px rgba(232,160,32,.55); }
    .btn-hero-outline {
      background: rgba(255,255,255,.12); color: #fff;
      border: 2px solid rgba(255,255,255,.45); border-radius: 14px;
      padding: 13px 30px; font-size: 15px; font-weight: 700;
      cursor: pointer; text-decoration: none;
      backdrop-filter: blur(8px);
      transition: background .15s, border-color .15s;
      display: flex; align-items: center; gap: 8px;
    }
    .btn-hero-outline:hover { background: rgba(255,255,255,.22); border-color: rgba(255,255,255,.7); }

    .hero-scroll {
      position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%);
      color: rgba(255,255,255,.5); font-size: 22px;
      animation: bounce 2s infinite;
    }
    @keyframes bounce {
      0%, 100% { transform: translateX(-50%) translateY(0); }
      50%       { transform: translateX(-50%) translateY(6px); }
    }

    /* ──────────────────────────────
       STATS BAR
    ────────────────────────────── */
    .stats-bar {
      background: var(--navy);
      padding: 28px 5%;
      display: grid; grid-template-columns: repeat(4, 1fr);
      gap: 24px; text-align: center;
    }
    .stat-item {}
    .stat-num {
      color: var(--amber); font-size: 28px; font-weight: 900;
      letter-spacing: -.5px; display: block;
    }
    .stat-label { color: rgba(255,255,255,.6); font-size: 12.5px; margin-top: 2px; }

    /* ──────────────────────────────
       SECCIONES GENERALES
    ────────────────────────────── */
    .section { padding: 80px 5%; }
    .section-header { text-align: center; margin-bottom: 56px; }
    .section-tag {
      display: inline-block;
      background: #FFF8E1; color: #F9A825; border: 1px solid #FFE082;
      font-size: 11px; font-weight: 700; letter-spacing: 1px;
      text-transform: uppercase; padding: 4px 14px; border-radius: 999px;
      margin-bottom: 14px;
    }
    .section-title {
      font-size: clamp(26px, 4vw, 40px); font-weight: 900;
      color: var(--navy); letter-spacing: -.5px; line-height: 1.15;
    }
    .section-sub {
      color: var(--muted); font-size: 15px; margin-top: 12px;
      max-width: 560px; margin-left: auto; margin-right: auto;
    }

    /* ──────────────────────────────
       NOSOTROS
    ────────────────────────────── */
    #nosotros { background: #fff; }

    .nosotros-grid {
      display: grid; grid-template-columns: 1fr 1fr; gap: 64px;
      align-items: center; max-width: 1100px; margin: 0 auto 64px;
    }
    .nosotros-img {
      border-radius: 22px; overflow: hidden;
      box-shadow: 0 20px 56px rgba(0,0,0,.18);
      aspect-ratio: 4/3;
    }
    .nosotros-img img { width: 100%; height: 100%; object-fit: cover; }
    .nosotros-text h3 {
      font-size: 28px; font-weight: 800; color: var(--navy);
      margin-bottom: 16px; line-height: 1.2;
    }
    .nosotros-text p {
      color: var(--muted); font-size: 15px; line-height: 1.75;
      margin-bottom: 14px;
    }
    .nosotros-text .highlight {
      color: var(--amber); font-weight: 700;
    }

    .vm-grid {
      display: grid; grid-template-columns: 1fr 1fr; gap: 24px;
      max-width: 1100px; margin: 0 auto;
    }
    .vm-card {
      background: var(--cream); border-radius: 20px;
      padding: 32px; border-left: 4px solid var(--amber);
    }
    .vm-card.mision { border-left-color: var(--navy); }
    .vm-icon {
      width: 52px; height: 52px; border-radius: 14px;
      background: #FFF8E1;
      display: flex; align-items: center; justify-content: center;
      font-size: 24px; color: var(--amber);
      margin-bottom: 18px;
    }
    .vm-card.mision .vm-icon { background: #EEF2FF; color: var(--navy); }
    .vm-card h4 {
      font-size: 18px; font-weight: 700; color: var(--navy);
      margin-bottom: 12px;
    }
    .vm-card p { color: var(--muted); font-size: 14px; line-height: 1.75; }

    /* ──────────────────────────────
       GALERÍA
    ────────────────────────────── */
    #galeria { background: var(--cream); }

    .gallery-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 16px;
      max-width: 1100px; margin: 0 auto;
    }
    .gallery-item {
      border-radius: 16px; overflow: hidden;
      position: relative; cursor: pointer;
      aspect-ratio: 4/3;
      box-shadow: 0 4px 16px rgba(0,0,0,.12);
      transition: transform .3s, box-shadow .3s;
    }
    .gallery-item:hover { transform: scale(1.03); box-shadow: 0 12px 32px rgba(0,0,0,.2); }
    .gallery-item.tall { aspect-ratio: 3/4; }
    .gallery-item img {
      width: 100%; height: 100%; object-fit: cover;
      transition: transform .4s;
    }
    .gallery-item:hover img { transform: scale(1.06); }
    .gallery-overlay {
      position: absolute; inset: 0;
      background: linear-gradient(to top, rgba(13,27,42,.7) 0%, transparent 60%);
      opacity: 0; transition: opacity .3s;
      display: flex; align-items: flex-end; padding: 16px;
    }
    .gallery-item:hover .gallery-overlay { opacity: 1; }
    .gallery-caption {
      color: #fff; font-size: 13px; font-weight: 600;
    }

    /* ──────────────────────────────
       RESEÑAS
    ────────────────────────────── */
    #resenas { background: var(--navy); }
    #resenas .section-tag { background: rgba(232,160,32,.15); color: var(--amber); border-color: rgba(232,160,32,.3); }
    #resenas .section-title { color: #fff; }
    #resenas .section-sub { color: rgba(255,255,255,.6); }

    .reviews-grid {
      display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;
      max-width: 1100px; margin: 0 auto;
    }
    .review-card {
      background: rgba(255,255,255,.06);
      border: 1px solid rgba(255,255,255,.1);
      border-radius: 20px; padding: 28px;
      transition: background .2s, transform .2s;
    }
    .review-card:hover { background: rgba(255,255,255,.1); transform: translateY(-3px); }

    .review-stars { color: var(--amber); font-size: 16px; letter-spacing: 2px; margin-bottom: 14px; }
    .review-text {
      color: rgba(255,255,255,.82); font-size: 14px; line-height: 1.75;
      margin-bottom: 20px; font-style: italic;
    }
    .review-author { display: flex; align-items: center; gap: 12px; }
    .review-avatar {
      width: 40px; height: 40px; border-radius: 50%;
      background: rgba(232,160,32,.25);
      display: flex; align-items: center; justify-content: center;
      font-size: 15px; font-weight: 700; color: var(--amber);
      flex-shrink: 0;
    }
    .review-name { color: #fff; font-size: 13.5px; font-weight: 600; }
    .review-date { color: rgba(255,255,255,.4); font-size: 11.5px; }

    /* Extra review — highlight */
    .review-card.featured {
      background: rgba(232,160,32,.12);
      border-color: rgba(232,160,32,.3);
    }

    /* ──────────────────────────────
       CTA RESERVAR
    ────────────────────────────── */
    .cta-section {
      background: linear-gradient(135deg, var(--amber) 0%, #c98418 100%);
      padding: 72px 5%; text-align: center;
    }
    .cta-section h2 {
      font-size: clamp(24px, 4vw, 40px); font-weight: 900;
      color: #1A2E4A; margin-bottom: 14px;
    }
    .cta-section p { color: rgba(26,46,74,.75); font-size: 16px; margin-bottom: 32px; }
    .cta-actions { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
    .btn-cta-dark {
      background: var(--navy); color: #fff;
      border: none; border-radius: 14px;
      padding: 15px 34px; font-size: 15px; font-weight: 800;
      cursor: pointer; text-decoration: none;
      box-shadow: 0 8px 28px rgba(13,27,42,.3);
      transition: transform .15s;
      display: flex; align-items: center; gap: 9px;
    }
    .btn-cta-dark:hover { transform: translateY(-2px); }
    .btn-cta-light {
      background: rgba(26,46,74,.12); color: var(--navy);
      border: 2px solid rgba(26,46,74,.3); border-radius: 14px;
      padding: 13px 30px; font-size: 15px; font-weight: 700;
      cursor: pointer; text-decoration: none;
      transition: background .15s;
      display: flex; align-items: center; gap: 9px;
    }
    .btn-cta-light:hover { background: rgba(26,46,74,.2); }

    /* ──────────────────────────────
       FOOTER
    ────────────────────────────── */
    footer {
      background: var(--dark); color: rgba(255,255,255,.6);
      padding: 56px 5% 30px;
    }
    .footer-grid {
      display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 48px;
      max-width: 1100px; margin: 0 auto 48px;
    }
    .footer-brand-icon {
      width: 48px; height: 48px; border-radius: 14px;
      background: var(--amber);
      display: flex; align-items: center; justify-content: center;
      font-size: 24px; color: #fff; margin-bottom: 16px;
    }
    .footer-brand-name { color: #fff; font-size: 22px; font-weight: 800; }
    .footer-brand-desc { font-size: 13px; line-height: 1.7; margin-top: 10px; max-width: 280px; }

    .footer-col h5 { color: #fff; font-size: 13px; font-weight: 700; letter-spacing: .5px; text-transform: uppercase; margin-bottom: 16px; }
    .footer-col ul { list-style: none; }
    .footer-col ul li { margin-bottom: 10px; }
    .footer-col ul li a { color: rgba(255,255,255,.55); text-decoration: none; font-size: 13.5px; transition: color .15s; }
    .footer-col ul li a:hover { color: var(--amber); }

    .footer-contact-item {
      display: flex; align-items: flex-start; gap: 10px;
      font-size: 13px; margin-bottom: 12px; color: rgba(255,255,255,.55);
    }
    .footer-contact-item i { color: var(--amber); font-size: 15px; margin-top: 2px; flex-shrink: 0; }

    .footer-bottom {
      border-top: 1px solid rgba(255,255,255,.08);
      padding-top: 24px; text-align: center;
      font-size: 12px; max-width: 1100px; margin: 0 auto;
    }

    /* ──────────────────────────────
       LIGHTBOX
    ────────────────────────────── */
    .lightbox {
      display: none; position: fixed; inset: 0; z-index: 500;
      background: rgba(0,0,0,.92);
      align-items: center; justify-content: center; padding: 20px;
    }
    .lightbox.open { display: flex; animation: fadeIn .2s ease; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    .lightbox img {
      max-width: 90vw; max-height: 85vh;
      border-radius: 12px; object-fit: contain;
    }
    .lightbox-close {
      position: absolute; top: 20px; right: 24px;
      background: rgba(255,255,255,.15); border: none;
      color: #fff; font-size: 24px; width: 44px; height: 44px;
      border-radius: 50%; cursor: pointer; display: flex;
      align-items: center; justify-content: center;
      transition: background .15s;
    }
    .lightbox-close:hover { background: rgba(255,255,255,.25); }

    /* ──────────────────────────────
       MOBILE NAV
    ────────────────────────────── */
    .mobile-nav {
      display: none; position: fixed; inset: 0; z-index: 90;
      background: rgba(13,27,42,.97);
      flex-direction: column; align-items: center; justify-content: center;
      gap: 10px;
    }
    .mobile-nav.open { display: flex; }
    .mobile-nav a {
      color: rgba(255,255,255,.85); text-decoration: none;
      font-size: 20px; font-weight: 600; padding: 12px 0;
      transition: color .15s;
    }
    .mobile-nav a:hover { color: var(--amber); }
    .mobile-nav-close {
      position: absolute; top: 20px; right: 24px;
      background: none; border: none; color: #fff;
      font-size: 28px; cursor: pointer;
    }

    /* ──────────────────────────────
       RESPONSIVE
    ────────────────────────────── */
    @media (max-width: 768px) {
      .nav-links, .nav-right .nav-user { display: none; }
      .nav-hamburger { display: block; }
      .nosotros-grid { grid-template-columns: 1fr; gap: 36px; }
      .vm-grid { grid-template-columns: 1fr; }
      .gallery-grid { grid-template-columns: repeat(2, 1fr); }
      .reviews-grid { grid-template-columns: 1fr; }
      .stats-bar { grid-template-columns: repeat(2, 1fr); }
      .footer-grid { grid-template-columns: 1fr; gap: 32px; }
    }
    @media (max-width: 480px) {
      .gallery-grid { grid-template-columns: 1fr; }
      .hero-actions { flex-direction: column; align-items: center; }
    }
  </style>
</head>
<body>

  <!-- ══ NAVBAR ══ -->
  <nav class="navbar" id="navbar">
    <a class="nav-brand" href="portal-cliente.php">
      <div class="nav-brand-icon"><i class="bi bi-shop"></i></div>
      <span class="nav-brand-text">La Antigua</span>
    </a>

    <ul class="nav-links">
      <li><a href="#inicio">Inicio</a></li>
      <li><a href="#nosotros">Nosotros</a></li>
      <li><a href="#galeria">Galería</a></li>
      <li><a href="#resenas">Reseñas</a></li>
      <li><a href="menu-cliente.php">Menú</a></li>
    </ul>

    <div class="nav-right">
      <div class="nav-user" id="navUser" style="display:none">
        <div class="nav-avatar" id="navAvatar">?</div>
        <span class="nav-user-name" id="navUserName">Visitante</span>
      </div>
      <a class="btn-nav-reservar" href="menu-cliente.php">
        <i class="bi bi-bag-check"></i> Pedir en línea
      </a>
      <button class="nav-hamburger" onclick="toggleMobileNav()" aria-label="Menú">
        <i class="bi bi-list"></i>
      </button>
    </div>
  </nav>

  <!-- ══ MOBILE NAV ══ -->
  <div class="mobile-nav" id="mobileNav">
    <button class="mobile-nav-close" onclick="toggleMobileNav()"><i class="bi bi-x"></i></button>
    <a href="#inicio"    onclick="toggleMobileNav()">Inicio</a>
    <a href="#nosotros"  onclick="toggleMobileNav()">Nosotros</a>
    <a href="#galeria"   onclick="toggleMobileNav()">Galería</a>
    <a href="#resenas"   onclick="toggleMobileNav()">Reseñas</a>
    <a href="menu-cliente.php" style="color:var(--amber)">Pedir en línea</a>
  </div>

  <!-- ══ HERO ══ -->
  <section class="hero" id="inicio">
    <div class="hero-bg"></div>
    <div class="hero-content">
      <div class="hero-badge">
        <i class="bi bi-geo-alt-fill"></i>
        Puerto Barrios, Izabal · Guatemala
      </div>
      <h1 class="hero-title">
        Sabores del <span>Caribe</span><br>guatemalteco
      </h1>
      <p class="hero-sub">
        Mariscos frescos, recetas tradicionales y el ambiente único<br>
        del Caribe de Guatemala. Bienvenido a La Antigua.
      </p>
      <div class="hero-actions">
        <a class="btn-hero-primary" href="menu-cliente.php">
          <i class="bi bi-book-open"></i> Ver el menú
        </a>
        <a class="btn-hero-outline" href="menu-cliente.php">
          <i class="bi bi-bag-check"></i> Pedir en línea
        </a>
      </div>
    </div>
    <div class="hero-scroll"><i class="bi bi-chevron-double-down"></i></div>
  </section>

  <!-- ══ STATS BAR ══ -->
  <div class="stats-bar">
    <div class="stat-item">
      <span class="stat-num">15+</span>
      <span class="stat-label">Años de tradición</span>
    </div>
    <div class="stat-item">
      <span class="stat-num">60+</span>
      <span class="stat-label">Platillos en el menú</span>
    </div>
    <div class="stat-item">
      <span class="stat-num">500+</span>
      <span class="stat-label">Clientes satisfechos/mes</span>
    </div>
    <div class="stat-item">
      <span class="stat-num">4.9★</span>
      <span class="stat-label">Calificación promedio</span>
    </div>
  </div>

  <!-- ══ NOSOTROS ══ -->
  <section class="section" id="nosotros">
    <div class="section-header">
      <span class="section-tag">Nuestra historia</span>
      <h2 class="section-title">Más de 15 años compartiendo<br>la mesa con Puerto Barrios</h2>
      <p class="section-sub">Desde 2009, La Antigua ha sido el lugar favorito de familias y visitantes para disfrutar los sabores auténticos del Caribe guatemalteco.</p>
    </div>

    <div class="nosotros-grid">
      <div class="nosotros-img">
        <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=700&fit=crop&auto=format&q=80"
             alt="Interior del Restaurante La Antigua" loading="lazy">
      </div>
      <div class="nosotros-text">
        <h3>Un rincón caribeño<br>lleno de sabor y tradición</h3>
        <p>Ubicados en el corazón de <span class="highlight">Puerto Barrios, Izabal</span>, nuestro restaurante nació del amor por la cocina caribeña guatemalteca. Cada platillo que preparamos cuenta la historia de nuestra tierra: mariscos frescos del Atlántico, especias tropicales y la calidez de nuestra gente.</p>
        <p>Nuestros chefs trabajan con ingredientes frescos y locales, honrando las recetas tradicionales mientras incorporan técnicas modernas para crear experiencias gastronómicas únicas e inolvidables.</p>
        <p>Te invitamos a vivir la experiencia <span class="highlight">La Antigua</span> — donde cada visita se convierte en un recuerdo que llevará en su paladar.</p>
      </div>
    </div>

    <!-- Visión y Misión -->
    <div class="vm-grid">
      <div class="vm-card">
        <div class="vm-icon"><i class="bi bi-eye"></i></div>
        <h4>Nuestra Visión</h4>
        <p>Ser el restaurante de referencia en Puerto Barrios y el Caribe guatemalteco, ofreciendo experiencias gastronómicas únicas que celebran los sabores de nuestra región con los más altos estándares de calidad, calidez y hospitalidad.</p>
      </div>
      <div class="vm-card mision">
        <div class="vm-icon"><i class="bi bi-bullseye"></i></div>
        <h4>Nuestra Misión</h4>
        <p>Brindar a nuestros clientes una experiencia culinaria auténtica del Caribe guatemalteco, utilizando ingredientes frescos y de origen local, con un servicio cálido y profesional que refleja la hospitalidad de nuestra tierra y hace sentir a cada visitante como en casa.</p>
      </div>
    </div>
  </section>

  <!-- ══ GALERÍA ══ -->
  <section class="section" id="galeria">
    <div class="section-header">
      <span class="section-tag">Galería</span>
      <h2 class="section-title">Conoce nuestro espacio</h2>
      <p class="section-sub">Un ambiente tropical y acogedor, perfectamente diseñado para celebrar momentos especiales con quienes más quieres.</p>
    </div>

    <div class="gallery-grid">
      <div class="gallery-item" onclick="openLightbox(this)">
        <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=600&fit=crop&auto=format&q=80"
             alt="Salón principal" loading="lazy">
        <div class="gallery-overlay">
          <span class="gallery-caption"><i class="bi bi-zoom-in"></i> Salón principal</span>
        </div>
      </div>
      <div class="gallery-item" onclick="openLightbox(this)">
        <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=600&fit=crop&auto=format&q=80"
             alt="Área del bar" loading="lazy">
        <div class="gallery-overlay">
          <span class="gallery-caption"><i class="bi bi-zoom-in"></i> Área del bar</span>
        </div>
      </div>
      <div class="gallery-item" onclick="openLightbox(this)">
        <img src="https://images.unsplash.com/photo-1559329007-40df8a9345d8?w=600&fit=crop&auto=format&q=80"
             alt="Especialidades del Chef" loading="lazy">
        <div class="gallery-overlay">
          <span class="gallery-caption"><i class="bi bi-zoom-in"></i> Especialidades del Chef</span>
        </div>
      </div>
      <div class="gallery-item" onclick="openLightbox(this)">
        <img src="https://images.unsplash.com/photo-1466978913421-dad2ebd01d17?w=600&fit=crop&auto=format&q=80"
             alt="Terraza exterior" loading="lazy">
        <div class="gallery-overlay">
          <span class="gallery-caption"><i class="bi bi-zoom-in"></i> Terraza exterior</span>
        </div>
      </div>
      <div class="gallery-item" onclick="openLightbox(this)">
        <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=600&fit=crop&auto=format&q=80"
             alt="Nuestros platillos" loading="lazy">
        <div class="gallery-overlay">
          <span class="gallery-caption"><i class="bi bi-zoom-in"></i> Presentación de platillos</span>
        </div>
      </div>
      <div class="gallery-item" onclick="openLightbox(this)">
        <img src="https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=600&fit=crop&auto=format&q=80"
             alt="Montaje de mesa especial" loading="lazy">
        <div class="gallery-overlay">
          <span class="gallery-caption"><i class="bi bi-zoom-in"></i> Montaje para eventos</span>
        </div>
      </div>
    </div>
  </section>

  <!-- ══ RESEÑAS ══ -->
  <section class="section" id="resenas">
    <div class="section-header">
      <span class="section-tag">Reseñas</span>
      <h2 class="section-title">Lo que dicen nuestros clientes</h2>
      <p class="section-sub">Más de 500 familias y visitantes nos visitan cada mes. Su satisfacción es nuestra mayor recompensa.</p>
    </div>

    <div class="reviews-grid">
      <div class="review-card featured">
        <div class="review-stars">★★★★★</div>
        <p class="review-text">"El tapado de mariscos es simplemente el mejor que he probado en toda mi vida. El ambiente es increíble, muy cerca del mar. Sin duda el mejor restaurante de Izabal."</p>
        <div class="review-author">
          <div class="review-avatar">MJ</div>
          <div>
            <div class="review-name">María José García</div>
            <div class="review-date">Puerto Barrios · Mayo 2026</div>
          </div>
        </div>
      </div>

      <div class="review-card">
        <div class="review-stars">★★★★★</div>
        <p class="review-text">"Los camarones al coco son una experiencia que no tiene precio. El servicio fue rápido y el personal muy amable. Ya tenemos reservación para el próximo mes."</p>
        <div class="review-author">
          <div class="review-avatar">CR</div>
          <div>
            <div class="review-name">Carlos Rodríguez</div>
            <div class="review-date">Guatemala City · Abril 2026</div>
          </div>
        </div>
      </div>

      <div class="review-card">
        <div class="review-stars">★★★★★</div>
        <p class="review-text">"Vine con mi familia para festejar y superó todas nuestras expectativas. El ambiente tropical, la comida fresca... ¡absolutamente mágico! Volveremos siempre."</p>
        <div class="review-author">
          <div class="review-avatar">AL</div>
          <div>
            <div class="review-name">Ana Lucía Méndez</div>
            <div class="review-date">Cobán, A.V. · Marzo 2026</div>
          </div>
        </div>
      </div>

      <div class="review-card">
        <div class="review-stars">★★★★☆</div>
        <p class="review-text">"La langosta al vapor estaba perfectamente preparada. El ceviche de camarón de entrada fue excepcional. Definitivamente el mejor restaurante de mariscos del Caribe guatemalteco."</p>
        <div class="review-author">
          <div class="review-avatar">PM</div>
          <div>
            <div class="review-name">Pedro Morales</div>
            <div class="review-date">Santo Tomás de Castilla · Feb. 2026</div>
          </div>
        </div>
      </div>

      <div class="review-card">
        <div class="review-stars">★★★★★</div>
        <p class="review-text">"Excelente relación calidad-precio. Pedimos la paella caribeña para compartir y fue espectacular. Los frescos naturales de tamarindo son únicos. ¡100% recomendado!"</p>
        <div class="review-author">
          <div class="review-avatar">RS</div>
          <div>
            <div class="review-name">Roberto Salazar</div>
            <div class="review-date">Morales, Izabal · Enero 2026</div>
          </div>
        </div>
      </div>

      <div class="review-card">
        <div class="review-stars">★★★★★</div>
        <p class="review-text">"Celebramos nuestro aniversario aquí y fue perfecto. El equipo nos sorprendió con una presentación especial. La comida, el trato y el ambiente… simplemente inolvidable."</p>
        <div class="review-author">
          <div class="review-avatar">DL</div>
          <div>
            <div class="review-name">Diana & Luis Flores</div>
            <div class="review-date">Puerto Barrios · Diciembre 2025</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ══ CTA ══ -->
  <section class="cta-section">
    <h2>¿Listo para pedir?</h2>
    <p>Haz tu pedido en línea y nuestro equipo lo prepara para llevar o disfrutar en el restaurante.</p>
    <div class="cta-actions">
      <a class="btn-cta-dark" href="menu-cliente.php">
        <i class="bi bi-bag-check"></i> Pedir en línea
      </a>
      <a class="btn-cta-light" href="menu-cliente.php">
        <i class="bi bi-book-open"></i> Ver el menú completo
      </a>
    </div>
  </section>

  <!-- ══ FOOTER ══ -->
  <footer>
    <div class="footer-grid">
      <div>
        <div class="footer-brand-icon"><i class="bi bi-shop"></i></div>
        <div class="footer-brand-name">La Antigua</div>
        <p class="footer-brand-desc">Restaurante Caribeño en el corazón de Puerto Barrios, Izabal. Sabores auténticos, ambiente único y la hospitalidad guatemalteca en cada visita.</p>
      </div>
      <div class="footer-col">
        <h5>Navegación</h5>
        <ul>
          <li><a href="#inicio">Inicio</a></li>
          <li><a href="#nosotros">Nosotros</a></li>
          <li><a href="#galeria">Galería</a></li>
          <li><a href="#resenas">Reseñas</a></li>
          <li><a href="menu-cliente.php">Menú</a></li>
          <li><a href="menu-cliente.php">Pedir en línea</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h5>Contacto</h5>
        <div class="footer-contact-item">
          <i class="bi bi-geo-alt-fill"></i>
          <span>3a Calle y 7a Avenida,<br>Puerto Barrios, Izabal</span>
        </div>
        <div class="footer-contact-item">
          <i class="bi bi-telephone-fill"></i>
          <span>+502 7948-0000</span>
        </div>
        <div class="footer-contact-item">
          <i class="bi bi-envelope-fill"></i>
          <span>info@laantiguagt.com</span>
        </div>
        <div class="footer-contact-item">
          <i class="bi bi-clock-fill"></i>
          <span>Lun–Dom: 11:00 – 22:00</span>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      © 2026 Restaurante La Antigua · Puerto Barrios, Izabal, Guatemala · Todos los derechos reservados
    </div>
  </footer>

  <!-- ══ LIGHTBOX ══ -->
  <div class="lightbox" id="lightbox" onclick="closeLightbox()">
    <button class="lightbox-close" onclick="closeLightbox()"><i class="bi bi-x"></i></button>
    <img id="lightboxImg" src="" alt="">
  </div>

  <script>
    /* ── Navbar scroll ── */
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
      navbar.classList.toggle('scrolled', window.scrollY > 60);
    });

    /* ── Mobile nav ── */
    function toggleMobileNav() {
      document.getElementById('mobileNav').classList.toggle('open');
      document.body.style.overflow =
        document.getElementById('mobileNav').classList.contains('open') ? 'hidden' : '';
    }

    /* ── User from sessionStorage ── */
    (function loadUser() {
      try {
        const raw = sessionStorage.getItem('laantigua_cliente');
        if (!raw) return;
        const u = JSON.parse(raw);
        const userEl = document.getElementById('navUser');
        if (userEl) {
          document.getElementById('navAvatar').textContent  = u.initial || '?';
          document.getElementById('navUserName').textContent = u.nombre  || 'Cliente';
          userEl.style.display = 'flex';
        }
      } catch(e) {}
    })();

    /* ── Gallery lightbox ── */
    function openLightbox(item) {
      const img = item.querySelector('img');
      document.getElementById('lightboxImg').src = img.src;
      document.getElementById('lightbox').classList.add('open');
      document.body.style.overflow = 'hidden';
    }
    function closeLightbox() {
      document.getElementById('lightbox').classList.remove('open');
      document.body.style.overflow = '';
    }
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape') closeLightbox();
    });

    /* ── Smooth appear animation on scroll ── */
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = '1';
          entry.target.style.transform = 'translateY(0)';
        }
      });
    }, { threshold: 0.12 });

    document.querySelectorAll('.vm-card, .gallery-item, .review-card, .stat-item').forEach(el => {
      el.style.opacity = '0';
      el.style.transform = 'translateY(24px)';
      el.style.transition = 'opacity .5s ease, transform .5s ease';
      observer.observe(el);
    });
  </script>
</body>
</html>
