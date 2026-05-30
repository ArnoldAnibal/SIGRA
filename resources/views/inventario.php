<?php
require_once __DIR__ . '/includes/bootstrap.php';
sigra_require_role(['ADMIN']);
$SIGRA_USER = sigra_current_user();
$resMenu = api_request('GET', '/productos-menu', [], sigra_token());
$menuRaw = $resMenu['data']['data'] ?? $resMenu['data'] ?? [];
$SIGRA_INVENTARIO = array_map(fn($p) => [
    'id'          => $p['id_producto'],
    'nombre'      => $p['nombre'],
    'unidad'      => 'und',
    'stockActual' => (float)($p['stock'] ?? 0),
    'stockMinimo' => (float)($p['stock_minimo'] ?? 5),
    'categoria'   => 'Menú',
], $menuRaw);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIGRA — Inventario</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="styles.css">
  <style>
    .inv-body { padding: 24px; overflow-y: auto; height: calc(100vh - 64px - 73px); }
    .inv-grid { display: grid; grid-template-columns: 1fr 240px; gap: 24px; max-width: 1100px; margin: 0 auto; }
    @media (max-width: 900px) { .inv-grid { grid-template-columns: 1fr; } .alerts-sidebar { display: none; } }

    /* Alerts sidebar */
    .alert-item { padding: 12px 16px; border-bottom: 1px solid var(--border-lt); }
    .alert-item:last-child { border-bottom: none; }
    .alert-name  { font-size: 12.5px; font-weight: 600; color: #374151; }
    .alert-stock { font-size: 12px; color: #9CA3AF; margin-top: 2px; }

    /* Type selector */
    .type-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
    .type-btn {
      height: 44px; border-radius: var(--r); border: 2px solid var(--border);
      font-size: 13px; font-weight: 600; color: #6B7280;
      background: #fff; cursor: pointer; transition: all .15s;
    }
    .type-btn.active-in  { background: var(--green); color: #fff; border-color: var(--green); }
    .type-btn.active-out { background: var(--red);   color: #fff; border-color: var(--red); }
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
              <div class="page-title">Control de Inventario</div>
              <div class="page-sub" id="invSubtitle">— insumos · — alertas activas</div>
            </div>
            <button class="btn btn-primary" onclick="SIGRA.openModal('movModal')">
              <i class="bi bi-plus"></i> Registrar Movimiento
            </button>
          </div>
        </div>

        <div class="inv-body">
          <div class="inv-grid">

            <!-- Main table -->
            <div>
              <!-- Filter bar -->
              <div class="filter-bar" style="margin-bottom:16px">
                <i class="bi bi-filter" style="color:#9CA3AF;font-size:15px"></i>
                <button class="filter-chip active" id="chip-TODOS" onclick="setFilter('TODOS')">TODOS</button>
                <button class="filter-chip" id="chip-OK"      onclick="setFilter('OK')">OK <span id="cnt-OK"></span></button>
                <button class="filter-chip" id="chip-ALERTA"  onclick="setFilter('ALERTA')">ALERTA <span id="cnt-ALERTA"></span></button>
                <button class="filter-chip" id="chip-CRÍTICO" onclick="setFilter('CRÍTICO')">CRÍTICO <span id="cnt-CRÍTICO"></span></button>
              </div>

              <div class="card" style="overflow:hidden">
                <div class="table-wrap">
                  <table>
                    <thead>
                      <tr>
                        <th>Insumo</th>
                        <th>Categoría</th>
                        <th>Unidad</th>
                        <th class="r">Stock Actual</th>
                        <th class="r">Stock Mínimo</th>
                        <th>Estado</th>
                      </tr>
                    </thead>
                    <tbody id="invTable"></tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- Alerts sidebar -->
            <div class="alerts-sidebar">
              <div class="card" style="overflow:hidden">
                <div class="card-header">
                  <span style="font-size:14px;font-weight:600;color:var(--navy);display:flex;align-items:center;gap:6px">
                    <i class="bi bi-exclamation-triangle" style="color:var(--red)"></i> Alertas de Stock
                  </span>
                </div>
                <div id="alertsList"><!-- filled by JS --></div>
              </div>
            </div>

          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Movement Modal -->
  <div class="modal-overlay" id="movModal">
    <div class="modal" style="max-width:440px">
      <div class="modal-header">
        <h3 class="modal-title">Registrar Movimiento</h3>
        <button class="modal-close" onclick="SIGRA.closeModal('movModal')"><i class="bi bi-x"></i></button>
      </div>
      <div style="display:flex;flex-direction:column;gap:14px">
        <!-- Type -->
        <div>
          <label class="form-label">Tipo de movimiento</label>
          <div class="type-grid">
            <button class="type-btn active-in" id="typeIn"  onclick="setMovType('ENTRADA')">📦 ENTRADA</button>
            <button class="type-btn"           id="typeOut" onclick="setMovType('SALIDA')">📤 SALIDA</button>
          </div>
        </div>
        <!-- Item select -->
        <div>
          <label class="form-label">Insumo</label>
          <select id="movItem" class="form-control" onchange="clearMErr('itemErr')">
            <option value="">Seleccione un insumo...</option>
          </select>
          <div class="form-error" id="itemErr">Seleccione un insumo.</div>
        </div>
        <!-- Quantity -->
        <div>
          <label class="form-label">Cantidad</label>
          <input type="number" id="movQty" class="form-control" placeholder="0.00" oninput="clearMErr('qtyErr')">
          <div class="form-error" id="qtyErr">Ingrese una cantidad válida.</div>
        </div>
        <!-- Date -->
        <div>
          <label class="form-label">Fecha</label>
          <input type="date" id="movFecha" class="form-control" value="2026-05-08" oninput="clearMErr('fechaErr')">
          <div class="form-error" id="fechaErr">La fecha es requerida.</div>
        </div>
        <!-- Supplier ref -->
        <div>
          <label class="form-label">Referencia de proveedor <span style="color:#9CA3AF;font-weight:400">(opcional)</span></label>
          <input type="text" id="movRef" class="form-control" placeholder="No. factura o nota de entrega">
        </div>
        <div style="display:flex;gap:10px;margin-top:4px">
          <button class="btn btn-outline" style="flex:1" onclick="SIGRA.closeModal('movModal')">Cancelar</button>
          <button class="btn" id="movSubmitBtn" style="flex:1;color:#fff" onclick="submitMovimiento()">
            Registrar ENTRADA
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Success Toast -->
  <div class="toast toast-success" id="successToast">
    <i class="bi bi-check-circle" style="font-size:18px"></i>
    <span class="toast-title">Movimiento registrado exitosamente</span>
  </div>

<?= sigra_bootstrap_script([
    'session' => $SIGRA_USER,
    'inventario' => $SIGRA_INVENTARIO
]) ?>

  <script src="layout.js"></script>
  <script>
    SIGRA.initLayout('Inventario');

    const boot = (() => {
    try { return JSON.parse(document.getElementById('sigra-bootstrap').textContent.trim() || '{}'); }
    catch { return {}; }
})();
let items = boot.inventario || [];

    const statusConfig = {
      OK:       { bg:'#E8F5E9', text:'#2E7D32', label:'OK'      },
      ALERTA:   { bg:'#FFF3E0', text:'#E65100', label:'ALERTA'  },
      CRÍTICO:  { bg:'#FFEBEE', text:'#C62828', label:'CRÍTICO' },
    };

    function getStatus(item) {
      const r = item.stockActual / item.stockMinimo;
      if (r <= 0.5) return 'CRÍTICO';
      if (r <= 1)   return 'ALERTA';
      return 'OK';
    }

    let activeFilter = 'TODOS';
    let movType = 'ENTRADA';

    function setFilter(f) {
      activeFilter = f;
      ['TODOS','OK','ALERTA','CRÍTICO'].forEach(k => {
        document.getElementById(`chip-${k}`).classList.toggle('active', k === f);
      });
      renderTable();
    }

    function renderTable() {
      const alerts  = items.filter(i => getStatus(i) !== 'OK');
      const filtered = activeFilter === 'TODOS' ? items : items.filter(i => getStatus(i) === activeFilter);

      // Update subtitle
      document.getElementById('invSubtitle').textContent =
        `${items.length} insumos · ${alerts.length} alertas activas`;

      // Update counts on chips
      ['OK','ALERTA','CRÍTICO'].forEach(k => {
        const cnt = document.getElementById(`cnt-${k}`);
        if (cnt) cnt.textContent = `(${items.filter(i => getStatus(i) === k).length})`;
      });

      // Table
      if (filtered.length === 0) {
        document.getElementById('invTable').innerHTML = `
          <tr><td colspan="6" style="text-align:center;padding:40px;color:#9CA3AF">
            <i class="bi bi-box-seam" style="font-size:32px;display:block;margin-bottom:8px;opacity:.4"></i>
            No hay insumos con este filtro
          </td></tr>`;
      } else {
        document.getElementById('invTable').innerHTML = filtered.map(item => {
          const s = getStatus(item);
          const cfg = statusConfig[s];
          const stockColor = s === 'CRÍTICO' ? '#C62828' : s === 'ALERTA' ? '#E65100' : '#1F2937';
          const rowBg = s === 'CRÍTICO' ? 'background:rgba(255,235,238,.3)' : '';
          return `
            <tr style="${rowBg}">
              <td style="font-weight:500;color:#1F2937">${item.nombre}</td>
              <td style="color:#6B7280">${item.categoria}</td>
              <td style="color:#6B7280">${item.unidad}</td>
              <td class="r" style="font-weight:700;color:${stockColor}">${item.stockActual}</td>
              <td class="r" style="color:#6B7280">${item.stockMinimo}</td>
              <td><span class="badge" style="background:${cfg.bg};color:${cfg.text}">${cfg.label}</span></td>
            </tr>`;
        }).join('');
      }

      // Alerts sidebar
      if (alerts.length === 0) {
        document.getElementById('alertsList').innerHTML = `
          <div style="padding:20px;text-align:center;color:#9CA3AF">
            <i class="bi bi-check-circle" style="font-size:24px;display:block;margin-bottom:8px;opacity:.4"></i>
            <p style="font-size:12px">Sin alertas activas</p>
          </div>`;
      } else {
        document.getElementById('alertsList').innerHTML = alerts.map(item => {
          const s = getStatus(item);
          const cfg = statusConfig[s];
          return `
            <div class="alert-item">
              <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px">
                <div>
                  <div class="alert-name">${item.nombre}</div>
                  <div class="alert-stock">${item.stockActual} / ${item.stockMinimo} ${item.unidad}</div>
                </div>
                <span class="badge badge-sm" style="background:${cfg.bg};color:${cfg.text};flex-shrink:0">${cfg.label}</span>
              </div>
            </div>`;
        }).join('');
      }
    }

    /* ── Movement form ── */
    function setMovType(type) {
      movType = type;
      document.getElementById('typeIn').className  = `type-btn ${type==='ENTRADA' ? 'active-in' : ''}`;
      document.getElementById('typeOut').className = `type-btn ${type==='SALIDA'  ? 'active-out' : ''}`;
      document.getElementById('movSubmitBtn').textContent = `Registrar ${type}`;
      document.getElementById('movSubmitBtn').style.background = type === 'ENTRADA' ? '#2E7D32' : '#C62828';
    }
    setMovType('ENTRADA');

    function populateItemSelect() {
      const sel = document.getElementById('movItem');
      sel.innerHTML = '<option value="">Seleccione un insumo...</option>' +
        items.map(i => `<option value="${i.nombre}">${i.nombre}</option>`).join('');
    }

    function clearMErr(id) {
      document.getElementById(id).style.display = 'none';
    }

    function submitMovimiento() {
      const itemName = document.getElementById('movItem').value;
      const qty      = Number(document.getElementById('movQty').value);
      const fecha    = document.getElementById('movFecha').value;
      let valid = true;
      if (!itemName)              { document.getElementById('itemErr').style.display='block';  valid = false; }
      if (!qty || qty <= 0)       { document.getElementById('qtyErr').style.display='block';   valid = false; }
      if (!fecha)                 { document.getElementById('fechaErr').style.display='block'; valid = false; }
      if (!valid) return;

      items = items.map(item => {
        if (item.nombre === itemName) {
          const newStock = movType === 'ENTRADA'
            ? item.stockActual + qty
            : Math.max(0, item.stockActual - qty);
          return { ...item, stockActual: newStock };
        }
        return item;
      });

      SIGRA.closeModal('movModal');
      document.getElementById('movItem').value = '';
      document.getElementById('movQty').value  = '';
      SIGRA.showToast('successToast', 2500);
      renderTable();
    }

    /* Init */
    populateItemSelect();
    renderTable();
    // Re-populate when modal opens
    document.getElementById('movModal').addEventListener('click', e => {
      if (e.target === e.currentTarget) SIGRA.closeModal('movModal');
    });
  </script>
</body>
</html>
