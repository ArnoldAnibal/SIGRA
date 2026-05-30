/* ============================================================
   SIGRA — Layout compartido
   Maneja sidebar, topbar, auth y navegación
   ============================================================ */

(function () {
  'use strict';

  /* ─── Bootstrap inyectado por PHP (si la página es .php) ─── */
  function readPhpBootstrap() {
    const el = document.getElementById('sigra-bootstrap');
    if (!el) return {};
    try { return JSON.parse(el.textContent.trim() || '{}'); }
    catch { return {}; }
  }
  const PHP_BOOT = readPhpBootstrap();
  if (PHP_BOOT.session)   window.SIGRA_SESSION   = PHP_BOOT.session;
  if (PHP_BOOT.menu)      window.SIGRA_MENU_SEED  = PHP_BOOT.menu;
if (PHP_BOOT.mesas)     window.SIGRA_MESAS_SEED  = PHP_BOOT.mesas;
if (PHP_BOOT.pedidos)   window.SIGRA_PEDIDOS_SEED = PHP_BOOT.pedidos;
if (PHP_BOOT.cola)      window.SIGRA_COCINA_SEED  = PHP_BOOT.cola;
if (PHP_BOOT.planillas) window.SIGRA_PLANILLAS_SEED = PHP_BOOT.planillas;
if (PHP_BOOT.empleados) window.SIGRA_EMPLEADOS_SEED = PHP_BOOT.empleados; 
if (PHP_BOOT.asistencia)window.SIGRA_ASISTENCIA_SEED = PHP_BOOT.asistencia;
if (PHP_BOOT.inventario)window.SIGRA_INVENTARIO_SEED = PHP_BOOT.inventario;
if (PHP_BOOT.proveedores) window.SIGRA_PROVEEDORES_SEED = PHP_BOOT.proveedores;
if (PHP_BOOT.cuentas_prov) window.SIGRA_CUENTAS_PROV_SEED = PHP_BOOT.cuentas_prov;
if (PHP_BOOT.pedidos_pendientes) window.SIGRA_PEDIDOS_PEND_SEED = PHP_BOOT.pedidos_pendientes;
if (PHP_BOOT.empresas)  window.SIGRA_EMPRESAS_SEED = PHP_BOOT.empresas;
if (PHP_BOOT.empresas) window.SIGRA_EMPRESAS_SEED = PHP_BOOT.empresas;
if (PHP_BOOT.ventas_mes) window.SIGRA_VENTAS_SEED = PHP_BOOT.ventas_mes;

  /* ─── Auth Check ─── */
  function getUser() {
    // 1) Sesión inyectada por backend PHP (modo producción).
    if (window.SIGRA_SESSION) return window.SIGRA_SESSION;
    // 2) Fallback: sessionStorage (modo demo, sin backend).
    try {
      const raw = sessionStorage.getItem('sigra_user');
      return raw ? JSON.parse(raw) : null;
    } catch { return null; }
  }

  function requireAuth() {
    const user = getUser();
    if (!user) {
      window.location.href = 'index.php';
      return null;
    }
    return user;
  }

  /* Determina la URL de logout según si es invitado o empleado */
  function logoutUrl(user) {
    return (user && user.isGuest) ? 'index.php' : 'login-empleados.php';
  }

  /* ─── Nav Items ─── */
  const NAV = [
    { label: 'Dashboard',          icon: 'bi-grid-1x2',     path: 'dashboard.php',         roles: ['ADMIN'] },
    { label: 'Pedidos Mesa',        icon: 'bi-cart3',        path: 'pedidos-mesa.php',       roles: ['ADMIN','MESERO'] },
    { label: 'Domicilio / Llevar',  icon: 'bi-truck',        path: 'pedidos-domicilio.php',  roles: ['ADMIN','MESERO','CAJERO'] },
    { label: 'Cocina (KDS)',        icon: 'bi-fire',         path: 'cocina.php',             roles: ['ADMIN','COCINA'] },
    { label: 'Facturación FEL',     icon: 'bi-receipt',      path: 'facturacion.php',        roles: ['ADMIN','CAJERO'] },
    { label: 'Crédito Empresarial', icon: 'bi-building',     path: 'credito.php',            roles: ['ADMIN','CAJERO'] },
    { label: 'Menú',                icon: 'bi-journal-text', path: 'menu.php',               roles: ['ADMIN','INVITADO'] },
    { label: 'Inventario',          icon: 'bi-box-seam',     path: 'inventario.php',         roles: ['ADMIN'] },
    { label: 'Nómina',             icon: 'bi-people',       path: 'nomina.php',             roles: ['ADMIN'] },
    { label: 'Asistencia',          icon: 'bi-clock-history',path: 'asistencia.php',         roles: ['ADMIN','MESERO','COCINA','CAJERO'] },
    { label: 'Proveedores',         icon: 'bi-truck-flatbed',path: 'proveedores.php',        roles: ['ADMIN'] },
    { label: 'Reportes',            icon: 'bi-bar-chart',    path: 'reportes.php',           roles: ['ADMIN'] },
    { label: 'Configuración',       icon: 'bi-gear',         path: 'configuracion.php',      roles: ['ADMIN'] },
  ];

  /* ─── Build Sidebar HTML ─── */
  const ROLE_LABELS = {
    ADMIN:    'Administrador',
    MESERO:   'Mesero/a',
    COCINA:   'Cocina',
    CAJERO:   'Cajero/a',
    INVITADO: 'Invitado',
  };

  function buildSidebar(user) {
    const currentFile = location.pathname.split('/').pop() || 'index.php';
    const filtered = NAV.filter(n => n.roles.includes(user.role));

    const links = filtered.map(n => {
      const active = currentFile === n.path ? 'class="active"' : '';
      return `<a href="${n.path}" ${active}>
        <i class="bi ${n.icon}"></i>
        <span class="nav-label">${n.label}</span>
      </a>`;
    }).join('');

    const initial = user.initial || user.name.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase();
    const currentPage = filtered.find(n => n.path === currentFile);
    const roleLabel = ROLE_LABELS[user.role] || user.role;

    return { links, initial, currentPage, roleLabel };
  }

  /* ─── Inject Layout ─── */
  function initLayout(pageName) {
    const user = requireAuth();
    if (!user) return;

    const { links, initial, currentPage, roleLabel } = buildSidebar(user);
    const breadcrumb = pageName || (currentPage && currentPage.label) || 'Panel';

    /* Sidebar */
    const sidebar = document.getElementById('sidebar');
    if (sidebar) {
      sidebar.innerHTML = `
        <div class="sidebar-logo">
          <div class="sidebar-logo-icon">SG</div>
          <div class="sidebar-logo-text">
            <div class="sidebar-logo-name">SIGRA</div>
            <div class="sidebar-logo-sub">La Antigua</div>
          </div>
        </div>
        <nav class="sidebar-nav">${links}</nav>
        <div class="sidebar-footer">
          <div class="sidebar-user">
            <div class="sidebar-avatar">${initial}</div>
            <div class="sidebar-user-info">
              <div class="sidebar-user-name">${user.name}</div>
              <div class="sidebar-user-role">${roleLabel}</div>
            </div>
            <a href="${logoutUrl(user)}" class="sidebar-logout" title="Cerrar sesión" onclick="sessionStorage.removeItem('sigra_user')">
              <i class="bi bi-box-arrow-right"></i>
            </a>
          </div>
        </div>
        <button class="sidebar-toggle" id="sidebarToggle">
          <i class="bi bi-chevron-left" id="toggleIcon"></i>
        </button>`;
    }

    /* Topbar */
    const topbar = document.getElementById('topbar');
    if (topbar) {
      topbar.innerHTML = `
        <div class="topbar-left">
          <button class="topbar-menu-btn" id="mobileMenuBtn"><i class="bi bi-list"></i></button>
          <div class="topbar-crumb">
            <span class="prefix">SIGRA</span>
            <span class="sep">/</span>
            <span class="current">${breadcrumb}</span>
          </div>
        </div>
        <div class="topbar-right">
          <div style="position:relative">
            <button class="topbar-bell" id="bellBtn" onclick="SIGRA.toggleNotifPanel()">
              <i class="bi bi-bell"></i>
              <span class="topbar-badge" id="bellBadge" style="display:none">0</span>
            </button>
            <div id="notifPanel" style="display:none;position:absolute;top:100%;right:0;width:340px;max-height:440px;background:#fff;border-radius:12px;box-shadow:0 12px 36px rgba(0,0,0,.18);border:1px solid #E5E7EB;z-index:120;margin-top:8px;overflow:hidden">
              <div style="padding:14px 16px;border-bottom:1px solid #E5E7EB;display:flex;justify-content:space-between;align-items:center;background:#FAFAFA">
                <div style="font-weight:700;color:#1A2E4A;font-size:14px"><i class="bi bi-bell-fill" style="color:#E8A020"></i> Notificaciones</div>
                <button onclick="SIGRA.markAllRead()" style="background:none;border:none;color:#1565C0;font-size:11.5px;font-weight:600;cursor:pointer">Marcar todo leído</button>
              </div>
              <div id="notifList" style="max-height:340px;overflow-y:auto"></div>
              <div style="padding:10px 16px;border-top:1px solid #E5E7EB;background:#FAFAFA;text-align:center">
                <button onclick="SIGRA.clearNotifs()" style="background:none;border:none;color:#C62828;font-size:12px;font-weight:600;cursor:pointer"><i class="bi bi-trash"></i> Limpiar todo</button>
              </div>
            </div>
          </div>
          <div class="topbar-user-wrap">
            <div class="topbar-avatar">${initial}</div>
            <span class="topbar-uname">${user.name}</span>
          </div>
        </div>`;

      /* Cerrar dropdown al click fuera */
      document.addEventListener('click', (e) => {
        const panel = document.getElementById('notifPanel');
        const btn = document.getElementById('bellBtn');
        if (panel && panel.style.display === 'block' &&
            !panel.contains(e.target) && btn && !btn.contains(e.target)) {
          panel.style.display = 'none';
        }
      });

      renderNotifs();
      /* Auto-refresh notificaciones cada 5s */
      setInterval(refreshNotifsFromState, 5000);
    }

    /* ─── Sidebar toggle (desktop) ─── */
    const mainWrap = document.getElementById('mainWrap');
    document.addEventListener('click', (e) => {
      const toggleBtn = document.getElementById('sidebarToggle');
      if (toggleBtn && toggleBtn.contains(e.target)) {
        sidebar.classList.toggle('collapsed');
        if (mainWrap) mainWrap.classList.toggle('collapsed');
        const icon = document.getElementById('toggleIcon');
        if (icon) {
          icon.className = sidebar.classList.contains('collapsed')
            ? 'bi bi-chevron-right' : 'bi bi-chevron-left';
        }
      }
    });

    /* ─── Mobile sidebar ─── */
    const overlay = document.getElementById('sidebarOverlay');
    document.addEventListener('click', (e) => {
      const mobileBtn = document.getElementById('mobileMenuBtn');
      if (mobileBtn && mobileBtn.contains(e.target)) {
        sidebar.classList.add('mobile-open');
        if (overlay) overlay.classList.add('show');
      }
    });
    if (overlay) {
      overlay.addEventListener('click', () => {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('show');
      });
    }

    /* Close sidebar on nav click (mobile) */
    document.querySelectorAll('.sidebar-nav a').forEach(a => {
      a.addEventListener('click', () => {
        sidebar.classList.remove('mobile-open');
        if (overlay) overlay.classList.remove('show');
      });
    });
  }

  /* ─── Toast helper ─── */
  function showToast(id, duration) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.add('show');
    setTimeout(() => el.classList.remove('show'), duration || 2500);
  }

  /* ─── Modal helpers ─── */
  function openModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('show');
  }
  function closeModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('show');
  }

  /* ─── Notificaciones ─── */
  const NOTIF_KEY = 'sigra_notifs_v1';

  function loadNotifs() {
    try { return JSON.parse(localStorage.getItem(NOTIF_KEY) || '[]'); }
    catch { return []; }
  }
  function saveNotifs(arr) {
    try { localStorage.setItem(NOTIF_KEY, JSON.stringify(arr.slice(0, 30))); } catch {}
  }
  function pushNotif(notif) {
    const arr = loadNotifs();
    arr.unshift({ id: Date.now() + Math.random(), ts: Date.now(), read: false, ...notif });
    saveNotifs(arr);
    renderNotifs();
  }

  let lastSeenCocinaIds = null;
  function refreshNotifsFromState() {
    if (!window.SIGRA_DATA) return;
    const cocina = SIGRA_DATA.getCocina();
    const ids = new Set(cocina.map(p => p.id));
    if (lastSeenCocinaIds === null) { lastSeenCocinaIds = ids; renderNotifs(); return; }
    cocina.forEach(p => {
      if (!lastSeenCocinaIds.has(p.id)) {
        pushNotif({
          icon: p.tipo === 'LLEVAR' ? 'bi-bag' : (p.tipo === 'DOMICILIO' ? 'bi-truck' : 'bi-cart-check'),
          color: '#E8A020',
          title: `Nuevo pedido ${p.id}`,
          desc: `${p.mesa || ''} · ${(p.items||[]).reduce((s,i)=>s+i.cantidad,0)} ítems`,
          link: 'cocina.php',
        });
      }
    });
    lastSeenCocinaIds = ids;
    renderNotifs();
  }

  function renderNotifs() {
    const list = document.getElementById('notifList');
    const badge = document.getElementById('bellBadge');
    if (!list || !badge) return;
    const arr = loadNotifs();
    const unread = arr.filter(n => !n.read).length;
    if (unread > 0) { badge.style.display = 'flex'; badge.textContent = unread > 9 ? '9+' : unread; }
    else badge.style.display = 'none';
    if (!arr.length) {
      list.innerHTML = '<div style="padding:30px 20px;text-align:center;color:#9CA3AF;font-size:13px"><i class="bi bi-bell-slash" style="font-size:28px;opacity:.4;display:block;margin-bottom:8px"></i>Sin notificaciones</div>';
      return;
    }
    list.innerHTML = arr.map(n => {
      const minAgo = Math.floor((Date.now() - n.ts) / 60000);
      const t = minAgo < 1 ? 'ahora' : (minAgo < 60 ? `hace ${minAgo} min` : `hace ${Math.floor(minAgo/60)} h`);
      const click = n.link ? `onclick="window.location='${n.link}'"` : '';
      return `<div ${click} style="padding:12px 16px;border-bottom:1px solid #F3F4F6;display:flex;gap:12px;cursor:${n.link?'pointer':'default'};background:${n.read?'#fff':'#FFFBEB'};transition:background .15s" onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='${n.read?'#fff':'#FFFBEB'}'">
        <div style="width:34px;height:34px;border-radius:10px;background:${n.color||'#1A2E4A'}22;color:${n.color||'#1A2E4A'};display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="bi ${n.icon||'bi-info-circle'}"></i></div>
        <div style="flex:1;min-width:0">
          <div style="font-size:13px;font-weight:600;color:#1F2937">${n.title}</div>
          <div style="font-size:12px;color:#6B7280;margin-top:2px">${n.desc||''}</div>
          <div style="font-size:11px;color:#9CA3AF;margin-top:4px">${t}</div>
        </div>
      </div>`;
    }).join('');
  }

  function toggleNotifPanel() {
    const panel = document.getElementById('notifPanel');
    if (!panel) return;
    const open = panel.style.display === 'block';
    panel.style.display = open ? 'none' : 'block';
    if (!open) {
      const arr = loadNotifs().map(n => ({ ...n, read: true }));
      saveNotifs(arr);
      setTimeout(renderNotifs, 100);
    }
  }
  function markAllRead() {
    saveNotifs(loadNotifs().map(n => ({ ...n, read: true })));
    renderNotifs();
  }
  function clearNotifs() {
    saveNotifs([]);
    renderNotifs();
  }

  /* ─── Expose to global ─── */
  window.SIGRA = { initLayout, getUser, showToast, openModal, closeModal,
                   toggleNotifPanel, markAllRead, clearNotifs, pushNotif };

})();
