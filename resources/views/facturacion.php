<?php
require_once __DIR__ . '/includes/bootstrap.php';
sigra_require_role(['ADMIN', 'CAJERO']);
$SIGRA_USER = sigra_current_user();
$res = api_pedidos(sigra_token()); $SIGRA_PEDIDOS_PENDIENTES = array_values(array_filter($res["data"]["data"] ?? $res["data"] ?? [], fn($p) => $p["estado"] === "Preparado"));
$resE = api_clientes(sigra_token());
$SIGRA_EMPRESAS = array_values(array_filter($resE["data"]["data"] ?? $resE["data"] ?? [], fn($c) => $c["tipo_cliente"] === "Empresa"));
$resEmp = api_empleados(sigra_token());
$empleadosMap = [];
foreach ($resEmp['data'] as $e) { $empleadosMap[$e['id']] = $e['nombre']; }
$resMenuRaw = api_request('GET', '/productos-menu', [], sigra_token());
$menuRaw = $resMenuRaw['data']['data'] ?? $resMenuRaw['data'] ?? [];
$menuMap = [];
foreach ($menuRaw as $mp) { $menuMap[$mp['id_producto']] = $mp['nombre']; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIGRA — Facturación FEL</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="styles.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.0/jspdf.plugin.autotable.min.js"></script>
  <style>
    .fel-body { padding: 24px; overflow-y: auto; height: calc(100vh - 64px - 73px); }
    .fel-inner { max-width: 1200px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px; }

    /* Tabs facturación / historial */
    .fel-tabs { display: flex; gap: 4px; border-bottom: 2px solid var(--border); margin-bottom: 4px; }
    .fel-tab {
      padding: 10px 18px; font-size: 13.5px; font-weight: 600;
      background: none; border: none; cursor: pointer;
      color: #6B7280; border-bottom: 2px solid transparent; margin-bottom: -2px;
    }
    .fel-tab:hover { color: var(--navy); }
    .fel-tab.active { color: var(--navy); border-bottom-color: var(--amber); }

    /* Order rows */
    .order-table { width: 100%; border-collapse: collapse; }
    .order-table th {
      text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .04em;
      color: #6B7280; font-weight: 700; padding: 12px 16px;
      background: #F9FAFB; border-bottom: 1px solid var(--border-lt);
    }
    .order-table td { padding: 14px 16px; border-bottom: 1px solid var(--border-lt); font-size: 13.5px; }
    .order-row { cursor: pointer; transition: background .12s; }
    .order-row:hover { background: #FAFBFC; }
    .order-row.expanded { background: #FFFBF0; }
    .chev { transition: transform .15s; color: #9CA3AF; }
    .chev.up { transform: rotate(180deg); }

    .detail-row td { background: #FAFBFC; padding: 0 !important; }
    .detail-card { padding: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
    @media (max-width: 900px) { .detail-card { grid-template-columns: 1fr; } }

    .det-items { background: #fff; border-radius: var(--r); padding: 14px; border: 1px solid var(--border-lt); }
    .det-items h4 { font-size: 13px; font-weight: 700; color: var(--navy); margin-bottom: 10px; }
    .det-items-row {
      display: flex; justify-content: space-between; padding: 6px 0;
      font-size: 13px; border-bottom: 1px dashed var(--border-lt);
    }
    .det-items-row:last-of-type { border-bottom: none; }
    .det-iva-row {
      display: flex; justify-content: space-between; font-size: 13px; color: #6B7280; padding: 3px 0;
    }
    .det-iva-total {
      display: flex; justify-content: space-between; font-size: 16px;
      font-weight: 700; color: var(--navy);
      padding-top: 8px; margin-top: 8px; border-top: 1px solid var(--border);
    }

    /* CF/NIT/Cred radio cards */
    .receptor-tabs { display: flex; gap: 6px; margin-bottom: 12px; }
    .receptor-tab {
      flex: 1; padding: 10px 8px; text-align: center;
      border: 2px solid var(--border); border-radius: var(--r);
      background: #fff; cursor: pointer; font-size: 12px; font-weight: 600;
      color: #6B7280; transition: all .15s;
    }
    .receptor-tab:hover { border-color: #C7D2FE; }
    .receptor-tab.active { border-color: var(--navy); background: #EFF6FF; color: var(--navy); }
    .receptor-tab.cred.active { border-color: #B45309; background: #FFFBEB; color: #B45309; }

    .offline-banner {
      background: var(--red); padding: 10px 24px;
      display: flex; align-items: center; gap: 12px; font-size: 13px; color: #fff;
    }
    .empty-state {
      display: flex; flex-direction: column; align-items: center;
      justify-content: center; padding: 48px; color: #9CA3AF; text-align: center;
    }
    .empty-state i { font-size: 36px; margin-bottom: 10px; opacity: .4; }

    .fact-actions { display: flex; gap: 8px; flex-wrap: wrap; }
    .fact-actions .btn { flex: 1; min-width: 130px; }

    /* Historial */
    .hist-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
    .hist-table th, .hist-table td { padding: 12px 16px; border-bottom: 1px solid var(--border-lt); text-align: left; }
    .hist-table th { background: #F9FAFB; font-size: 11px; text-transform: uppercase; color: #6B7280; }
    .hist-uuid { font-family: 'SF Mono', Menlo, monospace; font-size: 11px; color: #6B7280; }
  </style>
</head>
<body>
  <div class="app">
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <aside class="sidebar" id="sidebar"></aside>

    <div class="main-wrap" id="mainWrap">
      <header class="topbar" id="topbar"></header>

      <main class="page-content" style="display:flex;flex-direction:column">
        <div class="page-header">
          <div class="page-header-row">
            <div>
              <div class="page-title" style="display:flex;align-items:center;gap:10px">
                Emisión FEL · Punto de Venta
                <span class="badge badge-success" id="felBadge">SAT Activo</span>
              </div>
              <div class="page-sub">Facturación Electrónica en Línea</div>
            </div>
            <button class="btn btn-outline btn-sm" id="felToggle" onclick="toggleFelMode()">
              <i class="bi bi-check" id="felIcon"></i>
              <span id="felLabel">FEL Online</span>
            </button>
          </div>
        </div>

        <div class="offline-banner" id="offlineBanner" style="display:none">
          <i class="bi bi-wifi-off" style="font-size:18px;flex-shrink:0"></i>
          <div><strong>Modo Contingencia Activo</strong> — Sin conexión con SAT. Las facturas se guardarán localmente.</div>
        </div>

        <div class="fel-body">
          <div class="fel-inner">

            <!-- Tabs -->
            <div class="fel-tabs">
              <button class="fel-tab active" data-tab="pendientes" onclick="setTab('pendientes')">
                <i class="bi bi-receipt"></i> Pendientes de Facturar
              </button>
              <button class="fel-tab" data-tab="historial" onclick="setTab('historial')">
                <i class="bi bi-archive"></i> Historial
              </button>
            </div>

            <!-- TAB: Pendientes -->
            <div id="tabPendientes">
              <div class="card" style="overflow:hidden">
                <div class="card-header" style="display:flex;justify-content:space-between;align-items:center">
                  <span class="card-title">Pedidos listos para facturar</span>
                  <span style="font-size:12px;color:#9CA3AF" id="queueCount">0 pedidos</span>
                </div>
                <div style="overflow-x:auto">
                  <table class="order-table">
                    <thead>
                      <tr>
                        <th style="width:30px"></th>
                        <th>Pedido</th>
                        <th>Mesa / Cliente</th>
                        <th>Ítems</th>
                        <th>Mesero</th>
                        <th class="r">Total</th>
                      </tr>
                    </thead>
                    <tbody id="orderTableBody"></tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- TAB: Historial -->
            <div id="tabHistorial" style="display:none">
              <div class="card" style="overflow:hidden">
                <div class="card-header" style="display:flex;justify-content:space-between;align-items:center">
                  <span class="card-title">Facturas emitidas</span>
                  <span style="font-size:12px;color:#9CA3AF" id="histCount">0 facturas</span>
                </div>
                <div style="overflow-x:auto">
                  <table class="hist-table">
                    <thead>
                      <tr>
                        <th>Fecha</th>
                        <th>Pedido</th>
                        <th>Receptor</th>
                        <th>UUID SAT</th>
                        <th>Tipo</th>
                        <th class="r">Total</th>
                        <th class="r">Acciones</th>
                      </tr>
                    </thead>
                    <tbody id="histTableBody"></tbody>
                  </table>
                </div>
              </div>
            </div>

          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Success Modal -->
  <div class="modal-overlay" id="successModal">
    <div class="modal modal-lg" style="text-align:center">
      <div class="modal-icon" style="background:#E8F5E9;width:64px;height:64px">
        <i class="bi bi-check" style="color:#2E7D32;font-size:32px"></i>
      </div>
      <h3 style="font-size:20px;font-weight:700;color:var(--navy);margin-bottom:6px">¡Factura Emitida!</h3>
      <p style="font-size:13px;color:#6B7280;margin-bottom:20px" id="successSub">Autorizada por SAT · Guatemala</p>

      <div style="background:#F9FAFB;border-radius:var(--r-xl);padding:16px;text-align:left;margin-bottom:20px">
        <p style="font-size:11px;color:#9CA3AF;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">UUID SAT</p>
        <p class="font-mono" style="font-weight:700;color:#1F2937;font-size:13px;word-break:break-all" id="uuidDisplay"></p>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:12px;font-size:12px;color:#6B7280" id="invoiceDetails"></div>
      </div>

      <div style="display:flex;gap:10px;margin-bottom:10px">
        <button class="btn btn-outline" style="flex:1" onclick="imprimirUltima()"><i class="bi bi-printer"></i> Imprimir</button>
        <button class="btn btn-outline" style="flex:1" onclick="descargarUltimaPdf()"><i class="bi bi-download"></i> Descargar PDF</button>
      </div>
      <button class="btn btn-primary" style="width:100%;height:44px" onclick="resetInvoice()">Cerrar</button>
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

<?= sigra_bootstrap_script([
    'session' => $SIGRA_USER,
    'pedidos_pendientes' => $SIGRA_PEDIDOS_PENDIENTES,
    'empresas' => $SIGRA_EMPRESAS,
    'empleados_map' => $empleadosMap,
    'menu_map' => $menuMap,
]) ?>

  <script src="layout.js"></script>
  <script src="sigra-data.js"></script>
  <script src="sigra-pdf.js"></script>
  <script>
    SIGRA.initLayout('Facturación FEL');
    const user = SIGRA.getUser();
    const isAdmin = user && user.role === 'ADMIN';

    const IVA_RATE = 0;
    let felOffline = false;
    let expandedOrderId = null;
    let receptorTipo = 'NIT'; // 'NIT' | 'CF' | 'CRED'
    let selectedEmpresaId = null;
    let lastFactura = null;
    let currentTab = 'pendientes';

    function setTab(t) {
      currentTab = t;
      document.querySelectorAll('.fel-tab').forEach(b => b.classList.toggle('active', b.dataset.tab === t));
      document.getElementById('tabPendientes').style.display = t === 'pendientes' ? '' : 'none';
      document.getElementById('tabHistorial').style.display  = t === 'historial'  ? '' : 'none';
      if (t === 'historial') renderHistorial();
    }

    function toggleFelMode() {
      felOffline = !felOffline;
      const banner = document.getElementById('offlineBanner');
      const label  = document.getElementById('felLabel');
      const icon   = document.getElementById('felIcon');
      const btn    = document.getElementById('felToggle');
      const badge  = document.getElementById('felBadge');
      banner.style.display = felOffline ? 'flex' : 'none';
      label.textContent = felOffline ? 'Simular: FEL Offline' : 'FEL Online';
      icon.className    = felOffline ? 'bi bi-wifi-off' : 'bi bi-check';
      btn.style.background = felOffline ? '#FFEBEE' : '';
      btn.style.color      = felOffline ? '#B71C1C' : '';
      badge.textContent = felOffline ? 'SAT Offline' : 'SAT Activo';
      badge.className   = felOffline ? 'badge badge-danger' : 'badge badge-success';
    }

    function getPendientes() {
    const boot = (() => {
        try { return JSON.parse(document.getElementById('sigra-bootstrap').textContent.trim() || '{}'); }
        catch { return {}; }
    })();
    const pedidos = boot.pedidos_pendientes || [];
    return pedidos.map(p => ({
        id: 'PED-' + String(p.id_pedido).padStart(3, '0'),
        mesaId: p.id_mesa,
        ubicacion: p.id_mesa ? 'Mesa ' + p.id_mesa : p.tipo,
       mesero: boot.empleados_map?.[p.id_empleado] ?? ('Empleado #' + p.id_empleado),
        total: Number(p.total),
        items: (p.detalles || []).map(d => ({
            nombre: boot.menu_map?.[d.id_producto] ?? ('Producto #' + d.id_producto),
            cantidad: d.cantidad,
            precio: Number(d.subtotal) / d.cantidad,
            notas: d.notas || '',
        })),
    }));
}

    function renderQueue() {
      const pendientes = getPendientes();
      document.getElementById('queueCount').textContent = `${pendientes.length} pedido${pendientes.length !== 1 ? 's' : ''}`;
      const body = document.getElementById('orderTableBody');
      if (pendientes.length === 0) {
        body.innerHTML = `<tr><td colspan="6"><div class="empty-state">
          <i class="bi bi-receipt"></i><p>No hay pedidos pendientes de facturar.</p>
        </div></td></tr>`;
        return;
      }
      body.innerHTML = pendientes.map(p => {
        const isExp = expandedOrderId === p.id;
        const totalItems = p.items.reduce((a, i) => a + i.cantidad, 0);
        const row = `
          <tr class="order-row ${isExp ? 'expanded' : ''}" onclick="toggleExpand('${p.id}')">
            <td><i class="bi bi-chevron-down chev ${isExp ? 'up' : ''}"></i></td>
            <td><strong style="color:var(--navy)">${p.id}</strong></td>
            <td>${p.ubicacion}</td>
            <td>${totalItems} ítems</td>
            <td style="color:#6B7280">${p.mesero || '—'}</td>
            <td class="r"><strong>Q ${p.total.toFixed(2)}</strong></td>
          </tr>`;
        const detail = isExp ? renderDetail(p) : '';
        return row + detail;
      }).join('');
    }

    function renderDetail(p) {
      const subtotal = p.total / (1 + IVA_RATE);
      const iva = p.total - subtotal;
      const empresas = SIGRA_DATA.getEmpresas();
      const empOptions = empresas.map(e => {
        const disp = e.limiteCredito - e.utilizado;
        return `<option value="${e.id}" ${selectedEmpresaId === e.id ? 'selected' : ''}>
          ${e.nombre} (Q ${disp.toFixed(2)} disp.)
        </option>`;
      }).join('');
      const adminBlocked = !isAdmin && receptorTipo === 'CRED';
      return `
        <tr class="detail-row"><td colspan="6">
          <div class="detail-card" onclick="event.stopPropagation()">
            <!-- Items + IVA -->
            <div class="det-items">
              <h4><i class="bi bi-list-ul"></i> Detalle del pedido</h4>
              ${p.items.map(i => `
                <div class="det-items-row">
                  <span>${i.cantidad}x ${i.nombre}${i.notas ? ` <em style="color:#9CA3AF">(${i.notas})</em>` : ''}</span>
                  <strong>Q ${(i.precio * i.cantidad).toFixed(2)}</strong>
                </div>`).join('')}
              <div style="margin-top:14px;padding-top:10px;border-top:1px solid var(--border)">
                <div class="det-iva-total"><span>TOTAL</span><span>Q ${p.total.toFixed(2)}</span></div>
              </div>
            </div>

            <!-- Form -->
            <div>
              <div style="font-size:13px;font-weight:700;color:var(--navy);margin-bottom:10px">Datos del receptor</div>

              <div class="receptor-tabs">
                <button class="receptor-tab ${receptorTipo==='NIT'?'active':''}" onclick="setReceptor('NIT')">
                  <i class="bi bi-person-vcard"></i><br>NIT
                </button>
                <button class="receptor-tab ${receptorTipo==='CF'?'active':''}" onclick="setReceptor('CF')">
                  <i class="bi bi-person"></i><br>Consumidor Final
                </button>
                <button class="receptor-tab cred ${receptorTipo==='CRED'?'active':''}"
                  onclick="setReceptor('CRED')"
                  ${!isAdmin ? 'title="Solo administradores"' : ''}>
                  <i class="bi bi-building"></i><br>Crédito Empresa
                </button>
              </div>

              ${receptorTipo === 'NIT' ? `
                <div class="form-group">
                  <label class="form-label">NIT</label>
                  <input id="recNit" class="form-control" placeholder="1234567-8">
                </div>
                <div class="form-group">
                  <label class="form-label">Nombre / Razón Social</label>
                  <input id="recNombre" class="form-control" placeholder="Nombre del receptor">
                </div>
              ` : receptorTipo === 'CF' ? `
                <div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:var(--r);padding:10px 12px;font-size:12.5px;color:#1E40AF;margin-bottom:12px">
                  Factura a nombre de <strong>Consumidor Final (CF)</strong> · No requiere NIT.
                </div>
              ` : adminBlocked ? `
                <div style="background:#FFEBEE;border:1px solid #FECACA;border-radius:var(--r);padding:10px 12px;font-size:12.5px;color:#B71C1C;margin-bottom:12px">
                  Solo el administrador puede emitir facturas a crédito empresarial.
                </div>
              ` : `
                <div class="form-group">
                  <label class="form-label">Empresa con crédito</label>
                  <select id="recEmpresa" class="form-control" onchange="onEmpresaChange()">
                    <option value="">-- Selecciona empresa --</option>
                    ${empOptions}
                  </select>
                  <div id="empCreditInfo" style="font-size:12px;color:#6B7280;margin-top:6px"></div>
                </div>
                <div class="form-group">
                  <label class="form-label">Referencia interna</label>
                  <input id="recRef" class="form-control" placeholder="Orden de compra, contacto, etc.">
                </div>
              `}

              <div class="fact-actions" style="margin-top:14px">
                <button class="btn btn-primary" onclick="emitirFactura('${p.id}')">
                  <i class="bi bi-receipt"></i> Emitir Factura
                </button>
              </div>
            </div>
          </div>
        </td></tr>`;
    }

    function toggleExpand(id) {
      expandedOrderId = expandedOrderId === id ? null : id;
      receptorTipo = 'NIT';
      selectedEmpresaId = null;
      renderQueue();
    }

    function setReceptor(t) {
      if (t === 'CRED' && !isAdmin) {
        receptorTipo = 'CRED'; // mostramos mensaje
      } else {
        receptorTipo = t;
      }
      renderQueue();
    }

    function onEmpresaChange() {
      const sel = document.getElementById('recEmpresa');
      selectedEmpresaId = sel.value ? Number(sel.value) : null;
      const empresas = SIGRA_DATA.getEmpresas();
      const emp = empresas.find(e => e.id === selectedEmpresaId);
      const info = document.getElementById('empCreditInfo');
      if (emp) {
        const disp = emp.limiteCredito - emp.utilizado;
        const pct = (emp.utilizado / emp.limiteCredito) * 100;
        info.innerHTML = `Límite: <strong>Q ${emp.limiteCredito.toFixed(2)}</strong> · Utilizado: <strong>Q ${emp.utilizado.toFixed(2)}</strong> (${pct.toFixed(0)}%)`;
      } else {
        info.textContent = '';
      }
    }

    /* ── Emitir factura ── */
    function emitirFactura(orderId) {
      const pendientes = getPendientes();
      const p = pendientes.find(x => x.id === orderId);
      if (!p) return;

      let nit = 'CF', nombre = 'Consumidor Final';
      let tipo = 'CONTADO';
      let empresaId = null, ref = '';

      if (receptorTipo === 'NIT') {
        nit    = (document.getElementById('recNit')?.value || '').trim();
        nombre = (document.getElementById('recNombre')?.value || '').trim();
        if (!nit || !nombre) {
          showToast('Datos incompletos', 'Ingresa NIT y nombre del receptor', 'error');
          return;
        }
      } else if (receptorTipo === 'CRED') {
        if (!isAdmin) {
          showToast('No autorizado', 'Solo admin puede facturar a crédito', 'error');
          return;
        }
        empresaId = Number(document.getElementById('recEmpresa').value);
        if (!empresaId) {
          showToast('Selecciona empresa', 'Elige una empresa para el crédito', 'error');
          return;
        }
        const empresas = SIGRA_DATA.getEmpresas();
        const emp = empresas.find(e => e.id === empresaId);
        nit = emp.nit;
        nombre = emp.nombre;
        tipo = 'CREDITO';
        ref = (document.getElementById('recRef')?.value || '').trim();
        const disponible = emp.limiteCredito - emp.utilizado;
        if (p.total > disponible) {
          const cont = confirm(
            `Esta empresa solo tiene Q ${disponible.toFixed(2)} disponibles ` +
            `de su límite de Q ${emp.limiteCredito.toFixed(2)}.\n\n` +
            `El total a cargar es Q ${p.total.toFixed(2)}.\n\n` +
            `¿Deseas autorizar el sobregiro?`
          );
          if (!cont) return;
        }
      }

      const uuid = generarUUID();
      const factura = {
        id: uuid,
        pedidoId: p.id,
        ubicacion: p.ubicacion,
        mesero: p.mesero || null,
        items: p.items.map(i => ({ ...i })),
        subtotal: p.total / (1 + IVA_RATE),
        iva: p.total - p.total / (1 + IVA_RATE),
        total: p.total,
        nit, nombre,
        tipo, empresaId, ref,
        fecha: new Date().toISOString().slice(0, 10),
        hora: new Date().toLocaleTimeString('es-GT', { hour: '2-digit', minute: '2-digit' }),
        offline: felOffline,
      };

      SIGRA_DATA.addFactura(factura);

      // Si es crédito, cargar al saldo de la empresa
      if (tipo === 'CREDITO') {
        const empresas = SIGRA_DATA.getEmpresas();
        const emp = empresas.find(e => e.id === empresaId);
        SIGRA_DATA.updateEmpresa(empresaId, { utilizado: emp.utilizado + p.total });
        const abonos = SIGRA_DATA.getAbonos();
        abonos.unshift({
          id: Date.now(), empresaId, monto: p.total, tipo: 'CARGO',
          fecha: factura.fecha, ref: `Factura ${uuid.slice(0,8)}... ${ref}`.trim(),
        });
        SIGRA_DATA.setAbonos(abonos);
      }

      // Liberar la mesa
      SIGRA_DATA.updateMesa(p.mesaId, { estado: 'LIBRE', pedido: null, reserva: null });

      lastFactura = factura;
      document.getElementById('uuidDisplay').textContent = uuid;
      document.getElementById('successSub').textContent = felOffline
        ? 'Modo contingencia · Pendiente de envío a SAT'
        : 'Autorizada por SAT · Guatemala';
      document.getElementById('invoiceDetails').innerHTML = `
        <div><span style="font-weight:600">Receptor:</span> ${nombre}</div>
        <div><span style="font-weight:600">NIT:</span> ${nit}</div>
        <div><span style="font-weight:600">Tipo:</span> ${tipo}</div>
        <div><span style="font-weight:600">Total:</span> Q ${p.total.toFixed(2)}</div>
        <div><span style="font-weight:600">Fecha:</span> ${factura.fecha}</div>
        <div><span style="font-weight:600">Pedido:</span> ${p.id}</div>`;

      expandedOrderId = null;
      receptorTipo = 'NIT';
      selectedEmpresaId = null;
      SIGRA.openModal('successModal');
      renderQueue();
    }

    function generarUUID() {
      return [8, 4, 4, 4, 12].map(n =>
        Math.random().toString(36).substr(2, n).toUpperCase().padEnd(n, '0').slice(0, n)
      ).join('-');
    }

    function resetInvoice() { SIGRA.closeModal('successModal'); }
    function imprimirUltima()    { if (lastFactura) SIGRA_PDF.imprimirFactura(lastFactura); }
    function descargarUltimaPdf(){ if (lastFactura) SIGRA_PDF.descargarFactura(lastFactura); }

    /* ── Historial ── */
    function renderHistorial() {
      const facts = SIGRA_DATA.getFacturas();
      document.getElementById('histCount').textContent = `${facts.length} factura${facts.length !== 1 ? 's' : ''}`;
      const body = document.getElementById('histTableBody');
      if (facts.length === 0) {
        body.innerHTML = `<tr><td colspan="7"><div class="empty-state">
          <i class="bi bi-archive"></i><p>Aún no hay facturas emitidas.</p>
        </div></td></tr>`;
        return;
      }
      body.innerHTML = facts.map(f => `
        <tr>
          <td>${f.fecha} ${f.hora}</td>
          <td><strong style="color:var(--navy)">${f.pedidoId}</strong><br><span style="font-size:11px;color:#9CA3AF">${f.ubicacion}</span></td>
          <td>${f.nombre}<br><span style="font-size:11px;color:#9CA3AF">NIT: ${f.nit}</span></td>
          <td class="hist-uuid">${f.id.slice(0, 18)}...</td>
          <td><span class="badge ${f.tipo === 'CREDITO' ? 'badge-orange' : 'badge-success'}">${f.tipo}</span></td>
          <td class="r"><strong>Q ${f.total.toFixed(2)}</strong></td>
          <td class="r">
            <button class="btn btn-sm btn-outline" onclick='reimprimir(${JSON.stringify(f.id)})' title="Imprimir">
              <i class="bi bi-printer"></i>
            </button>
            <button class="btn btn-sm btn-outline" onclick='redescargar(${JSON.stringify(f.id)})' title="PDF">
              <i class="bi bi-download"></i>
            </button>
          </td>
        </tr>`).join('');
    }

    function reimprimir(id) {
      const f = SIGRA_DATA.getFacturas().find(x => x.id === id);
      if (f) SIGRA_PDF.imprimirFactura(f);
    }
    function redescargar(id) {
      const f = SIGRA_DATA.getFacturas().find(x => x.id === id);
      if (f) SIGRA_PDF.descargarFactura(f);
    }

    function showToast(title, sub, type) {
      const toast = document.getElementById('successToast');
      toast.className = 'toast ' + (type === 'error' ? 'toast-error' : 'toast-success');
      document.getElementById('toastTitle').textContent = title;
      document.getElementById('toastSub').textContent = sub || '';
      SIGRA.showToast('successToast', 2500);
    }

    /* Init */
    renderQueue();
    window.addEventListener('storage', (e) => {
      if (e.key === SIGRA_DATA.KEYS.MESAS) renderQueue();
    });
  </script>
</body>
</html>
