<?php
require_once __DIR__ . '/includes/bootstrap.php';
sigra_require_role(['ADMIN', 'MESERO', 'CAJERO']);
$SIGRA_USER = sigra_current_user();
$resD = api_pedidos(sigra_token()); $SIGRA_PEDIDOS_DOMICILIO = array_values(array_filter($resD["data"]["data"] ?? $resD["data"] ?? [], fn($p) => in_array($p["tipo"], ["Para Llevar","Online"])));
$SIGRA_PEDIDOS_ONLINE = array_values(array_filter($resD["data"]["data"] ?? $resD["data"] ?? [], fn($p) => $p["tipo"] === "Online"));
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIGRA — Pedidos Domicilio</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="styles.css">
  <style>
    .pd-body { padding: 24px; overflow-y: auto; height: calc(100vh - 64px - 69px); }
    .pd-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; max-width: 1100px; margin: 0 auto; }
    @media (max-width: 900px) { .pd-grid { grid-template-columns: 1fr; } }

    .product-item {
      display: flex; align-items: flex-start; justify-content: space-between;
      padding: 10px 12px; border-radius: var(--r); border: 1px solid var(--border-lt);
      background: #fff; margin-bottom: 8px; transition: all .15s;
    }
    .product-item.in-order { border-color: #FCD34D; background: #FFFBEB; }
    .product-name { font-size: 13.5px; font-weight: 500; color: #1F2937; }
    .product-desc { font-size: 12px; color: #9CA3AF; margin-top: 2px; }
    .product-notes {
      width: 100%; font-size: 12px; border: 1px solid var(--border);
      border-radius: 4px; padding: 4px 8px; margin-top: 6px; outline: none; background: #fff;
    }
    .qty-ctrl { display: flex; align-items: center; gap: 6px; }
    .qty-btn {
      width: 28px; height: 28px; border-radius: 50%;
      border: 1px solid var(--border);
      display: flex; align-items: center; justify-content: center; font-size: 14px; transition: all .15s;
    }
    .qty-btn:hover { background: #F3F4F6; }
    .qty-btn.add { background: var(--amber); color: #fff; border-color: var(--amber); }
    .qty-val { width: 24px; text-align: center; font-size: 13.5px; font-weight: 600; }

    .client-card {
      background: #EEF2FF; border: 1px solid #C7D2FE;
      border-radius: var(--r); padding: 12px;
      display: flex; align-items: flex-start; gap: 10px;
    }
    .client-avatar {
      width: 36px; height: 36px; border-radius: 50%;
      background: var(--navy); color: #fff;
      display: flex; align-items: center; justify-content: center;
      font-weight: 600; font-size: 14px; flex-shrink: 0;
    }
    .order-total-row {
      display: flex; justify-content: space-between; align-items: center;
      font-weight: 700; color: var(--navy);
      padding-top: 8px; border-top: 1px solid var(--border-lt);
    }
    .summary-row { display: flex; justify-content: space-between; font-size: 13px; color: #374151; margin-bottom: 6px; }
    .summary-notes { font-size: 11px; color: #9CA3AF; font-style: italic; }
  </style>
</head>
<body>
  <div class="app">
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <aside class="sidebar" id="sidebar"></aside>

    <div class="main-wrap" id="mainWrap">
      <header class="topbar" id="topbar"></header>

      <main class="page-content">
        <div class="page-header">
          <div class="page-header-row">
            <div>
              <div class="page-title" style="display:flex;align-items:center;gap:10px">
                Pedidos Domicilio / Para Llevar
                <span class="badge badge-amber" id="tipoBadge">DOMICILIO</span>
              </div>
              <div class="page-sub">Elige el tipo y registra el pedido — llegará directo a cocina</div>
              <div style="margin-top:10px;display:inline-flex;background:#F3F4F6;border-radius:10px;padding:4px;gap:4px">
                <button id="tipoDom" onclick="setTipo('DOMICILIO')" style="padding:8px 16px;border:none;background:#1A2E4A;color:#fff;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer">
                  <i class="bi bi-truck"></i> Domicilio
                </button>
                <button id="tipoLlevar" onclick="setTipo('LLEVAR')" style="padding:8px 16px;border:none;background:transparent;color:#6B7280;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer">
                  <i class="bi bi-bag"></i> Para llevar
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="pd-body">
          <div class="pd-grid">

            <!-- LEFT: Client & Summary -->
            <div style="display:flex;flex-direction:column;gap:20px">

              <!-- Client card -->
              <div class="card">
                <div class="card-header">
                  <span class="card-title"><i class="bi bi-person" style="margin-right:6px"></i>Datos del Cliente</span>
                </div>
                <div class="card-body" style="display:flex;flex-direction:column;gap:14px">

                  <!-- Search -->
                  <div class="form-group" style="margin-bottom:0">
                    <label class="form-label">Cliente</label>
                    <div class="input-group relative">
                      <i class="bi bi-search input-icon"></i>
                      <input type="text" id="clientSearch" class="form-control" placeholder="Buscar por nombre o teléfono..."
                        oninput="onSearchInput()" onfocus="onSearchFocus()">
                      <button class="input-action" id="clearClientBtn" style="display:none" onclick="clearClient()">
                        <i class="bi bi-x-circle"></i>
                      </button>
                      <div class="autocomplete-list" id="autocompleteList" style="display:none"></div>
                    </div>
                    <div class="form-error" id="clientError">Seleccione un cliente registrado.</div>
                  </div>

                  <!-- Selected client card -->
                  <div class="client-card" id="clientCard" style="display:none">
                    <div class="client-avatar" id="clientAvatar"></div>
                    <div>
                      <div style="font-size:13.5px;font-weight:600;color:#1F2937" id="clientName"></div>
                      <div style="font-size:12px;color:#6B7280;margin-top:2px">
                        <i class="bi bi-phone" style="font-size:10px;margin-right:4px"></i>
                        <span id="clientPhone"></span>
                      </div>
                    </div>
                  </div>

                  <!-- Address -->
                  <div class="form-group" style="margin-bottom:0">
                    <label class="form-label"><i class="bi bi-geo-alt" style="margin-right:4px;font-size:12px"></i>Dirección de Entrega</label>
                    <textarea id="deliveryAddress" class="form-control" rows="2"
                      placeholder="Ingrese la dirección completa de entrega..." oninput="clearErr('addressError')"></textarea>
                    <div class="form-error" id="addressError">La dirección de entrega es requerida.</div>
                  </div>

                  <!-- Notes -->
                  <div class="form-group" style="margin-bottom:0">
                    <label class="form-label">Notas del Pedido</label>
                    <textarea id="orderNotes" class="form-control" rows="2"
                      placeholder="Instrucciones especiales de entrega..."></textarea>
                  </div>
                </div>
              </div>

              <!-- Order Summary -->
              <div class="card">
                <div class="card-header">
                  <span class="card-title">Resumen del Pedido</span>
                </div>
                <div class="card-body">
                  <div id="summaryEmpty" style="text-align:center;padding:24px;color:#9CA3AF;font-size:13px">
                    No hay ítems en el pedido
                  </div>
                  <div id="summaryList" style="display:none;margin-bottom:12px"></div>
                  <div class="form-error" id="itemsError" style="margin-bottom:8px">Debe agregar al menos un producto.</div>
                  <button class="btn btn-primary" id="submitOrderBtn" style="width:100%;height:48px;font-size:15px" onclick="submitOrder()">
                    <i class="bi bi-send"></i> Registrar Pedido Domicilio
                  </button>
                </div>
              </div>

            </div>

            <!-- RIGHT: Products -->
            <div class="card" style="display:flex;flex-direction:column;min-height:500px">
              <div class="tab-bar" id="catTabs"></div>
              <div style="flex:1;overflow-y:auto;padding:12px" id="productList"></div>
            </div>

          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Success Modal -->
  <div class="modal-overlay" id="successModal">
    <div class="modal" style="text-align:center;max-width:380px">
      <div class="modal-icon" style="background:#E8F5E9;width:64px;height:64px">
        <i class="bi bi-check-circle" style="color:#2E7D32;font-size:32px"></i>
      </div>
      <h3 style="font-size:18px;font-weight:700;color:#1A2E4A;margin-bottom:8px">¡Pedido Registrado!</h3>
      <p style="font-size:13px;color:#6B7280" id="successMsg"></p>
    </div>
  </div>

<?= sigra_bootstrap_script([
    'session' => $SIGRA_USER,
    'pedidos_domicilio' => $SIGRA_PEDIDOS_DOMICILIO, 'pedidos_online' => $SIGRA_PEDIDOS_ONLINE
]) ?>

  <script src="layout.js"></script>
  <script src="sigra-data.js"></script>
  <script>
    SIGRA.initLayout('Pedidos Domicilio / Llevar');

    const clients = [
      { id:1, nombre:'Roberto Castellanos', telefono:'5555-1234', direccion:'6a Av. 3-45, Puerto Barrios' },
      { id:2, nombre:'Lucía Morales',        telefono:'5555-5678', direccion:'10a Calle 2-10, Puerto Barrios' },
      { id:3, nombre:'Miguel Ángel Ruiz',    telefono:'5555-9012', direccion:'Barrio El Centro, Puerto Barrios' },
      { id:4, nombre:'Sandra Patricia López',telefono:'5555-3456', direccion:'Colonia Las Flores, Puerto Barrios' },
      { id:5, nombre:'Fernando Ajú',         telefono:'5555-7890', direccion:'Av. La Marina 15-20, Puerto Barrios' },
    ];

    const menuCategories = {
      bebidas: [
        { id:1,  nombre:'Agua Pura',          precio:10.00, descripcion:'500ml' },
        { id:2,  nombre:'Gaseosa',             precio:15.00, descripcion:'Coca-Cola, Pepsi' },
        { id:3,  nombre:'Jugo Natural',        precio:25.00, descripcion:'Naranja, Sandía, Mango' },
        { id:4,  nombre:'Café',                precio:20.00, descripcion:'Americano o con leche' },
        { id:5,  nombre:'Limonada',            precio:22.00, descripcion:'Con o sin soda' },
        { id:6,  nombre:'Cerveza',             precio:35.00, descripcion:'Gallo o Dorada' },
      ],
      platos: [
        { id:7,  nombre:'Churrasco a la Parrilla',precio:120.00,descripcion:'Con papas y ensalada' },
        { id:8,  nombre:'Filete de Mojarra',      precio:95.00, descripcion:'Frito o a la plancha' },
        { id:9,  nombre:'Caldo de Res',           precio:75.00, descripcion:'Con verduras de temporada' },
        { id:10, nombre:'Pollo a la Plancha',     precio:85.00, descripcion:'Con arroz y frijoles' },
        { id:11, nombre:'Mariscos al Ajillo',     precio:145.00,descripcion:'Camarones y calamar' },
        { id:12, nombre:'Pasta Marinara',         precio:65.00, descripcion:'Con salsa de tomate y hierbas' },
      ],
      entradas: [
        { id:13, nombre:'Guacamol con Tostadas',precio:35.00,descripcion:'Aguacate fresco' },
        { id:14, nombre:'Ceviche',              precio:55.00,descripcion:'Limón y cilantro' },
        { id:15, nombre:'Alitas de Pollo',      precio:65.00,descripcion:'BBQ o picante' },
        { id:16, nombre:'Sopa del Día',         precio:40.00,descripcion:'Consultar al mesero' },
      ],
      postres: [
        { id:17, nombre:'Pastel de Chocolate',precio:40.00,descripcion:'Con helado de vainilla' },
        { id:18, nombre:'Flan de Caramelo',   precio:35.00,descripcion:'Casero' },
        { id:19, nombre:'Tres Leches',        precio:38.00,descripcion:'Con merengue' },
        { id:20, nombre:'Helado',             precio:28.00,descripcion:'3 sabores' },
      ],
    };
    const catLabels = { bebidas:'Bebidas', platos:'Platos Fuertes', entradas:'Entradas', postres:'Postres' };

    /* State */
    let selectedClient = null;
    let orderItems = [];
    let activeCategory = 'platos';
    let tipoPedido = 'DOMICILIO';

    function setTipo(t) {
      tipoPedido = t;
      const isDom = t === 'DOMICILIO';
      document.getElementById('tipoBadge').textContent = isDom ? 'DOMICILIO' : 'PARA LLEVAR';
      document.getElementById('tipoBadge').className = isDom ? 'badge badge-amber' : 'badge';
      document.getElementById('tipoBadge').style.background = isDom ? '' : '#E3F2FD';
      document.getElementById('tipoBadge').style.color = isDom ? '' : '#1565C0';
      document.getElementById('tipoDom').style.background = isDom ? '#1A2E4A' : 'transparent';
      document.getElementById('tipoDom').style.color = isDom ? '#fff' : '#6B7280';
      document.getElementById('tipoLlevar').style.background = !isDom ? '#1A2E4A' : 'transparent';
      document.getElementById('tipoLlevar').style.color = !isDom ? '#fff' : '#6B7280';
      document.getElementById('submitOrderBtn').innerHTML = isDom
        ? '<i class="bi bi-send"></i> Registrar Pedido Domicilio'
        : '<i class="bi bi-bag-check"></i> Registrar Pedido Para Llevar';
      const addrLabel = document.querySelector('label[for="deliveryAddress"]') || document.querySelectorAll('.form-label')[1];
      const addrWrap = document.getElementById('deliveryAddress').parentElement;
      addrWrap.style.display = isDom ? '' : 'none';
    }

    /* ── Client search ── */
    function onSearchInput() {
      const q = document.getElementById('clientSearch').value.trim();
      if (!q) { hideAutocomplete(); clearClient(); return; }
      selectedClient = null;
      updateClientCard();
      const filtered = clients.filter(c =>
        c.nombre.toLowerCase().includes(q.toLowerCase()) || c.telefono.includes(q)
      );
      showAutocomplete(filtered);
    }
    function onSearchFocus() {
      const q = document.getElementById('clientSearch').value.trim();
      if (q && !selectedClient) onSearchInput();
    }
    function showAutocomplete(list) {
      const el = document.getElementById('autocompleteList');
      if (!list.length) { el.style.display = 'none'; return; }
      el.innerHTML = list.map(c => `
        <button class="autocomplete-item" onclick="selectClient(${c.id})">
          <div class="autocomplete-name">${c.nombre}</div>
          <div class="autocomplete-sub"><i class="bi bi-phone" style="font-size:10px;margin-right:3px"></i>${c.telefono}</div>
        </button>`).join('');
      el.style.display = 'block';
    }
    function hideAutocomplete() {
      document.getElementById('autocompleteList').style.display = 'none';
    }
    function selectClient(id) {
      selectedClient = clients.find(c => c.id === id);
      document.getElementById('clientSearch').value = selectedClient.nombre;
      document.getElementById('deliveryAddress').value = selectedClient.direccion;
      document.getElementById('clearClientBtn').style.display = 'block';
      hideAutocomplete();
      updateClientCard();
      clearErr('clientError');
    }
    function clearClient() {
      selectedClient = null;
      document.getElementById('clientSearch').value = '';
      document.getElementById('deliveryAddress').value = '';
      document.getElementById('clearClientBtn').style.display = 'none';
      updateClientCard();
    }
    function updateClientCard() {
      const card = document.getElementById('clientCard');
      if (selectedClient) {
        const init = selectedClient.nombre.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase();
        document.getElementById('clientAvatar').textContent = init;
        document.getElementById('clientName').textContent = selectedClient.nombre;
        document.getElementById('clientPhone').textContent = selectedClient.telefono;
        card.style.display = 'flex';
      } else {
        card.style.display = 'none';
      }
    }
    // Close autocomplete on outside click
    document.addEventListener('click', e => {
      if (!e.target.closest('#clientSearch') && !e.target.closest('#autocompleteList')) {
        hideAutocomplete();
      }
    });

    /* ── Category tabs ── */
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

    /* ── Products ── */
    function renderProducts() {
      const products = menuCategories[activeCategory] || [];
      document.getElementById('productList').innerHTML = products.map(p => {
        const inOrder = orderItems.find(i => i.id === p.id);
        return `
          <div class="product-item ${inOrder ? 'in-order' : ''}">
            <div style="flex:1;min-width:0">
              <div class="product-name">${p.nombre}</div>
              <div class="product-desc">${p.descripcion}</div>
              ${inOrder ? `<input class="product-notes" placeholder="Notas (opcional)"
                value="${inOrder.notas}" onchange="updateNotas(${p.id},this.value)">` : ''}
            </div>
            <div style="display:flex;align-items:center;gap:8px;margin-left:12px;flex-shrink:0">
              <span style="font-size:13.5px;font-weight:600;color:var(--navy)">Q ${p.precio.toFixed(2)}</span>
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
      const p = Object.values(menuCategories).flat().find(x => x.id === id);
      if (!p) return;
      const ex = orderItems.find(i => i.id === id);
      if (ex) ex.cantidad++; else orderItems.push({ ...p, cantidad:1, notas:'' });
      renderProducts();
      renderSummary();
    }
    function updateQty(id, delta) {
      const idx = orderItems.findIndex(i => i.id === id);
      if (idx === -1) return;
      orderItems[idx].cantidad += delta;
      if (orderItems[idx].cantidad <= 0) orderItems.splice(idx, 1);
      renderProducts();
      renderSummary();
    }
    function updateNotas(id, notas) {
      const item = orderItems.find(i => i.id === id);
      if (item) item.notas = notas;
    }

    function renderSummary() {
      const total = orderItems.reduce((a, i) => a + i.precio * i.cantidad, 0);
      const qty   = orderItems.reduce((a, i) => a + i.cantidad, 0);
      const list  = document.getElementById('summaryList');
      const empty = document.getElementById('summaryEmpty');
      if (orderItems.length === 0) {
        list.style.display = 'none'; empty.style.display = 'block';
      } else {
        empty.style.display = 'none'; list.style.display = 'block';
        list.innerHTML = orderItems.map(i => `
          <div class="summary-row">
            <div>
              <span style="font-weight:500">${i.cantidad}x ${i.nombre}</span>
              ${i.notas ? `<div class="summary-notes">${i.notas}</div>` : ''}
            </div>
            <span>Q ${(i.precio*i.cantidad).toFixed(2)}</span>
          </div>`).join('') +
          `<div class="order-total-row">
            <span>Total (${qty} ítems)</span><span>Q ${total.toFixed(2)}</span>
          </div>`;
      }
    }

    /* ── Validation & Submit ── */
    function clearErr(id) {
      document.getElementById(id).style.display = 'none';
      const ctrl = document.getElementById(id.replace('Error',''));
      if (ctrl) ctrl.classList.remove('is-invalid');
    }
    function showErr(id) {
      document.getElementById(id).style.display = 'block';
    }

    function submitOrder() {
      let valid = true;
      if (!selectedClient) { showErr('clientError'); valid = false; } else clearErr('clientError');
      const addr = document.getElementById('deliveryAddress').value.trim();
      if (tipoPedido === 'DOMICILIO') {
        if (!addr) { showErr('addressError'); valid = false; } else clearErr('addressError');
      } else clearErr('addressError');
      if (orderItems.length === 0) { showErr('itemsError'); valid = false; } else clearErr('itemsError');
      if (!valid) return;

      const total = orderItems.reduce((a, i) => a + i.precio * i.cantidad, 0);
      const notas = document.getElementById('orderNotes').value.trim();
      const user = SIGRA.getUser();
      const id = (window.SIGRA_DATA && SIGRA_DATA.nextPedidoId) ? SIGRA_DATA.nextPedidoId() : ('PED-' + Date.now());

      if (window.SIGRA_DATA) {
        SIGRA_DATA.addCocinaPedido({
          id,
          mesa: tipoPedido === 'LLEVAR' ? 'Para Llevar' : `Dom. ${selectedClient.nombre}`,
          tipo: tipoPedido,
          canal: 'MOSTRADOR',
          cliente: selectedClient.nombre,
          telefono: selectedClient.telefono,
          direccion: tipoPedido === 'DOMICILIO' ? addr : null,
          atendidoPor: user ? user.name : null,
          estado: 'NUEVO',
          tiempoMin: 0,
          total,
          notas,
          items: orderItems.map(i => ({ nombre: i.nombre, cantidad: i.cantidad, precio: i.precio, notas: i.notas || '' })),
        });
      }

      document.getElementById('successMsg').textContent = tipoPedido === 'LLEVAR'
        ? `Pedido Para Llevar ${id} registrado para ${selectedClient.nombre}. Listo para preparar.`
        : `Pedido domicilio ${id} para ${selectedClient.nombre} registrado y enviado a cocina.`;
      SIGRA.openModal('successModal');

      setTimeout(() => {
        SIGRA.closeModal('successModal');
        clearClient();
        orderItems = [];
        document.getElementById('orderNotes').value = '';
        renderProducts();
        renderSummary();
      }, 2500);
    }

    /* Init */
    renderCatTabs();
    renderProducts();
    renderSummary();
  </script>
</body>
</html>
