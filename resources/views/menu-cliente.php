<?php
require_once __DIR__ . '/includes/bootstrap.php';
// Menú visible al cliente final. Página pública (cualquiera puede ver el menú).
// El cliente registrado puede hacer pedidos en línea desde aquí; el pedido
// entra a cocina y al admin (ver pedidos-domicilio.php).
$SIGRA_CLIENTE = $_SESSION['cliente'] ?? null;
$resM = api_productos(); $SIGRA_MENU = $resM["data"]["data"] ?? $resM["data"] ?? [];
$SIGRA_MENU = $SIGRA_MENU ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Menú — Restaurante La Antigua</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    :root {
      --navy:   #1A2E4A;
      --amber:  #E8A020;
      --cream:  #FAF7F2;
      --dark:   #0D1B2A;
      --text:   #2D3748;
      --muted:  #6B7280;
      --border: #E5E7EB;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }
    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: var(--cream); color: var(--text); line-height: 1.6;
    }

    /* ── NAVBAR ── */
    .navbar {
      position: sticky; top: 0; z-index: 100;
      background: var(--navy);
      padding: 0 5%; height: 62px;
      display: flex; align-items: center; justify-content: space-between;
      box-shadow: 0 2px 16px rgba(0,0,0,.25);
    }
    .nav-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }
    .nav-brand-icon {
      width: 34px; height: 34px; border-radius: 9px;
      background: var(--amber); display: flex;
      align-items: center; justify-content: center;
    }
    .nav-brand-icon i { color: #fff; font-size: 17px; }
    .nav-brand-text { color: #fff; font-size: 17px; font-weight: 800; }
    .nav-links { display: flex; gap: 4px; }
    .nav-links a {
      color: rgba(255,255,255,.75); text-decoration: none;
      font-size: 13px; font-weight: 500; padding: 7px 13px;
      border-radius: 8px; transition: color .15s, background .15s;
    }
    .nav-links a:hover { color: #fff; background: rgba(255,255,255,.1); }
    .nav-links a.active { color: var(--amber); }
    .nav-right { display: flex; align-items: center; gap: 10px; }
    .nav-user-badge {
      display: flex; align-items: center; gap: 8px;
      background: rgba(255,255,255,.1); border-radius: 18px;
      padding: 4px 12px 4px 5px;
    }
    .nav-avatar {
      width: 28px; height: 28px; border-radius: 50%;
      background: var(--amber); display: flex;
      align-items: center; justify-content: center;
      font-size: 11px; font-weight: 700; color: #fff;
    }
    .nav-user-name { color: rgba(255,255,255,.85); font-size: 12.5px; font-weight: 500; }
    .btn-reservar-nav {
      background: var(--amber); color: #1A2E4A;
      border: none; border-radius: 9px; padding: 8px 16px;
      font-size: 13px; font-weight: 700; cursor: pointer;
      text-decoration: none; display: flex; align-items: center; gap: 6px;
      transition: filter .15s;
    }
    .btn-reservar-nav:hover { filter: brightness(1.1); }

    /* ── PAGE HEADER ── */
    .page-header {
      background: linear-gradient(135deg, var(--navy) 0%, #243d5f 100%);
      padding: 56px 5% 48px; text-align: center;
    }
    .page-header-tag {
      display: inline-flex; align-items: center; gap: 7px;
      background: rgba(232,160,32,.15); border: 1px solid rgba(232,160,32,.35);
      color: var(--amber); font-size: 12px; font-weight: 700;
      letter-spacing: .6px; text-transform: uppercase;
      padding: 5px 16px; border-radius: 999px; margin-bottom: 18px;
    }
    .page-header h1 {
      color: #fff; font-size: clamp(28px, 5vw, 48px);
      font-weight: 900; letter-spacing: -.5px; margin-bottom: 10px;
    }
    .page-header p { color: rgba(255,255,255,.65); font-size: 15px; }

    /* ── CATEGORY TABS ── */
    .tabs-wrap {
      position: sticky; top: 62px; z-index: 90;
      background: #fff; border-bottom: 2px solid var(--border);
      box-shadow: 0 4px 16px rgba(0,0,0,.06);
    }
    .tabs {
      display: flex; gap: 0; overflow-x: auto;
      padding: 0 5%; max-width: 1200px; margin: 0 auto;
      scrollbar-width: none;
    }
    .tabs::-webkit-scrollbar { display: none; }
    .tab-btn {
      flex-shrink: 0; padding: 16px 22px;
      background: none; border: none; border-bottom: 3px solid transparent;
      font-size: 13.5px; font-weight: 600; color: var(--muted);
      cursor: pointer; transition: color .15s, border-color .15s;
      display: flex; align-items: center; gap: 7px;
      white-space: nowrap;
      margin-bottom: -2px;
    }
    .tab-btn:hover { color: var(--navy); }
    .tab-btn.active { color: var(--navy); border-bottom-color: var(--amber); }
    .tab-btn i { font-size: 15px; }
    .tab-badge {
      background: var(--cream); color: var(--muted);
      font-size: 10.5px; font-weight: 700;
      padding: 2px 7px; border-radius: 999px;
    }
    .tab-btn.active .tab-badge { background: #FFF8E1; color: #F9A825; }

    /* ── MAIN CONTENT ── */
    .main { max-width: 1200px; margin: 0 auto; padding: 40px 5% 80px; }

    /* ── CATEGORY SECTION ── */
    .category-section { margin-bottom: 56px; }
    .category-section[data-hidden="true"] { display: none; }

    .category-header {
      display: flex; align-items: center; gap: 14px;
      margin-bottom: 28px; padding-bottom: 16px;
      border-bottom: 2px solid var(--border);
    }
    .cat-icon {
      width: 48px; height: 48px; border-radius: 13px;
      display: flex; align-items: center; justify-content: center;
      font-size: 22px; flex-shrink: 0;
    }
    .category-header h2 {
      font-size: 22px; font-weight: 800; color: var(--navy);
    }
    .category-header .cat-count {
      margin-left: auto; background: var(--cream);
      color: var(--muted); font-size: 12px; font-weight: 600;
      padding: 4px 12px; border-radius: 999px; flex-shrink: 0;
    }

    /* ── MENU GRID ── */
    .menu-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 22px;
    }

    /* ── MENU CARD ── */
    .menu-card {
      background: #fff; border-radius: 18px;
      overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.07);
      transition: transform .25s, box-shadow .25s;
      position: relative;
    }
    .menu-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 32px rgba(0,0,0,.13);
    }
    .card-img {
      position: relative; aspect-ratio: 16/10; overflow: hidden;
    }
    .card-img img {
      width: 100%; height: 100%; object-fit: cover;
      transition: transform .35s;
    }
    .menu-card:hover .card-img img { transform: scale(1.05); }

    .card-badge {
      position: absolute; top: 10px; left: 10px;
      background: rgba(13,27,42,.75); color: #fff;
      font-size: 10.5px; font-weight: 700; letter-spacing: .4px;
      text-transform: uppercase; padding: 3px 10px; border-radius: 999px;
      backdrop-filter: blur(6px);
    }
    .card-badge.popular { background: rgba(232,160,32,.88); color: #1A2E4A; }
    .card-badge.especial { background: rgba(46,125,50,.85); color: #fff; }
    .card-badge.nuevo { background: rgba(25,118,210,.85); color: #fff; }

    .card-body { padding: 18px 18px 16px; }
    .card-name {
      font-size: 15px; font-weight: 700; color: var(--navy);
      margin-bottom: 5px; line-height: 1.3;
    }
    .card-desc {
      font-size: 12.5px; color: var(--muted); line-height: 1.55;
      margin-bottom: 14px;
    }
    .card-footer {
      display: flex; align-items: center; justify-content: space-between;
      border-top: 1px solid var(--border); padding-top: 12px;
    }
    .card-price {
      font-size: 18px; font-weight: 900; color: var(--navy);
    }
    .card-price small { font-size: 11px; color: var(--muted); font-weight: 400; }
    .btn-add {
      width: 34px; height: 34px; border-radius: 50%;
      background: var(--amber); border: none; color: #fff;
      font-size: 18px; cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      transition: transform .15s, filter .15s;
      box-shadow: 0 3px 10px rgba(232,160,32,.35);
    }
    .btn-add:hover { filter: brightness(1.1); transform: scale(1.12); }

    /* Para 2 personas */
    .card-para-dos {
      background: var(--cream); border: none; border-radius: 18px;
      padding: 20px; display: flex; gap: 18px; align-items: center;
      box-shadow: 0 2px 12px rgba(0,0,0,.06);
      transition: transform .2s;
    }
    .card-para-dos:hover { transform: translateY(-3px); }
    .card-para-dos .card-para-img {
      width: 100px; height: 80px; border-radius: 12px; overflow: hidden; flex-shrink: 0;
    }
    .card-para-dos .card-para-img img { width: 100%; height: 100%; object-fit: cover; }
    .card-para-dos .card-name { font-size: 14px; }

    /* ── SPECIAL (featured large) ── */
    .menu-card.featured {
      grid-column: span 2;
    }
    .menu-card.featured .card-img { aspect-ratio: 21/9; }

    /* ── NOTA DE PRECIOS ── */
    .price-note {
      background: #fff; border: 1px solid var(--border);
      border-radius: 12px; padding: 14px 20px;
      display: flex; align-items: center; gap: 12px;
      font-size: 13px; color: var(--muted); margin-bottom: 32px;
    }
    .price-note i { color: var(--amber); font-size: 17px; flex-shrink: 0; }

    /* ── FOOTER ── */
    .page-footer {
      background: var(--dark); color: rgba(255,255,255,.5);
      text-align: center; padding: 28px 20px;
      font-size: 12.5px;
    }
    .page-footer a { color: var(--amber); text-decoration: none; }

    /* ── MOBILE NAV ── */
    @media (max-width: 768px) {
      .nav-links, .nav-user-badge { display: none; }
      .menu-card.featured { grid-column: span 1; }
      .menu-card.featured .card-img { aspect-ratio: 16/10; }
    }
    @media (max-width: 480px) {
      .menu-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

  <!-- ══ NAVBAR ══ -->
  <nav class="navbar">
    <a class="nav-brand" href="portal-cliente.php">
      <div class="nav-brand-icon"><i class="bi bi-shop"></i></div>
      <span class="nav-brand-text">La Antigua</span>
    </a>
    <div class="nav-links">
      <a href="portal-cliente.php">Inicio</a>
      <a href="menu-cliente.php" class="active">Menú</a>
      <a href="portal-cliente.php#galeria">Galería</a>
      <a href="portal-cliente.php#resenas">Reseñas</a>
    </div>
    <div class="nav-right">
      <div class="nav-user-badge" id="navUser" style="display:none">
        <div class="nav-avatar" id="navAvatar">?</div>
        <span class="nav-user-name" id="navUserName">Cliente</span>
      </div>
      <button class="btn-reservar-nav" onclick="toggleCart()" id="navCartBtn">
        <i class="bi bi-bag-check"></i> Mi pedido
        <span id="navCartCount" style="background:#fff;color:var(--navy,#1A2E4A);padding:1px 8px;border-radius:999px;font-size:11px;margin-left:4px;display:none;font-weight:700">0</span>
      </button>
    </div>
  </nav>

  <!-- ══ PAGE HEADER ══ -->
  <div class="page-header">
    <div class="page-header-tag"><i class="bi bi-book-open"></i> Carta del restaurante</div>
    <h1>Nuestro Menú</h1>
    <p>Sabores auténticos del Caribe guatemalteco · Precios en Quetzales (GTQ)</p>
  </div>

  <!-- ══ TABS ══ -->
  <div class="tabs-wrap">
    <div class="tabs" id="tabsContainer">
      <button class="tab-btn active" data-cat="todos" onclick="filterCat('todos', this)">
        <i class="bi bi-grid-3x3-gap"></i> Todos
        <span class="tab-badge" id="badge-todos">28</span>
      </button>
      <button class="tab-btn" data-cat="entradas" onclick="filterCat('entradas', this)">
        <i class="bi bi-egg-fried"></i> Entradas
        <span class="tab-badge" id="badge-entradas">5</span>
      </button>
      <button class="tab-btn" data-cat="fuertes" onclick="filterCat('fuertes', this)">
        <i class="bi bi-fire"></i> Platos Fuertes
        <span class="tab-badge" id="badge-fuertes">7</span>
      </button>
      <button class="tab-btn" data-cat="postres" onclick="filterCat('postres', this)">
        <i class="bi bi-cake2"></i> Postres
        <span class="tab-badge" id="badge-postres">4</span>
      </button>
      <button class="tab-btn" data-cat="bebidas" onclick="filterCat('bebidas', this)">
        <i class="bi bi-cup-straw"></i> Bebidas
        <span class="tab-badge" id="badge-bebidas">8</span>
      </button>
      <button class="tab-btn" data-cat="especiales" onclick="filterCat('especiales', this)">
        <i class="bi bi-star"></i> Especialidades
        <span class="tab-badge" id="badge-especiales">4</span>
      </button>
    </div>
  </div>

  <!-- ══ CONTENIDO DEL MENÚ ══ -->
  <main class="main">

    <div class="price-note">
      <i class="bi bi-info-circle-fill"></i>
      <span>Precios en <strong>Quetzales guatemaltecos (Q)</strong>.
      Agrega platillos a tu pedido y envíalo directamente desde tu cuenta — nuestro equipo lo recibirá al instante.</span>
    </div>

    <!-- ════════ ENTRADAS ════════ -->
    <section class="category-section" data-category="entradas" id="sec-entradas">
      <div class="category-header">
        <div class="cat-icon" style="background:#E8F5E9; color:#2E7D32"><i class="bi bi-egg-fried"></i></div>
        <h2>Entradas</h2>
        <span class="cat-count">5 platillos</span>
      </div>
      <div class="menu-grid">

        <div class="menu-card">
          <div class="card-img">
            <img src="https://images.unsplash.com/photo-1565299585323-38d6b0865b47?w=500&fit=crop&auto=format&q=80"
                 alt="Ceviche de Camarón" loading="lazy">
            <span class="card-badge popular">Más pedido</span>
          </div>
          <div class="card-body">
            <div class="card-name">Ceviche de Camarón</div>
            <div class="card-desc">Camarones frescos marinados en limón con tomate, cebolla morada, cilantro y chile. Servido con tostadas.</div>
            <div class="card-footer">
              <div class="card-price">Q 65 <small>/ porción</small></div>
              <button class="btn-add" title="Agregar"><i class="bi bi-plus"></i></button>
            </div>
          </div>
        </div>

        <div class="menu-card">
          <div class="card-img">
            <img src="https://images.unsplash.com/photo-1547592166-23ac45744acd?w=500&fit=crop&auto=format&q=80"
                 alt="Sopa de Tapado" loading="lazy">
            <span class="card-badge especial">Tradicional</span>
          </div>
          <div class="card-body">
            <div class="card-name">Sopa de Tapado</div>
            <div class="card-desc">Sopa tradicional Garífuna con mariscos, coco y plátano. Receta heredada de las costas del Atlántico guatemalteco.</div>
            <div class="card-footer">
              <div class="card-price">Q 85 <small>/ tazón</small></div>
              <button class="btn-add" title="Agregar"><i class="bi bi-plus"></i></button>
            </div>
          </div>
        </div>

        <div class="menu-card">
          <div class="card-img">
            <img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=500&fit=crop&auto=format&q=80"
                 alt="Ensalada Verde" loading="lazy">
          </div>
          <div class="card-body">
            <div class="card-name">Ensalada Verde con Aderezo de Mango</div>
            <div class="card-desc">Mix de lechugas frescas, pepino, zanahoria, tomate cherry y aderezo casero de mango y chile dulce.</div>
            <div class="card-footer">
              <div class="card-price">Q 45 <small>/ plato</small></div>
              <button class="btn-add" title="Agregar"><i class="bi bi-plus"></i></button>
            </div>
          </div>
        </div>

        <div class="menu-card">
          <div class="card-img">
            <img src="https://images.unsplash.com/photo-1551504734-5ee1c4a1479b?w=500&fit=crop&auto=format&q=80"
                 alt="Tostadas de Chicharrón" loading="lazy">
          </div>
          <div class="card-body">
            <div class="card-name">Tostadas de Chicharrón</div>
            <div class="card-desc">Tostadas crujientes con chicharrón de cerdo, guacamol, curtido de repollo y salsa de tomate casera.</div>
            <div class="card-footer">
              <div class="card-price">Q 35 <small>/ 3 unidades</small></div>
              <button class="btn-add" title="Agregar"><i class="bi bi-plus"></i></button>
            </div>
          </div>
        </div>

        <div class="menu-card">
          <div class="card-img">
            <img src="https://images.unsplash.com/photo-1565557623262-b51c2513a641?w=500&fit=crop&auto=format&q=80"
                 alt="Tortilla con Frijoles Volteados" loading="lazy">
          </div>
          <div class="card-body">
            <div class="card-name">Tortilla con Frijoles Volteados</div>
            <div class="card-desc">Tortillas de maíz hechas a mano con frijoles negros volteados, crema fresca y queso blanco del país.</div>
            <div class="card-footer">
              <div class="card-price">Q 25 <small>/ porción</small></div>
              <button class="btn-add" title="Agregar"><i class="bi bi-plus"></i></button>
            </div>
          </div>
        </div>

      </div>
    </section>

    <!-- ════════ PLATOS FUERTES ════════ -->
    <section class="category-section" data-category="fuertes" id="sec-fuertes">
      <div class="category-header">
        <div class="cat-icon" style="background:#FFF3E0; color:#E65100"><i class="bi bi-fire"></i></div>
        <h2>Platos Fuertes</h2>
        <span class="cat-count">7 platillos</span>
      </div>
      <div class="menu-grid">

        <div class="menu-card featured">
          <div class="card-img">
            <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=900&fit=crop&auto=format&q=80"
                 alt="Tapado Completo" loading="lazy">
            <span class="card-badge popular">Especialidad de la casa</span>
          </div>
          <div class="card-body">
            <div class="card-name">Tapado Completo — Receta Garífuna</div>
            <div class="card-desc">Nuestra joya de la cocina: langosta, camarones, cangrejo y pescado cocidos en leche de coco con plátano maduro, yuca y especias. La receta más auténtica del Caribe guatemalteco.</div>
            <div class="card-footer">
              <div class="card-price">Q 195 <small>/ persona</small></div>
              <button class="btn-add" title="Agregar"><i class="bi bi-plus"></i></button>
            </div>
          </div>
        </div>

        <div class="menu-card">
          <div class="card-img">
            <img src="https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?w=500&fit=crop&auto=format&q=80"
                 alt="Filete de Robalo" loading="lazy">
            <span class="card-badge popular">Favorito</span>
          </div>
          <div class="card-body">
            <div class="card-name">Filete de Robalo al Ajillo</div>
            <div class="card-desc">Filete fresco de robalo salteado en mantequilla con ajo, limón y hierbas finas. Con arroz blanco y ensalada.</div>
            <div class="card-footer">
              <div class="card-price">Q 145 <small>/ plato</small></div>
              <button class="btn-add" title="Agregar"><i class="bi bi-plus"></i></button>
            </div>
          </div>
        </div>

        <div class="menu-card">
          <div class="card-img">
            <img src="https://images.unsplash.com/photo-1559847844-5315695dadae?w=500&fit=crop&auto=format&q=80"
                 alt="Camarones al Coco" loading="lazy">
            <span class="card-badge especial">Temporada</span>
          </div>
          <div class="card-body">
            <div class="card-name">Camarones al Coco</div>
            <div class="card-desc">Camarones jumbo apanados en coco rallado, fritos hasta quedar dorados. Acompañados de salsa de mango-chile y arroz con coco.</div>
            <div class="card-footer">
              <div class="card-price">Q 165 <small>/ plato</small></div>
              <button class="btn-add" title="Agregar"><i class="bi bi-plus"></i></button>
            </div>
          </div>
        </div>

        <div class="menu-card">
          <div class="card-img">
            <img src="https://images.unsplash.com/photo-1510130315014-d27fe2e85ff8?w=500&fit=crop&auto=format&q=80"
                 alt="Langosta al Vapor" loading="lazy">
          </div>
          <div class="card-body">
            <div class="card-name">Langosta al Vapor</div>
            <div class="card-desc">Langosta fresca del Atlántico cocida al vapor con mantequilla de hierbas y ajo. Servida con papas asadas y ensalada verde.</div>
            <div class="card-footer">
              <div class="card-price">Q 225 <small>/ plato</small></div>
              <button class="btn-add" title="Agregar"><i class="bi bi-plus"></i></button>
            </div>
          </div>
        </div>

        <div class="menu-card">
          <div class="card-img">
            <img src="https://images.unsplash.com/photo-1546833999-b9f581a1996d?w=500&fit=crop&auto=format&q=80"
                 alt="Carne Asada" loading="lazy">
          </div>
          <div class="card-body">
            <div class="card-name">Carne Asada a la Parrilla</div>
            <div class="card-desc">Res marinada en especias criollas, asada al carbón. Con arroz blanco, frijoles volteados y ensalada fresca.</div>
            <div class="card-footer">
              <div class="card-price">Q 125 <small>/ plato</small></div>
              <button class="btn-add" title="Agregar"><i class="bi bi-plus"></i></button>
            </div>
          </div>
        </div>

        <div class="menu-card">
          <div class="card-img">
            <img src="https://images.unsplash.com/photo-1598103442097-8b74394b95c8?w=500&fit=crop&auto=format&q=80"
                 alt="Pollo en Salsa" loading="lazy">
          </div>
          <div class="card-body">
            <div class="card-name">Pollo en Salsa Guatemalteca</div>
            <div class="card-desc">Muslos de pollo en salsa de tomate criollo con especias guatemaltecas, recao y chile dulce. Con arroz y frijoles.</div>
            <div class="card-footer">
              <div class="card-price">Q 95 <small>/ plato</small></div>
              <button class="btn-add" title="Agregar"><i class="bi bi-plus"></i></button>
            </div>
          </div>
        </div>

        <div class="menu-card">
          <div class="card-img">
            <img src="https://images.unsplash.com/photo-1529042410759-befb1204b468?w=500&fit=crop&auto=format&q=80"
                 alt="Filete al Carbón con Salsa de Mango" loading="lazy">
            <span class="card-badge nuevo">Nuevo</span>
          </div>
          <div class="card-body">
            <div class="card-name">Filete al Carbón con Salsa de Mango y Piña</div>
            <div class="card-desc">Filete de res asado al carbón con salsa tropical de mango, piña y chile. Una fusión única entre lo criollo y el sabor del Caribe.</div>
            <div class="card-footer">
              <div class="card-price">Q 155 <small>/ plato</small></div>
              <button class="btn-add" title="Agregar"><i class="bi bi-plus"></i></button>
            </div>
          </div>
        </div>

      </div>
    </section>

    <!-- ════════ POSTRES ════════ -->
    <section class="category-section" data-category="postres" id="sec-postres">
      <div class="category-header">
        <div class="cat-icon" style="background:#FCE4EC; color:#C2185B"><i class="bi bi-cake2"></i></div>
        <h2>Postres</h2>
        <span class="cat-count">4 platillos</span>
      </div>
      <div class="menu-grid">

        <div class="menu-card">
          <div class="card-img">
            <img src="https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=500&fit=crop&auto=format&q=80"
                 alt="Tres Leches" loading="lazy">
            <span class="card-badge popular">Más pedido</span>
          </div>
          <div class="card-body">
            <div class="card-name">Pastel Tres Leches</div>
            <div class="card-desc">Esponjoso bizcocho bañado en tres tipos de leche, cubierto de crema batida y cerezas. Frío y cremoso.</div>
            <div class="card-footer">
              <div class="card-price">Q 45 <small>/ porción</small></div>
              <button class="btn-add" title="Agregar"><i class="bi bi-plus"></i></button>
            </div>
          </div>
        </div>

        <div class="menu-card">
          <div class="card-img">
            <img src="https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=500&fit=crop&auto=format&q=80"
                 alt="Torta de Chocolate" loading="lazy">
          </div>
          <div class="card-body">
            <div class="card-name">Torta de Chocolate Artesanal</div>
            <div class="card-desc">Moist chocolate cake con ganache de cacao guatemalteco de alta calidad. Con helado de vainilla artesanal.</div>
            <div class="card-footer">
              <div class="card-price">Q 40 <small>/ porción</small></div>
              <button class="btn-add" title="Agregar"><i class="bi bi-plus"></i></button>
            </div>
          </div>
        </div>

        <div class="menu-card">
          <div class="card-img">
            <img src="https://images.unsplash.com/photo-1488477181946-6428a0291777?w=500&fit=crop&auto=format&q=80"
                 alt="Flan de Vainilla" loading="lazy">
          </div>
          <div class="card-body">
            <div class="card-name">Flan de Vainilla con Caramelo</div>
            <div class="card-desc">Suave flan de huevo con vainilla natural y caramelo dorado. Receta tradicional guatemalteca.</div>
            <div class="card-footer">
              <div class="card-price">Q 35 <small>/ porción</small></div>
              <button class="btn-add" title="Agregar"><i class="bi bi-plus"></i></button>
            </div>
          </div>
        </div>

        <div class="menu-card">
          <div class="card-img">
            <img src="https://images.unsplash.com/photo-1563805042-7684c019e1cb?w=500&fit=crop&auto=format&q=80"
                 alt="Helado Artesanal de Coco" loading="lazy">
            <span class="card-badge especial">Artesanal</span>
          </div>
          <div class="card-body">
            <div class="card-name">Helado Artesanal de Coco</div>
            <div class="card-desc">Helado cremoso elaborado con coco fresco de Izabal. Sin conservantes. Sabores: coco, mango, guanábana.</div>
            <div class="card-footer">
              <div class="card-price">Q 30 <small>/ 2 bolas</small></div>
              <button class="btn-add" title="Agregar"><i class="bi bi-plus"></i></button>
            </div>
          </div>
        </div>

      </div>
    </section>

    <!-- ════════ BEBIDAS ════════ -->
    <section class="category-section" data-category="bebidas" id="sec-bebidas">
      <div class="category-header">
        <div class="cat-icon" style="background:#E3F2FD; color:#1565C0"><i class="bi bi-cup-straw"></i></div>
        <h2>Bebidas</h2>
        <span class="cat-count">8 opciones</span>
      </div>
      <div class="menu-grid">

        <div class="menu-card">
          <div class="card-img">
            <img src="https://images.unsplash.com/photo-1534353436294-0dbd4bdac845?w=500&fit=crop&auto=format&q=80"
                 alt="Fresco Natural" loading="lazy">
            <span class="card-badge popular">Favorito</span>
          </div>
          <div class="card-body">
            <div class="card-name">Fresco Natural</div>
            <div class="card-desc">Preparado artesanal en el día. Sabores: Jamaica, Tamarindo, Horchata o Piña con jengibre. Endulzado con panela.</div>
            <div class="card-footer">
              <div class="card-price">Q 25 <small>/ vaso</small></div>
              <button class="btn-add" title="Agregar"><i class="bi bi-plus"></i></button>
            </div>
          </div>
        </div>

        <div class="menu-card">
          <div class="card-img">
            <img src="https://images.unsplash.com/photo-1497534446932-c925b458314e?w=500&fit=crop&auto=format&q=80"
                 alt="Limonada Fresca" loading="lazy">
          </div>
          <div class="card-body">
            <div class="card-name">Limonada Fresca</div>
            <div class="card-desc">Jugo natural de limón con agua mineral, menta fresca y azúcar de caña. Servida con hielo y limón en el borde.</div>
            <div class="card-footer">
              <div class="card-price">Q 25 <small>/ vaso</small></div>
              <button class="btn-add" title="Agregar"><i class="bi bi-plus"></i></button>
            </div>
          </div>
        </div>

        <div class="menu-card">
          <div class="card-img">
            <img src="https://images.unsplash.com/photo-1559181567-c3190ca9be46?w=500&fit=crop&auto=format&q=80"
                 alt="Agua de Coco Natural" loading="lazy">
            <span class="card-badge especial">Del Caribe</span>
          </div>
          <div class="card-body">
            <div class="card-name">Agua de Coco Natural</div>
            <div class="card-desc">Coco joven fresco cortado al momento. Agua de coco pura, natural y refrescante. Directo de las palmeras de Izabal.</div>
            <div class="card-footer">
              <div class="card-price">Q 35 <small>/ coco</small></div>
              <button class="btn-add" title="Agregar"><i class="bi bi-plus"></i></button>
            </div>
          </div>
        </div>

        <div class="menu-card">
          <div class="card-img">
            <img src="https://images.unsplash.com/photo-1553361371-9b22f78e8b1d?w=500&fit=crop&auto=format&q=80"
                 alt="Jugo de Frutas Tropicales" loading="lazy">
          </div>
          <div class="card-body">
            <div class="card-name">Jugo de Frutas Tropicales</div>
            <div class="card-desc">Blend de frutas de temporada: mango, papaya, maracuyá, piña o guayaba. Recién licuado sin conservantes.</div>
            <div class="card-footer">
              <div class="card-price">Q 30 <small>/ vaso</small></div>
              <button class="btn-add" title="Agregar"><i class="bi bi-plus"></i></button>
            </div>
          </div>
        </div>

        <div class="menu-card">
          <div class="card-img">
            <img src="https://images.unsplash.com/photo-1535958636474-b021ee887b13?w=500&fit=crop&auto=format&q=80"
                 alt="Cerveza Nacional" loading="lazy">
          </div>
          <div class="card-body">
            <div class="card-name">Cerveza Nacional</div>
            <div class="card-desc">Gallo, Moza o Victoria. Bien fría, servida en botella o lata. Perfecta para acompañar nuestros mariscos.</div>
            <div class="card-footer">
              <div class="card-price">Q 35 <small>/ unidad</small></div>
              <button class="btn-add" title="Agregar"><i class="bi bi-plus"></i></button>
            </div>
          </div>
        </div>

        <div class="menu-card">
          <div class="card-img">
            <img src="https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=500&fit=crop&auto=format&q=80"
                 alt="Café de Guatemala" loading="lazy">
          </div>
          <div class="card-body">
            <div class="card-name">Café de Guatemala</div>
            <div class="card-desc">Café de origen guatemalteco, preparado al chorro. Americano, capuchino o con leche. Sabor único de nuestras tierras altas.</div>
            <div class="card-footer">
              <div class="card-price">Q 20 <small>/ taza</small></div>
              <button class="btn-add" title="Agregar"><i class="bi bi-plus"></i></button>
            </div>
          </div>
        </div>

        <div class="menu-card">
          <div class="card-img">
            <img src="https://images.unsplash.com/photo-1624552184280-9e9631bbeee9?w=500&fit=crop&auto=format&q=80"
                 alt="Gaseosa" loading="lazy">
          </div>
          <div class="card-body">
            <div class="card-name">Gaseosa / Refresco</div>
            <div class="card-desc">Coca-Cola, Pepsi, Sprite, Fanta o agua mineral con gas. Servida fría con hielo y limón.</div>
            <div class="card-footer">
              <div class="card-price">Q 20 <small>/ unidad</small></div>
              <button class="btn-add" title="Agregar"><i class="bi bi-plus"></i></button>
            </div>
          </div>
        </div>

        <div class="menu-card">
          <div class="card-img">
            <img src="https://images.unsplash.com/photo-1516485512905-1b68a0c3d9e3?w=500&fit=crop&auto=format&q=80"
                 alt="Agua Natural" loading="lazy">
          </div>
          <div class="card-body">
            <div class="card-name">Agua Natural Purificada</div>
            <div class="card-desc">Botella de agua purificada de 600ml o 1 litro. Opción sin gas o con gas mineral.</div>
            <div class="card-footer">
              <div class="card-price">Q 15 <small>/ 600ml</small></div>
              <button class="btn-add" title="Agregar"><i class="bi bi-plus"></i></button>
            </div>
          </div>
        </div>

      </div>
    </section>

    <!-- ════════ ESPECIALIDADES ════════ -->
    <section class="category-section" data-category="especiales" id="sec-especiales">
      <div class="category-header">
        <div class="cat-icon" style="background:#FFF8E1; color:#F9A825"><i class="bi bi-star-fill"></i></div>
        <h2>Especialidades del Chef</h2>
        <span class="cat-count">4 platillos</span>
      </div>
      <div class="menu-grid">

        <div class="menu-card featured">
          <div class="card-img">
            <img src="https://images.unsplash.com/photo-1534422298391-e4f8c172dddb?w=900&fit=crop&auto=format&q=80"
                 alt="Paella Caribeña" loading="lazy">
            <span class="card-badge popular">Chef's choice</span>
          </div>
          <div class="card-body">
            <div class="card-name">Paella Caribeña de La Antigua</div>
            <div class="card-desc">Arroz con azafrán, camarones jumbo, langosta, mejillones, calamares y trozos de pescado fresco. Preparación mínima 30 minutos. Para 2–3 personas.</div>
            <div class="card-footer">
              <div class="card-price">Q 280 <small>/ para 2-3</small></div>
              <button class="btn-add" title="Agregar"><i class="bi bi-plus"></i></button>
            </div>
          </div>
        </div>

        <div class="menu-card">
          <div class="card-img">
            <img src="https://images.unsplash.com/photo-1559329007-40df8a9345d8?w=500&fit=crop&auto=format&q=80"
                 alt="Mariscos al Coco" loading="lazy">
            <span class="card-badge especial">Para 2</span>
          </div>
          <div class="card-body">
            <div class="card-name">Mariscos al Coco (Para 2)</div>
            <div class="card-desc">Combinado de mariscos mixtos cocinados en leche de coco fresco con especias Garífunas. Acompañado de pan casero y arroz con coco.</div>
            <div class="card-footer">
              <div class="card-price">Q 350 <small>/ para 2</small></div>
              <button class="btn-add" title="Agregar"><i class="bi bi-plus"></i></button>
            </div>
          </div>
        </div>

        <div class="menu-card">
          <div class="card-img">
            <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=500&fit=crop&auto=format&q=80"
                 alt="Plato del Mar" loading="lazy">
            <span class="card-badge popular">Favorito</span>
          </div>
          <div class="card-body">
            <div class="card-name">Plato del Mar — La Antigua</div>
            <div class="card-desc">Filete de pescado, camarones asados, langostinos y ceviche. Todo en un plato. La experiencia completa del Caribe guatemalteco.</div>
            <div class="card-footer">
              <div class="card-price">Q 245 <small>/ persona</small></div>
              <button class="btn-add" title="Agregar"><i class="bi bi-plus"></i></button>
            </div>
          </div>
        </div>

        <div class="menu-card">
          <div class="card-img">
            <img src="https://images.unsplash.com/photo-1467003909585-2f8a72700288?w=500&fit=crop&auto=format&q=80"
                 alt="Menú Degustación" loading="lazy">
            <span class="card-badge nuevo">Premium</span>
          </div>
          <div class="card-body">
            <div class="card-name">Menú Degustación — Chef's Table</div>
            <div class="card-desc">Experiencia completa de 5 tiempos diseñada por nuestro chef. Reservación previa requerida. Incluye maridaje de bebidas artesanales.</div>
            <div class="card-footer">
              <div class="card-price">Q 450 <small>/ persona</small></div>
              <button class="btn-add" title="Agregar"><i class="bi bi-plus"></i></button>
            </div>
          </div>
        </div>

      </div>
    </section>

  </main>

  <!-- ══ FOOTER ══ -->
  <div class="page-footer">
    © 2026 Restaurante La Antigua · Puerto Barrios, Izabal ·
    <a href="portal-cliente.php">Inicio</a> ·
    <a href="menu-cliente.php">Pedir en línea</a> ·
    Tel: +502 7948-0000
  </div>

  <!-- ══ CARRITO PEDIDO EN LÍNEA ══ -->
  <div id="cartPanel" style="position:fixed;top:0;right:-420px;width:400px;max-width:100vw;height:100vh;background:#fff;box-shadow:-8px 0 32px rgba(0,0,0,.25);z-index:200;display:flex;flex-direction:column;transition:right .25s ease">
    <div style="padding:18px 20px;background:#1A2E4A;color:#fff;display:flex;align-items:center;justify-content:space-between">
      <div>
        <div style="font-size:15px;font-weight:700"><i class="bi bi-bag-check"></i> Mi pedido</div>
        <div style="font-size:11.5px;color:rgba(255,255,255,.6);margin-top:2px">Se enviará al restaurante</div>
      </div>
      <button onclick="toggleCart()" style="background:none;border:none;color:rgba(255,255,255,.7);font-size:22px;cursor:pointer"><i class="bi bi-x"></i></button>
    </div>
    <div id="cartItems" style="flex:1;overflow-y:auto;padding:14px;background:#FAF7F2"></div>
    <div style="padding:14px 18px;border-top:1px solid #E5E7EB;background:#fff">
      <div style="display:flex;justify-content:space-between;font-size:13px;color:#6B7280;margin-bottom:4px">
        <span><span id="cartCount">0</span> ítems</span>
      </div>
      <div style="display:flex;justify-content:space-between;font-size:18px;font-weight:800;color:#1A2E4A;margin-bottom:14px">
        <span>Total</span><span>Q <span id="cartTotal">0.00</span></span>
      </div>
      <div style="margin-bottom:10px">
        <label style="font-size:12px;font-weight:600;color:#374151">Tipo de pedido</label>
        <div style="display:flex;gap:8px;margin-top:4px">
          <label style="flex:1;display:flex;align-items:center;gap:6px;padding:8px 10px;border:1.5px solid #E5E7EB;border-radius:8px;cursor:pointer;font-size:13px"><input type="radio" name="cartTipo" value="LLEVAR" checked> Para llevar</label>
          <label style="flex:1;display:flex;align-items:center;gap:6px;padding:8px 10px;border:1.5px solid #E5E7EB;border-radius:8px;cursor:pointer;font-size:13px"><input type="radio" name="cartTipo" value="DOMICILIO"> Domicilio</label>
        </div>
      </div>
      <div style="margin-bottom:12px">
        <label style="font-size:12px;font-weight:600;color:#374151">Notas <span style="color:#9CA3AF;font-weight:400">(opcional)</span></label>
        <textarea id="cartNotas" rows="2" placeholder="Alergias, instrucciones..." style="width:100%;padding:8px;border:1.5px solid #E5E7EB;border-radius:8px;font-size:13px;font-family:inherit;outline:none;resize:vertical"></textarea>
      </div>
      <button id="btnEnviarPedido" onclick="enviarPedidoOnline()" style="width:100%;padding:14px;background:#E8A020;color:#fff;border:none;border-radius:11px;font-weight:800;font-size:14px;cursor:pointer">
        <i class="bi bi-send"></i> Enviar pedido al restaurante
      </button>
    </div>
  </div>
  <div id="cartOverlay" onclick="toggleCart()" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:199"></div>

  <!-- ══ TOAST ══ -->
  <div id="onlineToast" style="display:none;position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:#1A2E4A;color:#fff;padding:14px 22px;border-radius:12px;box-shadow:0 8px 32px rgba(0,0,0,.25);z-index:250;font-size:14px;font-weight:600"></div>

  <script src="sigra-data.js"></script>
  <script>
    /* ── Carrito cliente ── */
    let cart = JSON.parse(sessionStorage.getItem('laantigua_cart') || '[]');

    function saveCart() {
      sessionStorage.setItem('laantigua_cart', JSON.stringify(cart));
      renderCart();
    }

    function addToCart(nombre, precio) {
      const existing = cart.find(i => i.nombre === nombre);
      if (existing) existing.cantidad++;
      else cart.push({ nombre, precio, cantidad: 1 });
      saveCart();
      showOnlineToast(`Agregado: ${nombre}`);
    }

    function changeQty(idx, delta) {
      cart[idx].cantidad += delta;
      if (cart[idx].cantidad <= 0) cart.splice(idx, 1);
      saveCart();
    }

    function renderCart() {
      const total = cart.reduce((s, i) => s + i.precio * i.cantidad, 0);
      const count = cart.reduce((s, i) => s + i.cantidad, 0);
      document.getElementById('cartTotal').textContent = total.toFixed(2);
      document.getElementById('cartCount').textContent = count;
      const badge = document.getElementById('navCartCount');
      if (count > 0) { badge.style.display = 'inline-block'; badge.textContent = count; }
      else badge.style.display = 'none';
      const list = document.getElementById('cartItems');
      if (!cart.length) {
        list.innerHTML = '<div style="text-align:center;color:#9CA3AF;padding:40px 20px"><i class="bi bi-bag" style="font-size:42px;opacity:.4;display:block;margin-bottom:10px"></i>Tu pedido está vacío</div>';
        document.getElementById('btnEnviarPedido').disabled = true;
        document.getElementById('btnEnviarPedido').style.opacity = '.5';
        return;
      }
      document.getElementById('btnEnviarPedido').disabled = false;
      document.getElementById('btnEnviarPedido').style.opacity = '1';
      list.innerHTML = cart.map((i, idx) => `
        <div style="background:#fff;border:1px solid #E5E7EB;border-radius:10px;padding:10px 12px;margin-bottom:8px">
          <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px">
            <div style="flex:1">
              <div style="font-size:13.5px;font-weight:600;color:#1F2937">${i.nombre}</div>
              <div style="font-size:12px;color:#9CA3AF">Q ${i.precio.toFixed(2)} c/u</div>
            </div>
            <div style="font-size:13.5px;font-weight:700;color:#1A2E4A">Q ${(i.precio*i.cantidad).toFixed(2)}</div>
          </div>
          <div style="display:flex;justify-content:space-between;align-items:center;margin-top:8px">
            <div style="display:flex;align-items:center;gap:8px">
              <button onclick="changeQty(${idx},-1)" style="width:26px;height:26px;border:1px solid #E5E7EB;background:#fff;border-radius:50%;cursor:pointer">−</button>
              <span style="font-size:13.5px;font-weight:700;min-width:18px;text-align:center">${i.cantidad}</span>
              <button onclick="changeQty(${idx},1)" style="width:26px;height:26px;border:none;background:#E8A020;color:#fff;border-radius:50%;cursor:pointer">+</button>
            </div>
            <button onclick="changeQty(${idx},-99)" style="background:none;border:none;color:#C62828;font-size:13px;cursor:pointer"><i class="bi bi-trash"></i></button>
          </div>
        </div>`).join('');
    }

    function toggleCart() {
      const panel = document.getElementById('cartPanel');
      const overlay = document.getElementById('cartOverlay');
      const open = panel.style.right === '0px';
      panel.style.right = open ? '-420px' : '0px';
      overlay.style.display = open ? 'none' : 'block';
    }

    function showOnlineToast(msg) {
      const t = document.getElementById('onlineToast');
      t.textContent = msg;
      t.style.display = 'block';
      clearTimeout(t._h);
      t._h = setTimeout(() => t.style.display = 'none', 2200);
    }

    function enviarPedidoOnline() {
      if (!cart.length) return;
      const cliente = (() => {
        try { return JSON.parse(sessionStorage.getItem('laantigua_cliente') || 'null'); }
        catch { return null; }
      })();
      const tipo = document.querySelector('input[name=cartTipo]:checked').value;
      const notas = document.getElementById('cartNotas').value.trim();
      const id = SIGRA_DATA.nextPedidoId();
      const total = cart.reduce((s, i) => s + i.precio * i.cantidad, 0);
      const nombreCliente = cliente ? (cliente.fullName || cliente.nombre || 'Cliente') : 'Cliente sin registro';

      SIGRA_DATA.addCocinaPedido({
        id,
        mesa: tipo === 'LLEVAR' ? 'Para Llevar' : 'Domicilio',
        tipo: tipo === 'LLEVAR' ? 'LLEVAR' : 'DOMICILIO',
        canal: 'EN_LINEA',
        cliente: nombreCliente,
        estado: 'NUEVO',
        tiempoMin: 0,
        total,
        notas,
        items: cart.map(i => ({ nombre: i.nombre, cantidad: i.cantidad, precio: i.precio, notas: '' })),
      });

      cart = [];
      sessionStorage.removeItem('laantigua_cart');
      document.getElementById('cartNotas').value = '';
      renderCart();
      toggleCart();
      showOnlineToast(`¡Pedido ${id} enviado al restaurante!`);
    }

    /* ── Cargar usuario ── */
    (function() {
      try {
        const raw = sessionStorage.getItem('laantigua_cliente');
        if (!raw) return;
        const u = JSON.parse(raw);
        const el = document.getElementById('navUser');
        if (el) {
          document.getElementById('navAvatar').textContent   = u.initial || '?';
          document.getElementById('navUserName').textContent = u.nombre  || 'Cliente';
          el.style.display = 'flex';
        }
      } catch(e) {}
    })();

    /* ── Filtro de categorías ── */
    function filterCat(cat, btn) {
      /* Actualizar tab activo */
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      /* Mostrar/ocultar secciones */
      document.querySelectorAll('.category-section').forEach(sec => {
        if (cat === 'todos') {
          sec.removeAttribute('data-hidden');
        } else {
          if (sec.dataset.category === cat) {
            sec.removeAttribute('data-hidden');
          } else {
            sec.setAttribute('data-hidden', 'true');
          }
        }
      });

      /* Scroll al top del contenido */
      window.scrollTo({ top: 220, behavior: 'smooth' });
    }

    /* ── Botón agregar → carrito ── */
    document.querySelectorAll('.btn-add').forEach(btn => {
      btn.addEventListener('click', function() {
        const card = this.closest('.menu-card');
        if (!card) return;
        const nombre = card.querySelector('.card-name')?.textContent.trim() || 'Item';
        const priceTxt = card.querySelector('.card-price')?.firstChild?.textContent || '';
        const precio = Number((priceTxt.match(/[\d.]+/) || ['0'])[0]);
        if (!precio) return;
        addToCart(nombre, precio);
        const orig = this.innerHTML;
        this.style.background = '#2E7D32';
        this.innerHTML = '<i class="bi bi-check"></i>';
        setTimeout(() => { this.style.background = ''; this.innerHTML = orig; }, 1000);
      });
    });

    renderCart();
  </script>
</body>
</html>
