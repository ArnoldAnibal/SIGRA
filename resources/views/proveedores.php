<?php
require_once __DIR__ . '/includes/bootstrap.php';
sigra_require_role(['ADMIN']);
$SIGRA_USER = sigra_current_user();
$resProv = api_request("GET", "/proveedores", [], sigra_token()); $SIGRA_PROVEEDORES = $resProv["data"]["data"] ?? $resProv["data"] ?? [];
$resCtas = api_request("GET", "/cuentas-por-pagar", [], sigra_token()); $SIGRA_CUENTAS_PROV = $resCtas["data"]["data"] ?? $resCtas["data"] ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIGRA — Proveedores</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="styles.css">
  <style>
    .prov-body  { padding: 24px; overflow-y: auto; height: calc(100vh - 64px - 73px); }
    .prov-inner { max-width: 1100px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px; }
    .prov-item {
      padding: 16px 20px;
      border-bottom: 1px solid var(--border-lt);
      display: flex; align-items: center; justify-content: space-between; gap: 16px;
    }
    .prov-item:last-child { border-bottom: none; }
    .prov-icon {
      width: 44px; height: 44px; border-radius: var(--r-lg);
      background: #F0F4FF;
      display: flex; align-items: center; justify-content: center;
      font-size: 18px; color: var(--navy); flex-shrink: 0;
    }
    .prov-name    { font-weight: 600; color: #1F2937; }
    .prov-contact { font-size: 12px; color: #9CA3AF; margin-top: 2px; }
    .prov-email   { font-size: 12px; color: #9CA3AF; }
    .prov-actions { display: flex; gap: 6px; }
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
              <div class="page-title">Proveedores y Cuentas por Pagar</div>
              <div class="page-sub" id="provSubtitle">— proveedores · — cuentas pendientes</div>
            </div>
            <div style="display:flex;gap:8px">
              <button class="btn btn-outline" onclick="openCuentaForm()">
                <i class="bi bi-file-earmark-plus"></i> Registrar Cuenta
              </button>
              <button class="btn btn-primary" onclick="openProvForm()">
                <i class="bi bi-plus"></i> Nuevo Proveedor
              </button>
            </div>
          </div>
        </div>

        <div class="prov-body">
          <div class="prov-inner">

            <div class="grid-3" id="summaryCards"></div>

            <div class="info-box info-box-red" id="overdueAlert" style="display:none">
              <i class="bi bi-exclamation-circle" style="flex-shrink:0;font-size:18px"></i>
              <div>
                <p style="font-size:13.5px;font-weight:600" id="overdueTitle"></p>
                <p style="font-size:12px;margin-top:2px" id="overdueDetail"></p>
              </div>
            </div>

            <div class="card" style="overflow:hidden">
              <div class="card-header" style="display:flex;justify-content:space-between;align-items:center">
                <span class="card-title">Directorio de Proveedores</span>
                <span style="font-size:12px;color:#9CA3AF" id="dirCount"></span>
              </div>
              <div id="provList"></div>
            </div>

            <div class="card" style="overflow:hidden">
              <div class="card-header" style="display:flex;justify-content:space-between;align-items:center">
                <span class="card-title">Cuentas por Pagar</span>
                <span style="font-size:12px;color:#9CA3AF" id="cuentasCount"></span>
              </div>
              <div class="table-wrap">
                <table>
                  <thead>
                    <tr>
                      <th>Proveedor</th>
                      <th>Concepto</th>
                      <th class="r">Monto</th>
                      <th>Vencimiento</th>
                      <th>Estado</th>
                      <th class="r">Acción</th>
                    </tr>
                  </thead>
                  <tbody id="cuentasTable"></tbody>
                </table>
              </div>
            </div>

          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Nuevo Proveedor Modal -->
  <div class="modal-overlay" id="provModal">
    <div class="modal" style="max-width:440px">
      <div class="modal-header">
        <h3 class="modal-title" id="provModalTitle">Nuevo Proveedor</h3>
        <button class="modal-close" onclick="SIGRA.closeModal('provModal')"><i class="bi bi-x"></i></button>
      </div>
      <div class="form-group">
        <label class="form-label">Razón social *</label>
        <input id="pNombre" class="form-control" placeholder="Distribuidora ABC">
      </div>
      <div style="display:flex;gap:10px">
        <div class="form-group" style="flex:1">
          <label class="form-label">Contacto</label>
          <input id="pContacto" class="form-control" placeholder="Nombre del contacto">
        </div>
        <div class="form-group" style="flex:1">
          <label class="form-label">Teléfono</label>
          <input id="pTel" class="form-control" placeholder="5555-5555">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Email</label>
        <input id="pEmail" type="email" class="form-control" placeholder="ventas@proveedor.gt">
      </div>
      <div class="form-error" id="pError" style="margin-bottom:12px">Ingresa al menos la razón social.</div>
      <div style="display:flex;gap:10px">
        <button class="btn btn-outline" style="flex:1" onclick="SIGRA.closeModal('provModal')">Cancelar</button>
        <button class="btn btn-primary" style="flex:1" onclick="guardarProveedor()">Guardar</button>
      </div>
    </div>
  </div>

  <!-- Nueva Cuenta Modal -->
  <div class="modal-overlay" id="cuentaModal">
    <div class="modal" style="max-width:440px">
      <div class="modal-header">
        <h3 class="modal-title">Registrar Cuenta por Pagar</h3>
        <button class="modal-close" onclick="SIGRA.closeModal('cuentaModal')"><i class="bi bi-x"></i></button>
      </div>
      <div class="form-group">
        <label class="form-label">Proveedor *</label>
        <select id="cProveedor" class="form-control">
          <option value="">-- Selecciona --</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Concepto *</label>
        <input id="cConcepto" class="form-control" placeholder="Ej. Compra de abarrotes semana 18">
      </div>
      <div style="display:flex;gap:10px">
        <div class="form-group" style="flex:1">
          <label class="form-label">Monto (Q) *</label>
          <input id="cMonto" type="number" min="0" step="0.01" class="form-control" placeholder="0.00">
        </div>
        <div class="form-group" style="flex:1">
          <label class="form-label">Fecha vencimiento *</label>
          <input id="cFecha" type="date" class="form-control">
        </div>
      </div>
      <div class="form-error" id="cError" style="margin-bottom:12px">Completa los campos obligatorios.</div>
      <div style="display:flex;gap:10px">
        <button class="btn btn-outline" style="flex:1" onclick="SIGRA.closeModal('cuentaModal')">Cancelar</button>
        <button class="btn btn-primary" style="flex:1" onclick="guardarCuenta()">Guardar</button>
      </div>
    </div>
  </div>

  <!-- Pago Modal -->
  <div class="modal-overlay" id="pagoModal">
    <div class="modal" style="max-width:380px">
      <div class="modal-header">
        <h3 class="modal-title">Registrar Pago</h3>
        <button class="modal-close" onclick="SIGRA.closeModal('pagoModal')"><i class="bi bi-x"></i></button>
      </div>
      <div style="background:#F9FAFB;border-radius:var(--r);padding:12px;margin-bottom:14px">
        <p style="font-size:13.5px;font-weight:600;color:#374151" id="pagoProvName"></p>
        <p style="font-size:12px;color:#9CA3AF;margin-top:2px" id="pagoConcepto"></p>
      </div>
      <div class="form-group">
        <label class="form-label">Monto a pagar (Q)</label>
        <input type="number" id="pagoMonto" class="form-control" min="0" step="0.01" oninput="clearPagoErr()">
        <div class="form-error" id="pagoError">Ingrese un monto válido.</div>
      </div>
      <div class="form-group" style="margin-bottom:20px">
        <label class="form-label">Referencia de pago</label>
        <input type="text" id="pagoRef" class="form-control" placeholder="No. cheque, transferencia, efectivo...">
      </div>
      <div style="display:flex;gap:10px">
        <button class="btn btn-outline" style="flex:1" onclick="SIGRA.closeModal('pagoModal')">Cancelar</button>
        <button class="btn btn-primary" style="flex:1" onclick="registrarPago()">Registrar Pago</button>
      </div>
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
    'proveedores' => $SIGRA_PROVEEDORES, 'cuentas_prov' => $SIGRA_CUENTAS_PROV
]) ?>

  <script src="layout.js"></script>
  <script src="sigra-data.js"></script>
  <script>
    SIGRA.initLayout('Proveedores');

    let proveedores = SIGRA_DATA.getProveedores();
    let cuentas     = SIGRA_DATA.getCuentasProv();
    let editingProvId = null;
    let currentPagoIdx = null;

    function reload() {
      proveedores = SIGRA_DATA.getProveedores();
      cuentas = SIGRA_DATA.getCuentasProv();
      render();
    }

    function provName(id) {
      const p = proveedores.find(x => x.id === id);
      return p ? p.nombre : '— eliminado —';
    }

    const estadoConfig = {
      VIGENTE: { bg:'#E3F2FD', text:'#1565C0' },
      VENCIDA: { bg:'#FFEBEE', text:'#C62828' },
      PAGADA:  { bg:'#E8F5E9', text:'#2E7D32' },
    };

    /* Determina si una vigente debería marcarse vencida según la fecha */
    function autoEstado(c) {
      if (c.estado === 'PAGADA') return 'PAGADA';
      const hoy = new Date().toISOString().slice(0,10);
      return c.fechaVence < hoy ? 'VENCIDA' : 'VIGENTE';
    }

    function render() {
      const cuentasNorm = cuentas.map(c => ({ ...c, estado: autoEstado(c) }));
      const overdue = cuentasNorm.filter(c => c.estado === 'VENCIDA');
      const vigente = cuentasNorm.filter(c => c.estado === 'VIGENTE');
      const pagadas = cuentasNorm.filter(c => c.estado === 'PAGADA');
      const pending = cuentasNorm.filter(c => c.estado !== 'PAGADA');
      const tVigente = vigente.reduce((a, c) => a + Number(c.monto), 0);
      const tVencida = overdue.reduce((a, c) => a + Number(c.monto), 0);
      const tPagada  = pagadas.reduce((a, c) => a + Number(c.monto), 0);

      document.getElementById('provSubtitle').textContent =
        `${proveedores.length} proveedores · ${pending.length} cuentas pendientes`;
      document.getElementById('dirCount').textContent = `${proveedores.length} proveedor(es)`;
      document.getElementById('cuentasCount').textContent = `${cuentasNorm.length} cuenta(s)`;

      document.getElementById('summaryCards').innerHTML = `
        <div class="kpi-card">
          <p class="text-xs text-muted mb-1">Cuentas Vigentes</p>
          <p style="font-size:20px;font-weight:700;color:#1565C0">Q ${Number(tVigente).toFixed(2)}</p>
          <p class="text-xs text-muted mt-1">${vigente.length} facturas</p>
        </div>
        <div class="kpi-card" style="border-color:#FFCDD2">
          <p class="text-xs text-muted mb-1">Cuentas Vencidas</p>
          <p style="font-size:20px;font-weight:700;color:#C62828">Q ${tVencida.toFixed(2)}</p>
          <p class="text-xs text-muted mt-1">${overdue.length} vencidas</p>
        </div>
        <div class="kpi-card">
          <p class="text-xs text-muted mb-1">Pagadas</p>
          <p style="font-size:20px;font-weight:700;color:#2E7D32">Q ${tPagada.toFixed(2)}</p>
          <p class="text-xs text-muted mt-1">${pagadas.length} facturas</p>
        </div>`;

      const alertEl = document.getElementById('overdueAlert');
      if (overdue.length > 0) {
        alertEl.style.display = 'flex';
        document.getElementById('overdueTitle').textContent =
          `${overdue.length} cuenta(s) vencida(s) requieren atención inmediata`;
        const provs = [...new Set(overdue.map(c => provName(c.proveedorId)))].join(', ');
        document.getElementById('overdueDetail').textContent =
          `Total vencido: Q ${tVencida.toFixed(2)} · Proveedores: ${provs}`;
      } else {
        alertEl.style.display = 'none';
      }

      // Directorio
      if (proveedores.length === 0) {
        document.getElementById('provList').innerHTML = `
          <div style="padding:40px;text-align:center;color:#9CA3AF">
            <i class="bi bi-truck" style="font-size:34px;opacity:.4"></i>
            <p style="margin-top:10px">Aún no hay proveedores registrados.</p>
            <button class="btn btn-primary" style="margin-top:14px" onclick="openProvForm()">
              <i class="bi bi-plus"></i> Registrar primer proveedor
            </button>
          </div>`;
      } else {
        document.getElementById('provList').innerHTML = proveedores.map(p => {
          const activas = cuentasNorm.filter(c => c.proveedorId === p.id && c.estado !== 'PAGADA').length;
          return `
            <div class="prov-item">
              <div style="display:flex;align-items:center;gap:14px">
                <div class="prov-icon"><i class="bi bi-truck"></i></div>
                <div>
                  <div class="prov-name">${p.nombre}</div>
                  <div class="prov-contact">
                    ${p.contacto ? `${p.contacto} · ` : ''}${p.telefono || ''}
                  </div>
                </div>
              </div>
              <div style="display:flex;align-items:center;gap:14px;flex-shrink:0">
                <div style="text-align:right">
                  <div style="font-size:13.5px;font-weight:500;color:#6B7280">${activas} cuenta(s) activa(s)</div>
                  <div class="prov-email">${p.email || ''}</div>
                </div>
                <div class="prov-actions">
                  <button class="btn btn-outline btn-sm" title="Editar" onclick="openProvForm(${p.id})">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button class="btn btn-outline btn-sm" title="Registrar cuenta" onclick="openCuentaForm(${p.id})">
                    <i class="bi bi-file-earmark-plus"></i>
                  </button>
                </div>
              </div>
            </div>`;
        }).join('');
      }

      // Cuentas por pagar
      if (cuentasNorm.length === 0) {
        document.getElementById('cuentasTable').innerHTML = `<tr><td colspan="6" style="text-align:center;padding:30px;color:#9CA3AF">
          No hay cuentas por pagar registradas.
        </td></tr>`;
      } else {
        document.getElementById('cuentasTable').innerHTML = cuentasNorm.map(c => {
          const cfg = estadoConfig[c.estado];
          const montoColor = c.estado === 'VENCIDA' ? '#C62828' : '#1A2E4A';
          const fechaStyle = c.estado === 'VENCIDA' ? 'color:#C62828;font-weight:500' : 'color:#6B7280';
          const rowBg = c.estado === 'VENCIDA' ? 'background:rgba(255,235,238,.3)' : '';
          const actionBtn = c.estado !== 'PAGADA'
            ? `<button class="btn btn-sm" style="background:${c.estado==='VENCIDA'?'#C62828':'#1A2E4A'};color:#fff"
                 onclick="openPago(${c.id})">
                 <i class="bi bi-currency-dollar"></i> Pagar
               </button>`
            : '<span style="font-size:12px;color:#D1D5DB">Pagado</span>';
          return `
            <tr style="${rowBg}">
              <td style="font-weight:500;color:#1F2937">${provName(c.proveedorId)}</td>
              <td style="color:#6B7280">${c.concepto}</td>
              <td class="r" style="font-weight:700;color:${montoColor}">Q ${Number(c.monto).toFixed(2)}</td>
              <td><span style="${fechaStyle}">${c.fechaVence}${c.estado==='VENCIDA'?' ⚠️':''}</span></td>
              <td><span class="badge" style="background:${cfg.bg};color:${cfg.text}">${c.estado}</span></td>
              <td class="r">${actionBtn}</td>
            </tr>`;
        }).join('');
      }
    }

    /* ── Alta/edición de proveedor ── */
    function openProvForm(id) {
      editingProvId = id || null;
      const p = id ? proveedores.find(x => x.id === id) : null;
      document.getElementById('provModalTitle').textContent = p ? 'Editar Proveedor' : 'Nuevo Proveedor';
      document.getElementById('pNombre').value   = p ? p.nombre : '';
      document.getElementById('pContacto').value = p ? (p.contacto || '') : '';
      document.getElementById('pTel').value      = p ? (p.telefono || '') : '';
      document.getElementById('pEmail').value    = p ? (p.email || '') : '';
      document.getElementById('pError').style.display = 'none';
      SIGRA.openModal('provModal');
    }

    function guardarProveedor() {
      const nombre = document.getElementById('pNombre').value.trim();
      if (!nombre) {
        document.getElementById('pError').style.display = 'block';
        return;
      }
      const patch = {
        nombre,
        contacto: document.getElementById('pContacto').value.trim(),
        telefono: document.getElementById('pTel').value.trim(),
        email:    document.getElementById('pEmail').value.trim(),
      };
      if (editingProvId) {
        const arr = SIGRA_DATA.getProveedores().map(p => p.id === editingProvId ? { ...p, ...patch } : p);
        SIGRA_DATA.setProveedores(arr);
        showToast('Proveedor actualizado', nombre);
      } else {
        SIGRA_DATA.addProveedor(patch);
        showToast('Proveedor creado', nombre);
      }
      SIGRA.closeModal('provModal');
      reload();
    }

    /* ── Alta de cuenta por pagar ── */
    function openCuentaForm(provId) {
      const sel = document.getElementById('cProveedor');
      sel.innerHTML = '<option value="">-- Selecciona --</option>' +
        proveedores.map(p => `<option value="${p.id}" ${provId === p.id ? 'selected' : ''}>${p.nombre}</option>`).join('');
      document.getElementById('cConcepto').value = '';
      document.getElementById('cMonto').value = '';
      const d = new Date();
      d.setDate(d.getDate() + 30);
      document.getElementById('cFecha').value = d.toISOString().slice(0, 10);
      document.getElementById('cError').style.display = 'none';
      SIGRA.openModal('cuentaModal');
    }

    function guardarCuenta() {
      const proveedorId = Number(document.getElementById('cProveedor').value);
      const concepto    = document.getElementById('cConcepto').value.trim();
      const monto       = Number(document.getElementById('cMonto').value);
      const fechaVence  = document.getElementById('cFecha').value;
      if (!proveedorId || !concepto || !monto || !fechaVence) {
        document.getElementById('cError').style.display = 'block';
        return;
      }
      SIGRA_DATA.addCuentaProv({ proveedorId, concepto, monto, fechaVence, estado: 'VIGENTE' });
      SIGRA.closeModal('cuentaModal');
      showToast('Cuenta registrada', `${provName(proveedorId)} · Q ${monto.toFixed(2)}`);
      reload();
    }

    /* ── Pago ── */
    function openPago(id) {
      currentPagoIdx = id;
      const c = cuentas.find(x => x.id === id);
      document.getElementById('pagoProvName').textContent = provName(c.proveedorId);
      document.getElementById('pagoConcepto').textContent = c.concepto;
      document.getElementById('pagoMonto').value = c.monto;
      document.getElementById('pagoRef').value = '';
      document.getElementById('pagoError').style.display = 'none';
      SIGRA.openModal('pagoModal');
    }

    function clearPagoErr() {
      document.getElementById('pagoError').style.display = 'none';
    }

    function registrarPago() {
      const monto = Number(document.getElementById('pagoMonto').value);
      if (!monto || monto <= 0) {
        document.getElementById('pagoError').style.display = 'block';
        return;
      }
      SIGRA_DATA.updateCuentaProv(currentPagoIdx, { estado: 'PAGADA' });
      SIGRA.closeModal('pagoModal');
      showToast('Pago registrado', `Q ${monto.toFixed(2)}`);
      reload();
    }

    function showToast(title, sub) {
      document.getElementById('toastTitle').textContent = title;
      document.getElementById('toastSub').textContent = sub || '';
      SIGRA.showToast('successToast', 2500);
    }

    /* Init */
    reload();
    window.addEventListener('storage', (e) => {
      if (e.key === SIGRA_DATA.KEYS.PROVEEDORES || e.key === SIGRA_DATA.KEYS.CUENTAS_PROV) reload();
    });
  </script>
</body>
</html>
