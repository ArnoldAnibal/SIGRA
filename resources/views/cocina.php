<?php
require_once __DIR__ . '/includes/bootstrap.php';
sigra_require_role(['ADMIN', 'COCINA']);
$SIGRA_USER = sigra_current_user();

$res = api_pedidos(sigra_token());
$pedidosRaw = $res['data']['data'] ?? $res['data'] ?? [];

// Obtener productos SIN normalizar para tener id_producto original
$resMenuRaw = api_request('GET', '/productos-menu', [], sigra_token());
$menuRaw = $resMenuRaw['data']['data'] ?? $resMenuRaw['data'] ?? [];
$menuMap = [];
foreach ($menuRaw as $prod) {
    $menuMap[$prod['id_producto']] = $prod['nombre'];
}

$SIGRA_COCINA_PEDIDOS = array_values(array_map(function($p) use ($menuMap) {
    $detalles = $p['detalles'] ?? [];
    $items = array_map(fn($d) => [
        'nombre'   => $menuMap[$d['id_producto']] ?? 'Producto #' . $d['id_producto'],
        'cantidad' => $d['cantidad'],
        'notas'    => $d['notas'] ?? '',
    ], $detalles);
    return [
        'id'         => 'PED-' . str_pad($p['id_pedido'], 3, '0', STR_PAD_LEFT),
        'mesa'       => isset($p['id_mesa']) ? 'Mesa ' . $p['id_mesa'] : ($p['tipo'] ?? 'Mesa'),
        'tipo'       => $p['tipo'] === 'Para Aca' ? 'MESA' : ($p['tipo'] === 'Online' ? 'DOMICILIO' : 'LLEVAR'),
        'estado'     => $p['estado'] === 'Pendiente' ? 'RECIBIDO' : 'EN_PREP',
        'recibidoEn' => strtotime($p['created_at'] ?? 'now') * 1000,
        'items'      => $items,
    ];
}, array_filter($pedidosRaw, fn($p) => in_array($p['estado'], ['Pendiente', 'Preparando']))));
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIGRA — Pantalla de Cocina</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="styles.css">
  <style>
    /* ── Tema claro profesional para KDS ── */
    .kds-shell {
      display: flex; flex-direction: column;
      min-height: calc(100vh - 64px);
      background: #F4F6FA;
    }

    /* Header */
    .kds-bar {
      display: flex; align-items: center; justify-content: space-between;
      padding: 18px 28px;
      background: #fff;
      border-bottom: 1px solid #E5E9F0;
      flex-wrap: wrap; gap: 14px;
    }
    .kds-bar-left  { display: flex; align-items: center; gap: 14px; }
    .kds-bar-right { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }

    .kds-icon-box {
      width: 44px; height: 44px; border-radius: 10px;
      background: var(--navy);
      display: flex; align-items: center; justify-content: center;
      color: #fff; font-size: 20px;
    }
    .kds-title    { font-size: 18px; font-weight: 700; color: var(--navy); margin: 0; }
    .kds-subtitle { font-size: 12.5px; color: #6B7280; margin-top: 2px; }

    .kds-stat {
      display: inline-flex; align-items: center; gap: 8px;
      padding: 8px 14px; border-radius: 10px;
      background: #F4F6FA; border: 1px solid #E5E9F0;
      font-size: 13px; color: #374151; font-weight: 600;
    }
    .kds-stat strong { color: var(--navy); font-size: 15px; }
    .kds-stat .dot { width: 8px; height: 8px; border-radius: 50%; }

    .kds-clock {
      font-variant-numeric: tabular-nums;
      font-size: 14px; font-weight: 600; color: #1F2937;
    }

    /* Filtros */
    .kds-filters {
      display: flex; gap: 8px; padding: 14px 28px;
      background: #fff; border-bottom: 1px solid #E5E9F0;
      overflow-x: auto;
    }
    .kds-chip {
      padding: 7px 16px; border-radius: 999px;
      border: 1px solid #E5E9F0; background: #fff;
      font-size: 13px; font-weight: 500; color: #4B5563;
      cursor: pointer; transition: all .15s; white-space: nowrap;
    }
    .kds-chip:hover { background: #F4F6FA; }
    .kds-chip.active { background: var(--navy); color: #fff; border-color: var(--navy); }
    .kds-chip-count {
      display: inline-block; margin-left: 6px;
      font-size: 11px; padding: 1px 7px;
      background: rgba(0,0,0,.08); border-radius: 999px;
    }
    .kds-chip.active .kds-chip-count { background: rgba(255,255,255,.22); }

    /* Sección */
    .kds-section { padding: 22px 28px; }
    .kds-section-title {
      font-size: 13px; font-weight: 700; text-transform: uppercase;
      letter-spacing: .06em; color: #6B7280;
      margin-bottom: 14px; display: flex; align-items: center; gap: 8px;
    }
    .kds-section-title .count {
      background: #fff; border: 1px solid #E5E9F0;
      color: #374151; font-size: 11px; padding: 1px 8px; border-radius: 999px;
    }

    /* Grid */
    .kds-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 16px;
    }

    /* Ticket card */
    .ticket {
      background: #fff; border-radius: 12px;
      border: 1px solid #E5E9F0;
      box-shadow: 0 1px 2px rgba(13,27,42,.04);
      overflow: hidden; position: relative;
      display: flex; flex-direction: column;
    }
    .ticket-stripe {
      position: absolute; left: 0; top: 0; bottom: 0;
      width: 4px;
    }
    .ticket-head {
      padding: 14px 18px 10px 18px;
      display: flex; align-items: flex-start; justify-content: space-between;
      border-bottom: 1px dashed #E5E9F0;
    }
    .ticket-id {
      font-family: 'SF Mono', Menlo, Consolas, monospace;
      font-size: 14px; font-weight: 700; color: var(--navy);
    }
    .ticket-loc { font-size: 13px; color: #4B5563; margin-top: 2px; }
    .ticket-type {
      display: inline-block; margin-left: 6px;
      padding: 1px 8px; border-radius: 4px;
      font-size: 10.5px; font-weight: 700; letter-spacing: .04em;
      text-transform: uppercase;
    }
    .ticket-type.mesa      { background: #EFF6FF; color: #1D4ED8; }
    .ticket-type.domicilio { background: #FEF3C7; color: #92400E; }

    .ticket-time {
      display: flex; align-items: center; gap: 5px;
      font-size: 13px; font-weight: 700;
      font-variant-numeric: tabular-nums;
    }
    .ticket-time i { font-size: 13px; }

    .ticket-items { padding: 12px 18px 14px 18px; flex: 1; }
    .ticket-item {
      display: flex; align-items: flex-start; gap: 12px;
      padding: 8px 0;
      border-bottom: 1px solid #F4F6FA;
    }
    .ticket-item:last-child { border-bottom: none; }
    .ticket-item-qty {
      min-width: 28px; height: 28px; border-radius: 6px;
      display: flex; align-items: center; justify-content: center;
      font-size: 13px; font-weight: 700; color: #fff;
      flex-shrink: 0;
    }
    .ticket-item-body { flex: 1; min-width: 0; }
    .ticket-item-name { font-size: 14px; font-weight: 600; color: #1F2937; }
    .ticket-item-notes {
      font-size: 12px; color: #6B7280; margin-top: 3px;
      display: flex; align-items: flex-start; gap: 5px;
    }
    .ticket-item-notes i { margin-top: 2px; flex-shrink: 0; }

    .ticket-foot {
      padding: 12px 14px; background: #F9FAFB;
      border-top: 1px solid #E5E9F0;
      display: flex; gap: 8px;
    }
    .ticket-btn {
      flex: 1; padding: 10px;
      border-radius: 8px; border: none; cursor: pointer;
      font-size: 13px; font-weight: 600;
      display: flex; align-items: center; justify-content: center; gap: 6px;
      transition: opacity .15s, transform .15s;
    }
    .ticket-btn:hover { opacity: .9; }
    .ticket-btn:active { transform: scale(.98); }
    .ticket-btn-primary { background: var(--navy); color: #fff; }
    .ticket-btn-success { background: #16A34A; color: #fff; }
    .ticket-btn-ghost   { background: #fff; color: #6B7280; border: 1px solid #E5E9F0; }

    /* Listos */
    .ready-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 12px;
    }
    .ready-card {
      background: #fff; border-radius: 10px;
      border: 1px solid #BBF7D0;
      padding: 12px 14px;
      display: flex; align-items: center; justify-content: space-between; gap: 10px;
    }
    .ready-card-body { min-width: 0; }
    .ready-card-id   { font-family: 'SF Mono', Menlo, monospace; font-size: 13px; font-weight: 700; color: #166534; }
    .ready-card-loc  { font-size: 13px; color: #374151; margin-top: 2px; }
    .ready-card-meta { font-size: 11.5px; color: #6B7280; margin-top: 1px; }
    .ready-card-btn {
      width: 36px; height: 36px; border-radius: 8px;
      border: none; background: #16A34A; color: #fff;
      cursor: pointer; flex-shrink: 0;
      display: flex; align-items: center; justify-content: center; font-size: 16px;
    }
    .ready-card-btn:hover { opacity: .9; }

    /* Empty */
    .kds-empty {
      display: flex; flex-direction: column; align-items: center; justify-content: center;
      padding: 60px 24px; text-align: center;
      background: #fff; border: 1px dashed #E5E9F0; border-radius: 12px;
      color: #9CA3AF;
    }
    .kds-empty i { font-size: 36px; opacity: .45; margin-bottom: 10px; color: var(--navy); }
    .kds-empty h3 { color: #374151; font-size: 15px; font-weight: 600; margin-bottom: 4px; }
    .kds-empty p  { font-size: 13px; color: #9CA3AF; max-width: 320px; }

    /* Pulse para urgentes */
    .pulse-urgent { animation: pulseRed 1.8s ease-in-out infinite; }
    @keyframes pulseRed {
      0%,100% { box-shadow: 0 0 0 0 rgba(220,38,38,.0); }
      50%      { box-shadow: 0 0 0 6px rgba(220,38,38,.12); }
    }

    @media (max-width: 600px) {
      .kds-bar, .kds-filters, .kds-section { padding-left: 16px; padding-right: 16px; }
    }
  </style>
</head>
<body>
  <div class="app">
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <aside class="sidebar" id="sidebar"></aside>

    <div class="main-wrap" id="mainWrap">
      <header class="topbar" id="topbar"></header>

      <main class="page-content" style="overflow:auto">
        <div class="kds-shell">

          <!-- Header -->
          <div class="kds-bar">
            <div class="kds-bar-left">
              <div class="kds-icon-box"><i class="bi bi-fire"></i></div>
              <div>
                <div class="kds-title">Pantalla de Cocina</div>
                <div class="kds-subtitle">Tablero de pedidos en tiempo real</div>
              </div>
            </div>
            <div class="kds-bar-right">
              <div class="kds-stat">
                <span class="dot" style="background:#1D4ED8"></span>
                Activos: <strong id="statActive">0</strong>
              </div>
              <div class="kds-stat">
                <span class="dot" style="background:#16A34A"></span>
                Listos: <strong id="statReady">0</strong>
              </div>
              <div class="kds-stat">
                <span class="dot" style="background:#DC2626"></span>
                Urgentes: <strong id="statUrgent">0</strong>
              </div>
              <div class="kds-stat">
                <i class="bi bi-clock"></i>
                <span class="kds-clock" id="clock">--:--</span>
              </div>
            </div>
          </div>

          <!-- Filtros -->
          <div class="kds-filters">
            <button class="kds-chip active" data-filter="todos" onclick="setFilter('todos')">
              Todos <span class="kds-chip-count" id="cTodos">0</span>
            </button>
            <button class="kds-chip" data-filter="MESA" onclick="setFilter('MESA')">
              Mesa <span class="kds-chip-count" id="cMesa">0</span>
            </button>
            <button class="kds-chip" data-filter="DOMICILIO" onclick="setFilter('DOMICILIO')">
              Domicilio <span class="kds-chip-count" id="cDom">0</span>
            </button>
            <button class="kds-chip" data-filter="urgentes" onclick="setFilter('urgentes')">
              Urgentes <span class="kds-chip-count" id="cUrg">0</span>
            </button>
          </div>

          <!-- En Preparación -->
          <div class="kds-section">
            <div class="kds-section-title">
              <i class="bi bi-fire" style="color:#DC2626"></i>
              En Preparación
              <span class="count" id="countActive">0</span>
            </div>
            <div id="activeGrid" class="kds-grid"></div>
          </div>

          <!-- Listos para despacho -->
          <div class="kds-section" id="readySection" style="display:none">
            <div class="kds-section-title">
              <i class="bi bi-check2-circle" style="color:#16A34A"></i>
              Listos para Despacho
              <span class="count" id="countReady">0</span>
            </div>
            <div id="readyGrid" class="ready-grid"></div>
          </div>

        </div>
      </main>
    </div>
  </div>

<?= sigra_bootstrap_script([
    'session' => $SIGRA_USER,
    'cola' => $SIGRA_COCINA_PEDIDOS
]) ?>

  <script src="layout.js"></script>
  <script src="sigra-data.js"></script>
  <script>
    SIGRA.initLayout('Cocina (KDS)');

    let orders = SIGRA_DATA.getCocina();
    let currentFilter = 'todos';

    /* ── Tiempo en min desde recepción ── */
    function minSince(ts) {
      if (!ts) return 0;
      return Math.floor((Date.now() - ts) / 60000);
    }

    function getColor(min) {
      if (min >= 20) return '#DC2626'; // rojo
      if (min >= 10) return '#F59E0B'; // ámbar
      return '#1D4ED8';                // azul
    }

    function getColorLabel(min) {
      if (min >= 20) return 'Urgente';
      if (min >= 10) return 'Atención';
      return 'Normal';
    }

    function updateStatus(id, newStatus) {
      orders = orders.map(o => o.id === id ? { ...o, estado: newStatus } : o);
      SIGRA_DATA.setCocina(orders);
      if (newStatus === 'DESPACHADO') {
        orders = orders.filter(o => o.id !== id);
        SIGRA_DATA.setCocina(orders);
      }
      render();
    }

    function setFilter(f) {
      currentFilter = f;
      document.querySelectorAll('.kds-chip').forEach(c => c.classList.toggle('active', c.dataset.filter === f));
      render();
    }

    function filterActive(arr) {
      if (currentFilter === 'todos') return arr;
      if (currentFilter === 'urgentes') return arr.filter(o => minSince(o.recibidoEn) >= 20);
      return arr.filter(o => o.tipo === currentFilter);
    }

    function render() {
      orders = SIGRA_DATA.getCocina();
      const active = orders.filter(o => o.estado !== 'LISTO' && o.estado !== 'DESPACHADO');
      const ready  = orders.filter(o => o.estado === 'LISTO');
      const urgent = active.filter(o => minSince(o.recibidoEn) >= 20);

      /* Stats */
      document.getElementById('statActive').textContent = active.length;
      document.getElementById('statReady').textContent  = ready.length;
      document.getElementById('statUrgent').textContent = urgent.length;
      document.getElementById('cTodos').textContent = active.length;
      document.getElementById('cMesa').textContent  = active.filter(o => o.tipo === 'MESA').length;
      document.getElementById('cDom').textContent   = active.filter(o => o.tipo === 'DOMICILIO').length;
      document.getElementById('cUrg').textContent   = urgent.length;
      document.getElementById('countActive').textContent = active.length;
      document.getElementById('countReady').textContent  = ready.length;

      /* Active */
      const visible = filterActive(active);
      const grid = document.getElementById('activeGrid');
      if (visible.length === 0) {
        grid.innerHTML = `
          <div class="kds-empty" style="grid-column:1/-1">
            <i class="bi bi-bag-check"></i>
            <h3>Sin pedidos en preparación</h3>
            <p>Cuando los meseros envíen un pedido a cocina aparecerá aquí.</p>
          </div>`;
      } else {
        grid.innerHTML = visible.map(o => {
          const min = minSince(o.recibidoEn);
          const col = getColor(min);
          const pulse = min >= 20 ? 'pulse-urgent' : '';
          const typeClass = o.tipo === 'DOMICILIO' ? 'domicilio' : 'mesa';
          const typeLabel = o.tipo === 'DOMICILIO' ? 'Domicilio' : 'Mesa';
          const action = o.estado === 'RECIBIDO'
            ? `<button class="ticket-btn ticket-btn-primary" onclick="updateStatus('${o.id}','EN_PREP')">
                 <i class="bi bi-play-fill"></i> Iniciar preparación
               </button>`
            : `<button class="ticket-btn ticket-btn-success" onclick="updateStatus('${o.id}','LISTO')">
                 <i class="bi bi-check2"></i> Marcar como listo
               </button>`;
          return `
            <article class="ticket ${pulse}">
              <div class="ticket-stripe" style="background:${col}"></div>
              <div class="ticket-head" style="padding-left:22px">
                <div style="min-width:0">
                  <div style="display:flex;align-items:center;flex-wrap:wrap;gap:4px">
                    <span class="ticket-id">${o.id}</span>
                    <span class="ticket-type ${typeClass}">${typeLabel}</span>
                  </div>
                  <div class="ticket-loc">${o.mesa}</div>
                </div>
                <div class="ticket-time" style="color:${col}">
                  <i class="bi bi-clock"></i> ${min} min
                </div>
              </div>
              <div class="ticket-items" style="padding-left:22px">
                ${o.items.map(it => `
                  <div class="ticket-item">
                    <div class="ticket-item-qty" style="background:${col}">${it.cantidad}</div>
                    <div class="ticket-item-body">
                      <div class="ticket-item-name">${it.nombre}</div>
                      ${it.notas ? `<div class="ticket-item-notes">
                        <i class="bi bi-sticky"></i><span>${it.notas}</span>
                      </div>` : ''}
                    </div>
                  </div>`).join('')}
              </div>
              <div class="ticket-foot">${action}</div>
            </article>`;
        }).join('');
      }

      /* Ready */
      const ready2 = ready;
      const readySection = document.getElementById('readySection');
      if (ready2.length === 0) {
        readySection.style.display = 'none';
      } else {
        readySection.style.display = 'block';
        document.getElementById('readyGrid').innerHTML = ready2.map(o => `
          <div class="ready-card">
            <div class="ready-card-body">
              <div class="ready-card-id">${o.id}</div>
              <div class="ready-card-loc">${o.mesa}</div>
              <div class="ready-card-meta">${o.items.reduce((a,i)=>a+i.cantidad,0)} ítems · ${minSince(o.recibidoEn)} min</div>
            </div>
            <button class="ready-card-btn" title="Despachado" onclick="updateStatus('${o.id}','DESPACHADO')">
              <i class="bi bi-box-arrow-right"></i>
            </button>
          </div>`).join('');
      }
    }

    /* Reloj */
    function tickClock() {
      const d = new Date();
      const hh = String(d.getHours()).padStart(2,'0');
      const mm = String(d.getMinutes()).padStart(2,'0');
      const ss = String(d.getSeconds()).padStart(2,'0');
      document.getElementById('clock').textContent = `${hh}:${mm}:${ss}`;
    }

    setInterval(tickClock, 1000);
    setInterval(render, 30000);
    window.addEventListener('storage', (e) => {
      if (e.key === SIGRA_DATA.KEYS.COCINA) render();
    });

    tickClock();
    render();
  </script>
</body>
</html>
