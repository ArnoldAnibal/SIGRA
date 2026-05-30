<?php
require_once __DIR__ . '/includes/bootstrap.php';
sigra_require_role(['ADMIN', 'CAJERO']);
$SIGRA_USER = sigra_current_user();
$resEmpresa = api_empresas_credito(sigra_token());
$SIGRA_EMPRESAS = $resEmpresa['data'] ?? [];
$resFact = api_facturas(sigra_token()); $SIGRA_ABONOS = $resFact["data"]["data"] ?? $resFact["data"] ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIGRA — Crédito Empresarial</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="styles.css">
  <style>
    .cred-body { padding: 24px; overflow-y: auto; height: calc(100vh - 64px - 73px); }
    .cred-inner { max-width: 1000px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px; }

    .cred-tabs { display: flex; gap: 4px; border-bottom: 2px solid var(--border); }
    .cred-tab {
      padding: 10px 18px; font-size: 13.5px; font-weight: 600;
      background: none; border: none; cursor: pointer;
      color: #6B7280; border-bottom: 2px solid transparent; margin-bottom: -2px;
    }
    .cred-tab:hover { color: var(--navy); }
    .cred-tab.active { color: var(--navy); border-bottom-color: var(--amber); }

    .empresa-card { border-radius: var(--r-xl); border: 1px solid var(--border-lt); background: #fff; box-shadow: var(--shadow-sm); overflow: hidden; }
    .empresa-card.overlimit { border-color: #FCA5A5; }
    .empresa-header { padding: 20px; cursor: pointer; transition: background .15s; }
    .empresa-header:hover { background: #F9FAFB; }
    .empresa-icon {
      width: 44px; height: 44px; border-radius: var(--r-lg); background: #EEF2FF;
      display: flex; align-items: center; justify-content: center;
      font-size: 20px; color: var(--navy); flex-shrink: 0;
    }
    .empresa-name   { font-weight: 700; color: #1F2937; font-size: 15px; }
    .empresa-nit    { font-size: 12px; color: #9CA3AF; margin-top: 2px; }
    .progress-label { display: flex; justify-content: space-between; font-size: 12px; color: #9CA3AF; margin-bottom: 6px; }
    .progress-label strong { color: #374151; }
    .avail-amount   { font-size: 14px; font-weight: 700; }
    .chevron        { font-size: 18px; color: #9CA3AF; }
    .empresa-detail { border-top: 1px solid var(--border-lt); }
    .detail-bar {
      display: flex; align-items: center; justify-content: space-between;
      padding: 10px 20px; background: #F9FAFB; border-bottom: 1px solid var(--border-lt);
    }
    .detail-bar-title { font-size: 13.5px; font-weight: 600; color: #6B7280; }
    .overlimit-banner {
      background: #FFEBEE; color: #B71C1C; padding: 10px 14px;
      font-size: 12.5px; border-radius: 6px; margin-bottom: 10px;
      display: flex; align-items: center; gap: 8px;
    }
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
              <div class="page-title">Crédito Empresarial</div>
              <div class="page-sub" id="credSub">Gestión de cuentas por cobrar y convenios con empresas</div>
            </div>
            <button class="btn btn-primary" id="btnNuevaEmpresa" onclick="openEmpresaForm()" style="display:none">
              <i class="bi bi-building-add"></i> Nueva Empresa
            </button>
          </div>
        </div>

        <div class="cred-body">
          <div class="cred-inner">

            <div class="cred-tabs">
              <button class="cred-tab active" data-tab="empresas" onclick="setTab('empresas')">
                <i class="bi bi-building"></i> Empresas
              </button>
              <button class="cred-tab" data-tab="movimientos" onclick="setTab('movimientos')">
                <i class="bi bi-arrow-left-right"></i> Movimientos
              </button>
            </div>

            <!-- TAB Empresas -->
            <div id="tabEmpresas" style="display:flex;flex-direction:column;gap:16px">
              <div id="empresaList"></div>
            </div>

            <!-- TAB Movimientos -->
            <div id="tabMovimientos" style="display:none">
              <div class="card" style="overflow:hidden">
                <div class="card-header">
                  <span class="card-title">Historial de cargos y abonos</span>
                </div>
                <div class="table-wrap">
                  <table>
                    <thead>
                      <tr>
                        <th>Fecha</th>
                        <th>Empresa</th>
                        <th>Tipo</th>
                        <th>Referencia</th>
                        <th class="r">Monto</th>
                      </tr>
                    </thead>
                    <tbody id="movTable"></tbody>
                  </table>
                </div>
              </div>
            </div>

          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Pago Modal -->
  <div class="modal-overlay" id="pagoModal">
    <div class="modal" style="max-width:400px">
      <div class="modal-header">
        <h3 class="modal-title">Registrar Pago / Abono</h3>
        <button class="modal-close" onclick="SIGRA.closeModal('pagoModal')"><i class="bi bi-x"></i></button>
      </div>
      <p style="font-size:13px;color:#6B7280;margin-bottom:4px" id="pagoEmpresaName"></p>
      <p style="font-size:12px;color:#9CA3AF;margin-bottom:14px" id="pagoSaldoInfo"></p>
      <div class="form-group">
        <label class="form-label">Monto a abonar (Q)</label>
        <input type="number" id="pagoMonto" class="form-control" placeholder="0.00" min="0" step="0.01" oninput="clearPagoErr()">
        <div class="form-error" id="pagoError">Ingrese un monto válido.</div>
      </div>
      <div class="form-group" style="margin-bottom:8px">
        <label class="form-label">Referencia</label>
        <input type="text" id="pagoRef" class="form-control" placeholder="No. cheque, transferencia, etc.">
      </div>
      <div style="display:flex;gap:6px;margin-bottom:16px;flex-wrap:wrap">
        <button type="button" class="btn btn-outline btn-sm" onclick="setMontoPercent(0.5)">50% del saldo</button>
        <button type="button" class="btn btn-outline btn-sm" onclick="setMontoPercent(1)">Saldo total</button>
      </div>
      <div style="display:flex;gap:10px">
        <button class="btn btn-outline" style="flex:1" onclick="SIGRA.closeModal('pagoModal')">Cancelar</button>
        <button class="btn btn-primary" style="flex:1" onclick="registrarPago()">Registrar</button>
      </div>
    </div>
  </div>

  <!-- Empresa Form Modal -->
  <div class="modal-overlay" id="empresaModal">
    <div class="modal" style="max-width:440px">
      <div class="modal-header">
        <h3 class="modal-title" id="empresaModalTitle">Nueva Empresa</h3>
        <button class="modal-close" onclick="SIGRA.closeModal('empresaModal')"><i class="bi bi-x"></i></button>
      </div>
      <div class="form-group">
        <label class="form-label">Razón social *</label>
        <input id="empNombre" class="form-control" placeholder="Empresa S.A.">
      </div>
      <div class="form-group">
        <label class="form-label">NIT *</label>
        <input id="empNit" class="form-control" placeholder="1234567-8">
      </div>
      <div style="display:flex;gap:10px">
        <div class="form-group" style="flex:1">
          <label class="form-label">Contacto</label>
          <input id="empContacto" class="form-control" placeholder="Nombre">
        </div>
        <div class="form-group" style="flex:1">
          <label class="form-label">Teléfono</label>
          <input id="empTel" class="form-control" placeholder="5555-5555">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Límite de crédito (Q) *</label>
        <input id="empLimite" type="number" min="0" step="100" class="form-control" placeholder="5000">
      </div>
      <div class="form-error" id="empError" style="margin-bottom:12px">Completa los campos obligatorios.</div>
      <div style="display:flex;gap:10px">
        <button class="btn btn-outline" style="flex:1" onclick="SIGRA.closeModal('empresaModal')">Cancelar</button>
        <button class="btn btn-primary" style="flex:1" onclick="guardarEmpresa()">Guardar</button>
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
    'empresas' => $SIGRA_EMPRESAS, 'abonos' => $SIGRA_ABONOS
]) ?>

  <script src="layout.js"></script>
  <script src="sigra-data.js"></script>
  <script>
    SIGRA.initLayout('Crédito Empresarial');
    const user = SIGRA.getUser();
    const isAdmin = user && user.role === 'ADMIN';
    if (isAdmin) document.getElementById('btnNuevaEmpresa').style.display = '';

    let empresas = SIGRA_DATA.getEmpresas();
    let abonos   = SIGRA_DATA.getAbonos();
    let expandedId = null;
    let currentPagoEmpresa = null;
    let editingEmpresaId = null;
    let currentTab = 'empresas';

    function reload() {
      empresas = SIGRA_DATA.getEmpresas();
      abonos   = SIGRA_DATA.getAbonos();
      render();
    }

    function setTab(t) {
      currentTab = t;
      document.querySelectorAll('.cred-tab').forEach(b => b.classList.toggle('active', b.dataset.tab === t));
      document.getElementById('tabEmpresas').style.display    = t === 'empresas'    ? 'flex' : 'none';
      document.getElementById('tabMovimientos').style.display = t === 'movimientos' ? '' : 'none';
      if (t === 'movimientos') renderMovimientos();
    }

    function render() {
      document.getElementById('credSub').textContent =
        `${empresas.length} empresas · Q ${empresas.reduce((a,e)=>a+e.utilizado,0).toFixed(2)} en cuentas por cobrar`;

      document.getElementById('empresaList').innerHTML = empresas.map(c => {
        const disponible = c.limiteCredito - c.utilizado;
        const pct = Math.min(100, (c.utilizado / c.limiteCredito) * 100);
        const overlimit = c.utilizado > c.limiteCredito;
        const atLimit = disponible <= 0;
        const barColor = pct >= 100 ? '#C62828' : pct >= 80 ? '#F57C00' : '#2E7D32';
        const availColor = disponible < 0 ? '#C62828' : '#2E7D32';
        const isExp = expandedId === c.id;
        const cardCls = overlimit ? 'empresa-card overlimit' : 'empresa-card';

        const empAbonos = abonos.filter(a => a.empresaId === c.id).slice(0, 20);
        const totalCargos = empAbonos.filter(a => a.tipo === 'CARGO').reduce((s,a)=>s+a.monto,0);
        const totalAbonos = empAbonos.filter(a => a.tipo === 'ABONO').reduce((s,a)=>s+a.monto,0);

        const detail = isExp ? `
          <div class="empresa-detail">
            ${overlimit ? `<div class="overlimit-banner" style="margin:14px 20px 0">
              <i class="bi bi-exclamation-triangle-fill"></i>
              <span>Esta empresa está sobregirada por <strong>Q ${(c.utilizado - c.limiteCredito).toFixed(2)}</strong>.</span>
            </div>` : ''}
            <div class="detail-bar">
              <span class="detail-bar-title">Movimientos recientes</span>
              <span style="font-size:12px;color:#9CA3AF">${empAbonos.length} registro(s)</span>
            </div>
            <div class="table-wrap">
              <table>
                <thead><tr>
                  <th>Fecha</th><th>Tipo</th><th>Referencia</th><th class="r">Monto</th>
                </tr></thead>
                <tbody>
                  ${empAbonos.length === 0 ? `
                    <tr><td colspan="4" style="text-align:center;padding:20px;color:#9CA3AF">Sin movimientos registrados.</td></tr>
                  ` : empAbonos.map(a => `
                    <tr>
                      <td style="color:#6B7280">${a.fecha}</td>
                      <td>
                        <span class="badge ${a.tipo === 'CARGO' ? 'badge-orange' : 'badge-green'}">${a.tipo}</span>
                      </td>
                      <td style="color:#6B7280;font-size:12px">${a.ref || '—'}</td>
                      <td class="r" style="font-weight:600;color:${a.tipo === 'CARGO' ? '#C62828' : '#2E7D32'}">
                        ${a.tipo === 'CARGO' ? '+' : '−'} Q ${a.monto.toFixed(2)}
                      </td>
                    </tr>`).join('')}
                </tbody>
              </table>
            </div>
            <div style="padding:12px 20px;border-top:1px solid var(--border-lt);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
              <div style="font-size:12.5px;color:#6B7280">
                Cargos: <strong style="color:#C62828">Q ${totalCargos.toFixed(2)}</strong> ·
                Abonos: <strong style="color:#2E7D32">Q ${totalAbonos.toFixed(2)}</strong> ·
                Saldo: <strong style="color:var(--navy)">Q ${c.utilizado.toFixed(2)}</strong>
              </div>
              <div style="display:flex;gap:8px">
                ${isAdmin ? `<button class="btn btn-outline btn-sm" onclick="openEmpresaForm(${c.id});event.stopPropagation()">
                  <i class="bi bi-pencil"></i> Editar
                </button>` : ''}
                <button class="btn btn-amber btn-sm" onclick="openPago(${c.id});event.stopPropagation()" ${c.utilizado <= 0 ? 'disabled' : ''}>
                  <i class="bi bi-credit-card"></i> Registrar Pago
                </button>
              </div>
            </div>
          </div>` : '';

        return `
          <div class="${cardCls}">
            <div class="empresa-header" onclick="toggleExpand(${c.id})">
              <div style="display:flex;align-items:flex-start;gap:16px">
                <div class="empresa-icon"><i class="bi bi-building"></i></div>
                <div style="flex:1;min-width:0">
                  <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:6px">
                    <span class="empresa-name">${c.nombre}</span>
                    ${overlimit ? '<span class="badge badge-danger badge-sm">SOBREGIRO</span>' :
                      atLimit ? '<span class="badge badge-danger badge-sm">LÍMITE ALCANZADO</span>' : ''}
                  </div>
                  <div class="empresa-nit">NIT ${c.nit}${c.contacto ? ` · ${c.contacto}` : ''}</div>
                  <div class="progress-label" style="margin-top:10px">
                    <span>Utilizado: <strong>Q ${c.utilizado.toFixed(2)}</strong></span>
                    <span>Límite: <strong>Q ${c.limiteCredito.toFixed(2)}</strong></span>
                  </div>
                  <div class="progress">
                    <div class="progress-fill" style="width:${Math.min(100, pct)}%;background:${barColor}"></div>
                  </div>
                </div>
                <div style="display:flex;align-items:center;gap:12px;flex-shrink:0">
                  <div style="text-align:right">
                    <div class="avail-amount" style="color:${availColor}">Q ${disponible.toFixed(2)}</div>
                    <div style="font-size:12px;color:#9CA3AF">Disponible</div>
                  </div>
                  <button class="btn btn-primary btn-sm" onclick="openPago(${c.id});event.stopPropagation()" ${c.utilizado <= 0 ? 'disabled' : ''}>
                    <i class="bi bi-plus"></i> Pago
                  </button>
                  <i class="bi ${isExp?'bi-chevron-up':'bi-chevron-down'} chevron"></i>
                </div>
              </div>
            </div>
            ${detail}
          </div>`;
      }).join('');

      if (empresas.length === 0) {
        document.getElementById('empresaList').innerHTML = `
          <div class="card" style="padding:40px;text-align:center;color:#9CA3AF">
            <i class="bi bi-building" style="font-size:36px;opacity:.4"></i>
            <p style="margin-top:10px">No hay empresas registradas con crédito.</p>
            ${isAdmin ? '<button class="btn btn-primary" style="margin-top:14px" onclick="openEmpresaForm()"><i class="bi bi-plus"></i> Registrar primera empresa</button>' : ''}
          </div>`;
      }
    }

    function renderMovimientos() {
      const body = document.getElementById('movTable');
      if (abonos.length === 0) {
        body.innerHTML = `<tr><td colspan="5" style="text-align:center;padding:30px;color:#9CA3AF">
          Aún no hay movimientos registrados.
        </td></tr>`;
        return;
      }
      body.innerHTML = abonos.map(a => {
        const emp = empresas.find(e => e.id === a.empresaId);
        const empName = emp ? emp.nombre : '— eliminada —';
        return `<tr>
          <td style="color:#6B7280">${a.fecha}</td>
          <td>${empName}</td>
          <td><span class="badge ${a.tipo === 'CARGO' ? 'badge-orange' : 'badge-green'}">${a.tipo}</span></td>
          <td style="color:#6B7280;font-size:12px">${a.ref || '—'}</td>
          <td class="r" style="font-weight:600;color:${a.tipo === 'CARGO' ? '#C62828' : '#2E7D32'}">
            ${a.tipo === 'CARGO' ? '+' : '−'} Q ${a.monto.toFixed(2)}
          </td>
        </tr>`;
      }).join('');
    }

    function toggleExpand(id) {
      expandedId = expandedId === id ? null : id;
      render();
    }

    /* ── Pago / abono ── */
    function openPago(empresaId) {
      currentPagoEmpresa = empresaId;
      const c = empresas.find(x => x.id === empresaId);
      document.getElementById('pagoEmpresaName').textContent = c.nombre;
      document.getElementById('pagoSaldoInfo').textContent =
        `Saldo actual: Q ${c.utilizado.toFixed(2)} · Límite: Q ${c.limiteCredito.toFixed(2)}`;
      document.getElementById('pagoMonto').value = '';
      document.getElementById('pagoRef').value = '';
      document.getElementById('pagoError').style.display = 'none';
      SIGRA.openModal('pagoModal');
    }

    function setMontoPercent(pct) {
      const c = empresas.find(x => x.id === currentPagoEmpresa);
      if (!c) return;
      document.getElementById('pagoMonto').value = (c.utilizado * pct).toFixed(2);
    }

    function clearPagoErr() {
      document.getElementById('pagoError').style.display = 'none';
    }

    function registrarPago() {
      const monto = Number(document.getElementById('pagoMonto').value);
      const ref   = document.getElementById('pagoRef').value.trim();
      if (!monto || monto <= 0) {
        document.getElementById('pagoError').style.display = 'block';
        return;
      }
      const result = SIGRA_DATA.registrarAbono(currentPagoEmpresa, monto, ref);
      SIGRA.closeModal('pagoModal');
      const emp = empresas.find(e => e.id === currentPagoEmpresa);
      showToast(
        result.liquidado ? 'Saldo liquidado' : 'Abono registrado',
        `${emp.nombre} · Q ${result.aplicado.toFixed(2)} aplicado${result.sobrante > 0 ? ` (sobrante Q ${result.sobrante.toFixed(2)} no aplicado)` : ''}`
      );
      reload();
    }

    /* ── Alta / edición de empresa ── */
    function openEmpresaForm(id) {
      editingEmpresaId = id || null;
      const m = id ? empresas.find(e => e.id === id) : null;
      document.getElementById('empresaModalTitle').textContent = m ? 'Editar Empresa' : 'Nueva Empresa';
      document.getElementById('empNombre').value   = m ? m.nombre : '';
      document.getElementById('empNit').value      = m ? m.nit : '';
      document.getElementById('empContacto').value = m ? (m.contacto || '') : '';
      document.getElementById('empTel').value      = m ? (m.telefono || '') : '';
      document.getElementById('empLimite').value   = m ? m.limiteCredito : '';
      document.getElementById('empError').style.display = 'none';
      SIGRA.openModal('empresaModal');
    }

    function guardarEmpresa() {
      const nombre = document.getElementById('empNombre').value.trim();
      const nit    = document.getElementById('empNit').value.trim();
      const limite = Number(document.getElementById('empLimite').value);
      if (!nombre || !nit || !limite || limite <= 0) {
        document.getElementById('empError').style.display = 'block';
        return;
      }
      const patch = {
        nombre, nit,
        contacto: document.getElementById('empContacto').value.trim(),
        telefono: document.getElementById('empTel').value.trim(),
        limiteCredito: limite,
      };
      if (editingEmpresaId) {
        SIGRA_DATA.updateEmpresa(editingEmpresaId, patch);
        showToast('Empresa actualizada', nombre);
      } else {
        SIGRA_DATA.addEmpresa({ ...patch, utilizado: 0 });
        showToast('Empresa registrada', nombre);
      }
      SIGRA.closeModal('empresaModal');
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
      if (e.key === SIGRA_DATA.KEYS.EMPRESAS || e.key === SIGRA_DATA.KEYS.ABONOS) reload();
    });
  </script>
</body>
</html>
