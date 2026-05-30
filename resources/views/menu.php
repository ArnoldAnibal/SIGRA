<?php
require_once __DIR__ . '/includes/bootstrap.php';
sigra_require_role(['ADMIN', 'MESERO', 'INVITADO']);

$SIGRA_USER = sigra_current_user();
$resM = api_productos(sigra_token()); $SIGRA_MENU = $resM["data"]["data"] ?? $resM["data"] ?? [];
$SIGRA_MENU = $SIGRA_MENU ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIGRA — Gestión de Menú</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="styles.css">
  <style>
    .menu-view {
      --mv-cream:  #FAF7F2;
      --mv-text:   #2D3748;
      --mv-muted:  #6B7280;
      --mv-border: #E5E7EB;
      background: var(--mv-cream);
      color: var(--mv-text);
      min-height: 100%;
    }

    .mv-header {
      background: linear-gradient(135deg, var(--navy) 0%, #243d5f 100%);
      padding: 32px 5% 28px; text-align: center;
    }
    .mv-header-tag {
      display: inline-flex; align-items: center; gap: 7px;
      background: rgba(232,160,32,.15); border: 1px solid rgba(232,160,32,.35);
      color: var(--amber); font-size: 12px; font-weight: 700;
      letter-spacing: .6px; text-transform: uppercase;
      padding: 5px 16px; border-radius: 999px; margin-bottom: 14px;
    }
    .mv-header h1 {
      color: #fff; font-size: clamp(22px, 4vw, 32px);
      font-weight: 900; margin-bottom: 8px; letter-spacing: -.5px;
    }
    .mv-header p { color: rgba(255,255,255,.65); font-size: 13.5px; }

    .mv-tabs-wrap {
      position: sticky; top: 0; z-index: 5;
      background: #fff; border-bottom: 2px solid var(--mv-border);
      box-shadow: 0 4px 12px rgba(0,0,0,.05);
    }
    .mv-tabs {
      display: flex; gap: 0; overflow-x: auto;
      padding: 0 5%; max-width: 1200px; margin: 0 auto;
      scrollbar-width: none;
    }
    .mv-tabs::-webkit-scrollbar { display: none; }
    .mv-tab {
      flex-shrink: 0; padding: 14px 20px;
      background: none; border: none; border-bottom: 3px solid transparent;
      font-size: 13.5px; font-weight: 600; color: var(--mv-muted);
      cursor: pointer; transition: color .15s, border-color .15s;
      display: flex; align-items: center; gap: 7px;
      white-space: nowrap; margin-bottom: -2px;
    }
    .mv-tab:hover { color: var(--navy); }
    .mv-tab.active { color: var(--navy); border-bottom-color: var(--amber); }
    .mv-tab .count {
      background: var(--mv-cream); color: var(--mv-muted);
      font-size: 10.5px; font-weight: 700; padding: 2px 7px; border-radius: 999px;
    }
    .mv-tab.active .count { background: #FFF8E1; color: #F9A825; }

    .mv-main { max-width: 1200px; margin: 0 auto; padding: 28px 5% 60px; }

    .mv-toolbar {
      display: flex; justify-content: space-between; align-items: center;
      background: #fff; padding: 12px 18px; border-radius: 10px;
      border: 1px solid var(--mv-border); margin-bottom: 22px; gap: 12px; flex-wrap: wrap;
    }
    .mv-toolbar-info { font-size: 13px; color: var(--mv-muted); }
    .mv-toolbar-info i { color: var(--amber); margin-right: 6px; }

    .mv-cat { margin-bottom: 40px; }
    .mv-cat[data-hidden="true"] { display: none; }
    .mv-cat-head {
      display: flex; align-items: center; gap: 12px;
      margin-bottom: 18px; padding-bottom: 12px;
      border-bottom: 2px solid var(--mv-border);
    }
    .mv-cat-icon {
      width: 42px; height: 42px; border-radius: 11px;
      display: flex; align-items: center; justify-content: center;
      font-size: 19px; flex-shrink: 0;
    }
    .mv-cat-head h2 { font-size: 19px; font-weight: 800; color: var(--navy); margin: 0; }
    .mv-cat-count {
      margin-left: auto; background: var(--mv-cream); color: var(--mv-muted);
      font-size: 12px; font-weight: 600; padding: 4px 12px; border-radius: 999px;
    }

    .mv-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
      gap: 18px;
    }

    .mv-card {
      background: #fff; border-radius: 14px;
      overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,.06);
      transition: transform .2s, box-shadow .2s;
      display: flex; flex-direction: column;
    }
    .mv-card:hover { transform: translateY(-3px); box-shadow: 0 10px 24px rgba(0,0,0,.1); }
    .mv-card-img {
      aspect-ratio: 16/10; overflow: hidden; background: linear-gradient(135deg, #E5E7EB, #F3F4F6);
      position: relative;
    }
    .mv-card-img img { width: 100%; height: 100%; object-fit: cover; transition: transform .3s; display: block; }
    .mv-card:hover .mv-card-img img { transform: scale(1.05); }
    .mv-card-img-fallback {
      position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
      color: #9CA3AF; font-size: 36px;
    }
    .mv-card-body { padding: 14px 14px 12px; flex: 1; display: flex; flex-direction: column; }
    .mv-card-name { font-size: 14.5px; font-weight: 700; color: var(--navy); margin-bottom: 4px; }
    .mv-card-desc { font-size: 12.5px; color: var(--mv-muted); line-height: 1.5; flex: 1; margin-bottom: 10px; }
    .mv-card-foot {
      display: flex; align-items: center; justify-content: space-between;
      border-top: 1px solid var(--mv-border); padding-top: 10px;
    }
    .mv-card-price { font-size: 17px; font-weight: 900; color: var(--navy); }
    .mv-card-price input {
      width: 80px; padding: 4px 6px; border: 1px solid var(--amber); border-radius: 4px;
      font-size: 14px; font-weight: 700; color: var(--navy); outline: none;
    }
    .mv-card-btn {
      width: 32px; height: 32px; border-radius: 50%; border: none;
      background: var(--amber); color: #fff; cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      font-size: 16px;
      transition: transform .15s, filter .15s;
      box-shadow: 0 2px 8px rgba(232,160,32,.3);
    }
    .mv-card-btn:hover { filter: brightness(1.1); transform: scale(1.1); }
    .mv-card-btn.save { background: #16A34A; }
    .mv-card-edit-pencil {
      background: none; border: 1px solid var(--mv-border); color: var(--mv-muted);
      width: 28px; height: 28px; border-radius: 50%; cursor: pointer;
      margin-right: 6px;
      display: inline-flex; align-items: center; justify-content: center;
      transition: all .12s;
    }
    .mv-card-edit-pencil:hover { background: var(--amber); color: #fff; border-color: var(--amber); }

    @media (max-width: 480px) { .mv-grid { grid-template-columns: 1fr; } }
  </style>
</head>
<body>
  <div class="app">
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <aside class="sidebar" id="sidebar"></aside>

    <div class="main-wrap" id="mainWrap">
      <header class="topbar" id="topbar"></header>

      <main class="page-content">
        <div class="menu-view">

          <div class="mv-header">
            <div class="mv-header-tag"><i class="bi bi-journal-text"></i> Carta del Restaurante</div>
            <h1>Nuestro Menú</h1>
            <p>Sabores auténticos del Caribe guatemalteco · Precios en Quetzales (GTQ)</p>
          </div>

          <div class="mv-tabs-wrap">
            <div class="mv-tabs" id="catTabs"></div>
          </div>

          <main class="mv-main">
            <div class="mv-toolbar" id="adminToolbar" style="display:none">
              <div class="mv-toolbar-info">
                <i class="bi bi-info-circle-fill"></i>
                Modo administrador · Crea, edita o elimina productos y categorías.
              </div>
              <div style="display:flex;gap:8px;flex-wrap:wrap">
                <button class="btn btn-primary btn-sm" onclick="openProductoModal()">
                  <i class="bi bi-plus-circle"></i> Nuevo producto
                </button>
                <button class="btn btn-outline btn-sm" onclick="openCategoriaModal()">
                  <i class="bi bi-tag"></i> Nueva categoría
                </button>
                <button class="btn btn-outline btn-sm" onclick="resetPrecios()">
                  <i class="bi bi-arrow-counterclockwise"></i> Restablecer
                </button>
              </div>
            </div>

            <!-- Modal: Nuevo producto -->
            <div class="modal-overlay" id="productoModal">
              <div class="modal" style="max-width:460px">
                <div class="modal-header">
                  <h3 class="modal-title">Nuevo producto</h3>
                  <button class="modal-close" onclick="SIGRA.closeModal('productoModal')"><i class="bi bi-x"></i></button>
                </div>
                <div style="display:flex;flex-direction:column;gap:12px">
                  <div>
                    <label class="form-label">Nombre</label>
                    <input type="text" id="pNombre" class="form-control" placeholder="Ej. Tacos al Pastor">
                  </div>
                  <div>
                    <label class="form-label">Categoría</label>
                    <select id="pCategoria" class="form-control"></select>
                  </div>
                  <div style="display:flex;gap:10px">
                    <div style="flex:1">
                      <label class="form-label">Precio (Q)</label>
                      <input type="number" step="0.01" min="0" id="pPrecio" class="form-control" placeholder="0.00">
                    </div>
                  </div>
                  <div>
                    <label class="form-label">Descripción</label>
                    <input type="text" id="pDescripcion" class="form-control" placeholder="Breve descripción">
                  </div>
                  <div>
                    <label class="form-label">URL imagen <span style="color:#9CA3AF;font-weight:400">(opcional)</span></label>
                    <input type="text" id="pImg" class="form-control" placeholder="https://...">
                  </div>
                  <div style="display:flex;gap:10px;margin-top:4px">
                    <button class="btn btn-outline" style="flex:1" onclick="SIGRA.closeModal('productoModal')">Cancelar</button>
                    <button class="btn btn-primary" style="flex:1" onclick="guardarProducto()">Guardar</button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Modal: Nueva categoría -->
            <div class="modal-overlay" id="categoriaModal">
              <div class="modal" style="max-width:420px">
                <div class="modal-header">
                  <h3 class="modal-title">Categorías</h3>
                  <button class="modal-close" onclick="SIGRA.closeModal('categoriaModal')"><i class="bi bi-x"></i></button>
                </div>
                <div style="display:flex;flex-direction:column;gap:12px">
                  <div id="catList" style="max-height:180px;overflow-y:auto;border:1px solid var(--mv-border, #E5E7EB);border-radius:8px;padding:8px"></div>
                  <hr>
                  <div style="font-size:13px;font-weight:600;color:var(--navy)">Agregar nueva</div>
                  <div>
                    <label class="form-label">Nombre visible</label>
                    <input type="text" id="cLabel" class="form-control" placeholder="Ej. Especialidades">
                  </div>
                  <div>
                    <label class="form-label">Clave (sin espacios)</label>
                    <input type="text" id="cKey" class="form-control" placeholder="ej. especialidades">
                  </div>
                  <div>
                    <label class="form-label">Ícono Bootstrap <span style="color:#9CA3AF;font-weight:400">(opcional)</span></label>
                    <input type="text" id="cIcon" class="form-control" placeholder="bi-star" value="bi-bookmark">
                  </div>
                  <div style="display:flex;gap:10px;margin-top:4px">
                    <button class="btn btn-outline" style="flex:1" onclick="SIGRA.closeModal('categoriaModal')">Cerrar</button>
                    <button class="btn btn-primary" style="flex:1" onclick="guardarCategoria()">Agregar</button>
                  </div>
                </div>
              </div>
            </div>

            <div id="catContent"></div>
          </main>

        </div>
      </main>
    </div>
  </div>

  <!-- Toast -->
  <div class="toast toast-success" id="successToast">
    <i class="bi bi-check-circle" style="font-size:18px"></i>
    <div>
      <div class="toast-title" id="toastTitle">Listo</div>
      <div class="toast-sub" id="toastSub"></div>
    </div>
  </div>

  <!-- ── Datos inyectados desde PHP al cliente ── -->
<?= sigra_bootstrap_script([
    'session' => $SIGRA_USER,
    'menu'    => $SIGRA_MENU,
]) ?>

  <script src="layout.js"></script>
  <script src="sigra-data.js"></script>
  <script>
    SIGRA.initLayout('Menú');
    const user = SIGRA.getUser();   // ya lee window.SIGRA_SESSION si PHP la inyectó
    const isAdmin = user && user.role === 'ADMIN';
    if (isAdmin) document.getElementById('adminToolbar').style.display = 'flex';

    function buildCats() {
      const dyn = SIGRA_DATA.getCategorias().map(c => ({
        key: c.key, label: c.label, icon: c.icon || 'bi-bookmark',
        color: { bg: c.bg || '#F3F4F6', fg: c.fg || '#374151' }
      }));
      return [{ key:'todos', label:'Todos', icon:'bi-grid-3x3-gap' }, ...dyn];
    }
    let CATS = buildCats();

    let activeTab = 'todos';
    let editingId = null;

    function renderTabs() {
      const menu = SIGRA_DATA.getMenu();
      document.getElementById('catTabs').innerHTML = CATS.map(c => {
        const n = c.key === 'todos' ? menu.length : menu.filter(m => m.categoria === c.key).length;
        const active = activeTab === c.key ? 'active' : '';
        return `<button class="mv-tab ${active}" onclick="setTab('${c.key}')">
          <i class="bi ${c.icon}"></i> ${c.label}
          <span class="count">${n}</span>
        </button>`;
      }).join('');
    }

    function setTab(k) {
      activeTab = k;
      renderTabs();
      renderContent();
    }

    function renderContent() {
      const menu = SIGRA_DATA.getMenu();
      const cats = CATS.filter(c => c.key !== 'todos' && (activeTab === 'todos' || activeTab === c.key));
      document.getElementById('catContent').innerHTML = cats.map(c => {
        const items = menu.filter(m => m.categoria === c.key);
        if (!items.length) return '';
        return `
          <section class="mv-cat">
            <div class="mv-cat-head">
              <div class="mv-cat-icon" style="background:${c.color.bg};color:${c.color.fg}">
                <i class="bi ${c.icon}"></i>
              </div>
              <h2>${c.label}</h2>
              <span class="mv-cat-count">${items.length} ${items.length === 1 ? 'platillo' : 'platillos'}</span>
            </div>
            <div class="mv-grid">
              ${items.map(it => renderCard(it)).join('')}
            </div>
          </section>`;
      }).join('');
    }

    function renderCard(p) {
      const editing = editingId === p.id;
      const fallbackIcon = p.categoria === 'bebidas' ? 'bi-cup-straw'
        : p.categoria === 'postres' ? 'bi-cake2'
        : p.categoria === 'entradas' ? 'bi-egg-fried' : 'bi-fire';
      const img = p.img
        ? `<img src="${p.img}" alt="${p.nombre}" loading="lazy"
             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
           <div class="mv-card-img-fallback" style="display:none"><i class="bi ${fallbackIcon}"></i></div>`
        : `<div class="mv-card-img-fallback" style="display:flex"><i class="bi ${fallbackIcon}"></i></div>`;
      const priceArea = editing
        ? `<input type="number" step="0.01" min="0" id="priceInput-${p.id}" value="${p.precio.toFixed(2)}">`
        : `<span>Q ${p.precio.toFixed(2)}</span>`;
      const actionBtn = isAdmin
        ? (editing
            ? `<button class="mv-card-btn save" title="Guardar" onclick="guardarPrecio(${p.id})"><i class="bi bi-check2"></i></button>`
            : `<button class="mv-card-edit-pencil" title="Editar precio" onclick="editarPrecio(${p.id})"><i class="bi bi-pencil"></i></button>
               <button class="mv-card-edit-pencil" title="Eliminar producto" style="color:#C62828" onclick="eliminarProducto(${p.id})"><i class="bi bi-trash"></i></button>`)
        : `<button class="mv-card-btn" title="Agregar"><i class="bi bi-plus"></i></button>`;
      return `
        <div class="mv-card">
          <div class="mv-card-img">${img}</div>
          <div class="mv-card-body">
            <div class="mv-card-name">${p.nombre}</div>
            <div class="mv-card-desc">${p.descripcion || ''}</div>
            <div class="mv-card-foot">
              <div class="mv-card-price">${priceArea}</div>
              <div style="display:flex;align-items:center">${actionBtn}</div>
            </div>
          </div>
        </div>`;
    }

    function editarPrecio(id) {
      editingId = id;
      renderContent();
      setTimeout(() => {
        const inp = document.getElementById(`priceInput-${id}`);
        if (inp) { inp.focus(); inp.select(); }
      }, 50);
    }

    function guardarPrecio(id) {
      const inp = document.getElementById(`priceInput-${id}`);
      const val = Number(inp.value);
      if (!val || val < 0) { inp.focus(); return; }
      SIGRA_DATA.updateMenuItem(id, { precio: val });
      editingId = null;
      renderContent();
      renderTabs();
      const p = SIGRA_DATA.getMenu().find(x => x.id === id);
      showToast('Precio actualizado', `${p.nombre} → Q ${val.toFixed(2)}`);
    }

    function eliminarProducto(id) {
      const p = SIGRA_DATA.getMenu().find(x => x.id === id);
      if (!p) return;
      if (!confirm(`¿Eliminar "${p.nombre}" del menú?`)) return;
      SIGRA_DATA.deleteMenuItem(id);
      renderTabs(); renderContent();
      showToast('Producto eliminado', p.nombre);
    }

    function openProductoModal() {
      const sel = document.getElementById('pCategoria');
      sel.innerHTML = SIGRA_DATA.getCategorias()
        .map(c => `<option value="${c.key}">${c.label}</option>`).join('');
      document.getElementById('pNombre').value = '';
      document.getElementById('pPrecio').value = '';
      document.getElementById('pDescripcion').value = '';
      document.getElementById('pImg').value = '';
      SIGRA.openModal('productoModal');
    }

    function guardarProducto() {
      const nombre = document.getElementById('pNombre').value.trim();
      const categoria = document.getElementById('pCategoria').value;
      const precio = Number(document.getElementById('pPrecio').value);
      const descripcion = document.getElementById('pDescripcion').value.trim();
      const img = document.getElementById('pImg').value.trim();
      if (!nombre || !categoria || !precio || precio <= 0) {
        alert('Completa nombre, categoría y un precio mayor a 0.');
        return;
      }
      SIGRA_DATA.addMenuItem({ nombre, categoria, precio, descripcion, img });
      SIGRA.closeModal('productoModal');
      renderTabs(); renderContent();
      showToast('Producto creado', nombre);
    }

    function openCategoriaModal() {
      renderCatList();
      document.getElementById('cLabel').value = '';
      document.getElementById('cKey').value = '';
      document.getElementById('cIcon').value = 'bi-bookmark';
      SIGRA.openModal('categoriaModal');
    }

    function renderCatList() {
      const cats = SIGRA_DATA.getCategorias();
      document.getElementById('catList').innerHTML = cats.length
        ? cats.map(c => `
          <div style="display:flex;align-items:center;justify-content:space-between;padding:6px 8px;border-bottom:1px solid #F3F4F6">
            <span style="font-size:13px"><i class="bi ${c.icon||'bi-bookmark'}" style="color:${c.fg||'#374151'};margin-right:6px"></i>${c.label}</span>
            <button class="mv-card-edit-pencil" title="Eliminar" style="color:#C62828" onclick="eliminarCategoria('${c.key}')"><i class="bi bi-trash"></i></button>
          </div>`).join('')
        : '<div style="padding:8px;color:#9CA3AF;font-size:12px">Sin categorías</div>';
    }

    function guardarCategoria() {
      const label = document.getElementById('cLabel').value.trim();
      const key = document.getElementById('cKey').value.trim().toLowerCase().replace(/\s+/g,'-');
      const icon = document.getElementById('cIcon').value.trim() || 'bi-bookmark';
      if (!label || !key) { alert('Completa nombre y clave.'); return; }
      const ok = SIGRA_DATA.addCategoria({ key, label, icon, bg:'#F3F4F6', fg:'#374151' });
      if (!ok) { alert('Esa clave ya existe.'); return; }
      CATS = buildCats();
      renderCatList(); renderTabs(); renderContent();
      showToast('Categoría creada', label);
    }

    function eliminarCategoria(key) {
      const tieneProductos = SIGRA_DATA.getMenu().some(p => p.categoria === key);
      if (tieneProductos) {
        if (!confirm('Esta categoría tiene productos. Si la eliminas, esos productos quedarán sin categoría visible. ¿Continuar?')) return;
      } else if (!confirm('¿Eliminar la categoría?')) return;
      SIGRA_DATA.deleteCategoria(key);
      CATS = buildCats();
      if (activeTab === key) activeTab = 'todos';
      renderCatList(); renderTabs(); renderContent();
    }

    function resetPrecios() {
      if (!confirm('¿Restablecer todos los precios a los valores iniciales? Esta acción reemplaza también los platos.')) return;
      SIGRA_DATA.resetMenu();
      editingId = null;
      renderContent();
      renderTabs();
      showToast('Menú restablecido', 'Precios y platos volvieron al valor original');
    }

    function showToast(title, sub) {
      document.getElementById('toastTitle').textContent = title;
      document.getElementById('toastSub').textContent = sub || '';
      SIGRA.showToast('successToast', 2500);
    }

    /* Init */
    renderTabs();
    renderContent();
    window.addEventListener('storage', (e) => {
      if (e.key === SIGRA_DATA.KEYS.MENU) { renderTabs(); renderContent(); }
    });
  </script>
</body>
</html>
