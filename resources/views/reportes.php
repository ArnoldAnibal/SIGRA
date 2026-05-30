<?php
require_once __DIR__ . '/includes/bootstrap.php';
sigra_require_role(['ADMIN']);
$SIGRA_USER = sigra_current_user();
$resRep = api_pedidos(sigra_token()); $allPedidos = $resRep["data"]["data"] ?? $resRep["data"] ?? []; $SIGRA_VENTAS_MES = $allPedidos; $SIGRA_TOP_PRODS = []; $SIGRA_VENTAS_MESERO = [];

$resRep = api_pedidos(sigra_token()); $allPedidos = $resRep["data"]["data"] ?? $resRep["data"] ?? []; $SIGRA_VENTAS_MES = $allPedidos; $SIGRA_TOP_PRODS = []; $SIGRA_VENTAS_MESERO = [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIGRA — Reportes y Análisis</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="styles.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.0/jspdf.plugin.autotable.min.js"></script>
  <style>
    .rep-body  { padding: 24px; overflow-y: auto; height: calc(100vh - 64px - 73px); }
    .rep-inner { max-width: 1200px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px; }

    .rep-tabs { display: flex; gap: 4px; border-bottom: 2px solid var(--border); overflow-x: auto; }
    .rep-tab {
      padding: 10px 18px; font-size: 13.5px; font-weight: 600;
      background: none; border: none; cursor: pointer; white-space: nowrap;
      color: #6B7280; border-bottom: 2px solid transparent; margin-bottom: -2px;
    }
    .rep-tab:hover { color: var(--navy); }
    .rep-tab.active { color: var(--navy); border-bottom-color: var(--amber); }

    .filter-bar {
      background: #fff; border-radius: var(--r-lg); border: 1px solid var(--border-lt);
      padding: 14px 18px; display: flex; align-items: end; gap: 12px; flex-wrap: wrap;
    }
    .filter-bar label { font-size: 12px; color: #6B7280; font-weight: 600; display: block; margin-bottom: 4px; }
    .filter-bar input, .filter-bar select { padding: 8px 10px; }

    .chart-bar {
      display: flex; flex-direction: column; gap: 6px;
    }
    .chart-row {
      display: flex; align-items: center; gap: 12px;
    }
    .chart-row-label { width: 200px; font-size: 13px; color: #374151; flex-shrink: 0; }
    .chart-row-track {
      flex: 1; height: 22px; background: #F4F6FA; border-radius: 6px; overflow: hidden;
    }
    .chart-row-fill {
      height: 100%; background: linear-gradient(90deg, var(--navy), #1E40AF);
      display: flex; align-items: center; justify-content: flex-end; padding: 0 8px;
      color: #fff; font-size: 11px; font-weight: 600;
      transition: width .4s ease;
    }
    .chart-row-val { width: 100px; text-align: right; font-size: 13px; font-weight: 600; color: var(--navy); flex-shrink: 0; }
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
              <div class="page-title">Reportes y Análisis</div>
              <div class="page-sub">Indicadores clave del negocio</div>
            </div>
            <button class="btn btn-primary" id="btnExport">
              <i class="bi bi-download"></i> Exportar reporte actual
            </button>
          </div>
        </div>

        <div class="rep-body">
          <div class="rep-inner">

            <div class="rep-tabs">
              <button class="rep-tab active" data-tab="ventas" onclick="setTab('ventas')">
                <i class="bi bi-graph-up"></i> Ventas
              </button>
              <button class="rep-tab" data-tab="productos" onclick="setTab('productos')">
                <i class="bi bi-bar-chart-line"></i> Productos top
              </button>
              <button class="rep-tab" data-tab="credito" onclick="setTab('credito')">
                <i class="bi bi-building"></i> Crédito empresarial
              </button>
              <button class="rep-tab" data-tab="proveedores" onclick="setTab('proveedores')">
                <i class="bi bi-truck"></i> Proveedores
              </button>
              <button class="rep-tab" data-tab="planillas" onclick="setTab('planillas')">
                <i class="bi bi-people"></i> Planillas
              </button>
            </div>

            <!-- Filtros (solo para ventas y productos) -->
            <div class="filter-bar" id="filterBar">
              <div>
                <label>Desde</label>
                <input type="date" id="fDesde" class="form-control" onchange="render()">
              </div>
              <div>
                <label>Hasta</label>
                <input type="date" id="fHasta" class="form-control" onchange="render()">
              </div>
              <div style="margin-left:auto">
                <button class="btn btn-outline btn-sm" onclick="setRangoRapido(7)">Últimos 7 días</button>
                <button class="btn btn-outline btn-sm" onclick="setRangoRapido(30)">30 días</button>
                <button class="btn btn-outline btn-sm" onclick="setRangoRapido('mes')">Mes actual</button>
              </div>
            </div>

            <!-- Content -->
            <div id="repContent"></div>

          </div>
        </div>
      </main>
    </div>
  </div>

<?= sigra_bootstrap_script([
    'session' => $SIGRA_USER,
    'ventas_mes' => $SIGRA_VENTAS_MES, 'top_prods' => $SIGRA_TOP_PRODS, 'ventas_mesero' => $SIGRA_VENTAS_MESERO
]) ?>

  <script src="layout.js"></script>
  <script src="sigra-data.js"></script>
  <script src="sigra-pdf.js"></script>
  <script>
    SIGRA.initLayout('Reportes y Análisis');

    let currentTab = 'ventas';
    let lastReport = null;  /* { title, head, body } para exportar */

    /* Inicializa rango: últimos 30 días */
    setRangoRapido(30, true);

    function setRangoRapido(opt, init) {
      const hasta = new Date();
      let desde = new Date();
      if (opt === 'mes') {
        desde = new Date(hasta.getFullYear(), hasta.getMonth(), 1);
      } else {
        desde.setDate(hasta.getDate() - opt);
      }
      document.getElementById('fDesde').value = desde.toISOString().slice(0, 10);
      document.getElementById('fHasta').value = hasta.toISOString().slice(0, 10);
      if (!init) render();
    }

    function setTab(t) {
      currentTab = t;
      document.querySelectorAll('.rep-tab').forEach(b => b.classList.toggle('active', b.dataset.tab === t));
      const hideFilter = (t === 'credito' || t === 'planillas');
      document.getElementById('filterBar').style.display = hideFilter ? 'none' : 'flex';
      render();
    }

    function filtrarFacturas() {
    const boot = (() => {
        try { return JSON.parse(document.getElementById('sigra-bootstrap').textContent.trim() || '{}'); }
        catch { return {}; }
    })();
    const desde = document.getElementById('fDesde').value;
    const hasta = document.getElementById('fHasta').value;
    const pedidos = boot.ventas_mes || [];
    return pedidos
        .filter(p => p.estado === 'Entregado')
        .filter(p => {
            const fecha = (p.created_at || '').slice(0, 10);
            return fecha >= desde && fecha <= hasta;
        })
        .map(p => ({
            fecha: (p.created_at || '').slice(0, 10),
            pedidoId: 'PED-' + String(p.id_pedido).padStart(3, '0'),
            mesero: 'Empleado #' + p.id_empleado,
            nombre: p.tipo || 'Cliente',
            nit: 'CF',
            tipo: p.metodo_pago === 'Credito' ? 'CREDITO' : 'CONTADO',
            total: Number(p.total),
            subtotal: Number(p.total),
            iva: 0,
            items: (p.detalles || []).map(d => ({
                nombre: 'Producto #' + d.id_producto,
                cantidad: d.cantidad,
                precio: Number(d.subtotal) / d.cantidad,
            })),
        }));
}

    function render() {
      switch (currentTab) {
        case 'ventas':       renderVentas();      break;
        case 'productos':    renderProductos();   break;
        case 'credito':      renderCredito();     break;
        case 'proveedores':  renderProveedores(); break;
        case 'planillas':    renderPlanillas();   break;
      }
    }

    /* ── Ventas ── */
    function renderVentas() {
      const facts = filtrarFacturas();
      const total = facts.reduce((s, f) => s + f.total, 0);
      const ivaTot = facts.reduce((s, f) => s + (f.iva || 0), 0);
      const subTot = facts.reduce((s, f) => s + (f.subtotal || 0), 0);
      const contado = facts.filter(f => f.tipo !== 'CREDITO').reduce((s, f) => s + f.total, 0);
      const credito = facts.filter(f => f.tipo === 'CREDITO').reduce((s, f) => s + f.total, 0);
      const ticketProm = facts.length ? total / facts.length : 0;

      /* Agrupar por día */
      const porDia = {};
      facts.forEach(f => {
        porDia[f.fecha] = (porDia[f.fecha] || 0) + f.total;
      });
      const dias = Object.keys(porDia).sort();
      const maxDia = Math.max(...Object.values(porDia), 1);

      document.getElementById('repContent').innerHTML = `
        <div class="grid-4">
          <div class="kpi-card"><p class="text-xs text-muted mb-1">Ventas totales</p>
            <p style="font-size:22px;font-weight:800;color:var(--navy)">Q ${total.toFixed(2)}</p>
            <p class="text-xs text-muted mt-1">${facts.length} facturas</p>
          </div>
          <div class="kpi-card"><p class="text-xs text-muted mb-1">Ticket promedio</p>
            <p style="font-size:22px;font-weight:800;color:#1565C0">Q ${ticketProm.toFixed(2)}</p>
          </div>
          <div class="kpi-card"><p class="text-xs text-muted mb-1">Contado</p>
            <p style="font-size:22px;font-weight:800;color:#2E7D32">Q ${contado.toFixed(2)}</p>
          </div>
          <div class="kpi-card"><p class="text-xs text-muted mb-1">Crédito</p>
            <p style="font-size:22px;font-weight:800;color:#E65100">Q ${credito.toFixed(2)}</p>
          </div>
        </div>

        <div class="card" style="margin-top:18px">
          <div class="card-header"><span class="card-title">Ventas por día</span></div>
          <div class="card-body">
            ${dias.length === 0 ? '<p style="color:#9CA3AF;font-size:13px;text-align:center;padding:20px">Sin ventas en el rango.</p>' : `
              <div class="chart-bar">
                ${dias.map(d => `
                  <div class="chart-row">
                    <span class="chart-row-label">${d}</span>
                    <div class="chart-row-track">
                      <div class="chart-row-fill" style="width:${(porDia[d]/maxDia)*100}%"></div>
                    </div>
                    <span class="chart-row-val">Q ${porDia[d].toFixed(2)}</span>
                  </div>`).join('')}
              </div>`}
          </div>
        </div>

        <div class="card" style="margin-top:18px">
          <div class="card-header"><span class="card-title"><i class="bi bi-person-badge"></i> Ventas por mesero</span></div>
          <div class="card-body">
            ${(() => {
              const porMesero = {};
              facts.forEach(f => {
                const m = f.mesero || 'Sin asignar';
                if (!porMesero[m]) porMesero[m] = { total: 0, cant: 0 };
                porMesero[m].total += f.total;
                porMesero[m].cant++;
              });
              const rows = Object.entries(porMesero).sort((a,b) => b[1].total - a[1].total);
              if (!rows.length) return '<p style="color:#9CA3AF;font-size:13px;text-align:center;padding:20px">Sin ventas registradas.</p>';
              const maxT = Math.max(...rows.map(r => r[1].total), 1);
              return `<div class="chart-bar">${rows.map(([nombre, info]) => `
                <div class="chart-row">
                  <span class="chart-row-label" style="min-width:140px"><i class="bi bi-person-circle"></i> ${nombre}</span>
                  <div class="chart-row-track">
                    <div class="chart-row-fill" style="width:${(info.total/maxT)*100}%;background:linear-gradient(90deg,#1565C0,#1A2E4A)"></div>
                  </div>
                  <span class="chart-row-val">Q ${info.total.toFixed(2)} <small style="color:#9CA3AF">(${info.cant})</small></span>
                </div>`).join('')}</div>`;
            })()}
          </div>
        </div>

        <div class="card" style="margin-top:18px">
          <div class="card-header"><span class="card-title">Resumen</span></div>
          <div class="card-body">
            <div style="display:flex;justify-content:space-between;padding:10px 0;font-weight:700;color:var(--navy)">
              <span>TOTAL FACTURADO</span><strong>Q ${total.toFixed(2)}</strong>
            </div>
          </div>
        </div>`;

      lastReport = {
        title: 'Reporte de Ventas',
        subtitulo: `Del ${document.getElementById('fDesde').value} al ${document.getElementById('fHasta').value}`,
        head: ['Fecha', 'Pedido', 'Mesero', 'Receptor', 'Tipo', 'Total'],
        body: facts.map(f => [
          f.fecha, f.pedidoId, f.mesero || '—', f.nombre, f.tipo,
          `Q ${f.total.toFixed(2)}`,
        ]),
        landscape: true,
      };
    }

    /* ── Productos top ── */
    function renderProductos() {
      const facts = filtrarFacturas();
      const conteo = {};
      facts.forEach(f => f.items.forEach(i => {
        if (!conteo[i.nombre]) conteo[i.nombre] = { cant:0, total:0 };
        conteo[i.nombre].cant  += i.cantidad;
        conteo[i.nombre].total += i.cantidad * i.precio;
      }));
      const arr = Object.entries(conteo).map(([nombre, v]) => ({ nombre, ...v }))
        .sort((a, b) => b.total - a.total);
      const max = Math.max(...arr.map(a => a.total), 1);

      document.getElementById('repContent').innerHTML = `
        <div class="card">
          <div class="card-header"><span class="card-title">Top productos por venta</span></div>
          <div class="card-body">
            ${arr.length === 0 ? '<p style="color:#9CA3AF;font-size:13px;text-align:center;padding:20px">Sin productos vendidos en el rango.</p>' : `
              <div class="chart-bar">
                ${arr.map(p => `
                  <div class="chart-row">
                    <span class="chart-row-label">${p.nombre}</span>
                    <div class="chart-row-track">
                      <div class="chart-row-fill" style="width:${(p.total/max)*100}%">${p.cant}</div>
                    </div>
                    <span class="chart-row-val">Q ${p.total.toFixed(2)}</span>
                  </div>`).join('')}
              </div>`}
          </div>
        </div>`;

      lastReport = {
        title: 'Top Productos',
        subtitulo: `Del ${document.getElementById('fDesde').value} al ${document.getElementById('fHasta').value}`,
        head: ['Producto', 'Cantidad vendida', 'Total'],
        body: arr.map(p => [p.nombre, p.cant, `Q ${p.total.toFixed(2)}`]),
      };
    }

    /* ── Crédito ── */
    function renderCredito() {
      const empresas = SIGRA_DATA.getEmpresas();
      const abonos = SIGRA_DATA.getAbonos();
      const totalLimite = empresas.reduce((s, e) => s + e.limiteCredito, 0);
      const totalUtilizado = empresas.reduce((s, e) => s + e.utilizado, 0);
      const totalAbonos = abonos.filter(a => a.tipo === 'ABONO').reduce((s,a)=>s+a.monto,0);
      const totalCargos = abonos.filter(a => a.tipo === 'CARGO').reduce((s,a)=>s+a.monto,0);

      document.getElementById('repContent').innerHTML = `
        <div class="grid-4">
          <div class="kpi-card"><p class="text-xs text-muted mb-1">Límite total</p>
            <p style="font-size:22px;font-weight:800;color:var(--navy)">Q ${totalLimite.toFixed(2)}</p>
          </div>
          <div class="kpi-card"><p class="text-xs text-muted mb-1">Por cobrar</p>
            <p style="font-size:22px;font-weight:800;color:#C62828">Q ${totalUtilizado.toFixed(2)}</p>
          </div>
          <div class="kpi-card"><p class="text-xs text-muted mb-1">Total abonado</p>
            <p style="font-size:22px;font-weight:800;color:#2E7D32">Q ${totalAbonos.toFixed(2)}</p>
          </div>
          <div class="kpi-card"><p class="text-xs text-muted mb-1">Total cargado</p>
            <p style="font-size:22px;font-weight:800;color:#E65100">Q ${totalCargos.toFixed(2)}</p>
          </div>
        </div>

        <div class="card" style="margin-top:18px">
          <div class="card-header"><span class="card-title">Estado por empresa</span></div>
          <div class="table-wrap">
            <table>
              <thead><tr>
                <th>Empresa</th><th>NIT</th>
                <th class="r">Límite</th><th class="r">Utilizado</th>
                <th class="r">Disponible</th><th>%</th>
              </tr></thead>
              <tbody>
                ${empresas.map(e => {
                  const disp = e.limiteCredito - e.utilizado;
                  const pct  = (e.utilizado / e.limiteCredito) * 100;
                  const col  = pct >= 100 ? '#C62828' : pct >= 80 ? '#F57C00' : '#2E7D32';
                  return `<tr>
                    <td>${e.nombre}</td>
                    <td style="color:#6B7280;font-size:12px">${e.nit}</td>
                    <td class="r">Q ${e.limiteCredito.toFixed(2)}</td>
                    <td class="r" style="font-weight:600">Q ${e.utilizado.toFixed(2)}</td>
                    <td class="r" style="color:${disp<0?'#C62828':'#2E7D32'}">Q ${disp.toFixed(2)}</td>
                    <td>
                      <div class="chart-row-track" style="width:120px;height:14px">
                        <div class="chart-row-fill" style="background:${col};width:${Math.min(100,pct)}%"></div>
                      </div>
                    </td>
                  </tr>`;
                }).join('')}
              </tbody>
            </table>
          </div>
        </div>`;

      lastReport = {
        title: 'Reporte de Crédito Empresarial',
        subtitulo: `Al ${new Date().toISOString().slice(0,10)}`,
        head: ['Empresa', 'NIT', 'Límite', 'Utilizado', 'Disponible', '%'],
        body: empresas.map(e => {
          const disp = e.limiteCredito - e.utilizado;
          const pct  = (e.utilizado / e.limiteCredito) * 100;
          return [e.nombre, e.nit,
            `Q ${e.limiteCredito.toFixed(2)}`, `Q ${e.utilizado.toFixed(2)}`,
            `Q ${disp.toFixed(2)}`, `${pct.toFixed(0)}%`];
        }),
      };
    }

    /* ── Proveedores ── */
    function renderProveedores() {
      const proveedores = SIGRA_DATA.getProveedores();
      const cuentas = SIGRA_DATA.getCuentasProv();
      const hoy = new Date().toISOString().slice(0,10);

      const porProv = proveedores.map(p => {
        const ctas = cuentas.filter(c => c.proveedorId === p.id);
        return {
          ...p,
          cuentas: ctas.length,
          vigente: ctas.filter(c => c.estado !== 'PAGADA' && c.fechaVence >= hoy).reduce((s,c)=>s+c.monto,0),
          vencido: ctas.filter(c => c.estado !== 'PAGADA' && c.fechaVence < hoy).reduce((s,c)=>s+c.monto,0),
          pagado:  ctas.filter(c => c.estado === 'PAGADA').reduce((s,c)=>s+c.monto,0),
        };
      });
      const totalVig = porProv.reduce((s,p)=>s+p.vigente,0);
      const totalVenc = porProv.reduce((s,p)=>s+p.vencido,0);
      const totalPag  = porProv.reduce((s,p)=>s+p.pagado,0);

      document.getElementById('repContent').innerHTML = `
        <div class="grid-3">
          <div class="kpi-card"><p class="text-xs text-muted mb-1">Por pagar vigente</p>
            <p style="font-size:22px;font-weight:800;color:#1565C0">Q ${totalVig.toFixed(2)}</p>
          </div>
          <div class="kpi-card"><p class="text-xs text-muted mb-1">Vencido</p>
            <p style="font-size:22px;font-weight:800;color:#C62828">Q ${totalVenc.toFixed(2)}</p>
          </div>
          <div class="kpi-card"><p class="text-xs text-muted mb-1">Pagado</p>
            <p style="font-size:22px;font-weight:800;color:#2E7D32">Q ${totalPag.toFixed(2)}</p>
          </div>
        </div>

        <div class="card" style="margin-top:18px">
          <div class="card-header"><span class="card-title">Resumen por proveedor</span></div>
          <div class="table-wrap">
            <table>
              <thead><tr>
                <th>Proveedor</th><th class="r">Cuentas</th>
                <th class="r">Vigente</th><th class="r">Vencido</th><th class="r">Pagado</th>
              </tr></thead>
              <tbody>
                ${porProv.map(p => `<tr>
                  <td><strong>${p.nombre}</strong><br><span style="font-size:11px;color:#9CA3AF">${p.contacto || ''}</span></td>
                  <td class="r">${p.cuentas}</td>
                  <td class="r" style="color:#1565C0">Q ${p.vigente.toFixed(2)}</td>
                  <td class="r" style="color:#C62828;font-weight:600">Q ${p.vencido.toFixed(2)}</td>
                  <td class="r" style="color:#2E7D32">Q ${p.pagado.toFixed(2)}</td>
                </tr>`).join('')}
              </tbody>
            </table>
          </div>
        </div>`;

      lastReport = {
        title: 'Reporte de Proveedores',
        subtitulo: `Al ${hoy}`,
        head: ['Proveedor', 'Cuentas', 'Vigente', 'Vencido', 'Pagado'],
        body: porProv.map(p => [p.nombre, p.cuentas,
          `Q ${p.vigente.toFixed(2)}`,
          `Q ${p.vencido.toFixed(2)}`,
          `Q ${p.pagado.toFixed(2)}`]),
      };
    }

    /* ── Planillas ── */
    function renderPlanillas() {
      const planillas = SIGRA_DATA.getPlanillas();
      document.getElementById('repContent').innerHTML = `
        <div class="card">
          <div class="card-header"><span class="card-title">Planillas generadas</span></div>
          <div class="table-wrap">
            <table>
              <thead><tr>
                <th>Período</th><th>Fecha generación</th><th class="r">Empleados</th>
                <th class="r">Bruto</th><th class="r">IGSS</th><th class="r">ISR</th><th class="r">Neto</th>
              </tr></thead>
              <tbody>
                ${planillas.length === 0
                  ? '<tr><td colspan="7" style="text-align:center;padding:30px;color:#9CA3AF">Sin planillas generadas.</td></tr>'
                  : planillas.map(p => `<tr>
                    <td><strong>${p.periodo}</strong></td>
                    <td style="color:#6B7280">${p.fechaGeneracion}</td>
                    <td class="r">${p.empleados.length}</td>
                    <td class="r">Q ${p.totales.bruto.toFixed(2)}</td>
                    <td class="r">Q ${p.totales.igss.toFixed(2)}</td>
                    <td class="r">Q ${p.totales.isr.toFixed(2)}</td>
                    <td class="r" style="color:#2E7D32;font-weight:700">Q ${p.totales.neto.toFixed(2)}</td>
                  </tr>`).join('')}
              </tbody>
            </table>
          </div>
        </div>`;

      lastReport = {
        title: 'Historial de Planillas',
        head: ['Período', 'Generación', 'Empleados', 'Bruto', 'IGSS', 'ISR', 'Neto'],
        body: planillas.map(p => [p.periodo, p.fechaGeneracion, p.empleados.length,
          `Q ${p.totales.bruto.toFixed(2)}`, `Q ${p.totales.igss.toFixed(2)}`,
          `Q ${p.totales.isr.toFixed(2)}`, `Q ${p.totales.neto.toFixed(2)}`]),
      };
    }

    /* ── Export ── */
    document.getElementById('btnExport').addEventListener('click', () => {
      if (!lastReport) return;
      SIGRA_PDF.descargarReporte(lastReport.title, lastReport.head, lastReport.body, {
        subtitulo: lastReport.subtitulo,
        filename: `${lastReport.title.replace(/\s+/g,'_')}_${Date.now()}.pdf`,
        landscape: lastReport.landscape,
      });
    });

    render();
  </script>
</body>
</html>
