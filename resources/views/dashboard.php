<?php
require_once __DIR__ . '/includes/bootstrap.php';
sigra_require_role(['ADMIN']);
$SIGRA_USER = sigra_current_user();
$SIGRA_KPIS = [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIGRA — Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="styles.css">
  <style>
    /* ── Dashboard-specific ── */
    .dashboard-body { padding: 24px; display: flex; flex-direction: column; gap: 24px; }

    /* Header row */
    .dash-header { display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
    .dash-header h1 { font-size: 22px; font-weight: 700; color: var(--navy); }
    .dash-header p  { font-size: 13px; color: var(--text-muted); margin-top: 2px; }

    /* Chart tabs */
    .chart-tabs { display: flex; gap: 4px; background: #F3F4F6; border-radius: 8px; padding: 4px; }
    .chart-tab  {
      padding: 5px 14px; border-radius: 6px; font-size: 12px; font-weight: 500;
      color: #6B7280; transition: all .15s;
    }
    .chart-tab.active { background: #fff; color: #1F2937; box-shadow: 0 1px 3px rgba(0,0,0,.1); }

    /* Chart container */
    .chart-area { width: 100%; height: 210px; }
    .chart-area svg { width: 100%; height: 100%; display: block; }

    /* Mesa status dots */
    .mesa-dot { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; }

    /* Orders table link */
    .table-action { font-size: 13px; color: var(--text-muted); display: flex; align-items: center; gap: 4px; }
    .table-action:hover { color: var(--text-secondary); }

    /* Row actions */
    .row-action { color: #D1D5DB; font-size: 17px; padding: 4px; transition: color .15s; }
    .row-action:hover { color: #6B7280; }

    /* Right panel column layout */
    .charts-row { display: grid; grid-template-columns: 1fr 300px; gap: 24px; }
    @media (max-width: 1024px) { .charts-row { grid-template-columns: 1fr; } }
  </style>
</head>
<body>
  <div class="app">
    <!-- Sidebar overlay (mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar"></aside>

    <!-- Main -->
    <div class="main-wrap" id="mainWrap">
      <header class="topbar" id="topbar"></header>

      <main class="page-content">
        <div class="dashboard-body">

          <!-- Header -->
          <div class="dash-header">
            <div>
              <h1>Panel Principal</h1>
              <p>Viernes, 8 de mayo de 2026 · Puerto Barrios, Izabal</p>
            </div>
            <a href="pedidos-mesa.php" class="btn btn-amber">
              <i class="bi bi-bag"></i> Nuevo Pedido
            </a>
          </div>

          <!-- KPIs -->
          <div class="grid-4" id="kpiGrid">
            <!-- filled by JS -->
          </div>

          <!-- Charts + Mesa Status -->
          <div class="charts-row">
            <!-- Area/Bar Chart -->
            <div class="card">
              <div class="card-header">
                <span class="card-title">Ventas</span>
                <div class="chart-tabs">
                  <button class="chart-tab active" id="tabHoy" onclick="switchTab('hoy')">Hoy</button>
                  <button class="chart-tab" id="tabSemana" onclick="switchTab('semana')">Semana</button>
                </div>
              </div>
              <div class="card-body" style="padding-bottom:16px">
                <div class="chart-area" id="chartArea"></div>
              </div>
            </div>

            <!-- Mesa Status -->
            <div class="card">
              <div class="card-header">
                <span class="card-title">Estado de Mesas</span>
              </div>
              <div class="card-body">
                <div class="space-y-3" id="mesaStatus">
                  <!-- filled by JS -->
                </div>
                <div style="border-top:1px solid #F3F4F6;margin-top:16px;padding-top:16px">
                  <a href="pedidos-mesa.php" style="color:#E8A020;font-size:13px;font-weight:600;display:flex;align-items:center;gap:4px;">
                    Ver todas las mesas <i class="bi bi-arrow-up-right"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Orders Table -->
          <div class="card">
            <div class="card-header">
              <span class="card-title">Pedidos Recientes</span>
              <a href="pedidos-mesa.php" class="table-action">
                Ver todos <i class="bi bi-arrow-up-right"></i>
              </a>
            </div>
            <div class="table-wrap">
              <table>
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Mesa / Tipo</th>
                    <th>Estado</th>
                    <th>Total</th>
                    <th>Hora</th>
                    <th class="r">Acciones</th>
                  </tr>
                </thead>
                <tbody id="ordersTable"></tbody>
              </table>
            </div>
          </div>

        </div>
      </main>
    </div>
  </div>

<?= sigra_bootstrap_script([
    'session' => $SIGRA_USER,
    // TODO BACKEND: agregar aquí los datos cargados desde BD (kpis, pedidos, etc.)
]) ?>

  <script src="layout.js"></script>
  <script>
    SIGRA.initLayout('Panel Principal');

    /* ─── Data ─── */
    const kpis = [
      { label:'Ventas del Día',    value:'Q 9,847.50', sub:'+12% vs ayer',       icon:'bi-graph-up',    color:'#1A2E4A', bg:'#EEF2FF' },
      { label:'Pedidos Activos',   value:'8',          sub:'3 en preparación',    icon:'bi-bag',         color:'#1976D2', bg:'#E3F2FD' },
      { label:'Facturas Emitidas', value:'47',         sub:'Hoy · FEL activo',    icon:'bi-receipt',     color:'#2E7D32', bg:'#E8F5E9' },
      { label:'Alertas de Stock',  value:'4',          sub:'2 críticos',          icon:'bi-exclamation-triangle', color:'#C62828', bg:'#FFEBEE' },
    ];

    const salesHoy = [
      { label:'8am',  v:320 },{ label:'9am',  v:580 },
      { label:'10am', v:420 },{ label:'11am', v:950 },
      { label:'12pm', v:1850},{ label:'1pm',  v:2200},
      { label:'2pm',  v:1600},{ label:'3pm',  v:800 },
      { label:'4pm',  v:450 },{ label:'5pm',  v:620 },
    ];
    const salesSemana = [
      { label:'Lun', v:8500  },{ label:'Mar', v:9200 },
      { label:'Mié', v:7800  },{ label:'Jue', v:10200},
      { label:'Vie', v:12400 },{ label:'Sáb', v:15800},
      { label:'Dom', v:13200 },
    ];

    const mockOrders = [
      { id:'PED-001', mesa:'Mesa 3',   estado:'EN_PREP',   total:245.50, hora:'12:05', items:4 },
      { id:'PED-002', mesa:'Mesa 7',   estado:'LISTO',     total:180.00, hora:'11:50', items:3 },
      { id:'PED-003', mesa:'Domicilio',estado:'RECIBIDO',  total:320.75, hora:'12:10', items:5 },
      { id:'PED-004', mesa:'Mesa 1',   estado:'FACTURADO', total:95.00,  hora:'11:30', items:2 },
      { id:'PED-005', mesa:'Mesa 5',   estado:'DESPACHADO',total:410.00, hora:'11:15', items:6 },
      { id:'PED-006', mesa:'Domicilio',estado:'CANCELADO', total:155.00, hora:'10:55', items:2 },
      { id:'PED-007', mesa:'Mesa 9',   estado:'EN_PREP',   total:275.00, hora:'12:20', items:4 },
      { id:'PED-008', mesa:'Mesa 2',   estado:'LISTO',     total:190.50, hora:'12:00', items:3 },
    ];

    const estadoBadge = {
      RECIBIDO:   { label:'RECIBIDO',   cls:'badge-gray'    },
      EN_PREP:    { label:'EN PREP.',   cls:'badge-blue'    },
      LISTO:      { label:'LISTO',      cls:'badge-green'   },
      DESPACHADO: { label:'DESPACHADO', cls:'badge-orange'  },
      FACTURADO:  { label:'FACTURADO',  cls:'badge-green-d' },
      CANCELADO:  { label:'CANCELADO',  cls:'badge-red'     },
    };

    /* ─── Render KPIs ─── */
    document.getElementById('kpiGrid').innerHTML = kpis.map(k => `
      <div class="kpi-card">
        <div class="kpi-top">
          <div class="kpi-icon" style="background:${k.bg}">
            <i class="bi ${k.icon}" style="color:${k.color}"></i>
          </div>
          <i class="bi bi-arrow-up-right kpi-arr"></i>
        </div>
        <div class="kpi-value">${k.value}</div>
        <div class="kpi-label">${k.label}</div>
        <div class="kpi-sub" style="color:${k.color}">${k.sub}</div>
      </div>`).join('');

    /* ─── Mesa Status ─── */
    const mesaItems = [
      { label:'Libres',    count:7, color:'#2E7D32', bg:'#E8F5E9' },
      { label:'Ocupadas',  count:3, color:'#C62828', bg:'#FFEBEE' },
      { label:'Reservadas',count:2, color:'#F57C00', bg:'#FFF3E0' },
    ];
    document.getElementById('mesaStatus').innerHTML = mesaItems.map(m => `
      <div style="display:flex;align-items:center;gap:12px;">
        <div class="mesa-dot" style="background:${m.color}"></div>
        <span style="flex:1;font-size:13px;color:#6B7280">${m.label}</span>
        <span class="badge" style="background:${m.bg};color:${m.color}">${m.count}</span>
      </div>`).join('');

    /* ─── Orders Table ─── */
    document.getElementById('ordersTable').innerHTML = mockOrders.map(o => {
      const b = estadoBadge[o.estado] || estadoBadge.RECIBIDO;
      return `<tr>
        <td><span class="font-mono font-semi" style="color:#374151">${o.id}</span></td>
        <td>
          <div class="font-semi" style="color:#1F2937">${o.mesa}</div>
          <div class="text-xs text-muted">${o.items} ítems</div>
        </td>
        <td><span class="badge ${b.cls}">${b.label}</span></td>
        <td class="font-semi" style="color:#374151">Q ${o.total.toFixed(2)}</td>
        <td style="color:#9CA3AF">${o.hora}</td>
        <td class="r">
          <button class="row-action"><i class="bi bi-eye"></i></button>
          <button class="row-action"><i class="bi bi-three-dots-vertical"></i></button>
        </td>
      </tr>`;
    }).join('');

    /* ─── Charts ─── */
    let currentTab = 'hoy';

    function switchTab(tab) {
      currentTab = tab;
      document.getElementById('tabHoy').classList.toggle('active', tab === 'hoy');
      document.getElementById('tabSemana').classList.toggle('active', tab === 'semana');
      renderChart();
    }

    function renderChart() {
      const container = document.getElementById('chartArea');
      const W = container.clientWidth || 560;
      const H = 200;
      const pad = { top:12, right:16, bottom:32, left:44 };
      const innerW = W - pad.left - pad.right;
      const innerH = H - pad.top - pad.bottom;

      if (currentTab === 'hoy') {
        renderAreaChart(container, salesHoy, W, H, innerW, innerH, pad);
      } else {
        renderBarChart(container, salesSemana, W, H, innerW, innerH, pad);
      }
    }

    function renderAreaChart(container, data, W, H, innerW, innerH, pad) {
      const vals = data.map(d => d.v);
      const maxV = Math.max(...vals);
      const minV = 0;
      const rangeV = maxV - minV || 1;

      const xStep = innerW / (data.length - 1);

      const pts = data.map((d, i) => ({
        x: pad.left + i * xStep,
        y: pad.top + innerH - ((d.v - minV) / rangeV) * innerH,
      }));

      // Smooth path using bezier
      let pathD = `M ${pts[0].x} ${pts[0].y}`;
      for (let i = 1; i < pts.length; i++) {
        const cx = (pts[i-1].x + pts[i].x) / 2;
        pathD += ` C ${cx} ${pts[i-1].y}, ${cx} ${pts[i].y}, ${pts[i].x} ${pts[i].y}`;
      }

      const areaD = pathD
        + ` L ${pts[pts.length-1].x} ${pad.top+innerH}`
        + ` L ${pts[0].x} ${pad.top+innerH} Z`;

      // Y-axis labels (3 lines)
      const yLabels = [0, Math.round(maxV/2), maxV].map((v, i) => {
        const y = pad.top + innerH - (i * innerH / 2);
        return `<text x="${pad.left - 6}" y="${y + 4}" text-anchor="end" fill="#9CA3AF" font-size="10">Q${v >= 1000 ? Math.round(v/100)*100+'':v}</text>
          <line x1="${pad.left}" y1="${y}" x2="${pad.left+innerW}" y2="${y}" stroke="#F3F4F6" stroke-width="1"/>`;
      }).join('');

      // X-axis labels
      const xLabels = data.map((d, i) => `
        <text x="${pad.left + i * xStep}" y="${H - 6}" text-anchor="middle" fill="#9CA3AF" font-size="10">${d.label}</text>
      `).join('');

      container.innerHTML = `
        <svg viewBox="0 0 ${W} ${H}" preserveAspectRatio="none">
          <defs>
            <linearGradient id="areaGrad" x1="0" y1="0" x2="0" y2="1">
              <stop offset="5%"  stop-color="#1A2E4A" stop-opacity="0.15"/>
              <stop offset="95%" stop-color="#1A2E4A" stop-opacity="0"/>
            </linearGradient>
          </defs>
          ${yLabels}
          ${xLabels}
          <path d="${areaD}" fill="url(#areaGrad)"/>
          <path d="${pathD}" fill="none" stroke="#1A2E4A" stroke-width="2" stroke-linejoin="round"/>
          ${pts.map(p => `<circle cx="${p.x}" cy="${p.y}" r="3" fill="#1A2E4A"/>`).join('')}
        </svg>`;
    }

    function renderBarChart(container, data, W, H, innerW, innerH, pad) {
      const maxV = Math.max(...data.map(d => d.v));
      const barW = Math.floor(innerW / data.length * 0.55);
      const gap  = innerW / data.length;

      const bars = data.map((d, i) => {
        const barH = Math.round((d.v / maxV) * innerH);
        const x = pad.left + i * gap + (gap - barW) / 2;
        const y = pad.top + innerH - barH;
        return `
          <rect x="${x}" y="${y}" width="${barW}" height="${barH}" fill="#E8A020" rx="4" ry="4"/>
          <text x="${x + barW/2}" y="${H - 6}" text-anchor="middle" fill="#9CA3AF" font-size="10">${d.label}</text>`;
      }).join('');

      const yLines = [0, Math.round(maxV/2), maxV].map((v, i) => {
        const y = pad.top + innerH - (i * innerH / 2);
        return `<text x="${pad.left-6}" y="${y+4}" text-anchor="end" fill="#9CA3AF" font-size="10">Q${(v/1000).toFixed(0)}k</text>
          <line x1="${pad.left}" y1="${y}" x2="${pad.left+innerW}" y2="${y}" stroke="#F3F4F6" stroke-width="1"/>`;
      }).join('');

      container.innerHTML = `<svg viewBox="0 0 ${W} ${H}" preserveAspectRatio="none">${yLines}${bars}</svg>`;
    }

    // Initial render + re-render on resize
    renderChart();
    window.addEventListener('resize', renderChart);
  </script>
</body>
</html>
