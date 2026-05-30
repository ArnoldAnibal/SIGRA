<?php
require_once __DIR__ . '/includes/bootstrap.php';
sigra_require_role(['ADMIN', 'MESERO']);
$SIGRA_USER = sigra_current_user();
$res = api_mesas(sigra_token()); $SIGRA_MESAS = $res["data"] ?? [];
$res2 = api_pedidos(sigra_token()); $SIGRA_PEDIDOS_MESA = array_values(array_filter($res2["data"]["data"] ?? $res2["data"] ?? [], fn($p) => !in_array($p["estado"], ["Entregado","Cancelado"])));
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIGRA — Pedidos Mesa</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="styles.css">
  <style>
    .pm-body { display: flex; flex-direction: column; height: calc(100vh - 64px); }
    .pm-inner { flex: 1; display: flex; overflow: hidden; }

    .tables-area { flex: 1; overflow-y: auto; padding: 24px; }
    .tables-grid  {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
      gap: 16px;
    }

    .mesa-card {
      padding: 16px; border-radius: var(--r-xl); border: 2px solid;
      cursor: pointer; text-align: left;
      transition: all .2s; min-height: 120px;
      display: flex; flex-direction: column; justify-content: space-between;
    }
    .mesa-card:hover { box-shadow: var(--shadow); transform: translateY(-1px); }
    .mesa-card.selected { outline: 3px solid #E8A020; outline-offset: 2px; }
    .mesa-num  { font-size: 18px; font-weight: 700; }
    .mesa-cap  { font-size: 12px; margin-top: 4px; display: flex; align-items: center; gap: 4px; }
    .mesa-state-badge {
      display: inline-block; padding: 2px 8px; border-radius: var(--r-full);
      font-size: 11px; font-weight: 600; margin-top: 8px;
    }
    .mesa-ped  { font-size: 11px; margin-top: 4px; }
    .mesa-extra { font-size: 11px; margin-top: 4px; line-height: 1.3; }

    .order-panel {
      width: 0; overflow: hidden;
      background: #fff; border-left: 1px solid var(--border);
      display: flex; flex-direction: column;
      transition: width .25s ease;
      flex-shrink: 0;
    }
    .order-panel.open { width: 460px; }

    .op-header {
      padding: 16px 20px;
      background: var(--navy);
      display: flex; align-items: center; justify-content: space-between;
      flex-shrink: 0;
    }
    .op-title { color: #fff; font-weight: 700; font-size: 15px; }
    .op-sub   { color: rgba(255,255,255,.6); font-size: 12px; margin-top: 2px; }
    .op-close { color: rgba(255,255,255,.6); font-size: 20px; transition: color .15s; background: none; border: none; cursor: pointer; }
    .op-close:hover { color: #fff; }

    .product-list { flex: 1; overflow-y: auto; padding: 12px; }
    .product-item {
      display: flex; align-items: center; justify-content: space-between;
      padding: 10px 12px; border-radius: var(--r); border: 1px solid var(--border-lt);
      background: #fff; margin-bottom: 8px; transition: all .15s;
    }
    .product-item.in-order { border-color: #FCD34D; background: #FFFBEB; }
    .product-name { font-size: 13.5px; font-weight: 500; color: #1F2937; }
    .product-desc { font-size: 12px; color: #9CA3AF; margin-top: 2px; }
    .product-notes {
      width: 100%; font-size: 12px; border: 1px solid var(--border);
      border-radius: 4px; padding: 4px 8px; margin-top: 6px; outline: none;
      background: #fff;
    }
    .product-price { font-size: 13.5px; font-weight: 600; color: var(--navy); white-space: nowrap; }

    .qty-ctrl { display: flex; align-items: center; gap: 6px; }
    .qty-btn  {
      width: 28px; height: 28px; border-radius: 50%;
      border: 1px solid var(--border); background: #fff; cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      font-size: 14px; transition: all .15s;
    }
    .qty-btn:hover { background: #F3F4F6; }
    .qty-btn.add { background: var(--amber); color: #fff; border-color: var(--amber); }
    .qty-btn.add:hover { opacity: .85; }
    .qty-val { width: 24px; text-align: center; font-size: 13.5px; font-weight: 600; }

    .op-footer { border-top: 1px solid var(--border); padding: 14px 16px; flex-shrink: 0; }
    .order-summary {
      background: #F9FAFB; border-radius: var(--r);
      padding: 10px 12px; max-height: 100px; overflow-y: auto; margin-bottom: 12px;
    }
    .order-summary-row { display: flex; justify-content: space-between; font-size: 12px; color: #6B7280; margin-bottom: 4px; }
    .total-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
    .total-items { font-size: 13px; color: #6B7280; }
    .total-amount { font-size: 19px; font-weight: 700; color: var(--navy); }

    /* Detail view inside modal */
    .detail-section { background: #F9FAFB; border-radius: var(--r); padding: 14px; margin-bottom: 14px; }
    .detail-label { font-size: 11px; text-transform: uppercase; letter-spacing: .04em; color: #9CA3AF; font-weight: 700; margin-bottom: 4px; }
    .detail-value { font-size: 14px; color: #1F2937; font-weight: 500; }
    .detail-items { list-style: none; padding: 0; margin: 0; }
    .detail-items li {
      display: flex; justify-content: space-between; padding: 6px 0;
      border-bottom: 1px dashed var(--border-lt); font-size: 13px;
    }
    .detail-items li:last-child { border-bottom: none; }

    @media (max-width: 768px) {
      .order-panel.open { position: fixed; inset: 0; width: 100%; z-index: 25; }
    }
  </style>
</head>
<body>
  <div class="app">
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <aside class="sidebar" id="sidebar"></aside>

    <div class="main-wrap" id="mainWrap">
      <header class="topbar" id="topbar"></header>

      <main class="page-content">
        <div class="pm-body">
          <div class="page-header">
            <div class="page-header-row">
              <div>
                <div class="page-title">Gestión de Pedidos en Mesa</div>
                <div class="page-sub" id="pageSub">Seleccione una mesa para gestionar el pedido</div>
              </div>
              <div style="display:flex;gap:8px;flex-wrap:wrap">
                <button class="btn btn-outline" id="btnNuevaMesa" onclick="openMesaModal()" style="display:none">
                  <i class="bi bi-plus-square"></i> Nueva mesa
                </button>
                <button class="btn btn-primary" id="btnNuevaReserva" onclick="openReservaSelector()">
                  <i class="bi bi-calendar-plus"></i> Nueva Reservación
                </button>
              </div>
            </div>
          </div>

          <div class="pm-inner">
            <div class="tables-area">
              <div class="tables-grid" id="tablesGrid"></div>
            </div>

            <!-- Order Panel (para LIBRE -> tomar pedido) -->
            <div class="order-panel" id="orderPanel">
              <div class="op-header">
                <div>
                  <div class="op-title" id="opTitle">Mesa —</div>
                  <div class="op-sub" id="opSub">— personas · —</div>
                </div>
                <button class="op-close" onclick="closeOrderPanel()"><i class="bi bi-x"></i></button>
              </div>

              <div class="tab-bar" id="catTabs"></div>
              <div class="product-list" id="productList"></div>

              <div class="op-footer">
                <div class="order-summary" id="orderSummary" style="display:none"></div>
                <div class="total-row">
                  <div class="total-items"><span id="totalItems">0</span> ítems</div>
                  <div class="total-amount">Q <span id="totalAmount">0.00</span></div>
                </div>
                <div style="display:flex;gap:8px">
                  <button class="btn btn-outline" style="flex:1" onclick="confirmCancel()">Cancelar</button>
                  <button class="btn btn-primary" style="flex:1" id="sendBtn" onclick="enviarCocina()" disabled>
                    <i class="bi bi-send"></i> Enviar a Cocina
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Mesa Manage Modal (para OCUPADA o RESERVADA) -->
  <div class="modal-overlay" id="manageModal">
    <div class="modal" style="max-width:480px">
      <div class="modal-header">
        <h3 class="modal-title" id="manageTitle">Mesa —</h3>
        <button class="modal-close" onclick="SIGRA.closeModal('manageModal')"><i class="bi bi-x"></i></button>
      </div>
      <div id="manageBody"></div>
      <div style="display:flex;gap:10px;margin-top:8px" id="manageActions"></div>
    </div>
  </div>

  <!-- Reserva Selector Modal -->
  <div class="modal-overlay" id="reservaSelModal">
    <div class="modal" style="max-width:380px">
      <div class="modal-header">
        <h3 class="modal-title">Nueva Reservación — Selecciona Mesa</h3>
        <button class="modal-close" onclick="SIGRA.closeModal('reservaSelModal')"><i class="bi bi-x"></i></button>
      </div>
      <p style="font-size:13px;color:#6B7280;margin-bottom:12px">Solo se muestran mesas libres.</p>
      <div id="reservaSelBody" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(90px,1fr));gap:8px;max-height:300px;overflow-y:auto"></div>
    </div>
  </div>

  <!-- Reserva Form Modal -->
  <div class="modal-overlay" id="reservaModal">
    <div class="modal" style="max-width:420px">
      <div class="modal-header">
        <h3 class="modal-title" id="reservaTitle">Reservar Mesa</h3>
        <button class="modal-close" onclick="SIGRA.closeModal('reservaModal')"><i class="bi bi-x"></i></button>
      </div>
      <div class="form-group">
        <label class="form-label">Nombre del cliente *</label>
        <input id="resNombre" class="form-control" placeholder="Juan Pérez">
      </div>
      <div class="form-group">
        <label class="form-label">Teléfono</label>
        <input id="resTel" class="form-control" placeholder="5555-5555">
      </div>
      <div style="display:flex;gap:10px">
        <div class="form-group" style="flex:1">
          <label class="form-label">Fecha *</label>
          <input id="resFecha" type="date" class="form-control">
        </div>
        <div class="form-group" style="flex:1">
          <label class="form-label">Hora *</label>
          <input id="resHora" type="time" class="form-control">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">N° de personas</label>
        <input id="resPersonas" type="number" min="1" class="form-control" placeholder="4">
      </div>
      <div class="form-error" id="resError" style="margin-bottom:12px">Completa los campos obligatorios.</div>
      <div style="display:flex;gap:10px">
        <button class="btn btn-outline" style="flex:1" onclick="SIGRA.closeModal('reservaModal')">Cancelar</button>
        <button class="btn btn-primary" style="flex:1" onclick="guardarReserva()">Guardar Reservación</button>
      </div>
    </div>
  </div>

  <!-- Nueva Mesa Modal -->
  <div class="modal-overlay" id="mesaModal">
    <div class="modal" style="max-width:380px">
      <div class="modal-header">
        <h3 class="modal-title">Nueva mesa</h3>
        <button class="modal-close" onclick="SIGRA.closeModal('mesaModal')"><i class="bi bi-x"></i></button>
      </div>
      <div style="display:flex;flex-direction:column;gap:12px">
        <div>
          <label class="form-label">Número de mesa</label>
          <input type="number" min="1" id="mNumero" class="form-control" placeholder="Auto si lo dejas vacío">
        </div>
        <div>
          <label class="form-label">Capacidad (personas)</label>
          <input type="number" min="1" id="mCapacidad" class="form-control" value="4">
        </div>
        <div style="display:flex;gap:10px;margin-top:4px">
          <button class="btn btn-outline" style="flex:1" onclick="SIGRA.closeModal('mesaModal')">Cancelar</button>
          <button class="btn btn-primary" style="flex:1" onclick="guardarMesa()">Crear mesa</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Cancel Modal -->
  <div class="modal-overlay" id="cancelModal">
    <div class="modal" style="max-width:360px">
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
        <div class="modal-icon" style="background:#FFEBEE;width:40px;height:40px;margin:0;flex-shrink:0">
          <i class="bi bi-exclamation-circle" style="color:#C62828;font-size:20px"></i>
        </div>
        <h3 class="modal-title">¿Cancelar pedido?</h3>
      </div>
      <p style="font-size:13px;color:#6B7280;margin-bottom:20px" id="cancelMsg"></p>
      <div style="display:flex;gap:10px">
        <button class="btn btn-outline" style="flex:1" onclick="SIGRA.closeModal('cancelModal')">No, continuar</button>
        <button class="btn btn-danger" style="flex:1" onclick="doCancel()">Sí, cancelar</button>
      </div>
    </div>
  </div>

  <!-- Success Toast -->
  <div class="toast toast-success" id="successToast">
    <i class="bi bi-check-circle" style="font-size:18px"></i>
    <div>
      <div class="toast-title" id="toastTitle">¡Listo!</div>
      <div class="toast-sub" id="toastSub"></div>
    </div>
  </div>

<?= sigra_bootstrap_script([
    'session' => $SIGRA_USER,
    'mesas' => $SIGRA_MESAS, 'pedidos' => $SIGRA_PEDIDOS_MESA
]) ?>

  <script src="layout.js"></script>
  <script src="sigra-data.js"></script>
  <script>
    SIGRA.initLayout('Pedidos Mesa');
    const user = SIGRA.getUser();
    const isAdmin = user && user.role === 'ADMIN';
    if (!isAdmin) {
      document.getElementById('btnNuevaReserva').style.display = 'none';
    } else {
      document.getElementById('btnNuevaMesa').style.display = 'inline-flex';
    }

    function openMesaModal() {
      document.getElementById('mNumero').value = '';
      document.getElementById('mCapacidad').value = 4;
      SIGRA.openModal('mesaModal');
    }

    function guardarMesa() {
      const numero = Number(document.getElementById('mNumero').value) || null;
      const capacidad = Number(document.getElementById('mCapacidad').value) || 4;
      if (numero && mesasList.some(m => m.numero === numero)) {
        alert('Ya existe una mesa con ese número.');
        return;
      }
      SIGRA_DATA.addMesa({ numero, capacidad });
      SIGRA.closeModal('mesaModal');
      refreshMesas();
      document.getElementById('toastTitle').textContent = 'Mesa creada';
      document.getElementById('toastSub').textContent = `Capacidad: ${capacidad}`;
      SIGRA.showToast('successToast', 2500);
    }

    function eliminarMesa(id) {
      const m = mesasList.find(x => x.id === id);
      if (!m) return;
      if (m.estado !== 'LIBRE') {
        alert('Solo puedes eliminar mesas que estén LIBRES.');
        return;
      }
      if (!confirm(`¿Eliminar Mesa ${m.numero}?`)) return;
      SIGRA_DATA.deleteMesa(id);
      if (selectedMesa && selectedMesa.id === id) { selectedMesa = null; closeOrderPanel(); }
      refreshMesas();
      document.getElementById('toastTitle').textContent = 'Mesa eliminada';
      document.getElementById('toastSub').textContent = `Mesa ${m.numero}`;
      SIGRA.showToast('successToast', 2500);
    }

    const estadoConfig = {
      LIBRE:     { bg:'#E8F5E9', text:'#2E7D32', border:'#A5D6A7' },
      OCUPADA:   { bg:'#FFEBEE', text:'#C62828', border:'#EF9A9A' },
      RESERVADA: { bg:'#FFF8E1', text:'#F57C00', border:'#FFE082' },
    };

    const catLabels = { bebidas:'Bebidas', platos:'Platos Fuertes', entradas:'Entradas', postres:'Postres' };

    /* ── State (persisted via SIGRA_DATA) ── */
    let mesasList = SIGRA_DATA.getMesas();
    let selectedMesa = null;
    let orderItems = [];
    let activeCategory = 'platos';
    let reservaTargetId = null;

    function getMenuByCategory() {
      const all = SIGRA_DATA.getMenu();
      const grouped = { bebidas:[], platos:[], entradas:[], postres:[] };
      all.forEach(p => { if (grouped[p.categoria]) grouped[p.categoria].push(p); });
      return grouped;
    }

    function refreshMesas() {
      mesasList = SIGRA_DATA.getMesas();
      renderTables();
      updatePageSub();
    }

    function updatePageSub() {
      const ocupadas = mesasList.filter(m => m.estado === 'OCUPADA').length;
      const reservadas = mesasList.filter(m => m.estado === 'RESERVADA').length;
      const libres = mesasList.filter(m => m.estado === 'LIBRE').length;
      document.getElementById('pageSub').textContent =
        `${libres} libres · ${ocupadas} ocupadas · ${reservadas} reservadas`;
    }

    /* ── Render tables grid ── */
    function renderTables() {
      document.getElementById('tablesGrid').innerHTML = mesasList.map(m => {
        const cfg = estadoConfig[m.estado];
        const isSel = selectedMesa && selectedMesa.id === m.id;
        const bg     = isSel ? '#1A2E4A' : cfg.bg;
        const border = isSel ? '#1A2E4A' : cfg.border;
        const numC   = isSel ? '#fff' : cfg.text;
        const capC   = isSel ? 'rgba(255,255,255,.7)' : cfg.text;
        const extraC = isSel ? 'rgba(255,255,255,.65)' : '#6B7280';
        const statBg = isSel ? 'rgba(255,255,255,.2)' : cfg.bg;
        const statC  = isSel ? '#fff' : cfg.text;
        const selCls = isSel ? 'selected' : '';
        let extra = '';
        if (m.estado === 'OCUPADA' && m.pedido) {
          const totalItems = (m.pedido.items || []).reduce((a, i) => a + i.cantidad, 0);
          extra = `<div class="mesa-extra" style="color:${extraC}">
            <i class="bi bi-bag-check" style="font-size:11px"></i> ${m.pedido.id} · ${totalItems} ítems
          </div>`;
        } else if (m.estado === 'RESERVADA' && m.reserva) {
          extra = `<div class="mesa-extra" style="color:${extraC}">
            <i class="bi bi-person" style="font-size:11px"></i> ${m.reserva.nombre}<br>
            <i class="bi bi-clock" style="font-size:11px"></i> ${m.reserva.fecha} ${m.reserva.hora}
          </div>`;
        }
        const adminDelBtn = (isAdmin && m.estado === 'LIBRE')
          ? `<span onclick="event.stopPropagation();eliminarMesa(${m.id})" title="Eliminar mesa" style="position:absolute;top:6px;right:6px;width:22px;height:22px;border-radius:50%;background:rgba(198,40,40,.12);color:#C62828;display:inline-flex;align-items:center;justify-content:center;font-size:11px;cursor:pointer"><i class="bi bi-trash"></i></span>`
          : '';
        return `
          <button class="mesa-card ${selCls}"
            style="background:${bg};border-color:${border};position:relative"
            onclick="onMesaClick(${m.id})">
            ${adminDelBtn}
            <div>
              <div class="mesa-num" style="color:${numC}">Mesa ${m.numero}</div>
              <div class="mesa-cap" style="color:${capC}">
                <i class="bi bi-people" style="font-size:12px"></i>${m.capacidad} personas
              </div>
            </div>
            <div>
              <span class="mesa-state-badge" style="background:${statBg};color:${statC};border:1px solid ${isSel?'rgba(255,255,255,.3)':cfg.border}">
                ${m.estado}
              </span>
              ${extra}
            </div>
          </button>`;
      }).join('');
    }

    /* ── Click handler — depende del estado ── */
    function onMesaClick(id) {
      const m = mesasList.find(x => x.id === id);
      if (!m) return;
      if (m.estado === 'LIBRE') {
        openOrderPanel(m);
      } else {
        openManageModal(m);
      }
    }

    /* ── Manage Modal (OCUPADA / RESERVADA) ── */
    function openManageModal(m) {
      document.getElementById('manageTitle').textContent =
        `Mesa ${m.numero} · ${m.estado}`;
      const body = document.getElementById('manageBody');
      const actions = document.getElementById('manageActions');

      if (m.estado === 'OCUPADA' && m.pedido) {
        const total = m.pedido.items.reduce((a, i) => a + i.precio * i.cantidad, 0);
        body.innerHTML = `
          <div class="detail-section">
            <div class="detail-label">Pedido</div>
            <div class="detail-value">${m.pedido.id}</div>
            <div style="font-size:12px;color:#6B7280;margin-top:4px">
              Atendido por: <strong>${m.pedido.mesero || 'N/D'}</strong>
              · ${m.pedido.hora || ''}
            </div>
          </div>
          <div class="detail-section">
            <div class="detail-label">Productos</div>
            <ul class="detail-items">
              ${m.pedido.items.map(i => `
                <li>
                  <span>${i.cantidad}x ${i.nombre}${i.notas ? ` <em style="color:#9CA3AF">(${i.notas})</em>` : ''}</span>
                  <strong>Q ${(i.precio * i.cantidad).toFixed(2)}</strong>
                </li>`).join('')}
            </ul>
            <div style="display:flex;justify-content:space-between;padding-top:10px;margin-top:8px;border-top:1px solid var(--border);font-weight:700;color:var(--navy)">
              <span>TOTAL</span><span>Q ${total.toFixed(2)}</span>
            </div>
          </div>`;
        actions.innerHTML = `
          <button class="btn btn-outline" style="flex:1" onclick="SIGRA.closeModal('manageModal')">Cerrar</button>
          ${isAdmin ? `<button class="btn btn-danger" style="flex:1" onclick="liberarMesa(${m.id})">
            <i class="bi bi-unlock"></i> Marcar como Libre
          </button>` : ''}`;

      } else if (m.estado === 'RESERVADA' && m.reserva) {
        body.innerHTML = `
          <div class="detail-section">
            <div class="detail-label">Cliente</div>
            <div class="detail-value">${m.reserva.nombre}</div>
            ${m.reserva.telefono ? `<div style="font-size:12px;color:#6B7280;margin-top:4px"><i class="bi bi-telephone"></i> ${m.reserva.telefono}</div>` : ''}
          </div>
          <div class="detail-section">
            <div class="detail-label">Fecha y hora</div>
            <div class="detail-value">${m.reserva.fecha} a las ${m.reserva.hora}</div>
            <div style="font-size:12px;color:#6B7280;margin-top:4px">
              <i class="bi bi-people"></i> ${m.reserva.personas || m.capacidad} personas
            </div>
          </div>`;
        actions.innerHTML = `
          <button class="btn btn-outline" style="flex:1" onclick="SIGRA.closeModal('manageModal')">Cerrar</button>
          ${isAdmin ? `
          <button class="btn btn-amber" style="flex:1" onclick="convertirAOcupada(${m.id})">
            <i class="bi bi-check2-circle"></i> Cliente Llegó
          </button>
          <button class="btn btn-danger" style="flex:1" onclick="liberarMesa(${m.id})">
            <i class="bi bi-x-circle"></i> Liberar
          </button>` : ''}`;
      }

      SIGRA.openModal('manageModal');
    }

    function liberarMesa(id) {
      SIGRA_DATA.updateMesa(id, { estado: 'LIBRE', pedido: null, reserva: null });
      SIGRA.closeModal('manageModal');
      refreshMesas();
      showToast('Mesa liberada', `Mesa ${id} disponible`);
    }

    function convertirAOcupada(id) {
      const m = mesasList.find(x => x.id === id);
      if (!m) return;
      SIGRA.closeModal('manageModal');
      const mesa = { ...m, estado: 'LIBRE', reserva: null };
      openOrderPanel(mesa);
    }

    /* ── Reserva ── */
    function openReservaSelector() {
      const libres = mesasList.filter(m => m.estado === 'LIBRE');
      const body = document.getElementById('reservaSelBody');
      if (libres.length === 0) {
        body.innerHTML = '<div style="grid-column:1/-1;text-align:center;color:#9CA3AF;font-size:13px;padding:20px">No hay mesas libres.</div>';
      } else {
        body.innerHTML = libres.map(m => `
          <button class="btn btn-outline" style="height:auto;padding:10px 6px;display:flex;flex-direction:column;gap:2px;font-size:12px"
            onclick="openReservaForm(${m.id})">
            <strong style="font-size:14px">Mesa ${m.numero}</strong>
            <span style="font-size:11px;color:#6B7280">${m.capacidad} pers</span>
          </button>`).join('');
      }
      SIGRA.openModal('reservaSelModal');
    }

    function openReservaForm(mesaId) {
      reservaTargetId = mesaId;
      const m = mesasList.find(x => x.id === mesaId);
      SIGRA.closeModal('reservaSelModal');
      document.getElementById('reservaTitle').textContent = `Reservar Mesa ${m.numero}`;
      document.getElementById('resNombre').value = '';
      document.getElementById('resTel').value = '';
      document.getElementById('resFecha').valueAsDate = new Date();
      document.getElementById('resHora').value = '19:00';
      document.getElementById('resPersonas').value = m.capacidad;
      document.getElementById('resError').style.display = 'none';
      SIGRA.openModal('reservaModal');
    }

    function guardarReserva() {
      const nombre = document.getElementById('resNombre').value.trim();
      const fecha  = document.getElementById('resFecha').value;
      const hora   = document.getElementById('resHora').value;
      if (!nombre || !fecha || !hora) {
        document.getElementById('resError').style.display = 'block';
        return;
      }
      const reserva = {
        nombre,
        telefono: document.getElementById('resTel').value.trim(),
        fecha, hora,
        personas: Number(document.getElementById('resPersonas').value) || null,
      };
      SIGRA_DATA.updateMesa(reservaTargetId, { estado: 'RESERVADA', reserva, pedido: null });
      SIGRA.closeModal('reservaModal');
      refreshMesas();
      showToast('Reservación guardada', `${nombre} · ${fecha} ${hora}`);
    }

    /* ── Order Panel (LIBRE → tomar pedido) ── */
    function openOrderPanel(m) {
      selectedMesa = m;
      orderItems = [];
      document.getElementById('orderPanel').classList.add('open');
      document.getElementById('opTitle').textContent = `Mesa ${m.numero}`;
      document.getElementById('opSub').textContent = `${m.capacidad} personas · Nuevo pedido`;
      renderCatTabs();
      renderProducts();
      updateFooter();
      renderTables();
    }

    function closeOrderPanel() {
      selectedMesa = null;
      orderItems = [];
      document.getElementById('orderPanel').classList.remove('open');
      renderTables();
    }

    function renderCatTabs() {
      document.getElementById('catTabs').innerHTML = Object.keys(catLabels).map(c => `
        <button class="tab-btn ${activeCategory===c?'active':''}" onclick="setCategory('${c}')">
          ${catLabels[c]}
        </button>`).join('');
    }

    function setCategory(cat) {
      activeCategory = cat;
      renderCatTabs();
      renderProducts();
    }

    function renderProducts() {
      const products = getMenuByCategory()[activeCategory] || [];
      document.getElementById('productList').innerHTML = products.map(p => {
        const inOrder = orderItems.find(i => i.id === p.id);
        return `
          <div class="product-item ${inOrder ? 'in-order' : ''}" id="product-${p.id}">
            <div style="flex:1;min-width:0">
              <div class="product-name">${p.nombre}</div>
              <div class="product-desc">${p.descripcion || ''}</div>
              ${inOrder ? `<input class="product-notes" placeholder="Notas (opcional)"
                value="${inOrder.notas}"
                onchange="updateNotas(${p.id}, this.value)">` : ''}
            </div>
            <div style="display:flex;align-items:center;gap:8px;margin-left:12px;flex-shrink:0">
              <span class="product-price">Q ${p.precio.toFixed(2)}</span>
              ${inOrder ? `
                <div class="qty-ctrl">
                  <button class="qty-btn" onclick="updateQty(${p.id},-1)"><i class="bi bi-dash"></i></button>
                  <span class="qty-val">${inOrder.cantidad}</span>
                  <button class="qty-btn add" onclick="updateQty(${p.id},1)"><i class="bi bi-plus"></i></button>
                </div>` : `
                <button class="qty-btn add" style="width:32px;height:32px" onclick="addItem(${p.id})">
                  <i class="bi bi-plus"></i>
                </button>`}
            </div>
          </div>`;
      }).join('');
    }

    function addItem(id) {
      const all = SIGRA_DATA.getMenu();
      const product = all.find(p => p.id === id);
      if (!product) return;
      const existing = orderItems.find(i => i.id === id);
      if (existing) {
        existing.cantidad++;
      } else {
        orderItems.push({ ...product, cantidad: 1, notas: '' });
      }
      renderProducts();
      updateFooter();
    }

    function updateQty(id, delta) {
      const idx = orderItems.findIndex(i => i.id === id);
      if (idx === -1) return;
      orderItems[idx].cantidad += delta;
      if (orderItems[idx].cantidad <= 0) orderItems.splice(idx, 1);
      renderProducts();
      updateFooter();
    }

    function updateNotas(id, notas) {
      const item = orderItems.find(i => i.id === id);
      if (item) item.notas = notas;
    }

    function updateFooter() {
      const total = orderItems.reduce((a, i) => a + i.precio * i.cantidad, 0);
      const totalQty = orderItems.reduce((a, i) => a + i.cantidad, 0);
      document.getElementById('totalItems').textContent = totalQty;
      document.getElementById('totalAmount').textContent = total.toFixed(2);
      document.getElementById('sendBtn').disabled = orderItems.length === 0;

      const summary = document.getElementById('orderSummary');
      if (orderItems.length > 0) {
        summary.style.display = 'block';
        summary.innerHTML = orderItems.map(i =>
          `<div class="order-summary-row">
            <span>${i.cantidad}x ${i.nombre}</span>
            <span>Q ${(i.precio*i.cantidad).toFixed(2)}</span>
          </div>`
        ).join('');
      } else {
        summary.style.display = 'none';
      }
    }

    function enviarCocina() {
      if (!selectedMesa || orderItems.length === 0) return;
      const totalQty = orderItems.reduce((a, i) => a + i.cantidad, 0);
      const total = orderItems.reduce((a, i) => a + i.precio * i.cantidad, 0);
      const pedido = {
        id: SIGRA_DATA.nextPedidoId(),
        mesero: user ? user.name : 'Sistema',
        hora: new Date().toLocaleTimeString('es-GT', { hour: '2-digit', minute: '2-digit' }),
        items: orderItems.map(i => ({ ...i })),
        total,
      };
      SIGRA_DATA.updateMesa(selectedMesa.id, { estado: 'OCUPADA', pedido, reserva: null });
      SIGRA_DATA.addCocinaPedido({
        id: pedido.id,
        mesa: `Mesa ${selectedMesa.numero}`,
        tipo: 'MESA',
        estado: 'RECIBIDO',
        items: pedido.items.map(i => ({ nombre: i.nombre, cantidad: i.cantidad, notas: i.notas })),
      });
      showToast('Pedido enviado a cocina', `Mesa ${selectedMesa.numero} · ${totalQty} ítems`);
      setTimeout(() => { closeOrderPanel(); refreshMesas(); }, 1500);
    }

    function confirmCancel() {
      if (!selectedMesa) return;
      document.getElementById('cancelMsg').textContent =
        `Se perderán todos los ítems agregados para Mesa ${selectedMesa.numero}.`;
      SIGRA.openModal('cancelModal');
    }

    function doCancel() {
      SIGRA.closeModal('cancelModal');
      closeOrderPanel();
    }

    function showToast(title, sub) {
      document.getElementById('toastTitle').textContent = title;
      document.getElementById('toastSub').textContent = sub || '';
      SIGRA.showToast('successToast', 2500);
    }

    /* ── Init ── */
    refreshMesas();

    /* Sincronizar entre pestañas */
    window.addEventListener('storage', (e) => {
      if (e.key === SIGRA_DATA.KEYS.MESAS) refreshMesas();
    });
  </script>
</body>
</html>
